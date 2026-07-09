<div class="wrap">
    <h1 class="wp-heading-inline">Consultoria Platform - Dashboard</h1>
    <hr class="wp-header-end">

    <div class="cp-admin-dashboard">
        <!-- Stats Cards -->
        <div class="cp-stats-grid">
            <div class="cp-stat-card">
                <div class="cp-stat-value" id="cp-gmv-month">-</div>
                <div class="cp-stat-label">GMV do Mês</div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-value" id="cp-revenue-month">-</div>
                <div class="cp-stat-label">Receita Líquida</div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-value" id="cp-consultants-active">-</div>
                <div class="cp-stat-label">Consultores Ativos</div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-value" id="cp-clients-active">-</div>
                <div class="cp-stat-label">Clientes</div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="cp-section">
            <h2>Aprovações Pendentes</h2>
            <div class="cp-pending-list" id="cp-pending-list">
                <p class="text-muted">Carregando...</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="cp-section">
            <h2>Ações Rápidas</h2>
            <div class="cp-actions">
                <a href="<?php echo admin_url('admin.php?page=cp-consultants'); ?>" class="button button-primary">Gerenciar Consultores</a>
                <a href="<?php echo admin_url('admin.php?page=cp-plans'); ?>" class="button">Gerenciar Planos</a>
                <a href="<?php echo admin_url('admin.php?page=cp-settings'); ?>" class="button">Configurações</a>
                <a href="<?php echo admin_url('admin.php?page=cp-withdrawals'); ?>" class="button">Saques Pendentes</a>
            </div>
        </div>
    </div>

    <style>
        .cp-admin-dashboard { max-width: 1200px; margin-top: 20px; }
        .cp-stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .cp-stat-card { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 20px; text-align: center; }
        .cp-stat-value { font-size: 28px; font-weight: 700; color: #2271b1; }
        .cp-stat-label { font-size: 13px; color: #666; margin-top: 5px; }
        .cp-section { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .cp-section h2 { margin-top: 0; font-size: 16px; }
        .cp-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .cp-pending-item { padding: 10px 0; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
    </style>

    <script>
    jQuery(document).ready(function($) {
        $.ajax({
            url: cpData.restUrl + '/dashboard/admin',
            method: 'GET',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', cpData.nonce);
            },
            success: function(resp) {
                if (resp.success) {
                    const d = resp.data;
                    $('#cp-gmv-month').text('R$ ' + d.gmv_month?.toFixed(2).replace('.', ',') || '0,00');
                    $('#cp-revenue-month').text('R$ ' + d.revenue_month?.toFixed(2).replace('.', ',') || '0,00');
                    $('#cp-consultants-active').text(d.active_consultants || 0);
                    $('#cp-clients-active').text(d.active_clients || 0);

                    let html = '';
                    if (d.pending_consultants > 0) {
                        html += '<div class="cp-pending-item"><span>' + d.pending_consultants + ' consultor(es) aguardando aprovação</span><a href="<?php echo admin_url('admin.php?page=cp-consultants'); ?>" class="button button-small">Revisar</a></div>';
                    }
                    if (d.pending_withdrawals > 0) {
                        html += '<div class="cp-pending-item"><span>' + d.pending_withdrawals + ' saque(s) pendente(s)</span><a href="<?php echo admin_url('admin.php?page=cp-withdrawals'); ?>" class="button button-small">Revisar</a></div>';
                    }
                    if (!html) html = '<p class="text-muted">Nenhuma aprovação pendente</p>';
                    $('#cp-pending-list').html(html);
                }
            },
            error: function() {
                $('#cp-pending-list').html('<p class="text-muted">Erro ao carregar dados</p>');
            }
        });
    });
    </script>
</div>
