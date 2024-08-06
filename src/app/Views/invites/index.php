<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                <li class="breadcrumb-item active">Meus Convites</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-envelope text-primary"></i>
                Meus Convites
            </h1>
            <a href="<?= base_url('invites/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Convite
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
                <h4 class="mt-2"><?= count(array_filter($invites, fn($i) => $i['status'] == 'pending')) ?></h4>
                <p class="text-muted mb-0">Pendentes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-check-circle text-success display-6"></i>
                <h4 class="mt-2"><?= count(array_filter($invites, fn($i) => $i['status'] == 'accepted')) ?></h4>
                <p class="text-muted mb-0">Aceitos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-x-circle text-danger display-6"></i>
                <h4 class="mt-2"><?= count(array_filter($invites, fn($i) => $i['status'] == 'rejected')) ?></h4>
                <p class="text-muted mb-0">Recusados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-hourglass-split text-secondary display-6"></i>
                <h4 class="mt-2"><?= count(array_filter($invites, fn($i) => $i['status'] == 'expired')) ?></h4>
                <p class="text-muted mb-0">Expirados</p>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Convites -->
<?php if (empty($invites)): ?>
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-envelope display-1 text-muted"></i>
                <h3 class="text-muted mt-3">Você ainda não enviou convites</h3>
                <p class="text-muted">Convide amigos e colegas para participar da plataforma.</p>
                <a href="<?= base_url('invites/create') ?>" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle"></i> Enviar Primeiro Convite
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
                        <i class="bi bi-list-ul"></i> Convites Enviados
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Convidado</th>
                                    <th>Email</th>
                                    <th>Mensagem</th>
                                    <th>Status</th>
                                    <th>Enviado em</th>
                                    <th>Expira em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invites as $invite): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($invite['name']) ?></strong>
                                        </td>
                                        <td>
                                            <a href="mailto:<?= esc($invite['email']) ?>" class="text-decoration-none">
                                                <?= esc($invite['email']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if ($invite['message']): ?>
                                                <span class="text-muted">
                                                    <?= esc(substr($invite['message'], 0, 50)) ?>
                                                    <?= strlen($invite['message']) > 50 ? '...' : '' ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch ($invite['status']) {
                                                case 'pending':
                                                    $statusClass = 'warning';
                                                    $statusText = 'Pendente';
                                                    break;
                                                case 'accepted':
                                                    $statusClass = 'success';
                                                    $statusText = 'Aceito';
                                                    break;
                                                case 'rejected':
                                                    $statusClass = 'danger';
                                                    $statusText = 'Recusado';
                                                    break;
                                                case 'expired':
                                                    $statusClass = 'secondary';
                                                    $statusText = 'Expirado';
                                                    break;
                                                default:
                                                    $statusClass = 'secondary';
                                                    $statusText = 'Desconhecido';
                                            }
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>">
                                                <?= $statusText ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($invite['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($invite['expires_at']): ?>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($invite['expires_at'])) ?>
                                                </small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" 
                                                        class="btn btn-outline-info" 
                                                        onclick="viewInvite(<?= $invite['id'] ?>)"
                                                        title="Ver detalhes">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                
                                                <?php if ($invite['status'] == 'pending'): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-warning" 
                                                            onclick="resendInvite(<?= $invite['id'] ?>)"
                                                            title="Reenviar">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                    
                                                    <button type="button" 
                                                            class="btn btn-outline-danger" 
                                                            onclick="cancelInvite(<?= $invite['id'] ?>)"
                                                            title="Cancelar">
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

<!-- Modal de Detalhes do Convite -->
<div class="modal fade" id="inviteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-envelope"></i> Detalhes do Convite
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="inviteModalBody">
                <!-- Conteúdo será carregado via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Configuração global do AJAX
    $.ajaxSetup({
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    });

    // Função para mostrar loading
    function showLoading(element) {
        $(element).html(`
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="text-muted mt-2">Carregando...</p>
            </div>
        `);
    }

    // Função para mostrar mensagem de sucesso
    function showSuccess(message) {
        const alertHtml = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').prepend(alertHtml);
        
        // Remove o alerta após 5 segundos
        setTimeout(() => {
            $('.alert-success').fadeOut();
        }, 5000);
    }

    // Função para mostrar mensagem de erro
    function showError(message) {
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').prepend(alertHtml);
        
        // Remove o alerta após 5 segundos
        setTimeout(() => {
            $('.alert-danger').fadeOut();
        }, 5000);
    }

    // Função para formatar data
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
    }

    // Função para obter classe do status
    function getStatusClass(status) {
        const statusClasses = {
            'pending': 'warning',
            'accepted': 'success',
            'rejected': 'danger',
            'expired': 'secondary'
        };
        return statusClasses[status] || 'secondary';
    }

    // Função para obter texto do status
    function getStatusText(status) {
        const statusTexts = {
            'pending': 'Pendente',
            'accepted': 'Aceito',
            'rejected': 'Recusado',
            'expired': 'Expirado'
        };
        return statusTexts[status] || 'Desconhecido';
    }

    // Função para ver detalhes do convite
    function viewInvite(inviteId) {
        const modalBody = document.getElementById('inviteModalBody');
        showLoading(modalBody);
        
        const modal = new bootstrap.Modal(document.getElementById('inviteModal'));
        modal.show();
        
        $.ajax({
            url: `<?= base_url('invites/details') ?>/${inviteId}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const invite = response.data;
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nome do Convidado:</label>
                                    <p class="mb-0">${invite.name}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email:</label>
                                    <p class="mb-0">
                                        <a href="mailto:${invite.email}" class="text-decoration-none">
                                            ${invite.email}
                                        </a>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mensagem:</label>
                                    <p class="mb-0">${invite.message || '<em class="text-muted">Nenhuma mensagem</em>'}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status:</label>
                                    <p class="mb-0">
                                        <span class="badge bg-${getStatusClass(invite.status)}">
                                            ${getStatusText(invite.status)}
                                        </span>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Enviado em:</label>
                                    <p class="mb-0">${formatDate(invite.created_at)}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Expira em:</label>
                                    <p class="mb-0">${invite.expires_at ? formatDate(invite.expires_at) : '<em class="text-muted">Não definido</em>'}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Limite de uso:</label>
                                    <p class="mb-0">${invite.max_uses || 1} ${invite.max_uses == 1 ? 'uso' : 'usos'}</p>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Usos realizados:</label>
                                    <p class="mb-0">${invite.used_count || 0}</p>
                                </div>
                    </div>
                </div>
            `;
                } else {
                    modalBody.innerHTML = `
                        <div class="text-center py-3">
                            <i class="bi bi-exclamation-triangle display-4 text-danger"></i>
                            <p class="text-danger mt-2">${response.message}</p>
                        </div>
                    `;
                }
            },
            error: function(xhr) {
                let errorMessage = 'Erro ao carregar detalhes do convite';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                modalBody.innerHTML = `
                    <div class="text-center py-3">
                        <i class="bi bi-exclamation-triangle display-4 text-danger"></i>
                        <p class="text-danger mt-2">${errorMessage}</p>
                    </div>
                `;
            }
        });
    }
    
    // Função para reenviar convite
    function resendInvite(inviteId) {
        document.getElementById('confirmMessage').textContent = 'Tem certeza que deseja reenviar este convite?';
        document.getElementById('confirmAction').onclick = function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();
            
            // Mostrar loading no botão
            const button = $(`button[onclick="resendInvite(${inviteId})"]`);
            const originalHtml = button.html();
            button.html('<i class="bi bi-hourglass-split"></i> Reenviando...').prop('disabled', true);
            
            $.ajax({
                url: `<?= base_url('invites/resend') ?>/${inviteId}`,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        // Recarregar a página para atualizar os dados
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showError(response.message);
                        button.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Erro ao reenviar convite';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showError(errorMessage);
                    button.html(originalHtml).prop('disabled', false);
                }
            });
        };
        
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    }
    
    // Função para cancelar convite
    function cancelInvite(inviteId) {
        document.getElementById('confirmMessage').textContent = 'Tem certeza que deseja cancelar este convite? Esta ação não pode ser desfeita.';
        document.getElementById('confirmAction').onclick = function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();
            
            // Mostrar loading no botão
            const button = $(`button[onclick="cancelInvite(${inviteId})"]`);
            const originalHtml = button.html();
            button.html('<i class="bi bi-hourglass-split"></i> Cancelando...').prop('disabled', true);
            
            $.ajax({
                url: `<?= base_url('invites/cancel') ?>/${inviteId}`,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSuccess(response.message);
                        // Recarregar a página para atualizar os dados
                        setTimeout(() => {
            location.reload();
                        }, 1500);
                    } else {
                        showError(response.message);
                        button.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Erro ao cancelar convite';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showError(errorMessage);
                    button.html(originalHtml).prop('disabled', false);
                }
            });
        };
        
        const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    }

    // Adicionar CSRF token aos requests AJAX
    $(document).ready(function() {
        // Obter o token CSRF da página
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (csrfToken) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
