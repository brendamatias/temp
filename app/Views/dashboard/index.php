<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Dashboard</h2>
            <a href="/applications/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Novo Aplicativo
            </a>
        </div>
    </div>
</div>

<?php if (empty($applications)): ?>
<div class="row">
    <div class="col-12">
        <div class="card text-center py-5">
            <div class="card-body">
                <i class="fas fa-mobile-alt fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">Nenhum aplicativo encontrado</h4>
                <p class="text-muted mb-4">Crie seu primeiro aplicativo para começar a enviar notificações</p>
                <a href="/applications/create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Criar Primeiro Aplicativo
                </a>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="row">
    <?php foreach ($applications as $app): ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white border-bottom-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?= esc($app['name']) ?></h5>
                    <span class="badge bg-<?= $app['is_active'] == 1 ? 'success' : 'secondary' ?>">
                        <?= $app['is_active'] == 1 ? 'Ativo' : 'Inativo' ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <?php 
                    $totalNotifications = 0;
                    $sentNotifications = 0;
                    if (isset($stats[$app['id']])) {
                        foreach ($stats[$app['id']] as $stat) {
                            $totalNotifications += $stat['count'];
                            if ($stat['status'] == 'sent' || $stat['status'] == 'delivered') {
                                $sentNotifications += $stat['count'];
                            }
                        }
                    }
                    ?>
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-0"><?= $totalNotifications ?></h4>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-0"><?= $sentNotifications ?></h4>
                        <small class="text-muted">Enviadas</small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">API Key:</small>
                    <code class="small"><?= substr($app['api_key'], 0, 16) ?>...</code>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="/applications/<?= $app['id'] ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-cog me-1"></i>
                        Configurar
                    </a>
                    <div class="btn-group" role="group">
                        <a href="/applications/<?= $app['id'] ?>/history" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-history me-1"></i>
                            Histórico
                        </a>
                        <a href="/applications/<?= $app['id'] ?>/send" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-paper-plane me-1"></i>
                            Enviar
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light text-muted small">
                Criado em <?= date('d/m/Y', strtotime($app['created_at'])) ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Estatísticas Gerais -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Estatísticas Gerais
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border-end">
                            <h3 class="text-primary"><?= count($applications) ?></h3>
                            <p class="text-muted mb-0">Aplicativos</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <?php 
                            $totalAll = 0;
                            foreach ($stats as $appStats) {
                                foreach ($appStats as $stat) {
                                    $totalAll += $stat['count'];
                                }
                            }
                            ?>
                            <h3 class="text-info"><?= $totalAll ?></h3>
                            <p class="text-muted mb-0">Total de Notificações</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end">
                            <?php 
                            $sentAll = 0;
                            foreach ($stats as $appStats) {
                                foreach ($appStats as $stat) {
                                    if ($stat['status'] == 'sent' || $stat['status'] == 'delivered') {
                                        $sentAll += $stat['count'];
                                    }
                                }
                            }
                            ?>
                            <h3 class="text-success"><?= $sentAll ?></h3>
                            <p class="text-muted mb-0">Enviadas com Sucesso</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <?php 
                        $failedAll = 0;
                        foreach ($stats as $appStats) {
                            foreach ($appStats as $stat) {
                                if ($stat['status'] == 'failed') {
                                    $failedAll += $stat['count'];
                                }
                            }
                        }
                        ?>
                        <h3 class="text-danger"><?= $failedAll ?></h3>
                        <p class="text-muted mb-0">Falharam</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->endSection() ?>