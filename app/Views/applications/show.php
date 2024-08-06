<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><?= esc($application['name']) ?></h2>
                <small class="text-muted">Criado em <?= date('d/m/Y H:i', strtotime($application['created_at'])) ?></small>
            </div>
            <div>
                <a href="/applications" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>
                    Voltar
                </a>
                <a href="/applications/<?= $application['id'] ?>/edit" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>
                    Editar
                </a>
            </div>
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

<div class="row">
    <!-- Informações do Aplicativo -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informações Gerais
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label small text-muted">Status</label>
                    <div>
                        <span class="badge bg-<?= $application['is_active'] == 1 ? 'success' : 'secondary' ?> fs-6">
                            <?= $application['is_active'] == 1 ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small text-muted">API Key</label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace small" 
                               value="<?= $application['api_key'] ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" 
                                onclick="copyToClipboard('<?= $application['api_key'] ?>')">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small text-muted">API Secret</label>
                    <div class="input-group">
                        <input type="password" class="form-control font-monospace small" 
                               id="apiSecret" value="<?= $application['api_secret'] ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" 
                                onclick="toggleSecret()">
                            <i class="fas fa-eye" id="secretIcon"></i>
                        </button>
                        <button class="btn btn-outline-secondary" type="button" 
                                onclick="copyToClipboard('<?= $application['api_secret'] ?>')">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-warning btn-sm" 
                            onclick="confirmRegenerateKeys()">
                        <i class="fas fa-sync me-2"></i>
                        Regenerar Chaves
                    </button>
                    <a href="/applications/<?= $application['id'] ?>/history" 
                       class="btn btn-outline-info btn-sm">
                        <i class="fas fa-history me-2"></i>
                        Ver Histórico
                    </a>
                    <a href="/applications/<?= $application['id'] ?>/send" 
                       class="btn btn-outline-success btn-sm">
                        <i class="fas fa-paper-plane me-2"></i>
                        Enviar Notificação
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Canais de Notificação -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-broadcast-tower me-2"></i>
                    Canais de Notificação
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($channels)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-broadcast-tower fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum canal configurado</h5>
                        <p class="text-muted">Configure os canais de notificação para começar a enviar mensagens</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($channels as $channel): ?>
                            <?php 
                            $config = json_decode($channel['configuration'], true) ?? [];
                            $isConfigured = !empty($config);
                            
                            $channelInfo = [
                                'webpush' => [
                                    'name' => 'Web Push',
                                    'icon' => 'fas fa-bell',
                                    'color' => 'primary',
                                    'url' => '/channels/webpush/' . $application['id']
                                ],
                                'email' => [
                                    'name' => 'E-mail',
                                    'icon' => 'fas fa-envelope',
                                    'color' => 'success',
                                    'url' => '/channels/email/' . $application['id']
                                ],
                                'sms' => [
                                    'name' => 'SMS',
                                    'icon' => 'fas fa-sms',
                                    'color' => 'warning',
                                    'url' => '/channels/sms/' . $application['id']
                                ]
                            ];
                            
                            $info = $channelInfo[$channel['channel_type']] ?? [];
                            ?>
                            <div class="col-md-6 mb-3">
                                <div class="card border-2 h-100 <?= $channel['is_enabled'] ? 'border-success' : 'border-secondary' ?>">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1">
                                                    <i class="<?= $info['icon'] ?> text-<?= $info['color'] ?> me-2"></i>
                                                    <?= $info['name'] ?>
                                                </h6>
                                            </div>
                                            <div>
                                                <?php if ($channel['is_enabled']): ?>
                                                    <span class="badge bg-success">Ativo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inativo</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <?php if ($isConfigured): ?>
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Configurado
                                                </small>
                                            <?php else: ?>
                                                <small class="text-warning">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Não configurado
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <a href="<?= $info['url'] ?>" class="btn btn-outline-<?= $info['color'] ?> btn-sm">
                                                <i class="fas fa-cog me-1"></i>
                                                <?= $isConfigured ? 'Configurar' : 'Configurar Agora' ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Documentação da API -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-code me-2"></i>
                    Integração API
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Use as informações abaixo para integrar este aplicativo com seus sistemas:</p>
                
                <div class="mb-3">
                    <label class="form-label small">Endpoint Base</label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace small" 
                               value="<?= base_url('api/v1') ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" 
                                onclick="copyToClipboard('<?= base_url('api/v1') ?>')">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label small">Exemplo de Envio (cURL)</label>
                    <div class="position-relative">
                        <pre class="bg-dark text-light p-3 rounded small"><code id="curlExample">curl -X POST <?= base_url('api/v1/notifications/send') ?> \
  -H "Content-Type: application/json" \
  -H "X-API-Key: <?= $application['api_key'] ?>" \
  -H "X-API-Secret: <?= $application['api_secret'] ?>" \
  -d '{
    "channel_type": "webpush",
    "title": "Título da Notificação",
    "message": "Mensagem da notificação",
    "recipients": ["user@example.com"]
  }'</code></pre>
                        <button class="btn btn-sm btn-outline-light position-absolute top-0 end-0 m-2" 
                                onclick="copyToClipboard(document.getElementById('curlExample').textContent)">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="alert alert-info small">
                    <i class="fas fa-info-circle me-1"></i>
                    <strong>Documentação completa:</strong> Acesse nossa documentação completa da API 
                    para mais exemplos e detalhes de integração.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmação para regenerar chaves -->
<div class="modal fade" id="regenerateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Regenerar Chaves API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atenção!</strong> Esta ação irá gerar novas chaves API.
                </div>
                <p>As chaves atuais deixarão de funcionar imediatamente. Certifique-se de atualizar 
                   todos os sistemas que utilizam as chaves atuais.</p>
                <p class="mb-0">Deseja continuar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="/applications/<?= $application['id'] ?>/regenerate-keys" class="btn btn-warning">
                    Regenerar Chaves
                </a>
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
                    Copiado para a área de transferência!
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

function toggleSecret() {
    const secretInput = document.getElementById('apiSecret');
    const secretIcon = document.getElementById('secretIcon');
    
    if (secretInput.type === 'password') {
        secretInput.type = 'text';
        secretIcon.className = 'fas fa-eye-slash';
    } else {
        secretInput.type = 'password';
        secretIcon.className = 'fas fa-eye';
    }
}

function confirmRegenerateKeys() {
    const modal = new bootstrap.Modal(document.getElementById('regenerateModal'));
    modal.show();
}
</script>
<?= $this->endSection() ?>