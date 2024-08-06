<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Enviar Notificação - <?= esc($application['name']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-paper-plane text-primary"></i> Enviar Notificação</h2>
                    <p class="text-muted mb-0">Envie notificações manuais para <?= esc($application['name']) ?></p>
                </div>
                <div>
                    <a href="/applications/<?= $application['id'] ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="/applications/<?= $application['id'] ?>/notifications" class="btn btn-outline-info">
                        <i class="fas fa-history"></i> Histórico
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

            <?php if (isset($validation)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Erro de validação:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach ($validation->getErrors() as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <!-- Formulário Principal -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-edit"></i> Compor Notificação</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/applications/<?= $application['id'] ?>/send" id="sendForm">
                                <?= csrf_field() ?>
                                
                                <!-- Seleção de Canal -->
                                <div class="mb-4">
                                    <label class="form-label">Canal de Envio *</label>
                                    <div class="row">
                                        <?php foreach ($channels as $channelType => $channelData): ?>
                                            <?php if ($channelData['configured']): ?>
                                                <div class="col-md-4">
                                                    <div class="card channel-card" data-channel="<?= $channelType ?>">
                                                        <div class="card-body text-center p-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" 
                                                                       name="channel" id="channel_<?= $channelType ?>" 
                                                                       value="<?= $channelType ?>" 
                                                                       <?= (old('channel') === $channelType) ? 'checked' : '' ?>
                                                                       onchange="updateChannelFields()">
                                                                <label class="form-check-label w-100" for="channel_<?= $channelType ?>">
                                                                    <i class="<?= $channelData['icon'] ?> fa-2x mb-2"></i>
                                                                    <h6 class="mb-1"><?= $channelData['name'] ?></h6>
                                                                    <small class="text-muted"><?= $channelData['description'] ?></small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="col-md-4">
                                                    <div class="card channel-card disabled">
                                                        <div class="card-body text-center p-3">
                                                            <i class="<?= $channelData['icon'] ?> fa-2x mb-2 text-muted"></i>
                                                            <h6 class="mb-1 text-muted"><?= $channelData['name'] ?></h6>
                                                            <small class="text-muted">Não configurado</small>
                                                            <div class="mt-2">
                                                                <a href="/applications/<?= $application['id'] ?>/channels/<?= $channelType ?>/configure" 
                                                                   class="btn btn-sm btn-outline-primary">
                                                                    Configurar
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Destinatários -->
                                <div class="mb-3">
                                    <label for="recipients" class="form-label">Destinatários *</label>
                                    <textarea class="form-control" id="recipients" name="recipients" rows="3" 
                                              placeholder="Digite os destinatários separados por vírgula ou quebra de linha..." 
                                              required><?= old('recipients') ?></textarea>
                                    <div class="form-text">
                                        <span id="recipientHelp">Digite os destinatários separados por vírgula ou quebra de linha.</span>
                                        <span class="float-end" id="recipientCount">0 destinatários</span>
                                    </div>
                                </div>

                                <!-- Campos específicos por canal -->
                                <div id="channelFields">
                                    <!-- Web Push Fields -->
                                    <div id="webpush_fields" class="channel-fields" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="webpush_title" class="form-label">Título *</label>
                                                <input type="text" class="form-control" id="webpush_title" 
                                                       name="webpush_title" value="<?= old('webpush_title') ?>" 
                                                       placeholder="Título da notificação">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="webpush_icon" class="form-label">Ícone (URL)</label>
                                                <input type="url" class="form-control" id="webpush_icon" 
                                                       name="webpush_icon" value="<?= old('webpush_icon') ?>" 
                                                       placeholder="https://exemplo.com/icon.png">
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label for="webpush_badge" class="form-label">Badge (URL)</label>
                                                <input type="url" class="form-control" id="webpush_badge" 
                                                       name="webpush_badge" value="<?= old('webpush_badge') ?>" 
                                                       placeholder="https://exemplo.com/badge.png">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="webpush_url" class="form-label">URL de Destino</label>
                                                <input type="url" class="form-control" id="webpush_url" 
                                                       name="webpush_url" value="<?= old('webpush_url') ?>" 
                                                       placeholder="https://exemplo.com/destino">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Email Fields -->
                                    <div id="email_fields" class="channel-fields" style="display: none;">
                                        <div class="mb-3">
                                            <label for="email_subject" class="form-label">Assunto *</label>
                                            <input type="text" class="form-control" id="email_subject" 
                                                   name="email_subject" value="<?= old('email_subject') ?>" 
                                                   placeholder="Assunto do e-mail">
                                        </div>
                                        <div class="mb-3">
                                            <label for="email_template" class="form-label">Template</label>
                                            <select class="form-select" id="email_template" name="email_template">
                                                <option value="default" <?= (old('email_template') === 'default') ? 'selected' : '' ?>>Padrão</option>
                                                <option value="minimal" <?= (old('email_template') === 'minimal') ? 'selected' : '' ?>>Minimalista</option>
                                                <option value="modern" <?= (old('email_template') === 'modern') ? 'selected' : '' ?>>Moderno</option>
                                                <option value="custom" <?= (old('email_template') === 'custom') ? 'selected' : '' ?>>Personalizado</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email_sender_name" class="form-label">Nome do Remetente</label>
                                            <input type="text" class="form-control" id="email_sender_name" 
                                                   name="email_sender_name" value="<?= old('email_sender_name') ?>" 
                                                   placeholder="Nome que aparecerá como remetente">
                                        </div>
                                    </div>

                                    <!-- SMS Fields -->
                                    <div id="sms_fields" class="channel-fields" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="sms_sender" class="form-label">Remetente</label>
                                                <input type="text" class="form-control" id="sms_sender" 
                                                       name="sms_sender" value="<?= old('sms_sender') ?>" 
                                                       placeholder="Nome ou número do remetente">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Opções</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="sms_unicode" name="sms_unicode" value="1" 
                                                           <?= old('sms_unicode') ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="sms_unicode">
                                                        Suporte Unicode (emojis, acentos)
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="sms_flash" name="sms_flash" value="1" 
                                                           <?= old('sms_flash') ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="sms_flash">
                                                        Flash SMS (exibição imediata)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mensagem -->
                                <div class="mb-3">
                                    <label for="message" class="form-label">Mensagem *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" 
                                              placeholder="Digite sua mensagem aqui..." 
                                              required><?= old('message') ?></textarea>
                                    <div class="form-text">
                                        <span id="messageHelp">Digite a mensagem da notificação.</span>
                                        <span class="float-end" id="messageCount">0 caracteres</span>
                                    </div>
                                </div>

                                <!-- Opções Avançadas -->
                                <div class="mb-3">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" 
                                            onclick="toggleAdvancedOptions()">
                                        <i class="fas fa-cog"></i> Opções Avançadas
                                        <i class="fas fa-chevron-down" id="advancedToggleIcon"></i>
                                    </button>
                                </div>

                                <div id="advancedOptions" style="display: none;">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="schedule_date" class="form-label">Agendar Envio</label>
                                                    <input type="datetime-local" class="form-control" 
                                                           id="schedule_date" name="schedule_date" 
                                                           value="<?= old('schedule_date') ?>">
                                                    <div class="form-text">Deixe em branco para enviar imediatamente</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="priority" class="form-label">Prioridade</label>
                                                    <select class="form-select" id="priority" name="priority">
                                                        <option value="normal" <?= (old('priority') === 'normal') ? 'selected' : '' ?>>Normal</option>
                                                        <option value="high" <?= (old('priority') === 'high') ? 'selected' : '' ?>>Alta</option>
                                                        <option value="low" <?= (old('priority') === 'low') ? 'selected' : '' ?>>Baixa</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="track_opens" name="track_opens" value="1" 
                                                               <?= old('track_opens') ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="track_opens">
                                                            Rastrear aberturas (apenas e-mail)
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="track_clicks" name="track_clicks" value="1" 
                                                               <?= old('track_clicks') ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="track_clicks">
                                                            Rastrear cliques em links
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botões de Ação -->
                                <div class="d-flex justify-content-between mt-4">
                                    <div>
                                        <button type="button" class="btn btn-outline-info" onclick="previewNotification()">
                                            <i class="fas fa-eye"></i> Pré-visualizar
                                        </button>
                                        <button type="button" class="btn btn-outline-warning" onclick="saveAsDraft()">
                                            <i class="fas fa-save"></i> Salvar Rascunho
                                        </button>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo"></i> Limpar
                                        </button>
                                        <button type="submit" class="btn btn-success" id="sendButton">
                                            <i class="fas fa-paper-plane"></i> Enviar Notificação
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <!-- Painel de Ajuda -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-question-circle"></i> Ajuda</h6>
                        </div>
                        <div class="card-body">
                            <div id="helpContent">
                                <p class="text-muted">Selecione um canal para ver dicas específicas.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Estatísticas Rápidas -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-chart-bar"></i> Estatísticas</h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-primary"><?= number_format($stats['total_sent'] ?? 0) ?></h4>
                                    <small class="text-muted">Enviadas</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-success"><?= $stats['success_rate'] ?? 0 ?>%</h4>
                                    <small class="text-muted">Taxa Sucesso</small>
                                </div>
                            </div>
                            <hr>
                            <div class="row text-center">
                                <div class="col-4">
                                    <small class="text-muted d-block">Hoje</small>
                                    <strong><?= number_format($stats['today'] ?? 0) ?></strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Esta Semana</small>
                                    <strong><?= number_format($stats['week'] ?? 0) ?></strong>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block">Este Mês</small>
                                    <strong><?= number_format($stats['month'] ?? 0) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rascunhos Salvos -->
                    <?php if (!empty($drafts)): ?>
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-file-alt"></i> Rascunhos Salvos</h6>
                            </div>
                            <div class="card-body">
                                <?php foreach ($drafts as $draft): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($draft['created_at'])) ?></small>
                                            <div class="text-truncate" style="max-width: 200px;">
                                                <?= esc(substr($draft['subject'] ?: $draft['message'], 0, 50)) ?>...
                                            </div>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="loadDraft(<?= $draft['id'] ?>)" title="Carregar">
                                                <i class="fas fa-upload"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="deleteDraft(<?= $draft['id'] ?>)" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pré-visualização -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pré-visualização da Notificação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Conteúdo carregado via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-success" onclick="sendFromPreview()">
                    <i class="fas fa-paper-plane"></i> Enviar
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const helpContent = {
    webpush: `
        <h6>Web Push</h6>
        <ul class="small">
            <li>Destinatários devem ser endpoints de push válidos</li>
            <li>Título é obrigatório (máx. 50 caracteres)</li>
            <li>Mensagem máx. 200 caracteres</li>
            <li>Ícone e badge devem ser URLs válidas</li>
            <li>URL de destino será aberta ao clicar</li>
        </ul>
    `,
    email: `
        <h6>E-mail</h6>
        <ul class="small">
            <li>Destinatários devem ser e-mails válidos</li>
            <li>Assunto é obrigatório</li>
            <li>Use variáveis: {nome}, {email}, {data}</li>
            <li>Templates personalizáveis disponíveis</li>
            <li>Rastreamento de abertura opcional</li>
        </ul>
    `,
    sms: `
        <h6>SMS</h6>
        <ul class="small">
            <li>Destinatários devem ser números válidos</li>
            <li>Mensagem máx. 160 caracteres (SMS simples)</li>
            <li>Unicode aumenta o custo</li>
            <li>Flash SMS aparece imediatamente</li>
            <li>Verifique limites do provedor</li>
        </ul>
    `
};

function updateChannelFields() {
    const selectedChannel = document.querySelector('input[name="channel"]:checked')?.value;
    
    // Esconder todos os campos específicos
    document.querySelectorAll('.channel-fields').forEach(field => {
        field.style.display = 'none';
    });
    
    // Mostrar campos do canal selecionado
    if (selectedChannel) {
        const channelFields = document.getElementById(selectedChannel + '_fields');
        if (channelFields) {
            channelFields.style.display = 'block';
        }
        
        // Atualizar ajuda
        document.getElementById('helpContent').innerHTML = helpContent[selectedChannel] || '<p class="text-muted">Selecione um canal para ver dicas específicas.</p>';
        
        // Atualizar placeholder e validação dos destinatários
        updateRecipientField(selectedChannel);
        
        // Atualizar contador de caracteres da mensagem
        updateMessageCounter(selectedChannel);
    }
    
    // Atualizar estilo dos cards
    document.querySelectorAll('.channel-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    if (selectedChannel) {
        const selectedCard = document.querySelector(`[data-channel="${selectedChannel}"]`);
        if (selectedCard) {
            selectedCard.classList.add('selected');
        }
    }
}

function updateRecipientField(channel) {
    const recipientField = document.getElementById('recipients');
    const recipientHelp = document.getElementById('recipientHelp');
    
    const placeholders = {
        webpush: 'Cole os endpoints de push, um por linha...',
        email: 'Digite os e-mails separados por vírgula ou quebra de linha...',
        sms: 'Digite os números de telefone separados por vírgula ou quebra de linha...'
    };
    
    const helps = {
        webpush: 'Cole os endpoints de push obtidos do navegador.',
        email: 'Digite e-mails válidos separados por vírgula ou quebra de linha.',
        sms: 'Digite números no formato internacional (+5511999999999) ou nacional.'
    };
    
    recipientField.placeholder = placeholders[channel] || 'Digite os destinatários...';
    recipientHelp.textContent = helps[channel] || 'Digite os destinatários separados por vírgula ou quebra de linha.';
}

function updateMessageCounter(channel) {
    const messageField = document.getElementById('message');
    const messageCount = document.getElementById('messageCount');
    const messageHelp = document.getElementById('messageHelp');
    
    const limits = {
        webpush: 200,
        email: null, // Sem limite
        sms: 160
    };
    
    const helps = {
        webpush: 'Mensagem da notificação push (máx. 200 caracteres).',
        email: 'Conteúdo do e-mail. Use HTML se necessário.',
        sms: 'Mensagem SMS (máx. 160 caracteres para SMS simples).'
    };
    
    messageHelp.textContent = helps[channel] || 'Digite a mensagem da notificação.';
    
    function updateCount() {
        const length = messageField.value.length;
        const limit = limits[channel];
        
        if (limit) {
            messageCount.textContent = `${length}/${limit} caracteres`;
            messageCount.className = length > limit ? 'text-danger' : 'text-muted';
        } else {
            messageCount.textContent = `${length} caracteres`;
            messageCount.className = 'text-muted';
        }
    }
    
    messageField.removeEventListener('input', updateCount);
    messageField.addEventListener('input', updateCount);
    updateCount();
}

function updateRecipientCounter() {
    const recipientField = document.getElementById('recipients');
    const recipientCount = document.getElementById('recipientCount');
    
    const recipients = recipientField.value
        .split(/[,\n]/) // Separar por vírgula ou quebra de linha
        .map(r => r.trim()) // Remover espaços
        .filter(r => r.length > 0); // Remover vazios
    
    recipientCount.textContent = `${recipients.length} destinatário${recipients.length !== 1 ? 's' : ''}`;
}

function toggleAdvancedOptions() {
    const panel = document.getElementById('advancedOptions');
    const icon = document.getElementById('advancedToggleIcon');
    
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        icon.className = 'fas fa-chevron-up';
    } else {
        panel.style.display = 'none';
        icon.className = 'fas fa-chevron-down';
    }
}

function previewNotification() {
    const formData = new FormData(document.getElementById('sendForm'));
    
    fetch('/api/notifications/preview', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('previewContent').innerHTML = data.preview;
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        } else {
            showAlert('danger', 'Erro', data.error || 'Erro ao gerar pré-visualização');
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro', 'Erro ao gerar pré-visualização: ' + error.message);
    });
}

