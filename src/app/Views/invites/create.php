<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('invites') ?>">Meus Convites</a></li>
                <li class="breadcrumb-item active">Novo Convite</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-plus-circle text-primary"></i>
                Novo Convite
            </h1>
            <a href="<?= base_url('invites') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-envelope"></i> Informações do Convite
                </h5>
            </div>
            
            <div class="card-body">
                <form action="<?= base_url('invites/create') ?>" method="post" id="inviteForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="bi bi-person"></i> Nome do Convidado *
                                </label>
                                <input type="text" 
                                       class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                                       id="name" 
                                       name="name" 
                                       value="<?= old('name') ?>" 
                                       placeholder="Nome completo" 
                                       required>
                                <?php if (session('errors.name')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.name') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope"></i> Email *
                                </label>
                                <input type="email" 
                                       class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                       id="email" 
                                       name="email" 
                                       value="<?= old('email') ?>" 
                                       placeholder="email@exemplo.com" 
                                       required>
                                <?php if (session('errors.email')): ?>
                                    <div class="invalid-feedback">
                                        <?= session('errors.email') ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">
                                    O convite será enviado para este email.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">
                            <i class="bi bi-chat"></i> Mensagem Pessoal
                        </label>
                        <textarea class="form-control <?= session('errors.message') ? 'is-invalid' : '' ?>" 
                                  id="message" 
                                  name="message" 
                                  rows="4" 
                                  placeholder="Escreva uma mensagem personalizada para o convite..."><?= old('message') ?></textarea>
                        <?php if (session('errors.message')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.message') ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">
                            Opcional: Adicione uma mensagem personalizada para tornar o convite mais atrativo.
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expires_at" class="form-label">
                                    <i class="bi bi-calendar"></i> Data de Expiração
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="expires_at" 
                                       name="expires_at" 
                                       value="<?= old('expires_at', date('Y-m-d', strtotime('+7 days'))) ?>">
                                <div class="form-text">
                                    O convite expira automaticamente nesta data.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_uses" class="form-label">
                                    <i class="bi bi-people"></i> Limite de Uso
                                </label>
                                <select class="form-select" id="max_uses" name="max_uses">
                                    <option value="1" <?= old('max_uses', '1') == '1' ? 'selected' : '' ?>>1 uso</option>
                                    <option value="3" <?= old('max_uses') == '3' ? 'selected' : '' ?>>3 usos</option>
                                    <option value="5" <?= old('max_uses') == '5' ? 'selected' : '' ?>>5 usos</option>
                                    <option value="10" <?= old('max_uses') == '10' ? 'selected' : '' ?>>10 usos</option>
                                    <option value="0" <?= old('max_uses') == '0' ? 'selected' : '' ?>>Ilimitado</option>
                                </select>
                                <div class="form-text">
                                    Quantas vezes este convite pode ser usado.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botões -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= base_url('invites') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Enviar Convite
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar com Informações -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle"></i> Sobre os Convites
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    <strong>Como funciona:</strong><br>
                    • O convite será enviado por email<br>
                    • O convidado receberá um link único<br>
                    • Após aceitar, poderá criar uma conta<br>
                    • Você será notificado quando for aceito
                </p>
                
                <hr>
                
                <p class="small text-muted">
                    <strong>Dicas para convites eficazes:</strong><br>
                    • Personalize a mensagem<br>
                    • Explique os benefícios da plataforma<br>
                    • Mencione que é gratuito<br>
                    • Use uma linguagem amigável
                </p>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-shield-check"></i> Política de Convites
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled small text-muted">
                    <li class="mb-1">
                        <i class="bi bi-check text-success"></i>
                        Convites são gratuitos
                    </li>
                    <li class="mb-1">
                        <i class="bi bi-check text-success"></i>
                        Expiração automática
                    </li>
                    <li class="mb-1">
                        <i class="bi bi-check text-success"></i>
                        Controle de uso
                    </li>
                    <li class="mb-1">
                        <i class="bi bi-check text-success"></i>
                        Cancelamento a qualquer momento
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-lightbulb"></i> Exemplo de Mensagem
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    "Olá! Venha participar da TechTrade, uma plataforma incrível para profissionais de tecnologia 
                    comprarem, venderem e trocarem produtos. É totalmente gratuito e seguro!"
                </p>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="useExampleMessage()">
                    <i class="bi bi-clipboard"></i> Usar Exemplo
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Usar mensagem de exemplo
    function useExampleMessage() {
        document.getElementById('message').value = "Olá! Venha participar da TechTrade, uma plataforma incrível para profissionais de tecnologia comprarem, venderem e trocarem produtos. É totalmente gratuito e seguro!";
    }
    
    // Validação do formulário
    document.getElementById('inviteForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        
        if (!name || !email) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos obrigatórios.');
            return false;
        }
        
        // Validação básica de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Por favor, informe um email válido.');
            document.getElementById('email').focus();
            return false;
        }
    });
    
    // Auto-preenchimento da data de expiração
    document.addEventListener('DOMContentLoaded', function() {
        const expiresAt = document.getElementById('expires_at');
        if (!expiresAt.value) {
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            expiresAt.value = nextWeek.toISOString().split('T')[0];
        }
    });
</script>
<?= $this->endSection() ?>
