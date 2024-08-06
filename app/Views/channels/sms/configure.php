<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Configurar SMS - <?= esc($application['name']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-sms text-info"></i> Configurar SMS</h2>
                    <p class="text-muted mb-0">Configure o canal de SMS para <?= esc($application['name']) ?></p>
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

            <form action="/channels/sms/<?= $application['id'] ?>/save" method="POST" id="smsConfigForm">
                <?= csrf_field() ?>
                
                <!-- Seleção de Provedor -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cloud"></i> Provedor de SMS</h5>
                        <small class="text-muted">Escolha o provedor de SMS que deseja utilizar</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="provider" class="form-label">Provedor *</label>
                                <select class="form-select" id="provider" name="provider" required onchange="toggleProviderFields()">
                                    <option value="">Selecione um provedor</option>
                                    <option value="twilio" <?= ($config['provider'] ?? '') === 'twilio' ? 'selected' : '' ?>>Twilio</option>
                                    <option value="nexmo" <?= ($config['provider'] ?? '') === 'nexmo' ? 'selected' : '' ?>>Nexmo (Vonage)</option>
                                    <option value="aws_sns" <?= ($config['provider'] ?? '') === 'aws_sns' ? 'selected' : '' ?>>Amazon SNS</option>
                                    <option value="custom" <?= ($config['provider'] ?? '') === 'custom' ? 'selected' : '' ?>>Provedor Personalizado</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-2 mt-4">
                                    <button type="button" class="btn btn-outline-info" onclick="showProviderInfo()">
                                        <i class="fas fa-info-circle"></i> Info dos Provedores
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" onclick="testConnection()">
                                        <i class="fas fa-plug"></i> Testar Conexão
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações do Twilio -->
                <div class="card mb-4" id="twilioConfig" style="display: none;">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fab fa-twilio"></i> Configurações do Twilio</h5>
                        <small class="text-muted">Configure suas credenciais do Twilio</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="account_sid" class="form-label">Account SID *</label>
                                <input type="text" class="form-control" id="account_sid" name="account_sid" 
                                       value="<?= esc($config['credentials']['account_sid'] ?? '') ?>" 
                                       placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                                <div class="form-text">Encontre no Console do Twilio</div>
                            </div>
                            <div class="col-md-6">
                                <label for="auth_token" class="form-label">Auth Token *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="auth_token" name="auth_token" 
                                           value="<?= esc($config['credentials']['auth_token'] ?? '') ?>" 
                                           placeholder="Token de autenticação">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('auth_token')">
                                        <i class="fas fa-eye" id="auth_token_icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações do Nexmo -->
                <div class="card mb-4" id="nexmoConfig" style="display: none;">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-satellite"></i> Configurações do Nexmo</h5>
                        <small class="text-muted">Configure suas credenciais do Nexmo/Vonage</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="api_key" class="form-label">API Key *</label>
                                <input type="text" class="form-control" id="api_key" name="api_key" 
                                       value="<?= esc($config['credentials']['api_key'] ?? '') ?>" 
                                       placeholder="12345678">
                            </div>
                            <div class="col-md-6">
                                <label for="api_secret" class="form-label">API Secret *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="api_secret" name="api_secret" 
                                           value="<?= esc($config['credentials']['api_secret'] ?? '') ?>" 
                                           placeholder="Secret de autenticação">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('api_secret')">
                                        <i class="fas fa-eye" id="api_secret_icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações do AWS SNS -->
                <div class="card mb-4" id="awsConfig" style="display: none;">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fab fa-aws"></i> Configurações do Amazon SNS</h5>
                        <small class="text-muted">Configure suas credenciais da AWS</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="access_key" class="form-label">Access Key ID *</label>
                                <input type="text" class="form-control" id="access_key" name="access_key" 
                                       value="<?= esc($config['credentials']['access_key'] ?? '') ?>" 
                                       placeholder="AKIAIOSFODNN7EXAMPLE">
                            </div>
                            <div class="col-md-4">
                                <label for="secret_key" class="form-label">Secret Access Key *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="secret_key" name="secret_key" 
                                           value="<?= esc($config['credentials']['secret_key'] ?? '') ?>" 
                                           placeholder="Secret key">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('secret_key')">
                                        <i class="fas fa-eye" id="secret_key_icon"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="region" class="form-label">Região *</label>
                                <select class="form-select" id="region" name="region">
                                    <option value="us-east-1" <?= ($config['credentials']['region'] ?? '') === 'us-east-1' ? 'selected' : '' ?>>us-east-1</option>
                                    <option value="us-west-2" <?= ($config['credentials']['region'] ?? '') === 'us-west-2' ? 'selected' : '' ?>>us-west-2</option>
                                    <option value="eu-west-1" <?= ($config['credentials']['region'] ?? '') === 'eu-west-1' ? 'selected' : '' ?>>eu-west-1</option>
                                    <option value="ap-southeast-1" <?= ($config['credentials']['region'] ?? '') === 'ap-southeast-1' ? 'selected' : '' ?>>ap-southeast-1</option>
                                    <option value="sa-east-1" <?= ($config['credentials']['region'] ?? '') === 'sa-east-1' ? 'selected' : '' ?>>sa-east-1</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações Personalizadas -->
                <div class="card mb-4" id="customConfig" style="display: none;">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cogs"></i> Provedor Personalizado</h5>
                        <small class="text-muted">Configure um provedor personalizado via API</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="custom_endpoint" class="form-label">Endpoint da API *</label>
                                <input type="url" class="form-control" id="custom_endpoint" name="custom_endpoint" 
                                       value="<?= esc($config['credentials']['endpoint'] ?? '') ?>" 
                                       placeholder="https://api.seuprovedor.com/sms/send">
                            </div>
                            <div class="col-md-4">
                                <label for="custom_method" class="form-label">Método HTTP *</label>
                                <select class="form-select" id="custom_method" name="custom_method">
                                    <option value="POST" <?= ($config['credentials']['method'] ?? 'POST') === 'POST' ? 'selected' : '' ?>>POST</option>
                                    <option value="GET" <?= ($config['credentials']['method'] ?? '') === 'GET' ? 'selected' : '' ?>>GET</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="custom_auth_type" class="form-label">Tipo de Autenticação</label>
                                <select class="form-select" id="custom_auth_type" name="custom_auth_type" onchange="toggleCustomAuth()">
                                    <option value="none" <?= ($config['credentials']['auth_type'] ?? 'none') === 'none' ? 'selected' : '' ?>>Nenhuma</option>
                                    <option value="bearer" <?= ($config['credentials']['auth_type'] ?? '') === 'bearer' ? 'selected' : '' ?>>Bearer Token</option>
                                    <option value="api_key" <?= ($config['credentials']['auth_type'] ?? '') === 'api_key' ? 'selected' : '' ?>>API Key</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="customAuthField" style="display: none;">
                                <label for="custom_auth_token" class="form-label">Token/Chave</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="custom_auth_token" name="custom_auth_token" 
                                           value="<?= esc($config['credentials']['auth_token'] ?? '') ?>" 
                                           placeholder="Token de autenticação">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('custom_auth_token')">
                                        <i class="fas fa-eye" id="custom_auth_token_icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="custom_headers" class="form-label">Headers Personalizados (JSON)</label>
                                <textarea class="form-control" id="custom_headers" name="custom_headers" rows="3" 
                                          placeholder='{"Content-Type": "application/json", "X-API-Key": "sua-chave"}'><?= esc($config['credentials']['headers'] ?? '{}') ?></textarea>
                                <div class="form-text">Headers adicionais em formato JSON</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações Gerais -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cog"></i> Configurações Gerais</h5>
                        <small class="text-muted">Configurações básicas do canal SMS</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="from_number" class="form-label">Número Remetente *</label>
                                <input type="text" class="form-control" id="from_number" name="from_number" 
                                       value="<?= esc($config['settings']['from_number'] ?? '') ?>" 
                                       placeholder="+5511999999999">
                                <div class="form-text">Número ou código curto para envio</div>
                            </div>
                            <div class="col-md-6">
                                <label for="webhook_url" class="form-label">URL de Webhook</label>
                                <input type="url" class="form-control" id="webhook_url" name="webhook_url" 
                                       value="<?= esc($config['settings']['webhook_url'] ?? '') ?>" 
                                       placeholder="https://seuapp.com/webhook/sms">
                                <div class="form-text">Para receber status de entrega</div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="default_message" class="form-label">Mensagem Padrão *</label>
                                <textarea class="form-control" id="default_message" name="default_message" rows="3" 
                                          maxlength="160" required placeholder="Sua mensagem padrão aqui..."><?= esc($config['defaults']['message'] ?? 'Nova notificação do seu app!') ?></textarea>
                                <div class="form-text">
                                    <span id="messageCounter">0</span>/160 caracteres
                                    <span class="float-end">
                                        <i class="fas fa-info-circle"></i> Use variáveis: {{user_name}}, {{app_name}}, {{message}}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SMS de Boas-vindas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0"><i class="fas fa-hand-wave"></i> SMS de Boas-vindas</h5>
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
                                <label for="welcome_message" class="form-label">Mensagem de Boas-vindas</label>
                                <textarea class="form-control" id="welcome_message" name="welcome_message" rows="3" 
                                          maxlength="160" placeholder="Bem-vindo! Obrigado por se inscrever em nossas notificações."><?= esc($config['welcome']['message'] ?? '') ?></textarea>
                                <div class="form-text">
                                    <span id="welcomeCounter">0</span>/160 caracteres
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
                                    <input class="form-check-input" type="checkbox" id="unicode_enabled" name="unicode_enabled" 
                                           <?= ($config['settings']['unicode_enabled'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="unicode_enabled">
                                        <strong>Suporte Unicode</strong><br>
                                        <small class="text-muted">Permitir caracteres especiais e emojis</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="delivery_reports" name="delivery_reports" 
                                           <?= ($config['settings']['delivery_reports'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="delivery_reports">
                                        <strong>Relatórios de Entrega</strong><br>
                                        <small class="text-muted">Receber confirmação de entrega</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="concatenated_sms" name="concatenated_sms" 
                                           <?= ($config['features']['concatenated_sms'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="concatenated_sms">
                                        <strong>SMS Concatenado</strong><br>
                                        <small class="text-muted">Permitir mensagens longas (múltiplos SMS)</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="flash_sms" name="flash_sms" 
                                           <?= ($config['features']['flash_sms'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="flash_sms">
                                        <strong>Flash SMS</strong><br>
                                        <small class="text-muted">SMS exibido diretamente na tela</small>
                                    </label>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="scheduled_sms" name="scheduled_sms" 
                                           <?= ($config['features']['scheduled_sms'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="scheduled_sms">
                                        <strong>SMS Agendado</strong><br>
                                        <small class="text-muted">Permitir agendamento de envios</small>
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
                        <small class="text-muted">Configure limites de envio para controlar custos</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="daily_limit" class="form-label">Limite Diário</label>
                                <input type="number" class="form-control" id="daily_limit" name="daily_limit" 
                                       value="<?= esc($config['limits']['daily_limit'] ?? '100') ?>" 
                                       min="1" max="10000">
                                <div class="form-text">Máximo de SMS por dia</div>
                            </div>
                            <div class="col-md-3">
                                <label for="hourly_limit" class="form-label">Limite por Hora</label>
                                <input type="number" class="form-control" id="hourly_limit" name="hourly_limit" 
                                       value="<?= esc($config['limits']['hourly_limit'] ?? '20') ?>" 
                                       min="1" max="1000">
                                <div class="form-text">Máximo de SMS por hora</div>
                            </div>
                            <div class="col-md-3">
                                <label for="max_length" class="form-label">Tamanho Máximo</label>
                                <input type="number" class="form-control" id="max_length" name="max_length" 
                                       value="<?= esc($config['limits']['max_length'] ?? '160') ?>" 
                                       min="70" max="1600">
                                <div class="form-text">Caracteres por SMS</div>
                            </div>
                            <div class="col-md-3">
                                <label for="rate_limit" class="form-label">Taxa de Envio</label>
                                <input type="number" class="form-control" id="rate_limit" name="rate_limit" 
                                       value="<?= esc($config['limits']['rate_limit'] ?? '1') ?>" 
                                       min="1" max="10">
                                <div class="form-text">SMS por segundo</div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="validity_period" class="form-label">Período de Validade</label>
                                <select class="form-select" id="validity_period" name="validity_period">
                                    <option value="60" <?= ($config['defaults']['validity_period'] ?? 1440) == 60 ? 'selected' : '' ?>>1 hora</option>
                                    <option value="360" <?= ($config['defaults']['validity_period'] ?? 1440) == 360 ? 'selected' : '' ?>>6 horas</option>
                                    <option value="720" <?= ($config['defaults']['validity_period'] ?? 1440) == 720 ? 'selected' : '' ?>>12 horas</option>
                                    <option value="1440" <?= ($config['defaults']['validity_period'] ?? 1440) == 1440 ? 'selected' : '' ?>>24 horas</option>
                                    <option value="4320" <?= ($config['defaults']['validity_period'] ?? 1440) == 4320 ? 'selected' : '' ?>>3 dias</option>
                                </select>
                                <div class="form-text">Tempo para tentar entregar SMS</div>
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
                                <button type="button" class="btn btn-outline-info ms-2" onclick="sendTestSms()">
                                    <i class="fas fa-paper-plane"></i> Enviar Teste
                                </button>
                            </div>
                            <div>
                                <?php if ($channel && $channel['is_enabled']): ?>
                                    <a href="/channels/sms/<?= $application['id'] ?>/disable" class="btn btn-outline-danger" 
                                       onclick="return confirm('Tem certeza que deseja desabilitar o canal SMS?')">
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

<!-- Modal de Informações dos Provedores -->
<div class="modal fade" id="providerInfoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Informações dos Provedores</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="providerInfoContent">
                <!-- Conteúdo carregado via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Teste de SMS -->
<div class="modal fade" id="testSmsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enviar SMS de Teste</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="test_number" class="form-label">Número de Destino</label>
                    <input type="tel" class="form-control" id="test_number" 
                           placeholder="+5511999999999">
                    <div class="form-text">Número com código do país (ex: +5511999999999)</div>
                </div>
                <div class="mb-3">
                    <label for="test_message" class="form-label">Mensagem</label>
                    <textarea class="form-control" id="test_message" rows="3" maxlength="160" 
                              placeholder="Mensagem de teste">Teste de SMS - <?= esc($application['name']) ?></textarea>
                    <div class="form-text">
                        <span id="testMessageCounter">0</span>/160 caracteres
                    </div>
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

function toggleProviderFields() {
    const provider = document.getElementById('provider').value;
    
    // Ocultar todos os campos
    document.getElementById('twilioConfig').style.display = 'none';
    document.getElementById('nexmoConfig').style.display = 'none';
    document.getElementById('awsConfig').style.display = 'none';
    document.getElementById('customConfig').style.display = 'none';
    
    // Mostrar campos do provedor selecionado
    if (provider === 'twilio') {
        document.getElementById('twilioConfig').style.display = 'block';
    } else if (provider === 'nexmo') {
        document.getElementById('nexmoConfig').style.display = 'block';
    } else if (provider === 'aws_sns') {
        document.getElementById('awsConfig').style.display = 'block';
    } else if (provider === 'custom') {
        document.getElementById('customConfig').style.display = 'block';
        toggleCustomAuth();
    }
}

function toggleCustomAuth() {
    const authType = document.getElementById('custom_auth_type').value;
    const authField = document.getElementById('customAuthField');
    
    if (authType === 'none') {
        authField.style.display = 'none';
    } else {
        authField.style.display = 'block';
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

function updateMessageCounter(textareaId, counterId) {
    const textarea = document.getElementById(textareaId);
    const counter = document.getElementById(counterId);
    
    counter.textContent = textarea.value.length;
    
    // Alterar cor baseado no limite
    if (textarea.value.length > 160) {
        counter.style.color = '#dc3545';
    } else if (textarea.value.length > 140) {
        counter.style.color = '#fd7e14';
    } else {
        counter.style.color = '#6c757d';
    }
}

function testConnection() {
    const provider = document.getElementById('provider').value;
    
    if (!provider) {
        showAlert('warning', 'Atenção', 'Selecione um provedor primeiro.');
        return;
    }
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Testando...';
    btn.disabled = true;
    
    fetch('/channels/sms/<?= $application['id'] ?>/test-connection', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Conexão testada!', data.message);
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

function sendTestSms() {
    const provider = document.getElementById('provider').value;
    
    if (!provider) {
        showAlert('warning', 'Atenção', 'Configure um provedor primeiro.');
        return;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('testSmsModal'));
    modal.show();
}

function confirmSendTest() {
    const number = document.getElementById('test_number').value;
    const message = document.getElementById('test_message').value;
    
    if (!number || !message) {
        showAlert('warning', 'Atenção', 'Preencha o número e a mensagem.');
        return;
    }
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('testSmsModal'));
    modal.hide();
    
    fetch('/channels/sms/<?= $application['id'] ?>/send-test', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ number: number, message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'SMS enviado!', data.message);
        } else {
            showAlert('danger', 'Erro no envio', data.error);
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro', 'Erro ao enviar SMS: ' + error.message);
    });
}

function showProviderInfo() {
    fetch('/channels/sms/provider-info')
    .then(response => response.json())
    .then(data => {
        let content = '';
        
        Object.keys(data).forEach(key => {
            const provider = data[key];
            content += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">${provider.name}</h6>
                    </div>
                    <div class="card-body">
                        <p>${provider.description}</p>
                        <ul>
                            ${provider.features.map(f => '<li>' + f + '</li>').join('')}
                        </ul>
                        <p><strong>Preços:</strong> ${provider.pricing}</p>
                        ${provider.setup_url ? '<a href="' + provider.setup_url + '" target="_blank" class="btn btn-sm btn-outline-primary">Configurar</a>' : ''}
                    </div>
                </div>
            `;
        });
        
        document.getElementById('providerInfoContent').innerHTML = content;
        const modal = new bootstrap.Modal(document.getElementById('providerInfoModal'));
        modal.show();
    })
    .catch(error => {
        showAlert('danger', 'Erro', 'Erro ao carregar informações: ' + error.message);
    });
}

function resetForm() {
    if (confirm('Tem certeza que deseja resetar todas as configurações?')) {
        document.getElementById('smsConfigForm').reset();
        toggleProviderFields();
        toggleWelcomeFields();
        updateMessageCounter('default_message', 'messageCounter');
        updateMessageCounter('welcome_message', 'welcomeCounter');
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

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar campos baseado no provedor selecionado
    toggleProviderFields();
    toggleWelcomeFields();
    
    // Contadores de caracteres
    const defaultMessage = document.getElementById('default_message');
    const welcomeMessage = document.getElementById('welcome_message');
    const testMessage = document.getElementById('test_message');
    
    defaultMessage.addEventListener('input', () => updateMessageCounter('default_message', 'messageCounter'));
    welcomeMessage.addEventListener('input', () => updateMessageCounter('welcome_message', 'welcomeCounter'));
    testMessage.addEventListener('input', () => updateMessageCounter('test_message', 'testMessageCounter'));
    
    // Inicializar contadores
    updateMessageCounter('default_message', 'messageCounter');
    updateMessageCounter('welcome_message', 'welcomeCounter');
    updateMessageCounter('test_message', 'testMessageCounter');
});

// Validação do formulário
document.getElementById('smsConfigForm').addEventListener('submit', function(e) {
    const provider = document.getElementById('provider').value;
    const fromNumber = document.getElementById('from_number').value;
    const defaultMessage = document.getElementById('default_message').value;
    
    if (!provider || !fromNumber || !defaultMessage) {
        e.preventDefault();
        showAlert('danger', 'Erro de Validação', 'Preencha todos os campos obrigatórios.');
        return false;
    }
    
    // Validar campos específicos do provedor
    if (provider === 'twilio') {
        const accountSid = document.getElementById('account_sid').value;
        const authToken = document.getElementById('auth_token').value;
        
        if (!accountSid || !authToken) {
            e.preventDefault();
            showAlert('danger', 'Erro de Validação', 'Preencha as credenciais do Twilio.');
            return false;
        }
    }
    
    return true;
});
</script>
<?= $this->endSection() ?>