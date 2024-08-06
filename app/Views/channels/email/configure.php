<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Configurar E-mail - <?= esc($application['name']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-envelope text-success"></i> Configurar E-mail</h2>
                    <p class="text-muted mb-0">Configure o canal de e-mail para <?= esc($application['name']) ?></p>
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

            <form action="/channels/email/<?= $application['id'] ?>/save" method="POST" id="emailConfigForm">
                <?= csrf_field() ?>
                
                <!-- Configurações SMTP -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-server"></i> Configurações SMTP</h5>
                        <small class="text-muted">Configure os dados do servidor de e-mail</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="smtp_host" class="form-label">Servidor SMTP *</label>
                                <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                                       value="<?= esc($config['smtp']['host'] ?? '') ?>" 
                                       placeholder="smtp.gmail.com" required>
                                <div class="form-text">Endereço do servidor SMTP (ex: smtp.gmail.com, smtp.outlook.com)</div>
                            </div>
                            <div class="col-md-4">
                                <label for="smtp_port" class="form-label">Porta *</label>
                                <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                       value="<?= esc($config['smtp']['port'] ?? '587') ?>" 
                                       min="1" max="65535" required>
                                <div class="form-text">Porta do servidor (587, 465, 25)</div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="smtp_username" class="form-label">Usuário/E-mail *</label>
                                <input type="email" class="form-control" id="smtp_username" name="smtp_username" 
                                       value="<?= esc($config['smtp']['username'] ?? '') ?>" 
                                       placeholder="seu@email.com" required>
                            </div>
                            <div class="col-md-6">
                                <label for="smtp_password" class="form-label">Senha *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                                           value="<?= esc($config['smtp']['password'] ?? '') ?>" 
                                           placeholder="Senha ou App Password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('smtp_password')">
                                        <i class="fas fa-eye" id="smtp_password_icon"></i>
                                    </button>
                                </div>
                                <div class="form-text">Use App Password para Gmail/Outlook</div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="smtp_encryption" class="form-label">Criptografia *</label>
                                <select class="form-select" id="smtp_encryption" name="smtp_encryption" required>
                                    <option value="tls" <?= ($config['smtp']['encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS (Recomendado)</option>
                                    <option value="ssl" <?= ($config['smtp']['encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                                    <option value="none" <?= ($config['smtp']['encryption'] ?? '') === 'none' ? 'selected' : '' ?>>Nenhuma</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-2 mt-4">
                                    <button type="button" class="btn btn-outline-info" onclick="testConnection()">
                                        <i class="fas fa-plug"></i> Testar Conexão
                                    </button>
                                    <button type="button" class="btn btn-outline-success" onclick="sendTestEmail()">
                                        <i class="fas fa-paper-plane"></i> Enviar Teste
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações de Remetente -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Remetente</h5>
                        <small class="text-muted">Configure os dados do remetente dos e-mails</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="from_email" class="form-label">E-mail do Remetente *</label>
                                <input type="email" class="form-control" id="from_email" name="from_email" 
                                       value="<?= esc($config['from']['email'] ?? '') ?>" 
                                       placeholder="noreply@seudominio.com" required>
                            </div>
                            <div class="col-md-6">
                                <label for="from_name" class="form-label">Nome do Remetente *</label>
                                <input type="text" class="form-control" id="from_name" name="from_name" 
                                       value="<?= esc($config['from']['name'] ?? '') ?>" 
                                       placeholder="Seu App" required>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="reply_to_email" class="form-label">E-mail de Resposta</label>
                                <input type="email" class="form-control" id="reply_to_email" name="reply_to_email" 
                                       value="<?= esc($config['reply_to']['email'] ?? '') ?>" 
                                       placeholder="contato@seudominio.com">
                                <div class="form-text">Deixe vazio para usar o e-mail do remetente</div>
                            </div>
                            <div class="col-md-6">
                                <label for="reply_to_name" class="form-label">Nome para Resposta</label>
                                <input type="text" class="form-control" id="reply_to_name" name="reply_to_name" 
                                       value="<?= esc($config['reply_to']['name'] ?? '') ?>" 
                                       placeholder="Suporte">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações Padrão -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cog"></i> Configurações Padrão</h5>
                        <small class="text-muted">Valores padrão para notificações</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="default_subject" class="form-label">Assunto Padrão *</label>
                                <input type="text" class="form-control" id="default_subject" name="default_subject" 
                                       value="<?= esc($config['defaults']['subject'] ?? 'Nova Notificação') ?>" 
                                       placeholder="Nova Notificação" required>
                            </div>
                            <div class="col-md-6">
                                <label for="default_template" class="form-label">Template Padrão</label>
                                <select class="form-select" id="default_template" name="default_template">
                                    <option value="default" <?= ($config['defaults']['template'] ?? 'default') === 'default' ? 'selected' : '' ?>>Padrão</option>
                                    <option value="minimal" <?= ($config['defaults']['template'] ?? '') === 'minimal' ? 'selected' : '' ?>>Minimalista</option>
                                    <option value="modern" <?= ($config['defaults']['template'] ?? '') === 'modern' ? 'selected' : '' ?>>Moderno</option>
                                    <option value="custom" <?= ($config['defaults']['template'] ?? '') === 'custom' ? 'selected' : '' ?>>Personalizado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- E-mail de Boas-vindas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0"><i class="fas fa-hand-wave"></i> E-mail de Boas-vindas</h5>
                                <small class="text-muted">Enviar automaticamente quando usuário se inscrever</small>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="welcome_enabled" name="welcome_enabled" 
                                       <?= ($config['welcome']['enabled'] ?? false) ? 'checked' : '' ?>
                                       onchange="toggleWelcomeFields()">
                                <label class="form-check-label" for="welcome_enabled">Habilitar</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="welcomeFields" style="<?= ($config['welcome']['enabled'] ?? false) ? '' : 'display: none;' ?>">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="welcome_subject" class="form-label">Assunto do E-mail de Boas-vindas</label>
                                <input type="text" class="form-control" id="welcome_subject" name="welcome_subject" 
                                       value="<?= esc($config['welcome']['subject'] ?? 'Bem-vindo!') ?>" 
                                       placeholder="Bem-vindo ao nosso app!">
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="welcome_template" class="form-label">Template do E-mail</label>
                                <textarea class="form-control" id="welcome_template" name="welcome_template" rows="6" 
                                          placeholder="Conteúdo HTML do e-mail de boas-vindas..."><?= esc($config['welcome']['template'] ?? '') ?></textarea>
                                <div class="form-text">
                                    Use variáveis: {{user_name}}, {{app_name}}, {{date}}, {{time}}
                                    <button type="button" class="btn btn-sm btn-outline-info ms-2" onclick="previewTemplate('welcome')">
                                        <i class="fas fa-eye"></i> Visualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recursos Avançados -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-tools"></i> Recursos Avançados</h5>
                        <small class="text-muted">Configurações adicionais do canal</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="unsubscribe_enabled" name="unsubscribe_enabled" 
                                           <?= ($config['features']['unsubscribe_enabled'] ?? false) ? 'checked' : '' ?>
                                           onchange="toggleUnsubscribeFields()">
                                    <label class="form-check-label" for="unsubscribe_enabled">
                                        <strong>Link de Descadastro</strong><br>
                                        <small class="text-muted">Adicionar link para cancelar inscrição</small>
                                    </label>
                                </div>
                                
                                <div id="unsubscribeFields" style="<?= ($config['features']['unsubscribe_enabled'] ?? false) ? '' : 'display: none;' ?>">
                                    <label for="unsubscribe_url" class="form-label">URL de Descadastro</label>
                                    <input type="url" class="form-control" id="unsubscribe_url" name="unsubscribe_url" 
                                           value="<?= esc($config['features']['unsubscribe_url'] ?? '') ?>" 
                                           placeholder="https://seuapp.com/unsubscribe">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="tracking_enabled" name="tracking_enabled" 
                                           <?= ($config['features']['tracking_enabled'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="tracking_enabled">
                                        <strong>Rastreamento de Abertura</strong><br>
                                        <small class="text-muted">Rastrear quando e-mails são abertos</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="attachments_enabled" name="attachments_enabled" 
                                           <?= ($config['features']['attachments_enabled'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="attachments_enabled">
                                        <strong>Permitir Anexos</strong><br>
                                        <small class="text-muted">Habilitar envio de arquivos anexos</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Limites e Controles -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Limites e Controles</h5>
                        <small class="text-muted">Configure limites de envio para evitar spam</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="daily_limit" class="form-label">Limite Diário</label>
                                <input type="number" class="form-control" id="daily_limit" name="daily_limit" 
                                       value="<?= esc($config['limits']['daily_limit'] ?? '1000') ?>" 
                                       min="1" max="10000">
                                <div class="form-text">Máximo de e-mails por dia</div>
                            </div>
                            <div class="col-md-4">
                                <label for="hourly_limit" class="form-label">Limite por Hora</label>
                                <input type="number" class="form-control" id="hourly_limit" name="hourly_limit" 
                                       value="<?= esc($config['limits']['hourly_limit'] ?? '100') ?>" 
                                       min="1" max="1000">
                                <div class="form-text">Máximo de e-mails por hora</div>
                            </div>
                            <div class="col-md-4">
                                <label for="max_recipients" class="form-label">Máx. Destinatários</label>
                                <input type="number" class="form-control" id="max_recipients" name="max_recipients" 
                                       value="<?= esc($config['limits']['max_recipients'] ?? '50') ?>" 
                                       min="1" max="100">
                                <div class="form-text">Por notificação</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Salvar Configurações
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" onclick="resetForm()">
                                    <i class="fas fa-undo"></i> Resetar
                                </button>
                            </div>
                            <div>
                                <?php if ($channel && $channel['is_enabled']): ?>
                                    <a href="/channels/email/<?= $application['id'] ?>/disable" class="btn btn-outline-danger" 
                                       onclick="return confirm('Tem certeza que deseja desabilitar o canal E-mail?')">
                                        <i class="fas fa-times"></i> Desabilitar Canal
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

<!-- Modal de Preview de Template -->
<div class="modal fade" id="templatePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview do Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="templatePreviewContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Teste de E-mail -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enviar E-mail de Teste</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="test_email" class="form-label">E-mail de Destino</label>
                    <input type="email" class="form-control" id="test_email" 
                           value="<?= esc($config['from']['email'] ?? '') ?>" 
                           placeholder="destino@email.com">
                    <div class="form-text">E-mail que receberá a mensagem de teste</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="confirmSendTest()">Enviar Teste</button>
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
        icon.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'fas fa-eye';
    }
}

function toggleWelcomeFields() {
    const checkbox = document.getElementById('welcome_enabled');
    const fields = document.getElementById('welcomeFields');
    
    if (checkbox.checked) {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }
}

function toggleUnsubscribeFields() {
    const checkbox = document.getElementById('unsubscribe_enabled');
    const fields = document.getElementById('unsubscribeFields');
    
    if (checkbox.checked) {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
    }
}

function testConnection() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testando...';
    btn.disabled = true;
    
    fetch('/channels/email/<?= $application['id'] ?>/test-connection', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Conexão testada com sucesso!', data.message);
        } else {
            showAlert('danger', 'Erro na conexão', data.error);
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro', 'Erro ao testar conexão: ' + error.message);
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function sendTestEmail() {
    const modal = new bootstrap.Modal(document.getElementById('testEmailModal'));
    modal.show();
}

function confirmSendTest() {
    const email = document.getElementById('test_email').value;
    
    if (!email) {
        showAlert('warning', 'Atenção', 'Por favor, informe um e-mail de destino.');
        return;
    }
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('testEmailModal'));
    modal.hide();
    
    fetch('/channels/email/<?= $application['id'] ?>/send-test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'E-mail enviado!', data.message);
        } else {
            showAlert('danger', 'Erro no envio', data.error);
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro', 'Erro ao enviar e-mail: ' + error.message);
    });
}

function previewTemplate(type) {
    const content = document.getElementById(type + '_template').value;
    
    if (!content.trim()) {
        showAlert('warning', 'Atenção', 'Template está vazio.');
        return;
    }
    
    fetch('/channels/email/<?= $application['id'] ?>/preview-template?type=' + type + '&content=' + encodeURIComponent(content))
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('templatePreviewContent').innerHTML = data.preview;
            const modal = new bootstrap.Modal(document.getElementById('templatePreviewModal'));
            modal.show();
        } else {
            showAlert('danger', 'Erro', 'Erro ao gerar preview: ' + data.error);
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro', 'Erro ao gerar preview: ' + error.message);
    });
}

function resetForm() {
    if (confirm('Tem certeza que deseja resetar todas as configurações?')) {
        document.getElementById('emailConfigForm').reset();
        toggleWelcomeFields();
        toggleUnsubscribeFields();
    }
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

// Validação do formulário
document.getElementById('emailConfigForm').addEventListener('submit', function(e) {
    const smtpHost = document.getElementById('smtp_host').value;
    const smtpPort = document.getElementById('smtp_port').value;
    const smtpUsername = document.getElementById('smtp_username').value;
    const smtpPassword = document.getElementById('smtp_password').value;
    const fromEmail = document.getElementById('from_email').value;
    const fromName = document.getElementById('from_name').value;
    const defaultSubject = document.getElementById('default_subject').value;
    
    if (!smtpHost || !smtpPort || !smtpUsername || !smtpPassword || !fromEmail || !fromName || !defaultSubject) {
        e.preventDefault();
        showAlert('danger', 'Erro de Validação', 'Por favor, preencha todos os campos obrigatórios.');
        return false;
    }
    
    // Validar porta
    const port = parseInt(smtpPort);
    if (port < 1 || port > 65535) {
        e.preventDefault();
        showAlert('danger', 'Erro de Validação', 'Porta SMTP deve estar entre 1 e 65535.');
        return false;
    }
    
    // Validar e-mails
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(smtpUsername) || !emailRegex.test(fromEmail)) {
        e.preventDefault();
        showAlert('danger', 'Erro de Validação', 'Por favor, informe e-mails válidos.');
        return false;
    }
    
    return true;
});

// Inicializar campos condicionais
document.addEventListener('DOMContentLoaded', function() {
    toggleWelcomeFields();
    toggleUnsubscribeFields();
});
</script>
<?= $this->endSection() ?>