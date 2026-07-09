<div class="cp-tickets-container">
    <div class="cp-tickets-header">
        <h2><?php _e('Meus Tickets', 'consultoria-platform'); ?></h2>
        <button class="cp-button cp-button-primary" id="cp-new-ticket-btn">
            <?php _e('Abrir Ticket', 'consultoria-platform'); ?>
        </button>
    </div>

    <div class="cp-tickets-filters">
        <select id="cp-ticket-status-filter">
            <option value=""><?php _e('Todos os status', 'consultoria-platform'); ?></option>
            <option value="open"><?php _e('Aberto', 'consultoria-platform'); ?></option>
            <option value="in_progress"><?php _e('Em Andamento', 'consultoria-platform'); ?></option>
            <option value="waiting_client"><?php _e('Aguardando Cliente', 'consultoria-platform'); ?></option>
            <option value="resolved"><?php _e('Resolvido', 'consultoria-platform'); ?></option>
            <option value="closed"><?php _e('Fechado', 'consultoria-platform'); ?></option>
        </select>
        <select id="cp-ticket-priority-filter">
            <option value=""><?php _e('Todas prioridades', 'consultoria-platform'); ?></option>
            <option value="low"><?php _e('Baixa', 'consultoria-platform'); ?></option>
            <option value="medium"><?php _e('Média', 'consultoria-platform'); ?></option>
            <option value="high"><?php _e('Alta', 'consultoria-platform'); ?></option>
            <option value="urgent"><?php _e('Urgente', 'consultoria-platform'); ?></option>
        </select>
    </div>

    <div class="cp-tickets-list" id="cp-tickets-list">
        <div class="cp-loading"><?php _e('Carregando tickets...', 'consultoria-platform'); ?></div>
    </div>

    <div class="cp-tickets-pagination" id="cp-tickets-pagination"></div>
</div>

<div id="cp-new-ticket-modal" class="cp-modal" style="display:none;">
    <div class="cp-modal-content">
        <div class="cp-modal-header">
            <h3><?php _e('Abrir Novo Ticket', 'consultoria-platform'); ?></h3>
            <button class="cp-modal-close">&times;</button>
        </div>
        <div class="cp-modal-body">
            <form id="cp-new-ticket-form">
                <div class="cp-form-group">
                    <label for="ticket-subject"><?php _e('Assunto', 'consultoria-platform'); ?> *</label>
                    <input type="text" id="ticket-subject" name="subject" required
                           placeholder="<?php _e('Resuma o problema em poucas palavras', 'consultoria-platform'); ?>">
                </div>
                <div class="cp-form-group">
                    <label for="ticket-category"><?php _e('Categoria', 'consultoria-platform'); ?></label>
                    <select id="ticket-category" name="category">
                        <option value=""><?php _e('Selecione...', 'consultoria-platform'); ?></option>
                        <option value="suporte_tecnico"><?php _e('Suporte Técnico', 'consultoria-platform'); ?></option>
                        <option value="duvida"><?php _e('Dúvida', 'consultoria-platform'); ?></option>
                        <option value="reclamacao"><?php _e('Reclamação', 'consultoria-platform'); ?></option>
                        <option value="sugestao"><?php _e('Sugestão', 'consultoria-platform'); ?></option>
                        <option value="financeiro"><?php _e('Financeiro', 'consultoria-platform'); ?></option>
                        <option value="outro"><?php _e('Outro', 'consultoria-platform'); ?></option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="ticket-priority"><?php _e('Prioridade', 'consultoria-platform'); ?></label>
                    <select id="ticket-priority" name="priority">
                        <option value="low"><?php _e('Baixa', 'consultoria-platform'); ?></option>
                        <option value="medium" selected><?php _e('Média', 'consultoria-platform'); ?></option>
                        <option value="high"><?php _e('Alta', 'consultoria-platform'); ?></option>
                        <option value="urgent"><?php _e('Urgente', 'consultoria-platform'); ?></option>
                    </select>
                </div>
                <div class="cp-form-group">
                    <label for="ticket-description"><?php _e('Descrição', 'consultoria-platform'); ?> *</label>
                    <textarea id="ticket-description" name="description" rows="5" required
                              placeholder="<?php _e('Descreva detalhadamente o problema ou solicitação', 'consultoria-platform'); ?>"></textarea>
                </div>
                <button type="submit" class="cp-button cp-button-primary">
                    <?php _e('Enviar Ticket', 'consultoria-platform'); ?>
                </button>
            </form>
        </div>
    </div>
</div>
