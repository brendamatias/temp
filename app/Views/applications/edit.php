<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Editar Aplicativo</h2>
            <a href="/applications/<?= $application['id'] ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->get('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            <?php foreach (session()->get('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Informações do Aplicativo
                </h5>
            </div>
            <div class="card-body">
                <?= form_open('/applications/' . $application['id']) ?>
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="mb-4">
                        <label for="name" class="form-label">Nome do Aplicativo *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= old('name', $application['name']) ?>" required 
                               placeholder="Ex: Meu App Mobile">
                        <div class="form-text">Nome que identificará seu aplicativo na plataforma</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="is_active" class="form-label">Status *</label>
                        <select class="form-select" id="is_active" name="is_active" required>
                            <option value="1" <?= old('is_active', $application['is_active']) == '1' ? 'selected' : '' ?>>
                                Ativo
                            </option>
                            <option value="0" <?= old('is_active', $application['is_active']) == '0' ? 'selected' : '' ?>>
                                Inativo
                            </option>
                        </select>
                        <div class="form-text">
                            Aplicativos inativos não podem enviar notificações
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/applications/<?= $application['id'] ?>" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Salvar Alterações
                        </button>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
        
        <!-- Gerenciar Canais -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-broadcast-tower me-2"></i>
                    Canais de Notificação
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    Gerencie os canais de notificação disponíveis para este aplicativo.
                </p>
                
                <?php if (empty($channels)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-broadcast-tower fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum canal configurado</h5>
                        <p class="text-muted">Adicione canais para começar a enviar notificações</p>
                        <button class="btn btn-primary" onclick="showAddChannelModal()">
                            <i class="fas fa-plus me-2"></i>
                            Adicionar Canal
                        </button>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php 
                        $channelInfo = [
                            'webpush' => [
                                'name' => 'Web Push',
                                'icon' => 'fas fa-bell',
                                'color' => 'primary'
                            ],
                            'email' => [
                                'name' => 'E-mail',
                                'icon' => 'fas fa-envelope',
                                'color' => 'success'
                            ],
                            'sms' => [
                                'name' => 'SMS',
                                'icon' => 'fas fa-sms',
                                'color' => 'warning'
                            ]
                        ];
                        
                        $existingChannels = array_column($channels, 'channel_type');
                        $availableChannels = array_diff(['webpush', 'email', 'sms'], $existingChannels);
                        ?>
                        
                        <?php foreach ($channels as $channel): ?>
                            <?php 
                            $config = json_decode($channel['configuration'], true) ?? [];
                            $isConfigured = !empty($config);
                            $info = $channelInfo[$channel['channel_type']] ?? [];
                            ?>
                            <div class="col-md-6 mb-3">
                                <div class="card border-2 h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="<?= $info['icon'] ?> text-<?= $info['color'] ?> me-2"></i>
                                                    <?= $info['name'] ?>
                                                </h6>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" 
                                                           href="/channels/<?= $channel['channel_type'] ?>/<?= $application['id'] ?>">
                                                            <i class="fas fa-cog me-2"></i>
                                                            Configurar
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item text-danger" 
                                                                onclick="confirmRemoveChannel('<?= $channel['id'] ?>', '<?= $info['name'] ?>')">
                                                            <i class="fas fa-trash me-2"></i>
                                                            Remover
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <?php if ($channel['is_enabled']): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inativo</span>
                                            <?php endif; ?>
                                            
                                            <?php if ($isConfigured): ?>
                                                <span class="badge bg-info">Configurado</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Não configurado</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (!empty($availableChannels)): ?>
                            <div class="col-12">
                                <div class="text-center">
                                    <button class="btn btn-outline-primary" onclick="showAddChannelModal()">
                                        <i class="fas fa-plus me-2"></i>
                                        Adicionar Canal
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informações
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Criado em:</small>
                    <strong><?= date('d/m/Y H:i', strtotime($application['created_at'])) ?></strong>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">Última atualização:</small>
                    <strong><?= date('d/m/Y H:i', strtotime($application['updated_at'])) ?></strong>
                </div>
                
                <div class="mb-3">
                    <small class="text-muted d-block">API Key:</small>
                    <code class="small"><?= substr($application['api_key'], 0, 16) ?>...</code>
                </div>
                
                <div class="alert alert-warning small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    <strong>Atenção:</strong> Alterar o status para "Inativo" impedirá 
                    o envio de novas notificações.
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Zona de Perigo
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">
                    Ações irreversíveis que afetarão permanentemente este aplicativo.
                </p>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-danger btn-sm" 
                            onclick="confirmDelete()">
                        <i class="fas fa-trash me-2"></i>
                        Excluir Aplicativo
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para adicionar canal -->
<div class="modal fade" id="addChannelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Canal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Selecione o canal que deseja adicionar:</p>
                <div id="availableChannels"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmação para remover canal -->
<div class="modal fade" id="removeChannelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remover Canal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover o canal <strong id="channelName"></strong>?</p>
                <p class="text-danger small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta ação não pode ser desfeita e todas as configurações do canal serão perdidas.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="removeChannelForm" method="POST" style="display: inline;">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Remover</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmação para excluir aplicativo -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Excluir Aplicativo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o aplicativo <strong><?= esc($application['name']) ?></strong>?</p>
                <p class="text-danger small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Esta ação não pode ser desfeita e todos os dados relacionados serão perdidos permanentemente.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="/applications/<?= $application['id'] ?>/delete" class="btn btn-danger">
                    Excluir Permanentemente
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const availableChannels = <?= json_encode($availableChannels ?? []) ?>;
const channelInfo = {
    'webpush': { name: 'Web Push', icon: 'fas fa-bell', color: 'primary' },
    'email': { name: 'E-mail', icon: 'fas fa-envelope', color: 'success' },
    'sms': { name: 'SMS', icon: 'fas fa-sms', color: 'warning' }
};

function showAddChannelModal() {
    const container = document.getElementById('availableChannels');
    container.innerHTML = '';
    
    if (availableChannels.length === 0) {
        container.innerHTML = '<p class="text-muted">Todos os canais já foram adicionados.</p>';
    } else {
        availableChannels.forEach(channel => {
            const info = channelInfo[channel];
            const div = document.createElement('div');
            div.className = 'mb-2';
            div.innerHTML = `
                <button class="btn btn-outline-${info.color} w-100" onclick="addChannel('${channel}')">
                    <i class="${info.icon} me-2"></i>
                    ${info.name}
                </button>
            `;
            container.appendChild(div);
        });
    }
    
    const modal = new bootstrap.Modal(document.getElementById('addChannelModal'));
    modal.show();
}

function addChannel(channelType) {
    // Implementar adição de canal via AJAX
    fetch(`/applications/<?= $application['id'] ?>/channels`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ channel_type: channelType })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Erro ao adicionar canal: ' + data.message);
        }
    })
    .catch(error => {
        alert('Erro ao adicionar canal.');
    });
}

function confirmRemoveChannel(channelId, channelName) {
    document.getElementById('channelName').textContent = channelName;
    document.getElementById('removeChannelForm').action = `/channels/${channelId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('removeChannelModal'));
    modal.show();
}

function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
<?= $this->endSection() ?>