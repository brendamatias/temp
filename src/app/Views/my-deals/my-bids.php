<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('my-deals') ?>">Minhas Negociações</a></li>
                <li class="breadcrumb-item active">Minhas Ofertas</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-hand-thumbs-up text-info"></i>
                Minhas Ofertas
            </h1>
            <a href="<?= base_url('my-deals') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-clock text-warning display-6"></i>
                <h4 class="mt-2"><?= count(array_filter($bids, fn($b) => $b['accepted'] === null)) ?></h4>
                <p class="text-muted mb-0">Pendentes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-check-circle text-success display-6"></i>
                <h4 class="mt-2"><?= count(array_filter($bids, fn($b) => $b['accepted'] === 1)) ?></h4>
                <p class="text-muted mb-0">Aceitas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-x-circle text-danger display-6"></i>
                <h4 class="mt-2"><?= count(array_filter($bids, fn($b) => $b['accepted'] === 0)) ?></h4>
                <p class="text-muted mb-0">Recusadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-chat-dots text-primary display-6"></i>
                <h4 class="mt-2"><?= count(array_filter($bids, fn($b) => !empty($b['description']))) ?></h4>
                <p class="text-muted mb-0">Com Mensagem</p>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Ofertas -->
<?php if (empty($bids)): ?>
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-hand-thumbs-up display-1 text-muted"></i>
                <h3 class="text-muted mt-3">Você ainda não fez ofertas</h3>
                <p class="text-muted">Explore as negociações disponíveis e faça sua primeira oferta.</p>
                <a href="<?= base_url('deals') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-search"></i> Ver Negociações
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
                        <i class="bi bi-list-ul"></i> Suas Ofertas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Negociação</th>
                                    <th>Tipo</th>
                                    <th>Valor/Oferta</th>
                                    <th>Status</th>
                                    <th>Mensagem</th>
                                    <th>Data</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bids as $bid): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="bi bi-briefcase text-muted"></i>
                                                </div>
                                                <div>
                                                    <strong><?= esc($bid['deal_description']) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Criada por: <?= esc($bid['creator_name'] ?? 'Usuário') ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $bid['type'] == 1 ? 'success' : 'info' ?>">
                                                <?= $bid['type'] == 1 ? 'Compra' : 'Troca' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($bid['type'] == 1): ?>
                                                <span class="text-success fw-bold">
                                                    R$ <?= number_format($bid['value'], 2, ',', '.') ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-info">
                                                    <?= esc($bid['trade_for'] ?? 'Troca') ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $statusClass = 'secondary';
                                            $statusText = 'Pendente';
                                            
                                            if ($bid['accepted'] === 1) {
                                                $statusClass = 'success';
                                                $statusText = 'Aceita';
                                            } elseif ($bid['accepted'] === 0) {
                                                $statusClass = 'danger';
                                                $statusText = 'Recusada';
                                            }
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>">
                                                <?= $statusText ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($bid['description'])): ?>
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-chat"></i> Sim
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($bid['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= base_url("deals/{$bid['deal_id']}") ?>" 
                                                   class="btn btn-outline-primary" 
                                                   title="Ver negociação">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if ($bid['accepted'] === null): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-warning" 
                                                            onclick="cancelBid(<?= $bid['id'] ?>)"
                                                            title="Cancelar oferta">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                <?php endif; ?>
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
                <button type="button" class="btn btn-danger" id="confirmAction">Confirmar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function cancelBid(bidId) {
        const message = 'Tem certeza que deseja cancelar esta oferta?';
        
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmAction').onclick = function() {
            // Implementar chamada para API
            console.log('Cancelar oferta:', bidId);
            
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
