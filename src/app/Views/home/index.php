<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-house-heart text-primary"></i>
                Bem-vindo ao TechTrade
            </h1>
            <?php if ($isAuthenticated): ?>
                <a href="<?= base_url('deals/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Criar Negociação
                </a>
            <?php endif; ?>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <p class="lead text-muted">
                    Plataforma para profissionais de tecnologia comprarem, venderem e trocarem produtos 
                    que possibilitem a realização de seus trabalhos de forma rápida e otimizada.
                </p>
            </div> 
        </div>
    </div>
</div>

<!-- Ofertas Principais -->
<div class="row">
    <div class="col-12">
        <h2 class="h4 mb-3">
            <i class="bi bi-star-fill text-warning"></i>
            Ofertas em Destaque
        </h2>
    </div>
</div>

<?php if (empty($deals)): ?>
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h3 class="text-muted mt-3">Nenhuma oferta disponível</h3>
                <p class="text-muted">Aguarde novas negociações serem criadas.</p>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($deals as $deal): ?>
            <div class="col">
                <div class="card deal-card h-100">
                    <?php if (!empty($deal['photos'])): ?>
                        <img src="<?= $deal['photos'][0]['src'] ?? '/assets/images/placeholder.jpg' ?>" 
                             class="card-img-top deal-image" alt="Foto do produto">
                    <?php else: ?>
                        <div class="card-img-top deal-image bg-light d-flex align-items-center justify-content-center">
                            <i class="bi bi-image text-muted display-6"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-<?= $deal['type'] == 1 ? 'success' : 'info' ?>">
                                <?= $deal['type'] == 1 ? 'Venda' : 'Troca' ?>
                            </span>
                            <?php if ($deal['urgency_type']): ?>
                                <span class="badge bg-danger">
                                    <i class="bi bi-clock"></i> Urgente
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <h5 class="card-title"><?= esc($deal['description']) ?></h5>
                        
                        <div class="mb-2">
                            <?php if ($deal['type'] == 1): ?>
                                <span class="h6 text-success">
                                    R$ <?= number_format($deal['value'], 2, ',', '.') ?>
                                </span>
                            <?php else: ?>
                                <span class="text-info">
                                    <i class="bi bi-arrow-repeat"></i> Troca por: <?= esc($deal['trade_for']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (isset($deal['location']) && $deal['location']['city']): ?>
                            <p class="card-text text-muted small">
                                <i class="bi bi-geo-alt"></i> 
                                <?= esc($deal['location']['city']) ?>, <?= esc($deal['location']['state']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer bg-transparent">
                        <a href="<?= base_url("deals/{$deal['id']}") ?>" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-eye"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="<?= base_url('deals') ?>" class="btn btn-outline-primary">
                <i class="bi bi-search"></i> Ver Todas as Negociações
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Estatísticas -->
<div class="row mt-5">
    <div class="col-12">
        <h2 class="h4 mb-4">
            <i class="bi bi-graph-up text-success"></i>
            Por que escolher o TechTrade?
        </h2>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="text-center">
            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                 style="width: 80px; height: 80px;">
                <i class="bi bi-lightning text-primary display-6"></i>
            </div>
            <h5>Rápido e Eficiente</h5>
            <p class="text-muted">Encontre produtos de tecnologia rapidamente para otimizar seus custos de trabalho.</p>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="text-center">
            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                 style="width: 80px; height: 80px;">
                <i class="bi bi-shield-check text-success display-6"></i>
            </div>
            <h5>Seguro e Confiável</h5>
            <p class="text-muted">Plataforma segura para profissionais de tecnologia realizarem transações.</p>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="text-center">
            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                 style="width: 80px; height: 80px;">
                <i class="bi bi-people text-info display-6"></i>
            </div>
            <h5>Comunidade Profissional</h5>
            <p class="text-muted">Conecte-se com outros profissionais da área de tecnologia.</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
