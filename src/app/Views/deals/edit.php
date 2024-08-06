<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('deals') ?>">Negociações</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url("deals/{$deal['id']}") ?>"><?= esc($deal['description']) ?></a></li>
                <li class="breadcrumb-item active">Editar</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-pencil text-warning"></i>
                Editar Negociação
            </h1>
            <a href="<?= base_url("deals/{$deal['id']}") ?>" class="btn btn-outline-secondary">
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
                <form action="<?= base_url("deals/{$deal['id']}/edit") ?>" method="post" id="dealForm">
                    <?= csrf_field() ?>
                    
                    <!-- Tipo de Negociação -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="type" class="form-label">
                                <i class="bi bi-tag"></i> Tipo de Negociação *
                            </label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Selecione o tipo</option>
                                <option value="1" <?= $deal['type'] == 1 ? 'selected' : '' ?>>Venda</option>
                                <option value="2" <?= $deal['type'] == 2 ? 'selected' : '' ?>>Troca</option>
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
                                   value="<?= $deal['value'] ?>" 
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
                                  required><?= esc($deal['description']) ?></textarea>
                        <div class="form-text">
                            Seja detalhado para aumentar as chances de venda/troca.
                        </div>
                    </div>
                    
                    <!-- Troca por (apenas para trocas) -->
                    <div class="mb-3" id="tradeForSection" style="display: <?= $deal['type'] == 2 ? 'block' : 'none' ?>;">
                        <label for="trade_for" class="form-label">
                            <i class="bi bi-arrow-repeat"></i> Troca por
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="trade_for" 
                               name="trade_for" 
                               placeholder="O que você gostaria de receber em troca?" 
                               value="<?= esc($deal['trade_for'] ?? '') ?>">
                        <div class="form-text">
                            Descreva o que você aceitaria em troca deste produto.
                        </div>
                    </div>
                    
                    <!-- Urgência -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="urgency_type" class="form-label">
                                <i class="bi bi-clock"></i> Nível de Urgência
                            </label>
                            <select class="form-select" id="urgency_type" name="urgency_type">
                                <option value="">Sem urgência</option>
                                <option value="1" <?= $deal['urgency_type'] == 1 ? 'selected' : '' ?>>Baixa</option>
                                <option value="2" <?= $deal['urgency_type'] == 2 ? 'selected' : '' ?>>Média</option>
                                <option value="3" <?= $deal['urgency_type'] == 3 ? 'selected' : '' ?>>Alta</option>
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
                                   value="<?= $deal['urgency_limit_date'] ?? '' ?>">
                        </div>
                    </div>
                    
                    <!-- Botões -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= base_url("deals/{$deal['id']}") ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-circle"></i> Atualizar Negociação
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
                    <i class="bi bi-lightbulb text-warning"></i> Dicas para Edição
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success"></i>
                        <strong>Mantenha informações atualizadas:</strong> Preços e descrições precisas aumentam as chances de venda
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success"></i>
                        <strong>Reveja a urgência:</strong> Ajuste conforme necessário para acelerar a venda
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success"></i>
                        <strong>Verifique detalhes:</strong> Certifique-se de que todas as informações estão corretas
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-info-circle text-info"></i> Informações da Negociação
                </h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">
                    <strong>ID:</strong> #<?= $deal['id'] ?><br>
                    <strong>Criada em:</strong> <?= date('d/m/Y H:i', strtotime($deal['created_at'])) ?><br>
                    <strong>Última atualização:</strong> <?= date('d/m/Y H:i', strtotime($deal['updated_at'])) ?><br>
                    <strong>Status:</strong> <span class="badge bg-<?= ($deal['status'] ?? 'active') == 'active' ? 'success' : 'secondary' ?>"><?= ($deal['status'] ?? 'active') == 'active' ? 'Ativa' : 'Inativa' ?></span>
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
    
    // Validação do formulário
    document.getElementById('dealForm').addEventListener('submit', function(e) {
        const type = document.getElementById('type').value;
        const value = document.getElementById('value').value;
        const description = document.getElementById('description').value.trim();
        
        if (!description) {
            e.preventDefault();
            alert('Por favor, preencha a descrição do produto.');
            document.getElementById('description').focus();
            return false;
        }
        
        if (type === '1' && (!value || value <= 0)) {
            e.preventDefault();
            alert('Por favor, informe um valor válido para venda.');
            document.getElementById('value').focus();
            return false;
        }
        
        if (type === '2') {
            const tradeFor = document.getElementById('trade_for').value.trim();
            if (!tradeFor) {
                e.preventDefault();
                alert('Por favor, descreva o que você aceita em troca.');
                document.getElementById('trade_for').focus();
                return false;
            }
        }
    });
</script>
<?= $this->endSection() ?>