function sendFromPreview() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
    modal.hide();
    document.getElementById('sendForm').submit();
}

function saveAsDraft() {
    const formData = new FormData(document.getElementById('sendForm'));
    formData.append('save_as_draft', '1');
    
    fetch('/api/notifications/draft', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Sucesso', 'Rascunho salvo com sucesso!');
        } else {
            showAlert('danger', 'Erro', data.error || 'Erro ao salvar rascunho');
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro', 'Erro ao salvar rascunho: ' + error.message);
    });
}

function loadDraft(draftId) {
    fetch(`/api/notifications/draft/${draftId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const draft = data.draft;
            
            // Preencher campos do formulário
            if (draft.channel) {
                document.querySelector(`input[name="channel"][value="${draft.channel}"]`).checked = true;
                updateChannelFields();
            }
            
            document.getElementById('recipients').value = draft.recipients || '';
            document.getElementById('message').value = draft.message || '';
            
            // Preencher campos específicos do canal
            Object.keys(draft).forEach(key => {
                const field = document.getElementById(key);
                if (field) {
                    if (field.type === 'checkbox') {
                        field.checked = draft[key] === '1' || draft[key] === true;
                    } else {
                        field.value = draft[key];
                    }
                }
            });
            
            updateRecipientCounter();
            showAlert('success', 'Sucesso', 'Rascunho carregado com sucesso!');
        } else {
            showAlert('danger', 'Erro', data.error || 'Erro ao carregar rascunho');
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro', 'Erro ao carregar rascunho: ' + error.message);
    });
}

function deleteDraft(draftId) {
    if (!confirm('Tem certeza que deseja excluir este rascunho?')) {
        return;
    }
    
    fetch(`/api/notifications/draft/${draftId}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Sucesso', 'Rascunho excluído com sucesso!');
            // Recarregar a página após 2 segundos
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert('danger', 'Erro', data.error || 'Erro ao excluir rascunho');
        }
    })
    .catch(error => {
        showAlert('danger', 'Erro', 'Erro ao excluir rascunho: ' + error.message);
    });
}

