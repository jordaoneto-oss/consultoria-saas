<?php

namespace Consultoria\Modules\Cashback;

use Consultoria\Modules\BaseModule;

class CashbackModule extends BaseModule {

    protected string $name = 'cashback';

    public function init(): void {
        $this->addAction('cp_order_completed', [$this, 'processCashback'], 10, 2);
    }

    public function processCashback(int $cpOrderId, int $userId, float $total): void {
        global $wpdb;

        $rule = $wpdb->get_row(
            "SELECT * FROM {$wpdb->prefix}cp_cashback_rules
             WHERE status = 'active'
             AND (valid_from IS NULL OR valid_from <= NOW())
             AND (valid_until IS NULL OR valid_until >= NOW())
             AND min_order_value <= {$total}
             ORDER BY percentage DESC
             LIMIT 1"
        );

        if (!$rule) return;

        $cashbackAmount = round($total * ($rule->percentage / 100), 2);
        if ($rule->max_cashback && $cashbackAmount > $rule->max_cashback) {
            $cashbackAmount = $rule->max_cashback;
        }

        if ($cashbackAmount <= 0) return;

        $wallet = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cp_wallets WHERE user_id = %d",
            $userId
        ));

        if ($wallet) {
            $wpdb->update(
                $wpdb->prefix . 'cp_wallets',
                [
                    'balance'      => $wallet->balance + $cashbackAmount,
                    'total_earned' => $wallet->total_earned + $cashbackAmount,
                ],
                ['id' => $wallet->id]
            );

            $wpdb->insert($wpdb->prefix . 'cp_transactions', [
                'wallet_id'      => $wallet->id,
                'type'           => 'cashback',
                'amount'         => $cashbackAmount,
                'balance_before' => $wallet->balance,
                'balance_after'  => $wallet->balance + $cashbackAmount,
                'description'    => "Cashback de {$rule->percentage}% - Pedido #{$cpOrderId}",
                'reference_type' => 'order',
                'reference_id'   => $cpOrderId,
                'status'         => 'completed',
                'created_at'     => current_time('mysql'),
            ]);
        }
    }
}
