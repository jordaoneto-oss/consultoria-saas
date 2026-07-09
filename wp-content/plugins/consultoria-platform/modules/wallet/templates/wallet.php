<div class="container py-4">
    <h1 class="h3 fw-bold mb-4">Minha Carteira</h1>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="dashboard-card text-center">
                <div class="stat-value text-success">R$ <?php echo number_format($wallet->available_balance ?? 0, 2, ',', '.'); ?></div>
                <div class="stat-label">Disponível</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card text-center">
                <div class="stat-value text-warning">R$ <?php echo number_format($wallet->blocked_balance ?? 0, 2, ',', '.'); ?></div>
                <div class="stat-label">Bloqueado</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card text-center">
                <div class="stat-value">R$ <?php echo number_format(($wallet->balance ?? 0), 2, ',', '.'); ?></div>
                <div class="stat-label">Saldo Total</div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                <i class="bi bi-cash-stack"></i> Solicitar Saque
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Últimas Transações</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Taxa</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transactions)) : ?>
                            <?php foreach ($transactions as $t) : ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($t->created_at)); ?></td>
                                    <td>
                                        <?php if ($t->type === 'credit') : ?>
                                            <span class="badge bg-success bg-opacity-10 text-success">Crédito</span>
                                        <?php elseif ($t->type === 'debit') : ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger">Débito</span>
                                        <?php elseif ($t->type === 'cashback') : ?>
                                            <span class="badge bg-info bg-opacity-10 text-info">Cashback</span>
                                        <?php else : ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary"><?php echo ucfirst($t->type); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo esc_html($t->description); ?></td>
                                    <td class="fw-bold <?php echo $t->type === 'credit' || $t->type === 'cashback' ? 'text-success' : 'text-danger'; ?>">
                                        R$ <?php echo number_format($t->amount, 2, ',', '.'); ?>
                                    </td>
                                    <td class="text-muted">
                                        R$ <?php echo number_format($t->fee_platform + $t->fee_stripe, 2, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <?php if ($t->status === 'completed') : ?>
                                            <span class="badge bg-success">Concluído</span>
                                        <?php else : ?>
                                            <span class="badge bg-warning"><?php echo ucfirst($t->status); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Nenhuma transação encontrada</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Solicitar Saque</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="withdraw-form">
                    <div class="mb-3">
                        <label class="form-label">Valor do Saque</label>
                        <input type="number" class="form-control" name="amount" step="0.01" min="50" max="50000" placeholder="R$ 0,00">
                        <small class="text-muted">Saldo disponível: R$ <?php echo number_format($wallet->available_balance ?? 0, 2, ',', '.'); ?></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chave PIX</label>
                        <input type="text" class="form-control" name="pix_key" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo da Chave PIX</label>
                        <select class="form-select" name="pix_key_type" required>
                            <option value="cpf">CPF</option>
                            <option value="cnpj">CNPJ</option>
                            <option value="email">E-mail</option>
                            <option value="phone">Telefone</option>
                            <option value="random">Chave Aleatória</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="submitWithdraw()">Solicitar Saque</button>
            </div>
        </div>
    </div>
</div>

<script>
function submitWithdraw() {
    const form = document.getElementById('withdraw-form');
    const formData = new FormData(form);
    const data = {};
    formData.forEach((value, key) => data[key] = value);

    jQuery.ajax({
        url: cpData.restUrl + '/withdrawals',
        method: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        beforeSend: function(xhr) {
            xhr.setRequestHeader('X-WP-Nonce', cpData.nonce);
        },
        success: function(resp) {
            if (resp.success) {
                alert('Saque solicitado com sucesso!');
                location.reload();
            } else {
                alert(resp.error?.message || 'Erro ao solicitar saque');
            }
        },
        error: function() {
            alert('Erro ao solicitar saque');
        }
    });
}
</script>
