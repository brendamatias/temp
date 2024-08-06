<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('deals') ?>">Negociações</a></li>
                <li class="breadcrumb-item active">Criar Negociação</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-plus-circle text-primary"></i>
                Criar Nova Negociação
            </h1>
            <a href="<?= base_url('deals') ?>" class="btn btn-outline-secondary">
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
                    <i class="bi bi-info-circle"></i> Informações da Negociação
                </h5>
            </div>
            
            <div class="card-body">
                <form action="<?= base_url('deals/create') ?>" method="post" id="dealForm">
                    <?= csrf_field() ?>
                    
                    <!-- Tipo de Negociação -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label">
                                <i class="bi bi-tag"></i> Tipo de Negociação *
                            </label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Selecione o tipo</option>
                                <option value="1" <?= old('type') == '1' ? 'selected' : '' ?>>Venda</option>
                                <option value="2" <?= old('type') == '2' ? 'selected' : '' ?>>Troca</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="value" class="form-label">
                                <i class="bi bi-currency-dollar"></i> Valor (R$) *
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="value" 
                                   name="value" 
                                   step="0.01" 
                                   min="0" 
                                   value="<?= old('value') ?>" 
                                   required>
                        </div>
                    </div>
                    
                    <!-- Descrição -->
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="bi bi-card-text"></i> Descrição do Produto *
                        </label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Descreva detalhadamente o produto, marca, modelo, estado de conservação, etc." 
                                  required><?= old('description') ?></textarea>
                        <div class="form-text">
                            Seja detalhado para aumentar as chances de venda/troca.
                        </div>
                    </div>
                    
                    <!-- Troca por (apenas para trocas) -->
                    <div class="mb-3" id="tradeForSection" style="display: none;">
                        <label for="trade_for" class="form-label">
                            <i class="bi bi-arrow-repeat"></i> Troca por
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="trade_for" 
                               name="trade_for" 
                               placeholder="O que você gostaria de receber em troca?" 
                               value="<?= old('trade_for') ?>">
                        <div class="form-text">
                            Descreva o que você aceitaria em troca deste produto.
                        </div>
                    </div>
                    
                    <!-- Urgência -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="urgency_type" class="form-label">
                                <i class="bi bi-clock"></i> Urgência
                            </label>
                            <select class="form-select" id="urgency_type" name="urgency_type">
                                <option value="">Sem urgência</option>
                                <option value="1" <?= old('urgency_type') == '1' ? 'selected' : '' ?>>Baixa</option>
                                <option value="2" <?= old('urgency_type') == '2' ? 'selected' : '' ?>>Média</option>
                                <option value="3" <?= old('urgency_type') == '3' ? 'selected' : '' ?>>Alta</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="urgency_limit_date" class="form-label">
                                <i class="bi bi-calendar"></i> Data Limite
                            </label>
                            <input type="date" 
                                   class="form-control" 
                                   id="urgency_limit_date" 
                                   name="urgency_limit_date" 
                                   value="<?= old('urgency_limit_date') ?>">
                        </div>
                    </div>
                    
                    <!-- Localização -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-geo-alt"></i> Localização
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="city" class="form-label">Cidade *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="city" 
                                           name="city" 
                                           value="<?= old('city') ?>" 
                                           required>
                                </div>
                                <div class="col-md-6">
                                    <label for="state" class="form-label">Estado *</label>
                                    <select class="form-select" id="state" name="state" required>
                                        <option value="">Selecione o estado</option>
                                        <option value="AC" <?= old('state') == 'AC' ? 'selected' : '' ?>>Acre</option>
                                        <option value="AL" <?= old('state') == 'AL' ? 'selected' : '' ?>>Alagoas</option>
                                        <option value="AP" <?= old('state') == 'AP' ? 'selected' : '' ?>>Amapá</option>
                                        <option value="AM" <?= old('state') == 'AM' ? 'selected' : '' ?>>Amazonas</option>
                                        <option value="BA" <?= old('state') == 'BA' ? 'selected' : '' ?>>Bahia</option>
                                        <option value="CE" <?= old('state') == 'CE' ? 'selected' : '' ?>>Ceará</option>
                                        <option value="DF" <?= old('state') == 'DF' ? 'selected' : '' ?>>Distrito Federal</option>
                                        <option value="ES" <?= old('state') == 'ES' ? 'selected' : '' ?>>Espírito Santo</option>
                                        <option value="GO" <?= old('state') == 'GO' ? 'selected' : '' ?>>Goiás</option>
                                        <option value="MA" <?= old('state') == 'MA' ? 'selected' : '' ?>>Maranhão</option>
                                        <option value="MT" <?= old('state') == 'MT' ? 'selected' : '' ?>>Mato Grosso</option>
                                        <option value="MS" <?= old('state') == 'MS' ? 'selected' : '' ?>>Mato Grosso do Sul</option>
                                        <option value="MG" <?= old('state') == 'MG' ? 'selected' : '' ?>>Minas Gerais</option>
                                        <option value="PA" <?= old('state') == 'PA' ? 'selected' : '' ?>>Pará</option>
                                        <option value="PB" <?= old('state') == 'PB' ? 'selected' : '' ?>>Paraíba</option>
                                        <option value="PR" <?= old('state') == 'PR' ? 'selected' : '' ?>>Paraná</option>
                                        <option value="PE" <?= old('state') == 'PE' ? 'selected' : '' ?>>Pernambuco</option>
                                        <option value="PI" <?= old('state') == 'PI' ? 'selected' : '' ?>>Piauí</option>
                                        <option value="RJ" <?= old('state') == 'RJ' ? 'selected' : '' ?>>Rio de Janeiro</option>
                                        <option value="RN" <?= old('state') == 'RN' ? 'selected' : '' ?>>Rio Grande do Norte</option>
                                        <option value="RS" <?= old('state') == 'RS' ? 'selected' : '' ?>>Rio Grande do Sul</option>
                                        <option value="RO" <?= old('state') == 'RO' ? 'selected' : '' ?>>Rondônia</option>
                                        <option value="RR" <?= old('state') == 'RR' ? 'selected' : '' ?>>Roraima</option>
                                        <option value="SC" <?= old('state') == 'SC' ? 'selected' : '' ?>>Santa Catarina</option>
                                        <option value="SP" <?= old('state') == 'SP' ? 'selected' : '' ?>>São Paulo</option>
                                        <option value="SE" <?= old('state') == 'SE' ? 'selected' : '' ?>>Sergipe</option>
                                        <option value="TO" <?= old('state') == 'TO' ? 'selected' : '' ?>>Tocantins</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Endereço</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="address" 
                                           name="address" 
                                           placeholder="Rua, número, bairro" 
                                           value="<?= old('address') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="zip_code" class="form-label">CEP</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="zip_code" 
                                           name="zip_code" 
                                           placeholder="00000-000" 
                                           value="<?= old('zip_code') ?>">
                                </div>
                            </div>
                            
                            <!-- Coordenadas (ocultas por padrão) -->
                            <input type="hidden" id="lat" name="lat" value="<?= old('lat', '0') ?>">
                            <input type="hidden" id="lng" name="lng" value="<?= old('lng', '0') ?>">
                            
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-info btn-sm" id="getLocationBtn">
                                    <i class="bi bi-geo-alt"></i> Usar Minha Localização
                                </button>
                                <small class="text-muted ms-2">
                                    Sua localização será usada para mostrar ofertas próximas
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botões -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= base_url('deals') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Criar Negociação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar com Dicas -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-lightbulb text-warning"></i> Dicas para uma Boa Negociação
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success"></i>
                        <strong>Fotos de qualidade:</strong> Mostre o produto de diferentes ângulos
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success"></i>
                        <strong>Descrição detalhada:</strong> Inclua marca, modelo e estado
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success"></i>
                        <strong>Preço competitivo:</strong> Pesquise preços similares no mercado
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success"></i>
                        <strong>Localização clara:</strong> Ajude compradores a encontrarem você
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success"></i>
                        <strong>Responda rápido:</strong> Mantenha comunicação ativa
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-shield-check text-info"></i> Políticas de Segurança
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    • Sempre encontre-se em locais públicos e seguros<br>
                    • Verifique a identidade do comprador/vendedor<br>
                    • Use métodos de pagamento seguros<br>
                    • Denuncie comportamentos suspeitos
                </p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Mostra/oculta campo de troca baseado no tipo selecionado
    document.getElementById('type').addEventListener('change', function() {
        const tradeForSection = document.getElementById('tradeForSection');
        const tradeForInput = document.getElementById('trade_for');
        
        if (this.value === '2') { // Troca
            tradeForSection.style.display = 'block';
            tradeForInput.required = true;
        } else {
            tradeForSection.style.display = 'none';
            tradeForInput.required = false;
            tradeForInput.value = '';
        }
    });
    
    // Obtém localização do usuário
    document.getElementById('getLocationBtn').addEventListener('click', function() {
        if (navigator.geolocation) {
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i> Obtendo...';
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('lat').value = position.coords.latitude;
                    document.getElementById('lng').value = position.coords.longitude;
                    
                    // Tenta obter endereço reverso
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.address) {
                                document.getElementById('city').value = data.address.city || data.address.town || '';
                                document.getElementById('state').value = data.address.state || '';
                                document.getElementById('address').value = data.display_name.split(',')[0] || '';
                            }
                        })
                        .catch(error => console.log('Erro ao obter endereço:', error));
                    
                    document.getElementById('getLocationBtn').innerHTML = '<i class="bi bi-check-circle text-success"></i> Localização Obtida';
                    document.getElementById('getLocationBtn').classList.add('btn-success');
                    document.getElementById('getLocationBtn').classList.remove('btn-outline-info');
                },
                function(error) {
                    alert('Erro ao obter localização: ' + error.message);
                    document.getElementById('getLocationBtn').disabled = false;
                    document.getElementById('getLocationBtn').innerHTML = '<i class="bi bi-geo-alt"></i> Usar Minha Localização';
                }
            );
        } else {
            alert('Geolocalização não é suportada por este navegador.');
        }
    });
    
    // Validação do formulário
    document.getElementById('dealForm').addEventListener('submit', function(e) {
        const type = document.getElementById('type').value;
        const tradeFor = document.getElementById('trade_for').value;
        
        if (type === '2' && !tradeFor.trim()) {
            e.preventDefault();
            alert('Para negociações de troca, é obrigatório informar o que você aceita em troca.');
            document.getElementById('trade_for').focus();
            return false;
        }
    });
</script>
<?= $this->endSection() ?>
