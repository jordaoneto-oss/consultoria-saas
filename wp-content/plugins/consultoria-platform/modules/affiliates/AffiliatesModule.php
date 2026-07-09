<?php

namespace Consultoria\Modules\Affiliates;

use Consultoria\Modules\BaseModule;

class AffiliatesModule extends BaseModule {

    protected string $name = 'affiliates';

    public function init(): void {
        $service = new AffiliateService();
        $this->registerService('affiliate', $service);

        $this->addAction('cp_order_completed', [$service, 'onOrderCompleted'], 10, 2);
        $this->addAction('template_redirect', [$service, 'trackClick']);
    }
}

class AffiliateService {

    private function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    public function dashboard(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $affiliate = $this->getAffiliate($userId);

        if (!$affiliate) {
            return \Consultoria\Helpers\Response::success(['affiliate' => null]);
        }

        $recentClicks = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_affiliate_clicks WHERE affiliate_id = %d ORDER BY created_at DESC LIMIT 20",
            $affiliate->id
        ));

        return \Consultoria\Helpers\Response::success([
            'affiliate'      => $affiliate,
            'recent_clicks'  => $recentClicks,
        ]);
    }

    public function register(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $existing = $this->getAffiliate($userId);

        if ($existing) {
            return \Consultoria\Helpers\Response::error('Você já é um afiliado');
        }

        $code = $this->generateUniqueCode();

        $result = $this->getDb()->insert($this->getDb()->prefix . 'cp_affiliates', [
            'user_id'         => $userId,
            'code'            => $code,
            'commission_rate' => (float) get_option('cp_affiliate_default_commission', 10),
            'status'          => 'active',
            'created_at'      => current_time('mysql'),
        ]);

        if (!$result) {
            return \Consultoria\Helpers\Response::error('Erro ao criar cadastro de afiliado');
        }

        return \Consultoria\Helpers\Response::created([
            'code'    => $code,
            'link'    => $this->getAffiliateLink($code),
            'qr_code' => $this->getQRCodeUrl($code),
        ]);
    }

    public function trackClick(): void {
        if (!isset($_GET['ref'])) return;

        $code = sanitize_text_field($_GET['ref']);
        $affiliate = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_affiliates WHERE code = %s AND status = 'active'",
            $code
        ));

        if (!$affiliate) return;

        $this->getDb()->insert($this->getDb()->prefix . 'cp_affiliate_clicks', [
            'affiliate_id'  => $affiliate->id,
            'ip_address'    => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent'    => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'referrer_url'  => $_SERVER['HTTP_REFERER'] ?? '',
            'landing_url'   => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}",
            'created_at'    => current_time('mysql'),
        ]);

        // Set cookie for 30 days
        setcookie('cp_affiliate_code', $code, time() + (30 * DAY_IN_SECONDS), '/');
    }

    public function onOrderCompleted(int $cpOrderId, int $userId): void {
        $code = $_COOKIE['cp_affiliate_code'] ?? null;
        if (!$code) return;

        $affiliate = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_affiliates WHERE code = %s AND status = 'active'",
            $code
        ));

        if (!$affiliate) return;

        $order = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_orders WHERE id = %d",
            $cpOrderId
        ));

        if (!$order) return;

        $commission = round($order->total * ($affiliate->commission_rate / 100), 2);

        // Update click conversion
        $this->getDb()->query($this->getDb()->prepare(
            "UPDATE {$this->getDb()->prefix}cp_affiliate_clicks
             SET converted = 1, converted_at = %s, order_id = %d, commission_earned = %f
             WHERE affiliate_id = %d AND converted = 0
             ORDER BY created_at DESC LIMIT 1",
            current_time('mysql'), $cpOrderId, $commission, $affiliate->id
        ));

        // Update affiliate totals
        $this->getDb()->update(
            $this->getDb()->prefix . 'cp_affiliates',
            [
                'total_conversions' => $affiliate->total_conversions + 1,
                'total_revenue'     => $affiliate->total_revenue + $order->total,
                'total_commission'  => $affiliate->total_commission + $commission,
            ],
            ['id' => $affiliate->id]
        );

        // Credit commission to affiliate wallet
        $wallet = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_wallets WHERE user_id = %d",
            $affiliate->user_id
        ));

        if ($wallet) {
            $this->getDb()->update(
                $this->getDb()->prefix . 'cp_wallets',
                [
                    'balance'      => $wallet->balance + $commission,
                    'total_earned' => $wallet->total_earned + $commission,
                ],
                ['id' => $wallet->id]
            );
        }
    }

    private function getAffiliate(int $userId): ?object {
        return $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_affiliates WHERE user_id = %d",
            $userId
        ));
    }

    private function generateUniqueCode(): string {
        do {
            $code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
            $exists = $this->getDb()->get_var($this->getDb()->prepare(
                "SELECT id FROM {$this->getDb()->prefix}cp_affiliates WHERE code = %s",
                $code
            ));
        } while ($exists);

        return $code;
    }

    private function getAffiliateLink(string $code): string {
        return add_query_arg('ref', $code, home_url());
    }

    private function getQRCodeUrl(string $code): string {
        $link = $this->getAffiliateLink($code);
        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($link);
    }
}
