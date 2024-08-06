<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </h4>
            </div>
            
            <div class="card-body p-4">
                <!-- SSO Login -->
                <?php if (isset($_GET['token'])): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Processando login via SSO...
                    </div>
                    <script>
                        // Redireciona para processar o token SSO
                        window.location.href = '<?= base_url('sso?token=' . $_GET['token']) ?>';
                    </script>
                <?php endif; ?>

                <!-- Formulário de Login -->
                <form action="<?= base_url('login') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="login" class="form-label">
                            <i class="bi bi-person"></i> Login ou Email
                        </label>
                        <input type="text" 
                               class="form-control <?= session('errors.login') ? 'is-invalid' : '' ?>" 
                               id="login" 
                               name="login" 
                               value="<?= old('login') ?>" 
                               required 
                               autofocus>
                        <?php if (session('errors.login')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.login') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Senha
                        </label>
                        <input type="password" 
                               class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                               id="password" 
                               name="password" 
                               required>
                        <?php if (session('errors.password')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.password') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Lembrar de mim
                        </label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right"></i> Entrar
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <!-- Links Úteis -->
                <div class="text-center">
                    <a href="<?= base_url('forgot-password') ?>" class="text-decoration-none">
                        <i class="bi bi-question-circle"></i> Esqueceu sua senha?
                    </a>
                </div>
                
                <div class="text-center mt-2">
                    <span class="text-muted">Não tem uma conta?</span>
                    <a href="<?= base_url('register') ?>" class="text-decoration-none ms-1">
                        Cadastre-se aqui
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Informações sobre SSO -->
        <div class="card mt-3">
            <div class="card-body text-center">
                <h6 class="card-title">
                    <i class="bi bi-shield-check text-info"></i> Login Corporativo
                </h6>
                <p class="card-text text-muted small">
                    Se sua empresa utiliza login único (SSO), entre em contato com o administrador.
                </p>
                <a href="<?= base_url('sso-info') ?>" class="btn btn-outline-info btn-sm">
                    <i class="bi bi-info-circle"></i> Saiba mais
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Informações SSO -->
<div class="modal fade" id="ssoInfoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-shield-check text-info"></i> Login Corporativo (SSO)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>
                    O <strong>Single Sign-On (SSO)</strong> permite que você faça login na plataforma 
                    usando suas credenciais corporativas, sem precisar criar uma nova conta.
                </p>
                <div class="alert alert-info">
                    <h6><i class="bi bi-lightbulb"></i> Como funciona:</h6>
                    <ol class="mb-0">
                        <li>Clique no link de login corporativo fornecido pela sua empresa</li>
                        <li>Faça login no sistema corporativo</li>
                        <li>Seja redirecionado automaticamente para o TechTrade</li>
                    </ol>
                </div>
                <p class="mb-0">
                    <strong>Dúvidas?</strong> Entre em contato com o administrador de sistemas da sua empresa.
                </p>
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
    // Auto-focus no campo de login
    document.getElementById('login').focus();
    
    // Validação do formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        const login = document.getElementById('login').value.trim();
        const password = document.getElementById('password').value;
        
        if (!login || !password) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos obrigatórios.');
            return false;
        }
    });
</script>
<?= $this->endSection() ?>
