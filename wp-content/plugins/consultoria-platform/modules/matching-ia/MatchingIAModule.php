<?php

namespace Consultoria\Modules\MatchingIA;

use Consultoria\Modules\BaseModule;

class MatchingIAModule extends BaseModule {

    protected string $name = 'matching-ia';
    protected float $minScore = 30.0;
    protected int $topN = 5;

    public function init(): void {
        $this->loadConfig();
        $service = new MatchingIAService($this->minScore, $this->topN);
        $this->registerService('matching', $service);

        $this->addAction('cp_project_created', [$service, 'onProjectCreated'], 10, 1);
        $this->addAction('cp_proposal_accepted', [$service, 'onProposalAccepted'], 10, 2);

        add_filter('cp_shortcodes', function ($shortcodes) {
            $shortcodes['cp_matching'] = [$this, 'renderMatching'];
            return $shortcodes;
        });
    }

    public function renderMatching(): string {
        if (!is_user_logged_in()) return '';
        ob_start();
        $templatePath = CP_PLUGIN_DIR . 'modules/matching-ia/templates/matching.php';
        if (file_exists($templatePath)) {
            include $templatePath;
        }
        return ob_get_clean();
    }

    private function loadConfig(): void {
        global $wpdb;
        $settings = $wpdb->get_results(
            "SELECT key_name, key_value FROM {$wpdb->prefix}cp_settings WHERE key_name IN ('matching_min_score', 'matching_top_n')"
        );
        foreach ($settings as $setting) {
            if ($setting->key_name === 'matching_min_score') {
                $this->minScore = (float) $setting->key_value;
            }
            if ($setting->key_name === 'matching_top_n') {
                $this->topN = (int) $setting->key_value;
            }
        }
    }
}

class MatchingIAService {

    private float $minScore;
    private int $topN;

    private const WEIGHTS = [
        'expertise'    => 0.30,
        'rating'       => 0.20,
        'availability' => 0.15,
        'budget'       => 0.10,
        'language'     => 0.05,
        'history'      => 0.10,
        'proximity'    => 0.05,
        'response'     => 0.05,
    ];

    public function __construct(float $minScore = 30.0, int $topN = 5) {
        $this->minScore = $minScore;
        $this->topN = $topN;
    }

