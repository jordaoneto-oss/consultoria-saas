<?php

namespace Consultoria\Modules\Wallet;

use Consultoria\Modules\BaseModule;

class WalletModule extends BaseModule {

    protected string $name = 'wallet';

    public function init(): void {
        $service = new WalletService();
        $this->registerService('wallet', $service);

        $this->addAction('cp_order_completed', [$service, 'onOrderCompleted'], 10, 2);
        $this->addAction('cp_hour_approved', [$service, 'onHourApproved'], 10, 3);
        $this->addAction('cp_withdrawal_approved', [$service, 'processWithdrawalPayment'], 10, 1);
        $this->addFilter('cp_shortcodes', [$this, 'registerShortcodes']);
    }

    public function registerShortcodes(array $shortcodes): array {
        $shortcodes['cp_wallet'] = [$this, 'renderWallet'];
        return $shortcodes;
    }

    public function renderWallet(): string {
        if (!is_user_logged_in()) return '<p>Faça login para acessar sua carteira.</p>';

        $userId = get_current_user_id();
        global $wpdb;
        $wallet = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cp_wallets WHERE user_id = %d",
            $userId
        ));

        $transactions = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cp_transactions WHERE wallet_id = %d ORDER BY created_at DESC LIMIT 20",
            $wallet->id ?? 0
        ));

        ob_start();
        include CP_PLUGIN_DIR . 'modules/wallet/templates/wallet.php';
        return ob_get_clean();
    }
}

class WalletService {

    private function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    public function getWallet(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $wallet = $this->getOrCreateWallet($userId);

        return \Consultoria\Helpers\Response::success([
            'wallet' => $wallet,
        ]);
    }

