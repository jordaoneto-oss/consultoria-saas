<?php

namespace Consultoria\Modules\Contracts;

use Consultoria\Modules\BaseModule;

class ContractsModule extends BaseModule {

    protected string $name = 'contracts';

    public function init(): void {
        $service = new ContractService();
        $this->registerService('contract', $service);

        $this->addAction('cp_proposal_accepted', [$service, 'onProposalAccepted'], 10, 2);
    }
}

class ContractService {

    private function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    public function onProposalAccepted(int $proposalId, int $projectId): void {
        $proposal = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_proposals WHERE id = %d",
            $proposalId
        ));

        $project = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_projects WHERE id = %d",
            $projectId
        ));

        if (!$proposal || !$project) return;

        $contractContent = $this->generateContractContent($project, $proposal);

        $this->getDb()->insert($this->getDb()->prefix . 'cp_contracts', [
            'project_id'       => $projectId,
            'proposal_id'      => $proposalId,
            'contract_content' => $contractContent,
            'status'           => 'pending',
            'created_at'       => current_time('mysql'),
        ]);

        $contractId = $this->getDb()->insert_id;

        // Update project with contract reference
        $this->getDb()->update(
            $this->getDb()->prefix . 'cp_projects',
            ['contract_id' => $contractId, 'status' => 'in_progress'],
            ['id' => $projectId]
        );

        do_action('cp_send_notification', $project->client_id, 'new_contract', [
            'title'          => 'Contrato gerado',
            'message'        => "O contrato do projeto {$project->title} foi gerado. Por favor, assine digitalmente.",
            'reference_type' => 'contract',
            'reference_id'   => $contractId,
        ]);
    }

    public function signContract(\WP_REST_Request $request): \WP_REST_Response {
        $contractId = (int) $request->get_param('id');
        $userId = get_current_user_id();

        $contract = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_contracts WHERE id = %d",
            $contractId
        ));

        if (!$contract) {
            return \Consultoria\Helpers\Response::notFound('Contrato não encontrado');
        }

        $project = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT p.*, c.user_id as client_user_id, co.user_id as consultant_user_id
             FROM {$this->getDb()->prefix}cp_projects p
             INNER JOIN {$this->getDb()->prefix}cp_clients c ON c.id = p.client_id
             INNER JOIN {$this->getDb()->prefix}cp_consultants co ON co.id = p.consultant_id
             WHERE p.id = %d",
            $contract->project_id
        ));

        if (!$project) {
            return \Consultoria\Helpers\Response::notFound('Projeto não encontrado');
        }

        $isClient = (int) $project->client_user_id === $userId;
        $isConsultant = (int) $project->consultant_user_id === $userId;

        if (!$isClient && !$isConsultant && !current_user_can('manage_options')) {
            return \Consultoria\Helpers\Response::forbidden();
        }

        $updateData = [];
        if ($isClient && !$contract->signed_by_client) {
            $updateData['signed_by_client'] = 1;
            $updateData['client_signed_at'] = current_time('mysql');
        }
        if ($isConsultant && !$contract->signed_by_consultant) {
            $updateData['signed_by_consultant'] = 1;
            $updateData['consultant_signed_at'] = current_time('mysql');
        }

        if (empty($updateData)) {
            return \Consultoria\Helpers\Response::error('Contrato já foi assinado por você');
        }

        $this->getDb()->update(
            $this->getDb()->prefix . 'cp_contracts',
            $updateData,
            ['id' => $contractId]
        );

        // Check if fully signed
        $contract = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_contracts WHERE id = %d",
            $contractId
        ));

        if ($contract->signed_by_client && $contract->signed_by_consultant) {
            $this->getDb()->update(
                $this->getDb()->prefix . 'cp_contracts',
                [
                    'status'    => 'signed',
                    'signed_at' => current_time('mysql'),
                ],
                ['id' => $contractId]
            );

            do_action('cp_contract_signed', $contractId);
        }

        return \Consultoria\Helpers\Response::success(['message' => 'Contrato assinado com sucesso']);
    }

    public function downloadContract(\WP_REST_Request $request): \WP_REST_Response {
        $contractId = (int) $request->get_param('id');
        $contract = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_contracts WHERE id = %d",
            $contractId
        ));

        if (!$contract) {
            return \Consultoria\Helpers\Response::notFound('Contrato não encontrado');
        }

        if (!$contract->document_url) {
            // Generate PDF on the fly
            $contract->document_url = $this->generatePDF($contract);
        }

        return \Consultoria\Helpers\Response::success([
            'download_url' => $contract->document_url,
            'status'       => $contract->status,
        ]);
    }

    private function generateContractContent(object $project, object $proposal): string {
        $client = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT u.display_name, c.company_name, u.user_email
             FROM {$this->getDb()->prefix}cp_clients c
             INNER JOIN {$this->getDb()->prefix}cp_users u ON u.id = c.user_id
             WHERE c.id = %d",
            $project->client_id
        ));

        $consultant = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT u.display_name, co.professional_title, u.user_email
             FROM {$this->getDb()->prefix}cp_consultants co
             INNER JOIN {$this->getDb()->prefix}cp_users u ON u.id = co.user_id
             WHERE co.id = %d",
            $project->consultant_id
        ));

        $clientName = $client->company_name ?: $client->display_name;

        return <<<CONTRACT
CONTRATO DE PRESTAÇÃO DE SERVIÇOS DE CONSULTORIA

CONTRATANTE: {$clientName}
Email: {$client->user_email}

CONTRATADO: {$consultant->display_name}
Profissão: {$consultant->professional_title}
Email: {$consultant->user_email}

OBJETO: {$project->title}
DESCRIÇÃO: {$project->description}
VALOR: R$ " . number_format($proposal->value, 2, ',', '.') . "
HORAS ESTIMADAS: {$proposal->estimated_hours}h

CLÁUSULAS:

1. O CONTRATADO se compromete a executar os serviços descritos no objeto deste contrato com a diligência e expertise esperadas de um profissional de sua área.

2. O CONTRATANTE se compromete a fornecer todas as informações e recursos necessários para a execução dos serviços.

3. O pagamento será processado através da plataforma Consultoria SaaS, garantindo a segurança financeira de ambas as partes.

4. O prazo estimado de entrega é de {$proposal->delivery_estimate} dias a partir da assinatura deste contrato.

5. Qualquer alteração no escopo deverá ser formalizada através de aditivo contratual.

6. As partes elegem o foro da cidade de São Paulo/SP para dirimir quaisquer dúvidas oriundas deste contrato.

Data: " . date('d/m/Y') . "

_________________________              _________________________
CONTRATANTE                            CONTRATADO
CONTRACT;
    }

    private function generatePDF(object $contract): string {
        $uploadDir = wp_upload_dir();
        $pdfDir = $uploadDir['basedir'] . '/consultoria-contracts';
        if (!file_exists($pdfDir)) {
            wp_mkdir_p($pdfDir);
        }

        $filename = "contract-{$contract->id}.html";
        $filepath = $pdfDir . '/' . $filename;
        file_put_contents($filepath, $contract->contract_content);

        return $uploadDir['baseurl'] . '/consultoria-contracts/' . $filename;
    }
}
