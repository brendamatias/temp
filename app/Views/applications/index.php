<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Meus Aplicativos</h2>
            <a href="/applications/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Novo Aplicativo
            </a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

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
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>API Key</th>
                                <th>Status</th>
                                <th>Criado em</th>
                                <th width="200">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-mobile-alt text-primary me-2"></i>
                                        <strong><?= esc($app['name']) ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <code class="small"><?= substr($app['api_key'], 0, 16) ?>...</code>
                                    <button class="btn btn-sm btn-outline-secondary ms-2" 
                                            onclick="copyToClipboard('<?= $app['api_key'] ?>')" 
                                            title="Copiar API Key completa">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $app['is_active'] == 1 ? 'success' : 'secondary' ?>">
                                        <?= $app['is_active'] == 1 ? 'Ativo' : 'Inativo' ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($app['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="/applications/<?= $app['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/applications/<?= $app['id'] ?>/edit" 
                                           class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?= $app['id'] ?>, '<?= esc($app['name']) ?>')" 
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
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

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o aplicativo <strong id="appName"></strong>?</p>
                <p class="text-danger small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta ação não pode ser desfeita e todos os dados relacionados serão perdidos.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Criar toast de sucesso
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
        toast.style.top = '20px';
        toast.style.right = '20px';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    API Key copiada para a área de transferência!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remover o toast após ser ocultado
        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toast);
        });
    });
}

function confirmDelete(id, name) {
    document.getElementById('appName').textContent = name;
    document.getElementById('deleteForm').action = '/applications/' + id;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
<?= $this->endSection() ?>