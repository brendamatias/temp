<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Novo Aplicativo</h2>
            <a href="/applications" class="btn btn-outline-secondary">
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
                    <i class="fas fa-mobile-alt me-2"></i>
                    Informações do Aplicativo
                </h5>
            </div>
            <div class="card-body">
                <?= form_open('/applications') ?>
                    <div class="mb-4">
                        <label for="name" class="form-label">Nome do Aplicativo *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= old('name') ?>" required 
                               placeholder="Ex: Meu App Mobile">
                        <div class="form-text">Nome que identificará seu aplicativo na plataforma</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Canais de Notificação *</label>
                        <div class="form-text mb-3">Selecione os canais que deseja utilizar neste aplicativo</div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-2 channel-card" data-channel="webpush">
                                    <div class="card-body text-center p-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="channel_webpush" name="channels[]" value="webpush">
                                            <label class="form-check-label w-100" for="channel_webpush">
                                                <i class="fas fa-bell fa-2x text-primary mb-2 d-block"></i>
                                                <strong>Web Push</strong>
                                                <small class="d-block text-muted mt-1">
                                                    Notificações push para navegadores
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card border-2 channel-card" data-channel="email">
                                    <div class="card-body text-center p-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="channel_email" name="channels[]" value="email">
                                            <label class="form-check-label w-100" for="channel_email">
                                                <i class="fas fa-envelope fa-2x text-success mb-2 d-block"></i>
                                                <strong>E-mail</strong>
                                                <small class="d-block text-muted mt-1">
                                                    Notificações por e-mail
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card border-2 channel-card" data-channel="sms">
                                    <div class="card-body text-center p-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="channel_sms" name="channels[]" value="sms">
                                            <label class="form-check-label w-100" for="channel_sms">
                                                <i class="fas fa-sms fa-2x text-warning mb-2 d-block"></i>
                                                <strong>SMS</strong>
                                                <small class="d-block text-muted mt-1">
                                                    Notificações por SMS
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="channel-error" class="text-danger small mt-2" style="display: none;">
                            Selecione pelo menos um canal de notificação
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="/applications" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Criar Aplicativo
                        </button>
                    </div>
                <?= form_close() ?>
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
                    <h6 class="text-primary">O que são os canais?</h6>
                    <p class="small text-muted">
                        Os canais determinam como suas notificações serão enviadas. 
                        Você pode configurar cada canal individualmente após criar o aplicativo.
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6 class="text-primary">Chaves API</h6>
                    <p class="small text-muted">
                        Após criar o aplicativo, você receberá uma API Key e Secret 
                        para integrar com seus sistemas.
                    </p>
                </div>
                
                <div class="alert alert-info small">
                    <i class="fas fa-lightbulb me-1"></i>
                    <strong>Dica:</strong> Você pode adicionar ou remover canais posteriormente 
                    nas configurações do aplicativo.
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Validação de canais
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const channelCards = document.querySelectorAll('.channel-card');
    const channelCheckboxes = document.querySelectorAll('input[name="channels[]"]');
    const channelError = document.getElementById('channel-error');
    
    // Adicionar efeito visual aos cards quando selecionados
    channelCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const card = this.closest('.channel-card');
            if (this.checked) {
                card.classList.add('border-primary', 'bg-light');
            } else {
                card.classList.remove('border-primary', 'bg-light');
            }
            
            // Esconder erro se pelo menos um canal estiver selecionado
            const anyChecked = Array.from(channelCheckboxes).some(cb => cb.checked);
            if (anyChecked) {
                channelError.style.display = 'none';
            }
        });
    });
    
    // Validação no submit
    form.addEventListener('submit', function(e) {
        const anyChecked = Array.from(channelCheckboxes).some(cb => cb.checked);
        
        if (!anyChecked) {
            e.preventDefault();
            channelError.style.display = 'block';
            
            // Scroll para o erro
            channelError.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }
    });
    
    // Permitir clicar no card para selecionar
    channelCards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                const checkbox = this.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
        
        // Adicionar cursor pointer
        card.style.cursor = 'pointer';
    });
});
</script>
<?= $this->endSection() ?>