    private function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    public function calculateScores(\WP_REST_Request $request): \WP_REST_Response {
        $projectId = (int) $request->get_param('project_id');

        $project = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_projects WHERE id = %d",
            $projectId
        ));

        if (!$project) {
            return \Consultoria\Helpers\Response::error('Projeto não encontrado.', 404);
        }

        $scores = $this->calculateForProject($project);

        return \Consultoria\Helpers\Response::success([
            'project_id' => $projectId,
            'scores'     => $scores,
            'weights'    => self::WEIGHTS,
        ]);
    }

    public function getTopConsultants(\WP_REST_Request $request): \WP_REST_Response {
        $projectId = (int) $request->get_param('project_id');
        $limit = min(20, max(1, (int) ($request->get_param('limit') ?? $this->topN)));

        $project = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_projects WHERE id = %d",
            $projectId
        ));

        if (!$project) {
            return \Consultoria\Helpers\Response::error('Projeto não encontrado.', 404);
        }

        $scores = $this->calculateForProject($project);
        $top = array_slice($scores, 0, $limit);

        return \Consultoria\Helpers\Response::success([
            'project_id' => $projectId,
            'top'        => $top,
        ]);
    }

    public function onProjectCreated(int $projectId): void {
        $project = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_projects WHERE id = %d",
            $projectId
        ));

        if (!$project) return;

        $scores = $this->calculateForProject($project);

        if (!empty($scores)) {
            $topIds = array_map(fn($s) => $s['consultant_id'], $scores);

            $consultants = $this->getDb()->get_results(
                "SELECT id, user_id FROM {$this->getDb()->prefix}cp_consultants WHERE id IN (" . implode(',', $topIds) . ")"
            );

            foreach ($consultants as $consultant) {
               $user = get_userdata($consultant->user_id);
               if ($user) {
                   do_action('cp_send_notification', $consultant->user_id, 'new_matching', [
                       'title'          => "Nova demanda compatível com seu perfil",
                       'message'        => "Uma nova demanda foi publicada na categoria {$project->category}. Seu score de matching é " . $this->getScoreForConsultant($scores, $consultant->id) . '%',
                       'reference_type' => 'project',
                       'reference_id'   => $projectId,
                   ]);
               }
            }
        }
    }

    public function onProposalAccepted(int $projectId, int $consultantId): void {
        $this->getDb()->query($this->getDb()->prepare(
            "UPDATE {$this->getDb()->prefix}cp_matching_scores SET score = score * 1.1 WHERE project_id = %d AND consultant_id = %d",
            $projectId, $consultantId
        ));
    }

    private function calculateForProject(object $project): array {
        $prefix = $this->getDb()->prefix;

        $consultants = $this->getDb()->get_results(
            "SELECT c.*, u.display_name, u.language
             FROM {$prefix}cp_consultants c
             INNER JOIN {$prefix}cp_users u ON u.id = c.user_id
             WHERE c.status = 'active'"
        );

        if (empty($consultants)) return [];

        $scores = [];

        foreach ($consultants as $consultant) {
            $score = $this->calculateIndividualScore($project, $consultant);

            if ($score['total'] >= $this->minScore) {
                $this->persistScore($project->id, $consultant->id, $score);
                $scores[] = [
                    'consultant_id'   => $consultant->id,
                    'consultant_name' => $consultant->display_name,
                    'total'           => round($score['total'], 2),
                    'breakdown'       => $score['breakdown'],
                ];
            }
        }

        usort($scores, fn($a, $b) => $b['total'] <=> $a['total']);

        return $scores;
    }

    private function calculateIndividualScore(object $project, object $consultant): array {
        $breakdown = [];
        $total = 0.0;

        $expertiseScore = $this->calculateExpertiseMatch($project, $consultant);
        $breakdown['expertise'] = $expertiseScore;
        $total += $expertiseScore * self::WEIGHTS['expertise'];

        $ratingScore = $this->normalize($consultant->rating, 0, 5) * 100;
        $breakdown['rating'] = $ratingScore;
        $total += $ratingScore * self::WEIGHTS['rating'];

        $availabilityScore = $this->calculateAvailability($consultant);
        $breakdown['availability'] = $availabilityScore;
        $total += $availabilityScore * self::WEIGHTS['availability'];

        $budgetScore = $this->calculateBudgetMatch($project, $consultant);
        $breakdown['budget'] = $budgetScore;
        $total += $budgetScore * self::WEIGHTS['budget'];

        $languageScore = 100.0;
        $breakdown['language'] = $languageScore;
        $total += $languageScore * self::WEIGHTS['language'];

        $historyScore = $this->calculateHistoryScore($consultant);
        $breakdown['history'] = $historyScore;
        $total += $historyScore * self::WEIGHTS['history'];

        $proximityScore = 50.0;
        $breakdown['proximity'] = $proximityScore;
        $total += $proximityScore * self::WEIGHTS['proximity'];

        $responseScore = $this->calculateResponseScore($consultant);
        $breakdown['response'] = $responseScore;
        $total += $responseScore * self::WEIGHTS['response'];

        return [
            'total'     => $total,
            'breakdown' => $breakdown,
        ];
    }

    private function calculateExpertiseMatch(object $project, object $consultant): float {
        $prefix = $this->getDb()->prefix;

        $expertise = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT category FROM {$prefix}cp_expertise WHERE consultant_id = %d",
            $consultant->id
        ));

        if (empty($expertise)) return 0;

        $categories = array_map(fn($e) => $e->category, $expertise);

        $exactMatch = in_array($project->category, $categories);

        if ($exactMatch) return 100;

        $partialMatches = 0;
        $projectWords = explode(' ', strtolower($project->category));
        foreach ($categories as $cat) {
            $catWords = explode(' ', strtolower($cat));
            $common = array_intersect($projectWords, $catWords);
            if (count($common) > 0) {
                $partialMatches += count($common) / max(count($projectWords), count($catWords));
            }
        }

        return min(80, $partialMatches / count($expertise) * 100);
    }

    private function calculateAvailability(object $consultant): float {
        if ($consultant->availability === 'full_time') return 100;
        if ($consultant->availability === 'part_time') return 60;

        $prefix = $this->getDb()->prefix;
        $today = (int) date('w');
        $currentTime = date('H:i:s');

        $available = $this->getDb()->get_var($this->getDb()->prepare(
            "SELECT COUNT(*) FROM {$prefix}cp_consultant_availability
             WHERE consultant_id = %d AND day_of_week = %d AND start_time <= %s AND end_time >= %s AND is_available = 1",
            $consultant->id, $today, $currentTime, $currentTime
        ));

        return $available > 0 ? 100 : 30;
    }

    private function calculateBudgetMatch(object $project, object $consultant): float {
        if ($project->budget <= 0 || $consultant->hourly_rate <= 0) return 50;

        $maxHours = $project->budget / $consultant->hourly_rate;
        $estimatedHours = $project->estimated_hours ?? $maxHours;

        if ($estimatedHours <= 0) return 50;

        $ratio = $maxHours / $estimatedHours;

        if ($ratio >= 1.5) return 100;
        if ($ratio >= 1.0) return 80;
        if ($ratio >= 0.7) return 50;
        return 20;
    }

    private function calculateHistoryScore(object $consultant): float {
        $score = 0;
        if ($consultant->completion_rate >= 95) $score += 40;
        elseif ($consultant->completion_rate >= 80) $score += 25;
        else $score += 10;

        if ($consultant->total_projects > 50) $score += 30;
        elseif ($consultant->total_projects > 20) $score += 20;
        elseif ($consultant->total_projects > 5) $score += 10;

        if ($consultant->total_hours_worked > 500) $score += 30;
        elseif ($consultant->total_hours_worked > 100) $score += 20;
        elseif ($consultant->total_hours_worked > 20) $score += 10;

        return min(100, $score);
    }

    private function calculateResponseScore(object $consultant): float {
        if ($consultant->avg_response_time === null) return 50;
        if ($consultant->avg_response_time <= 60) return 100;
        if ($consultant->avg_response_time <= 240) return 80;
        if ($consultant->avg_response_time <= 1440) return 50;
        return 20;
    }

    private function persistScore(int $projectId, int $consultantId, array $score): void {
        $breakdown = $score['breakdown'];

        $this->getDb()->replace($this->getDb()->prefix . 'cp_matching_scores', [
            'project_id'        => $projectId,
            'consultant_id'     => $consultantId,
            'score'             => $score['total'],
            'expertise_score'   => $breakdown['expertise'],
            'rating_score'      => $breakdown['rating'],
            'availability_score' => $breakdown['availability'],
            'budget_score'      => $breakdown['budget'],
            'language_score'    => $breakdown['language'],
            'history_score'     => $breakdown['history'],
            'proximity_score'   => $breakdown['proximity'],
            'model_version'     => '1.0.0',
            'created_at'        => current_time('mysql'),
        ]);
    }

    private function normalize(float $value, float $min, float $max): float {
        if ($max <= $min) return 0;
        return max(0, min(1, ($value - $min) / ($max - $min)));
    }

    private function getScoreForConsultant(array $scores, int $consultantId): string {
        foreach ($scores as $score) {
            if ($score['consultant_id'] === $consultantId) {
                return (string) $score['total'];
            }
        }
        return '0';
    }
}
