<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Histórico de Notificações - <?= esc($application['name']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-history text-primary"></i> Histórico de Notificações</h2>
                    <p class="text-muted mb-0">Histórico completo de notificações para <?= esc($application['name']) ?></p>
                </div>
                <div>
                    <a href="/applications/<?= $application['id'] ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="/applications/<?= $application['id'] ?>/send" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Enviar Notificação
                    </a>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($stats['total']) ?></h3>
                            <small>Total</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($stats['sent']) ?></h3>
                            <small>Enviadas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($stats['failed']) ?></h3>
                            <small>Falharam</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($stats['pending']) ?></h3>
                            <small>Pendentes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= number_format($stats['delivered']) ?></h3>
                            <small>Entregues</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-secondary text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1"><?= $stats['success_rate'] ?>%</h3>
                            <small>Taxa Sucesso</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearFilters()">
                                <i class="fas fa-times"></i> Limpar
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleFilters()">
                                <i class="fas fa-chevron-down" id="filterToggleIcon"></i> Expandir
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="filterPanel" style="display: none;">
                    <form method="GET" id="filterForm">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="channel" class="form-label">Canal</label>
                                <select class="form-select" id="channel" name="channel">
                                    <option value="">Todos os canais</option>
                                    <?php foreach ($channels as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= ($filters['channel'] === $key) ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos os status</option>
                                    <?php foreach ($statuses as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= ($filters['status'] === $key) ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">Data Inicial</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" 
                                       value="<?= esc($filters['date_from']) ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">Data Final</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" 
                                       value="<?= esc($filters['date_to']) ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="recipient" class="form-label">Destinatário</label>
                                <input type="text" class="form-control" id="recipient" name="recipient" 
                                       value="<?= esc($filters['recipient']) ?>" placeholder="E-mail, telefone...">
                            </div>
                            <div class="col-md-2">
                                <label for="search" class="form-label">Buscar</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?= esc($filters['search']) ?>" placeholder="Assunto, mensagem...">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="clearFilters()">
                                    <i class="fas fa-eraser"></i> Limpar Filtros
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Ações e Exportação -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted">
                                Exibindo <?= count($notifications) ?> de <?= $pager->getTotal() ?> notificações
                            </span>
                        </div>
                        <div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-download"></i> Exportar
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="<?= current_url() ?>/export/pdf<?= $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '' ?>">
                                            <i class="fas fa-file-pdf text-danger"></i> Exportar PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= current_url() ?>/export/excel<?= $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '' ?>">
                                            <i class="fas fa-file-excel text-success"></i> Exportar Excel
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="btn-group ms-2">
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-list"></i> <?= $per_page ?> por página
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= current_url() ?>?<?= http_build_query(array_merge($_GET, ['per_page' => 10])) ?>">10 por página</a></li>
                                    <li><a class="dropdown-item" href="<?= current_url() ?>?<?= http_build_query(array_merge($_GET, ['per_page' => 25])) ?>">25 por página</a></li>
                                    <li><a class="dropdown-item" href="<?= current_url() ?>?<?= http_build_query(array_merge($_GET, ['per_page' => 50])) ?>">50 por página</a></li>
                                    <li><a class="dropdown-item" href="<?= current_url() ?>?<?= http_build_query(array_merge($_GET, ['per_page' => 100])) ?>">100 por página</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Notificações -->
            <div class="card">
                <div class="card-body p-0">
                    <?php if (empty($notifications)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhuma notificação encontrada</h5>
                            <p class="text-muted">Não há notificações que correspondam aos filtros aplicados.</p>
                            <a href="/applications/<?= $application['id'] ?>/send" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Enviar Primeira Notificação
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Data/Hora</th>
                                        <th>Canal</th>
                                        <th>Destinatário</th>
                                        <th>Assunto/Título</th>
                                        <th>Mensagem</th>
                                        <th>Status</th>
                                        <th>Enviado em</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($notifications as $notification): ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y', strtotime($notification['created_at'])) ?><br>
                                                    <?= date('H:i:s', strtotime($notification['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php
                                                $channelIcons = [
                                                    'webpush' => 'fas fa-bell text-info',
                                                    'email' => 'fas fa-envelope text-primary',
                                                    'sms' => 'fas fa-sms text-success'
                                                ];
                                                $icon = $channelIcons[$notification['channel_type']] ?? 'fas fa-question';
                                                ?>
                                                <i class="<?= $icon ?>"></i>
                                                <small class="ms-1"><?= ucfirst($notification['channel_type']) ?></small>
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 150px;" 
                                                      title="<?= esc($notification['recipient']) ?>">
                                                    <?= esc($notification['recipient']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($notification['subject']): ?>
                                                    <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                                          title="<?= esc($notification['subject']) ?>">
                                                        <?= esc($notification['subject']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 250px;" 
                                                      title="<?= esc($notification['message']) ?>">
                                                    <?= esc(substr($notification['message'], 0, 100)) ?><?= strlen($notification['message']) > 100 ? '...' : '' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClasses = [
                                                    'sent' => 'badge bg-success',
                                                    'failed' => 'badge bg-danger',
                                                    'pending' => 'badge bg-warning',
                                                    'delivered' => 'badge bg-info',
                                                    'opened' => 'badge bg-primary',
                                                    'clicked' => 'badge bg-dark'
                                                ];
                                                $statusClass = $statusClasses[$notification['status']] ?? 'badge bg-secondary';
                                                $statusLabel = $statuses[$notification['status']] ?? ucfirst($notification['status']);
                                                ?>
                                                <span class="<?= $statusClass ?>"><?= $statusLabel ?></span>
                                            </td>
                                            <td>
                                                <?php if ($notification['sent_at']): ?>
                                                    <small class="text-muted">
                                                        <?= date('d/m/Y H:i:s', strtotime($notification['sent_at'])) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-info" 
                                                            onclick="showNotificationDetails(<?= $notification['id'] ?>)" 
                                                            title="Ver detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?php if ($notification['status'] === 'failed'): ?>
                                                        <button type="button" class="btn btn-outline-warning" 
                                                                onclick="retryNotification(<?= $notification['id'] ?>)" 
                                                                title="Tentar novamente">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Paginação -->
                        <?php if ($pager->getPageCount() > 1): ?>
                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            Página <?= $pager->getCurrentPage() ?> de <?= $pager->getPageCount() ?>
                                            (<?= number_format($pager->getTotal()) ?> registros)
                                        </small>
                                    </div>
                                    <div>
                                        <?= $pager->links('default', 'bootstrap_pagination') ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalhes da Notificação -->
<div class="modal fade" id="notificationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes da Notificação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="notificationDetailsContent">
                <!-- Conteúdo carregado via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleFilters() {
    const panel = document.getElementById('filterPanel');
    const icon = document.getElementById('filterToggleIcon');
    
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        icon.className = 'fas fa-chevron-up';
    } else {
        panel.style.display = 'none';
        icon.className = 'fas fa-chevron-down';
    }
}

function clearFilters() {
    // Limpar todos os campos do formulário
    document.getElementById('channel').value = '';
    document.getElementById('status').value = '';
    document.getElementById('date_from').value = '';
    document.getElementById('date_to').value = '';
    document.getElementById('recipient').value = '';
    document.getElementById('search').value = '';
    
    // Submeter o formulário para aplicar os filtros limpos
    document.getElementById('filterForm').submit();
}

function showNotificationDetails(notificationId) {
    // Buscar detalhes da notificação via AJAX
    fetch(`/api/notifications/${notificationId}/details`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notification = data.notification;
            
            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informações Básicas</h6>
                        <table class="table table-sm">
                            <tr><td><strong>ID:</strong></td><td>${notification.id}</td></tr>
                            <tr><td><strong>Canal:</strong></td><td>${notification.channel_type}</td></tr>
                            <tr><td><strong>Destinatário:</strong></td><td>${notification.recipient}</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="badge bg-${getStatusColor(notification.status)}">${notification.status}</span></td></tr>
                            <tr><td><strong>Criado em:</strong></td><td>${formatDateTime(notification.created_at)}</td></tr>
                            <tr><td><strong>Enviado em:</strong></td><td>${notification.sent_at ? formatDateTime(notification.sent_at) : '-'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Conteúdo</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Assunto:</strong></td><td>${notification.subject || '-'}</td></tr>
                        </table>
                        <div class="mt-3">
                            <strong>Mensagem:</strong>
                            <div class="border p-2 mt-1 bg-light">
                                ${notification.message}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            if (notification.provider_response) {
                content += `
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Resposta do Provedor</h6>
                            <pre class="bg-light p-2 small">${JSON.stringify(JSON.parse(notification.provider_response), null, 2)}</pre>
                        </div>
                    </div>
                `;
            }
            
            document.getElementById('notificationDetailsContent').innerHTML = content;
            
            const modal = new bootstrap.Modal(document.getElementById('notificationDetailsModal'));
            modal.show();
        } else {
            showAlert('danger', 'Erro', data.error || 'Erro ao carregar detalhes');
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro', 'Erro ao carregar detalhes: ' + error.message);
    });
}

function retryNotification(notificationId) {
    if (!confirm('Tem certeza que deseja tentar enviar esta notificação novamente?')) {
        return;
    }
    
    fetch(`/api/notifications/${notificationId}/retry`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Sucesso', 'Notificação reenviada com sucesso!');
            // Recarregar a página após 2 segundos
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert('danger', 'Erro', data.error || 'Erro ao reenviar notificação');
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro', 'Erro ao reenviar notificação: ' + error.message);
    });
}

function getStatusColor(status) {
    const colors = {
        'sent': 'success',
        'failed': 'danger',
        'pending': 'warning',
        'delivered': 'info',
        'opened': 'primary',
        'clicked': 'dark'
    };
    return colors[status] || 'secondary';
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR');
}

function showAlert(type, title, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <strong>${title}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid .row .col-12');
    const firstCard = container.querySelector('.card');
    firstCard.insertAdjacentHTML('beforebegin', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

// Mostrar filtros se algum estiver aplicado
document.addEventListener('DOMContentLoaded', function() {
    const hasFilters = <?= json_encode(array_filter($filters)) ?> && Object.keys(<?= json_encode(array_filter($filters)) ?>).length > 0;
    
    if (hasFilters) {
        toggleFilters();
    }
    
    // Auto-submit do formulário quando os filtros mudarem
    const filterInputs = document.querySelectorAll('#filterForm input, #filterForm select');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Pequeno delay para melhor UX
            setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 300);
        });
    });
});
</script>
<?= $this->endSection() ?>