function resetForm() {
    if (confirm('Tem certeza que deseja limpar todos os campos?')) {
        document.getElementById('sendForm').reset();
        updateChannelFields();
        updateRecipientCounter();
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
    // Atualizar campos quando um canal for selecionado
    document.querySelectorAll('input[name="channel"]').forEach(radio => {
        radio.addEventListener('change', updateChannelFields);
    });
    
    // Contador de destinatários
    document.getElementById('recipients').addEventListener('input', updateRecipientCounter);
    
    // Inicializar contadores
    updateChannelFields();
    updateRecipientCounter();
    
    // Validação do formulário
    document.getElementById('sendForm').addEventListener('submit', function(e) {
        const channel = document.querySelector('input[name="channel"]:checked');
        if (!channel) {
            e.preventDefault();
            showAlert('warning', 'Atenção', 'Selecione um canal para envio.');
            return;
        }
        
        const recipients = document.getElementById('recipients').value.trim();
        if (!recipients) {
            e.preventDefault();
            showAlert('warning', 'Atenção', 'Digite pelo menos um destinatário.');
            return;
        }
        
        const message = document.getElementById('message').value.trim();
        if (!message) {
            e.preventDefault();
            showAlert('warning', 'Atenção', 'Digite uma mensagem.');
            return;
        }
        
        // Desabilitar botão de envio para evitar duplo clique
        const sendButton = document.getElementById('sendButton');
        sendButton.disabled = true;
        sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
    });
});
</script>

<style>
.channel-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.channel-card:hover {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.channel-card.selected {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.channel-card.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.channel-card.disabled:hover {
    transform: none;
    box-shadow: none;
    border-color: transparent;
}

.channel-fields {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    background-color: #f8f9fa;
    margin-bottom: 1rem;
}
</style>
<?= $this->endSection() ?>