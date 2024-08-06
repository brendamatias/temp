<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/deals.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Container para alertas AJAX -->
<div id="alertContainer"></div>
<div class="row" data-deal-id="<?= $deal['id'] ?>">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('deals') ?>">Negociações</a></li>
                <li class="breadcrumb-item active"><?= esc($deal['description']) ?></li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-info-circle text-primary"></i>
                <?= esc($deal['description']) ?>
            </h1>
            <div>
                <?php if ($isOwner): ?>
                    <a href="<?= base_url("deals/{$deal['id']}/edit") ?>" class="btn btn-outline-primary me-2">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('deals') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informações da Negociação -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle"></i> Detalhes da Negociação
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tipo:</strong> 
                            <span class="badge bg-<?= $deal['type'] == 1 ? 'success' : 'info' ?>">
                                <?= $deal['type'] == 1 ? 'Venda' : 'Troca' ?>
                            </span>
                        </p>
                        
                        <?php if ($deal['type'] == 1): ?>
                            <p><strong>Valor:</strong> 
                                <span class="h5 text-success">R$ <?= number_format($deal['value'], 2, ',', '.') ?></span>
                            </p>
                        <?php else: ?>
                            <p><strong>Troca por:</strong> <?= esc($deal['trade_for']) ?></p>
                        <?php endif; ?>
                        
                        <p><strong>Status:</strong> 
                                            <span class="badge bg-<?= ($deal['status'] ?? 'active') == 'active' ? 'success' : 'secondary' ?>">
                    <?= ($deal['status'] ?? 'active') == 'active' ? 'Ativa' : 'Inativa' ?>
                </span>
                        </p>
                    </div>
                    
                    <div class="col-md-6">
                        <?php if (isset($deal['location']) && $deal['location']['city']): ?>
                            <p><strong>Localização:</strong></p>
                            <p class="text-muted">
                                <i class="bi bi-geo-alt"></i>
                                <?= esc($deal['location']['city']) ?>, <?= esc($deal['location']['state']) ?>
                            </p>
                            <?php if ($deal['location']['address']): ?>
                                <p class="text-muted">
                                    <i class="bi bi-house"></i>
                                    <?= esc($deal['location']['address']) ?>
                                </p>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php if ($deal['urgency_type']): ?>
                            <p><strong>Urgência:</strong> 
                                <span class="badge bg-danger">
                                    <i class="bi bi-clock"></i> 
                                    <?= $deal['urgency_type'] == 1 ? 'Baixa' : ($deal['urgency_type'] == 2 ? 'Média' : 'Alta') ?>
                                </span>
                            </p>
                            <?php if ($deal['urgency_limit_date']): ?>
                                <p class="text-muted">
                                    <i class="bi bi-calendar"></i>
                                    Limite: <?= date('d/m/Y', strtotime($deal['urgency_limit_date'])) ?>
                                </p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <h6><i class="bi bi-card-text"></i> Descrição Completa:</h6>
                    <p class="text-muted"><?= nl2br(esc($deal['description'])) ?></p>
                </div>
                
                <!-- Fotos -->
                <?php if (!empty($deal['photos'])): ?>
                    <div class="mb-3">
                        <h6><i class="bi bi-images"></i> Fotos do Produto:</h6>
                        <div class="row g-2">
                            <?php foreach ($deal['photos'] as $photo): ?>
                                <div class="col-md-4">
                                    <img src="<?= $photo['src'] ?>" 
                                         class="img-fluid rounded" 
                                         alt="Foto do produto"
                                         style="max-height: 200px; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Mensagens -->
        <div class="card shadow mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-chat-dots"></i> Mensagens
                </h5>
                <?php if ($isAuthenticated && !$isOwner): ?>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#messageModal">
                        <i class="bi bi-plus"></i> Nova Mensagem
                    </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($messages)): ?>
                    <p class="text-muted text-center py-3">
                        <i class="bi bi-chat-dots display-6"></i><br>
                        Nenhuma mensagem ainda.
                    </p>
                <?php else: ?>
                    <?php foreach ($messages as $message): ?>
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-person text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0"><?= esc($message['user_name'] ?? 'Usuário') ?></h6>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($message['created_at'])) ?>
                                    </small>
                                </div>
                                <p class="mb-0"><?= nl2br(esc($message['message'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Ofertas -->
        <?php if ($isOwner): ?>
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-hand-thumbs-up"></i> Ofertas Recebidas
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($bids)): ?>
                        <p class="text-muted text-center py-3">
                            <i class="bi bi-hand-thumbs-up display-6"></i><br>
                            Nenhuma oferta recebida ainda.
                        </p>
                    <?php else: ?>
                        <?php foreach ($bids as $bid): ?>
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0"><?= esc($bid['user_name'] ?? 'Usuário') ?></h6>
                                                                         <span class="badge bg-<?= $bid['accepted'] === null ? 'warning' : ($bid['accepted'] == 1 ? 'success' : 'secondary') ?>">
                                         <?= $bid['accepted'] === null ? 'Pendente' : ($bid['accepted'] == 1 ? 'Aceita' : 'Recusada') ?>
                                     </span>
                                </div>
                                
                                <?php if ($bid['type'] == 1): ?>
                                    <p class="mb-2">
                                        <strong>Oferta:</strong> R$ <?= number_format($bid['value'], 2, ',', '.') ?>
                                    </p>
                                <?php else: ?>
                                    <p class="mb-2">
                                        <strong>Troca por:</strong> <?= esc($bid['trade_for']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($bid['description']): ?>
                                    <p class="mb-2 text-muted"><?= nl2br(esc($bid['description'])) ?></p>
                                <?php endif; ?>
                                
                                <small class="text-muted">
                                    Oferta feita em <?= date('d/m/Y H:i', strtotime($bid['created_at'])) ?>
                                </small>
                                
                                                                 <?php if ($bid['accepted'] === null): ?>
                                     <div class="mt-2">
                                         <button type="button" class="btn btn-success btn-sm me-2" 
                                                 onclick="acceptBid(<?= $bid['id'] ?>)">
                                             <i class="bi bi-check"></i> Aceitar
                                         </button>
                                         <button type="button" class="btn btn-danger btn-sm" 
                                                 onclick="rejectBid(<?= $bid['id'] ?>)">
                                             <i class="bi bi-x"></i> Recusar
                                         </button>
                                     </div>
                                 <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Informações do Vendedor -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-person"></i> Informações do Vendedor
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 80px; height: 80px;">
                    <i class="bi bi-person text-white display-6"></i>
                </div>
                <h6><?= esc($deal['user_name'] ?? 'Usuário') ?></h6>
                <p class="text-muted small">
                    Membro desde <?= date('m/Y', strtotime($deal['user_created_at'] ?? 'now')) ?>
                </p>
                
                <?php if ($isAuthenticated && !$isOwner): ?>
                    <button type="button" class="btn btn-outline-primary btn-sm w-100" 
                            data-bs-toggle="modal" data-bs-target="#bidModal">
                        <i class="bi bi-hand-thumbs-up"></i> Fazer Oferta
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Ações Rápidas -->
        <?php if ($isAuthenticated && !$isOwner): ?>
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightning"></i> Ações Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-info btn-sm" 
                                data-bs-toggle="modal" data-bs-target="#messageModal">
                            <i class="bi bi-chat"></i> Enviar Mensagem
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" 
                                data-bs-toggle="modal" data-bs-target="#bidModal">
                            <i class="bi bi-hand-thumbs-up"></i> Fazer Oferta
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Status da Oferta do Usuário -->
        <?php if ($userBid): ?>
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle"></i> Sua Oferta
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                                                 <span class="badge bg-<?= $userBid['accepted'] === null ? 'warning' : ($userBid['accepted'] == 1 ? 'success' : 'secondary') ?> fs-6">
                             <?= $userBid['accepted'] === null ? 'Pendente' : ($userBid['accepted'] == 1 ? 'Aceita' : 'Recusada') ?>
                         </span>
                        <p class="mt-2 mb-0">
                            <small class="text-muted">
                                Oferta feita em <?= date('d/m/Y H:i', strtotime($userBid['created_at'])) ?>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Mensagem -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-chat"></i> Nova Mensagem
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url("deals/{$deal['id']}/message") ?>" method="post" id="messageForm" data-deal-id="<?= $deal['id'] ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="message" class="form-label">Mensagem:</label>
                        <textarea class="form-control" 
                                  id="message" 
                                  name="message" 
                                  rows="4" 
                                  placeholder="Digite sua mensagem..." 
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Oferta -->
<div class="modal fade" id="bidModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-hand-thumbs-up"></i> Nova Oferta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url("deals/{$deal['id']}/bid") ?>" method="post" id="bidForm" data-deal-id="<?= $deal['id'] ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bid_type" class="form-label">Tipo de Oferta:</label>
                        <select class="form-select" id="bid_type" name="type" required>
                            <option value="">Selecione o tipo</option>
                            <option value="1">Compra</option>
                            <option value="2">Troca</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="bidValueSection" style="display: none;">
                        <label for="bid_value" class="form-label">Valor da Oferta (R$):</label>
                        <input type="number" 
                               class="form-control" 
                               id="bid_value" 
                               name="value" 
                               step="0.01" 
                               min="0">
                    </div>
                    
                    <div class="mb-3" id="bidTradeSection" style="display: none;">
                        <label for="bid_trade_for" class="form-label">Troca por:</label>
                        <input type="text" 
                               class="form-control" 
                               id="bid_trade_for" 
                               name="trade_for" 
                               placeholder="O que você oferece em troca?">
                    </div>
                    
                    <div class="mb-3">
                        <label for="bid_description" class="form-label">Descrição (opcional):</label>
                        <textarea class="form-control" 
                                  id="bid_description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Adicione uma descrição para sua oferta..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Oferta</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/deals.js') ?>"></script>
<?= $this->endSection() ?>
