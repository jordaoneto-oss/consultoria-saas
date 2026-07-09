<?php

namespace Consultoria\Modules\Gamification;

use Consultoria\Modules\BaseModule;

class GamificationModule extends BaseModule {

    protected string $name = 'gamification';

    public function init(): void {
        $service = new GamificationService();
        $this->registerService('gamification', $service);

        $this->addAction('cp_project_completed', [$service, 'onProjectCompleted'], 10, 2);
        $this->addAction('cp_review_created', [$service, 'onReviewCreated'], 10, 3);
        $this->addAction('cp_hour_approved', [$service, 'onHourApproved'], 10, 3);
    }

    public function addXP(int $userId, int $xp, string $reason): void {
        $service = \ConsultoriaPlatform::getInstance()->getService('gamification');
        if ($service) {
            $service->addXpToUser($userId, $xp, $reason);
        }
    }
}

class GamificationService {

    private function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    private const LEVELS = [
        'iniciante'    => 0,
        'especialista' => 1000,
        'senior'       => 3000,
        'master'       => 6000,
        'elite'        => 10000,
        'diamond'      => 20000,
    ];

    public function getGamification(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $gamification = $this->getOrCreate($userId);

        $badges = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT a.*, ub.earned_at
             FROM {$this->getDb()->prefix}cp_user_badges ub
             INNER JOIN {$this->getDb()->prefix}cp_achievements a ON a.id = ub.achievement_id
             WHERE ub.user_id = %d
             ORDER BY ub.earned_at DESC",
            $userId
        ));

        $xpProgress = $gamification->xp_next_level > 0
            ? round(($gamification->xp / $gamification->xp_next_level) * 100, 1)
            : 100;

        return \Consultoria\Helpers\Response::success([
            'gamification' => $gamification,
            'badges'       => $badges,
            'xp_progress'  => $xpProgress,
            'next_level'   => $this->getNextLevel($gamification->level),
        ]);
    }

    public function getAchievements(\WP_REST_Request $request): \WP_REST_Response {
        $achievements = $this->getDb()->get_results(
            "SELECT * FROM {$this->getDb()->prefix}cp_achievements ORDER BY type, criteria_value"
        );

        return \Consultoria\Helpers\Response::success(['achievements' => $achievements]);
    }

    public function getRanking(\WP_REST_Request $request): \WP_REST_Response {
        $type = $request->get_param('type') ?? 'rating';
        $limit = min(50, max(1, (int) ($request->get_param('limit') ?? 10)));

        $orderBy = match ($type) {
            'rating' => 'c.rating DESC, c.rating_count DESC',
            'revenue' => 'c.total_revenue DESC',
            'projects' => 'c.total_projects DESC',
            'hours' => 'c.total_hours_worked DESC',
            default => 'c.rating DESC',
        };

        $consultants = $this->getDb()->get_results(
            "SELECT c.*, u.display_name, u.user_email
             FROM {$this->getDb()->prefix}cp_consultants c
             INNER JOIN {$this->getDb()->prefix}cp_users u ON u.id = c.user_id
             WHERE c.status = 'active'
             ORDER BY {$orderBy}
             LIMIT {$limit}"
        );

        return \Consultoria\Helpers\Response::success(['ranking' => $consultants, 'type' => $type]);
    }

    public function onProjectCompleted(int $projectId, int $consultantId): void {
        global $wpdb;

        $consultant = $wpdb->get_row($wpdb->prepare(
            "SELECT user_id FROM {$wpdb->prefix}cp_consultants WHERE id = %d",
            $consultantId
        ));
        if (!$consultant) return;

        $this->addXpToUser($consultant->user_id, 200, 'Projeto concluído');
        $this->checkAchievements($consultant->user_id, 'consultant');
    }

    public function onReviewCreated(int $projectId, int $reviewerId, int $targetId): void {
        $this->addXpToUser($targetId, 50, 'Avaliação recebida');
    }

    public function onHourApproved(int $projectId, int $consultantId, float $hours): void {
        $consultant = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT user_id FROM {$this->getDb()->prefix}cp_consultants WHERE id = %d",
            $consultantId
        ));
        if (!$consultant) return;

        $this->addXpToUser($consultant->user_id, (int) ($hours * 10), 'Horas aprovadas');
    }

    public function addXpToUser(int $userId, int $xp, string $reason = ''): void {
        $gamification = $this->getOrCreate($userId);
        $newXp = $gamification->total_xp_earned + $xp;

        $this->getDb()->update(
            $this->getDb()->prefix . 'cp_gamification',
            [
                'xp'              => $gamification->xp + $xp,
                'total_xp_earned' => $newXp,
            ],
            ['id' => $gamification->id]
        );

        $this->updateLevel($gamification->id, $gamification->xp + $xp);
        $this->updateRanking($userId);
        $this->checkAchievements($userId, 'user');
    }

    private function getOrCreate(int $userId): ?object {
        $gamification = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_gamification WHERE user_id = %d",
            $userId
        ));

        if (!$gamification) {
            $this->getDb()->insert($this->getDb()->prefix . 'cp_gamification', [
                'user_id'         => $userId,
                'level'           => 'iniciante',
                'xp'              => 0,
                'xp_next_level'   => 1000,
                'total_xp_earned' => 0,
            ]);
            $gamification = $this->getDb()->get_row($this->getDb()->prepare(
                "SELECT * FROM {$this->getDb()->prefix}cp_gamification WHERE user_id = %d",
                $userId
            ));
        }

        return $gamification;
    }

    private function updateLevel(int $gamificationId, int $xp): void {
        $newLevel = 'iniciante';
        foreach (self::LEVELS as $level => $requiredXp) {
            if ($xp >= $requiredXp) {
                $newLevel = $level;
            }
        }

        $currentLevel = $this->getDb()->get_var($this->getDb()->prepare(
            "SELECT level FROM {$this->getDb()->prefix}cp_gamification WHERE id = %d",
            $gamificationId
        ));

        if ($currentLevel !== $newLevel) {
            $levels = array_keys(self::LEVELS);
            $currentIdx = array_search($currentLevel, $levels);
            $newIdx = array_search($newLevel, $levels);

            $nextLevel = $this->getNextLevel($newLevel);

            $this->getDb()->update(
                $this->getDb()->prefix . 'cp_gamification',
                [
                    'level'         => $newLevel,
                    'xp_next_level' => $nextLevel ? self::LEVELS[$nextLevel] : 999999,
                ],
                ['id' => $gamificationId]
            );
        }
    }

    private function getNextLevel(string $currentLevel): ?string {
        $levels = array_keys(self::LEVELS);
        $idx = array_search($currentLevel, $levels);
        return $idx !== false && isset($levels[$idx + 1]) ? $levels[$idx + 1] : null;
    }

    private function updateRanking(int $userId): void {
        $position = $this->getDb()->get_var(
            "SELECT COUNT(*) + 1 FROM {$this->getDb()->prefix}cp_gamification
             WHERE xp > (SELECT xp FROM {$this->getDb()->prefix}cp_gamification WHERE user_id = {$userId})"
        );

        $this->getDb()->update(
            $this->getDb()->prefix . 'cp_gamification',
            ['ranking_position' => $position],
            ['user_id' => $userId]
        );
    }

    private function checkAchievements(int $userId, string $type): void {
        $achievements = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_achievements WHERE type = %s OR type = 'special'",
            $type === 'user' ? 'special' : $type
        ));

        foreach ($achievements as $achievement) {
            // Check if already earned
            $already = $this->getDb()->get_var($this->getDb()->prepare(
                "SELECT id FROM {$this->getDb()->prefix}cp_user_badges WHERE user_id = %d AND achievement_id = %d",
                $userId, $achievement->id
            ));

            if ($already) continue;

            $earned = $this->evaluateCriteria($userId, $achievement);
            if ($earned) {
                $this->getDb()->insert($this->getDb()->prefix . 'cp_user_badges', [
                    'user_id'       => $userId,
                    'achievement_id' => $achievement->id,
                    'earned_at'     => current_time('mysql'),
                ]);

                $this->addXpToUser($userId, $achievement->xp_reward, "Badge: {$achievement->name}");
            }
        }
    }

    private function evaluateCriteria(int $userId, object $achievement): bool {
        $value = match ($achievement->criteria) {
            'avg_rating' => (float) $this->getDb()->get_var($this->getDb()->prepare(
                "SELECT AVG(rating) FROM {$this->getDb()->prefix}cp_reviews WHERE target_id = %d",
                $userId
            )),
            'total_projects' => (int) $this->getDb()->get_var($this->getDb()->prepare(
                "SELECT total_projects FROM {$this->getDb()->prefix}cp_consultants WHERE user_id = %d",
                $userId
            )),
            'total_hours' => (int) $this->getDb()->get_var($this->getDb()->prepare(
                "SELECT total_hours_worked FROM {$this->getDb()->prefix}cp_consultants WHERE user_id = %d",
                $userId
            )),
            'total_revenue' => (float) $this->getDb()->get_var($this->getDb()->prepare(
                "SELECT total_revenue FROM {$this->getDb()->prefix}cp_consultants WHERE user_id = %d",
                $userId
            )),
            'response_time' => (int) $this->getDb()->get_var($this->getDb()->prepare(
                "SELECT avg_response_time FROM {$this->getDb()->prefix}cp_consultants WHERE user_id = %d",
                $userId
            )),
            'onboarding_complete' => (int) $this->getDb()->get_var($this->getDb()->prepare(
                "SELECT onboarding_completed FROM {$this->getDb()->prefix}cp_consultants WHERE user_id = %d",
                $userId
            )),
            'five_star_count' => (int) $this->getDb()->get_var($this->getDb()->prepare(
                "SELECT COUNT(*) FROM {$this->getDb()->prefix}cp_reviews WHERE target_id = %d AND rating = 5",
                $userId
            )),
            default => 0,
        };

        return $value >= $achievement->criteria_value;
    }
}