    public function getTransactions(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $wallet = $this->getOrCreateWallet($userId);
        $page = max(1, (int) ($request->get_param('page') ?? 1));
        $perPage = min(100, max(1, (int) ($request->get_param('per_page') ?? 20)));
        $offset = ($page - 1) * $perPage;

        $total = (int) $this->getDb()->get_var($this->getDb()->prepare(
            "SELECT COUNT(*) FROM {$this->getDb()->prefix}cp_transactions WHERE wallet_id = %d",
            $wallet->id
        ));

        $transactions = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_transactions WHERE wallet_id = %d ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $wallet->id, $perPage, $offset
        ));

        return \Consultoria\Helpers\Response::paginated($transactions, $total, $page, $perPage);
    }

    public function createWithdrawal(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $amount = (float) $request->get_param('amount');
        $pixKey = $request->get_param('pix_key');
        $pixKeyType = $request->get_param('pix_key_type');

        $validator = new \Consultoria\Helpers\Validator();
        if (!$validator->validate($request->get_params(), [
            'amount'      => 'required|numeric|min:1',
            'pix_key'     => 'required',
            'pix_key_type' => 'required|in:cpf,cnpj,email,phone,random',
        ])) {
            return \Consultoria\Helpers\Response::validationError($validator->getErrors());
        }

        $wallet = $this->getOrCreateWallet($userId);
        $minWithdrawal = (float) get_option('cp_min_withdrawal', 50);
        $maxWithdrawal = (float) get_option('cp_max_withdrawal', 50000);
        $withdrawalFee = (float) get_option('cp_withdrawal_fee', 5);

        if ($amount < $minWithdrawal) {
            return \Consultoria\Helpers\Response::error("Valor mínimo para saque é R$ {$minWithdrawal}");
        }

        if ($amount > $maxWithdrawal) {
            return \Consultoria\Helpers\Response::error("Valor máximo para saque é R$ {$maxWithdrawal}");
        }

        if ($amount > $wallet->balance) {
            return \Consultoria\Helpers\Response::error('Saldo insuficiente');
        }

        $result = $this->getDb()->insert($this->getDb()->prefix . 'cp_withdrawals', [
            'wallet_id'    => $wallet->id,
            'amount'       => $amount,
            'fee'          => $withdrawalFee,
            'pix_key'      => $pixKey,
            'pix_key_type' => $pixKeyType,
            'status'       => 'pending',
            'requested_at' => current_time('mysql'),
        ]);

        if (!$result) {
            return \Consultoria\Helpers\Response::error('Erro ao solicitar saque');
        }

        // Block balance
        $this->getDb()->update(
            $this->getDb()->prefix . 'cp_wallets',
            ['blocked_balance' => $wallet->blocked_balance + $amount],
            ['id' => $wallet->id]
        );

        return \Consultoria\Helpers\Response::created(['withdrawal_id' => $this->getDb()->insert_id]);
    }

    public function getWithdrawals(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $wallet = $this->getOrCreateWallet($userId);

        $withdrawals = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_withdrawals WHERE wallet_id = %d ORDER BY requested_at DESC",
            $wallet->id
        ));

        return \Consultoria\Helpers\Response::success(['withdrawals' => $withdrawals]);
    }

    public function approveWithdrawal(\WP_REST_Request $request): \WP_REST_Response {
        $withdrawalId = $request->get_param('id');
        $withdrawal = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_withdrawals WHERE id = %d AND status = 'pending'",
            $withdrawalId
        ));

        if (!$withdrawal) {
            return \Consultoria\Helpers\Response::notFound('Saque não encontrado ou já processado');
        }

        $this->getDb()->update(
            $this->getDb()->prefix . 'cp_withdrawals',
            [
                'status'      => 'approved',
                'approved_by' => get_current_user_id(),
                'approved_at' => current_time('mysql'),
            ],
            ['id' => $withdrawalId]
        );

        // In a real scenario, trigger Stripe transfer here

        return \Consultoria\Helpers\Response::success(['message' => 'Saque aprovado com sucesso']);
    }

    public function rejectWithdrawal(\WP_REST_Request $request): \WP_REST_Response {
        $withdrawalId = $request->get_param('id');
        $reason = $request->get_param('reason') ?? 'Solicitação rejeitada';

        $withdrawal = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_withdrawals WHERE id = %d AND status = 'pending'",
            $withdrawalId
        ));

        if (!$withdrawal) {
            return \Consultoria\Helpers\Response::notFound('Saque não encontrado');
        }

        $this->getDb()->update(
            $this->getDb()->prefix . 'cp_withdrawals',
            [
                'status'           => 'rejected',
                'rejection_reason' => $reason,
                'approved_by'      => get_current_user_id(),
            ],
            ['id' => $withdrawalId]
        );

        // Unblock balance
        $wallet = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_wallets WHERE id = %d",
            $withdrawal->wallet_id
        ));

        if ($wallet) {
            $this->getDb()->update(
                $this->getDb()->prefix . 'cp_wallets',
                ['blocked_balance' => max(0, $wallet->blocked_balance - $withdrawal->amount)],
                ['id' => $wallet->id]
            );
        }

        return \Consultoria\Helpers\Response::success(['message' => 'Saque rejeitado']);
    }

    public function onOrderCompleted(int $cpOrderId, int $userId): void {
        global $wpdb;
        $order = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cp_orders WHERE id = %d",
            $cpOrderId
        ));

        if (!$order) return;

        // Add transaction for client (debit)
        $wallet = $this->getOrCreateWallet($userId);
        $this->addTransaction($wallet->id, 'debit', $order->total, 'Compra de pacote: Pedido #' . $order->woocommerce_order_id);
    }

    public function onHourApproved(int $projectId, int $consultantId, float $hours): void {
        global $wpdb;
        $project = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cp_projects WHERE id = %d",
            $projectId
        ));

        if (!$project) return;

        $consultant = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cp_consultants WHERE id = %d",
            $consultantId
        ));

        if (!$consultant) return;

        $amount = $hours * $consultant->hourly_rate;
        $platformFee = round($amount * 0.20, 2);
        $stripeFee = round($amount * 0.029 + 0.50, 2);
        $netAmount = $amount - $platformFee - $stripeFee;

        $wallet = $this->getOrCreateWallet($consultant->user_id);

        $wpdb->update(
            $wpdb->prefix . 'cp_wallets',
            [
                'balance'       => $wallet->balance + $netAmount,
                'total_earned'  => $wallet->total_earned + $netAmount,
            ],
            ['id' => $wallet->id]
        );

        $this->addTransaction($wallet->id, 'credit', $netAmount, "Pagamento por horas aprovadas - Projeto #{$projectId}", $platformFee, $stripeFee);

        // Update consultant totals
        $wpdb->update(
            $wpdb->prefix . 'cp_consultants',
            [
                'total_hours_worked' => $consultant->total_hours_worked + $hours,
                'total_revenue'      => $consultant->total_revenue + $netAmount,
            ],
            ['id' => $consultantId]
        );
    }

    public function processWithdrawalPayment(int $withdrawalId): void {
        global $wpdb;
        $withdrawal = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cp_withdrawals WHERE id = %d AND status = 'approved'",
            $withdrawalId
        ));

        if (!$withdrawal) return;

        // Here we would call Stripe Connect to transfer funds
        // For now, mark as completed
        $wpdb->update(
            $wpdb->prefix . 'cp_withdrawals',
            [
                'status'  => 'completed',
                'paid_at' => current_time('mysql'),
            ],
            ['id' => $withdrawalId]
        );

        $wallet = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cp_wallets WHERE id = %d",
            $withdrawal->wallet_id
        ));

        if ($wallet) {
            $wpdb->update(
                $wpdb->prefix . 'cp_wallets',
                [
                    'balance'          => $wallet->balance - $withdrawal->amount,
                    'blocked_balance'  => max(0, $wallet->blocked_balance - $withdrawal->amount),
                    'total_withdrawn'  => $wallet->total_withdrawn + $withdrawal->amount,
                ],
                ['id' => $wallet->id]
            );
        }
    }

    private function getOrCreateWallet(int $userId): ?object {
        $wallet = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_wallets WHERE user_id = %d",
            $userId
        ));

        if (!$wallet) {
            $this->getDb()->insert($this->getDb()->prefix . 'cp_wallets', [
                'user_id' => $userId,
                'balance' => 0,
                'blocked_balance' => 0,
                'total_earned' => 0,
                'total_withdrawn' => 0,
                'total_spent' => 0,
            ]);
            $wallet = $this->getDb()->get_row($this->getDb()->prepare(
                "SELECT * FROM {$this->getDb()->prefix}cp_wallets WHERE user_id = %d",
                $userId
            ));
        }

        return $wallet;
    }

    private function addTransaction(int $walletId, string $type, float $amount, string $description, float $feePlatform = 0, float $feeStripe = 0): void {
        $wallet = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT balance FROM {$this->getDb()->prefix}cp_wallets WHERE id = %d",
            $walletId
        ));

        $balanceBefore = $wallet ? (float) $wallet->balance : 0;
        $balanceAfter = $type === 'credit' ? $balanceBefore + $amount : $balanceBefore - $amount;

        $this->getDb()->insert($this->getDb()->prefix . 'cp_transactions', [
            'wallet_id'           => $walletId,
            'type'                => $type,
            'amount'              => $amount,
            'balance_before'      => $balanceBefore,
            'balance_after'       => $balanceAfter,
            'fee_platform'        => $feePlatform,
            'fee_stripe'          => $feeStripe,
            'description'         => $description,
            'status'              => 'completed',
            'created_at'          => current_time('mysql'),
        ]);
    }
}
