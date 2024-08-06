<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Configurar Web Push - <?= esc($application['name']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-bell text-primary"></i> Configurar Web Push</h2>
                    <p class="text-muted mb-0">Aplicativo: <strong><?= esc($application['name']) ?></strong></p>
                </div>
                <div>
                    <a href="/applications/<?= $application['id'] ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
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

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Erros de validação:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="/channels/webpush/<?= $application['id'] ?>/save" method="post" id="webpushForm">
                <?= csrf_field() ?>
                
                <!-- Configurações VAPID -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-key text-warning"></i> Chaves VAPID
                            <small class="text-muted">(Voluntary Application Server Identification)</small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Sobre as chaves VAPID:</strong> São necessárias para identificar seu servidor junto aos navegadores e garantir a segurança das notificações push.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vapid_public_key" class="form-label">Chave Pública VAPID *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control font-monospace" id="vapid_public_key" name="vapid_public_key" 
                                               value="<?= esc($config['vapid']['public_key'] ?? '') ?>" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard('vapid_public_key')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">87 caracteres em Base64 URL-safe</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vapid_private_key" class="form-label">Chave Privada VAPID *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control font-monospace" id="vapid_private_key" name="vapid_private_key" 
                                               value="<?= esc($config['vapid']['private_key'] ?? '') ?>" required>
                                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('vapid_private_key')">
                                            <i class="fas fa-eye" id="vapid_private_key_icon"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">43 caracteres em Base64 URL-safe</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vapid_subject" class="form-label">E-mail de Contato *</label>
                                    <input type="email" class="form-control" id="vapid_subject" name="vapid_subject" 
                                           value="<?= esc($config['vapid']['subject'] ?? '') ?>" required>
                                    <div class="form-text">E-mail para contato em caso de problemas</div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="button" class="btn btn-success" onclick="generateVapidKeys()">
                                    <i class="fas fa-magic"></i> Gerar Chaves Automaticamente
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aparência -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-palette text-info"></i> Aparência das Notificações
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon_url" class="form-label">URL do Ícone</label>
                                    <input type="url" class="form-control" id="icon_url" name="icon_url" 
                                           value="<?= esc($config['appearance']['icon_url'] ?? '') ?>">
                                    <div class="form-text">Ícone exibido na notificação (recomendado: 192x192px)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="badge_url" class="form-label">URL do Badge</label>
                                    <input type="url" class="form-control" id="badge_url" name="badge_url" 
                                           value="<?= esc($config['appearance']['badge_url'] ?? '') ?>">
                                    <div class="form-text">Badge monocromático (recomendado: 96x96px)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações Padrão -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cog text-secondary"></i> Configurações Padrão
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="default_title" class="form-label">Título Padrão *</label>
                                    <input type="text" class="form-control" id="default_title" name="default_title" 
                                           value="<?= esc($config['defaults']['title'] ?? '') ?>" required maxlength="255">
                                    <div class="form-text">Título usado quando não especificado</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="default_url" class="form-label">URL Padrão</label>
                                    <input type="url" class="form-control" id="default_url" name="default_url" 
                                           value="<?= esc($config['defaults']['url'] ?? '') ?>">
                                    <div class="form-text">URL aberta ao clicar na notificação</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="default_body" class="form-label">Mensagem Padrão *</label>
                            <textarea class="form-control" id="default_body" name="default_body" rows="3" required maxlength="500"><?= esc($config['defaults']['body'] ?? '') ?></textarea>
                            <div class="form-text">Mensagem usada quando não especificada</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="require_interaction" name="require_interaction" 
                                           <?= ($config['defaults']['require_interaction'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="require_interaction">
                                        Exigir Interação
                                    </label>
                                    <div class="form-text">Notificação permanece até o usuário interagir</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="silent" name="silent" 
                                           <?= ($config['defaults']['silent'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="silent">
                                        Notificação Silenciosa
                                    </label>
                                    <div class="form-text">Não emite som nem vibração</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notificação de Boas-vindas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-hand-wave text-success"></i> Notificação de Boas-vindas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="welcome_enabled" name="welcome_enabled" 
                                   <?= ($config['welcome']['enabled'] ?? false) ? 'checked' : '' ?> onchange="toggleWelcomeFields()">
                            <label class="form-check-label" for="welcome_enabled">
                                <strong>Enviar notificação de boas-vindas</strong>
                            </label>
                            <div class="form-text">Enviada automaticamente após o usuário permitir notificações</div>
                        </div>
                        
                        <div id="welcomeFields" style="display: <?= ($config['welcome']['enabled'] ?? false) ? 'block' : 'none' ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="welcome_title" class="form-label">Título da Boas-vindas</label>
                                        <input type="text" class="form-control" id="welcome_title" name="welcome_title" 
                                               value="<?= esc($config['welcome']['title'] ?? '') ?>" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="welcome_url" class="form-label">URL da Boas-vindas</label>
                                        <input type="url" class="form-control" id="welcome_url" name="welcome_url" 
                                               value="<?= esc($config['welcome']['url'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="welcome_body" class="form-label">Mensagem de Boas-vindas</label>
                                <textarea class="form-control" id="welcome_body" name="welcome_body" rows="3" maxlength="500"><?= esc($config['welcome']['body'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Solicitação de Permissão -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shield-alt text-warning"></i> Solicitação de Permissão
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Importante:</strong> Estas configurações personalizam como a solicitação de permissão é apresentada ao usuário.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="permission_title" class="form-label">Título da Solicitação *</label>
                                    <input type="text" class="form-control" id="permission_title" name="permission_title" 
                                           value="<?= esc($config['permission']['title'] ?? 'Permitir Notificações') ?>" required maxlength="255">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="permission_allow_text" class="form-label">Texto "Permitir" *</label>
                                            <input type="text" class="form-control" id="permission_allow_text" name="permission_allow_text" 
                                                   value="<?= esc($config['permission']['allow_text'] ?? 'Permitir') ?>" required maxlength="50">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="permission_deny_text" class="form-label">Texto "Negar" *</label>
                                            <input type="text" class="form-control" id="permission_deny_text" name="permission_deny_text" 
                                                   value="<?= esc($config['permission']['deny_text'] ?? 'Não, obrigado') ?>" required maxlength="50">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="permission_message" class="form-label">Mensagem da Solicitação *</label>
                            <textarea class="form-control" id="permission_message" name="permission_message" rows="3" required maxlength="500"><?= esc($config['permission']['message'] ?? 'Gostaríamos de enviar notificações para mantê-lo atualizado.') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Salvar Configurações
                                </button>
                                <button type="button" class="btn btn-success" onclick="testNotification()">
                                    <i class="fas fa-paper-plane"></i> Testar Notificação
                                </button>
                            </div>
                            <div>
                                <?php if ($channel['is_enabled']): ?>
                                    <a href="/channels/webpush/<?= $application['id'] ?>/disable" class="btn btn-warning" 
                                       onclick="return confirm('Deseja realmente desabilitar o canal Web Push?')">
                                        <i class="fas fa-pause"></i> Desabilitar Canal
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Teste -->
<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resultado do Teste</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="testResult">
                <!-- Resultado será inserido aqui -->
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
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function copyToClipboard(fieldId) {
    const field = document.getElementById(fieldId);
    field.select();
    document.execCommand('copy');
    
    // Feedback visual
    const button = field.nextElementSibling;
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check text-success"></i>';
    setTimeout(() => {
        button.innerHTML = originalHtml;
    }, 2000);
}

function toggleWelcomeFields() {
    const checkbox = document.getElementById('welcome_enabled');
    const fields = document.getElementById('welcomeFields');
    fields.style.display = checkbox.checked ? 'block' : 'none';
    
    // Tornar campos obrigatórios se habilitado
    const titleField = document.getElementById('welcome_title');
    const bodyField = document.getElementById('welcome_body');
    
    if (checkbox.checked) {
        titleField.setAttribute('required', 'required');
        bodyField.setAttribute('required', 'required');
    } else {
        titleField.removeAttribute('required');
        bodyField.removeAttribute('required');
    }
}

function generateVapidKeys() {
    const button = event.target;
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gerando...';
    button.disabled = true;
    
    fetch('/channels/webpush/generate-vapid-keys', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Erro: ' + data.error);
        } else {
            document.getElementById('vapid_public_key').value = data.public_key;
            document.getElementById('vapid_private_key').value = data.private_key;
            alert('Chaves VAPID geradas com sucesso!');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao gerar chaves VAPID');
    })
    .finally(() => {
        button.innerHTML = originalHtml;
        button.disabled = false;
    });
}

function testNotification() {
    const button = event.target;
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testando...';
    button.disabled = true;
    
    fetch('/channels/webpush/<?= $application['id'] ?>/test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        let resultHtml = '';
        
        if (data.success) {
            resultHtml = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> ${data.message}
                </div>
                <div class="mt-3">
                    <h6>Detalhes do Teste:</h6>
                    <ul>
                        <li><strong>Título:</strong> ${data.details.title}</li>
                        <li><strong>Mensagem:</strong> ${data.details.body}</li>
                        <li><strong>Horário:</strong> ${data.details.timestamp}</li>
                    </ul>
                </div>
            `;
        } else {
            resultHtml = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> ${data.error || 'Erro ao enviar notificação de teste'}
                </div>
            `;
        }
        
        document.getElementById('testResult').innerHTML = resultHtml;
        new bootstrap.Modal(document.getElementById('testModal')).show();
    })
    .catch(error => {
        console.error('Erro:', error);
        document.getElementById('testResult').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> Erro de conexão ao testar notificação
            </div>
        `;
        new bootstrap.Modal(document.getElementById('testModal')).show();
    })
    .finally(() => {
        button.innerHTML = originalHtml;
        button.disabled = false;
    });
}

// Inicializar campos de boas-vindas no carregamento da página
document.addEventListener('DOMContentLoaded', function() {
    toggleWelcomeFields();
});
</script>
<?= $this->endSection() ?>