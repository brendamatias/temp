<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                <li class="breadcrumb-item active">Minhas Negociações</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-briefcase text-primary"></i>
                Minhas Negociações
            </h1>
            <a href="<?= base_url('deals/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nova Negociação
            </a>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-check-circle text-success display-6"></i>
                <h4 class="mt-2"><?= count(array_filter($deals, fn($d) => $d['status'] == 'active')) ?></h4>
                <p class="text-muted mb-0">Ativas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-clock text-warning display-6"></i>
                <h4 class="mt-2"><?= count(array_filter($deals, fn($d) => $d['status'] == 'pending')) ?></h4>
                <p class="text-muted mb-0">Pendentes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-hand-thumbs-up text-info display-6"></i>
                <h4 class="mt-2"><?= count(array_filter($deals, fn($d) => !empty($d['bids']))) ?></h4>
                <p class="text-muted mb-0">Com Ofertas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-chat-dots text-primary display-6"></i>
                <h4 class="mt-2"><?= count(array_filter($deals, fn($d) => !empty($d['messages']))) ?></h4>
                <p class="text-muted mb-0">Com Mensagens</p>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Negociações -->
<?php if (empty($deals)): ?>
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-briefcase display-1 text-muted"></i>
                <h3 class="text-muted mt-3">Você ainda não criou negociações</h3>
                <p class="text-muted">Comece criando sua primeira negociação para vender ou trocar produtos.</p>
                <a href="<?= base_url('deals/create') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle"></i> Criar Primeira Negociação
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul"></i> Suas Negociações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Tipo</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                    <th>Ofertas</th>
                                    <th>Mensagens</th>
                                    <th>Criada em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deals as $deal): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($deal['photos'])): ?>
                                                    <img src="<?= $deal['photos'][0]['src'] ?>" 
                                                         class="rounded me-2" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?= esc($deal['description']) ?></strong>
                                                    <?php if ($deal['urgency_type']): ?>
                                                        <span class="badge bg-danger ms-2">
                                                            <i class="bi bi-clock"></i> Urgente
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $deal['type'] == 1 ? 'success' : 'info' ?>">
                                                <?= $deal['type'] == 1 ? 'Venda' : 'Troca' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($deal['type'] == 1): ?>
                                                <span class="text-success fw-bold">
                                                    R$ <?= number_format($deal['value'], 2, ',', '.') ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-info">
                                                    Troca por: <?= esc($deal['trade_for']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                                            <span class="badge bg-<?= ($deal['status'] ?? 'active') == 'active' ? 'success' : 'secondary' ?>">
                    <?= ($deal['status'] ?? 'active') == 'active' ? 'Ativa' : 'Inativa' ?>
                </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $bidCount = count($deal['bids'] ?? []);
                                            if ($bidCount > 0): 
                                            ?>
                                                <span class="badge bg-info">
                                                    <?= $bidCount ?> oferta<?= $bidCount > 1 ? 's' : '' ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $messageCount = count($deal['messages'] ?? []);
                                            if ($messageCount > 0): 
                                            ?>
                                                <span class="badge bg-primary">
                                                    <?= $messageCount ?> mensagem<?= $messageCount > 1 ? 'ns' : '' ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y', strtotime($deal['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= base_url("deals/{$deal['id']}") ?>" 
                                                   class="btn btn-outline-primary" 
                                                   title="Ver detalhes">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url("deals/{$deal['id']}/edit") ?>" 
                                                   class="btn btn-outline-warning" 
                                                   title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" 
                                                                        class="btn btn-outline-<?= ($deal['status'] ?? 'active') == 'active' ? 'secondary' : 'success' ?>"
                onclick="toggleDealStatus(<?= $deal['id'] ?>, '<?= $deal['status'] ?? 'active' ?>')"
                title="<?= ($deal['status'] ?? 'active') == 'active' ? 'Desativar' : 'Ativar' ?>">
                <i class="bi bi-<?= ($deal['status'] ?? 'active') == 'active' ? 'pause' : 'play' ?>"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Ação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Confirmar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function toggleDealStatus(dealId, currentStatus) {
        const action = currentStatus === 'active' ? 'desativar' : 'ativar';
        const message = `Tem certeza que deseja ${action} esta negociação?`;
        
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmAction').onclick = function() {
            // Implementar chamada para API
            console.log(`${action} negociação:`, dealId);
            
            // Fecha o modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();
            
            // Recarrega a página para atualizar o status
            location.reload();
        };
        
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    }
</script>
<?= $this->endSection() ?>
