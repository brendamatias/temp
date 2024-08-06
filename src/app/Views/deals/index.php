<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2">
                <i class="bi bi-search text-primary"></i>
                Negociações Disponíveis
            </h1>
            <?php if ($isAuthenticated): ?>
                <a href="<?= base_url('deals/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Criar Negociação
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body">
                <form id="filtersForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Tipo</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Todos os tipos</option>
                            <option value="1">Venda</option>
                            <option value="2">Troca</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="min_price" class="form-label">Preço Mínimo</label>
                        <input type="number" class="form-control" id="min_price" name="min_price" 
                               placeholder="R$ 0,00" step="0.01">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="max_price" class="form-label">Preço Máximo</label>
                        <input type="number" class="form-control" id="max_price" name="max_price" 
                               placeholder="R$ 0,00" step="0.01">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="location" class="form-label">Localização</label>
                        <input type="text" class="form-control" id="location" name="location" 
                               placeholder="Cidade ou estado">
                    </div>
                    
                    <div class="col-12">
                        <button type="button" id="searchBtn" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <button type="button" id="clearBtn" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Limpar Filtros
                        </button>
                        <span id="resultsCount" class="ms-3 text-muted"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Negociações -->
<div id="dealsContainer">
    <!-- Loading state -->
    <div id="loadingState" class="row" style="display: none;">
        <div class="col-12">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="text-muted mt-3">Buscando negociações...</p>
            </div>
        </div>
    </div>

    <!-- Empty state -->
    <div id="emptyState" class="row" style="display: none;">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h3 class="text-muted mt-3">Nenhuma negociação encontrada</h3>
                <p class="text-muted">Tente ajustar os filtros ou aguarde novas negociações.</p>
                <?php if ($isAuthenticated): ?>
                    <a href="<?= base_url('deals/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Criar Primeira Negociação
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Results container -->
    <div id="resultsContainer" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <!-- Results will be loaded here via AJAX -->
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Carregar deals iniciais
    loadDeals();

    // Event listeners
    $('#searchBtn').on('click', function() {
        loadDeals();
    });

    $('#clearBtn').on('click', function() {
        clearFilters();
        loadDeals();
    });

    // Buscar ao pressionar Enter nos campos
    $('#filtersForm input, #filtersForm select').on('keypress', function(e) {
        if (e.which === 13) {
            loadDeals();
        }
    });

    function loadDeals() {
        showLoading();
        
        const filters = {
            type: $('#type').val(),
            min_price: $('#min_price').val(),
            max_price: $('#max_price').val(),
            location: $('#location').val()
        };

        // Remover filtros vazios
        Object.keys(filters).forEach(key => {
            if (!filters[key]) {
                delete filters[key];
            }
        });

        $.ajax({
            url: '<?= base_url('deals/search') ?>',
            method: 'GET',
            data: filters,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayDeals(response.data);
                    updateResultsCount(response.count);
                } else {
                    showError('Erro ao carregar negociações: ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Erro ao carregar negociações';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showError(errorMessage);
            }
        });
    }

    function displayDeals(deals) {
        const container = $('#resultsContainer');
        container.empty();

        if (deals.length === 0) {
            showEmptyState();
            return;
        }

        hideEmptyState();

        deals.forEach(function(deal) {
            const dealCard = createDealCard(deal);
            container.append(dealCard);
        });
    }

    function createDealCard(deal) {
        const typeText = deal.type == 1 ? 'Venda' : 'Troca';
        const typeClass = deal.type == 1 ? 'success' : 'info';
        const priceDisplay = deal.type == 1 
            ? `<span class="h6 text-success">R$ ${formatPrice(deal.value)}</span>`
            : `<span class="text-info"><i class="bi bi-arrow-repeat"></i> Troca por: ${escapeHtml(deal.trade_for || '')}</span>`;
        
        const locationDisplay = deal.city && deal.state 
            ? `<p class="card-text text-muted small"><i class="bi bi-geo-alt"></i> ${escapeHtml(deal.city)}, ${escapeHtml(deal.state)}</p>`
            : '';

        const urgencyBadge = deal.urgency_type 
            ? '<span class="badge bg-danger"><i class="bi bi-clock"></i> Urgente</span>'
            : '';

        const photoDisplay = deal.photos && deal.photos.length > 0
            ? `<img src="${deal.photos[0].src}" class="card-img-top deal-image" alt="Foto do produto">`
            : `<div class="card-img-top deal-image bg-light d-flex align-items-center justify-content-center"><i class="bi bi-image text-muted display-6"></i></div>`;

        return $(`
            <div class="col">
                <div class="card deal-card h-100">
                    ${photoDisplay}
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-${typeClass}">${typeText}</span>
                            ${urgencyBadge}
                        </div>
                        <h5 class="card-title">${escapeHtml(deal.description)}</h5>
                        <div class="mb-2">${priceDisplay}</div>
                        ${locationDisplay}
                        <p class="card-text text-muted small">
                            <i class="bi bi-person"></i> ${escapeHtml(deal.user_name || 'Usuário')}
                        </p>
                        <p class="card-text text-muted small">
                            <i class="bi bi-calendar"></i> Criada em ${formatDate(deal.created_at)}
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="${'<?= base_url('deals') ?>'}/${deal.id}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-eye"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        `);
    }

    function clearFilters() {
        $('#filtersForm')[0].reset();
    }

    function showLoading() {
        $('#loadingState').show();
        $('#emptyState').hide();
        $('#resultsContainer').hide();
    }

    function hideLoading() {
        $('#loadingState').hide();
    }

    function showEmptyState() {
        hideLoading();
        $('#emptyState').show();
        $('#resultsContainer').hide();
    }

    function hideEmptyState() {
        hideLoading();
        $('#emptyState').hide();
        $('#resultsContainer').show();
    }

    function updateResultsCount(count) {
        const countText = count === 1 ? '1 negociação encontrada' : `${count} negociações encontradas`;
        $('#resultsCount').text(countText);
    }

    function showError(message) {
        hideLoading();
        showEmptyState();
        
        // Mostrar alerta de erro
        const alertHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').prepend(alertHtml);
        
        // Remove o alerta após 5 segundos
        setTimeout(() => {
            $('.alert-danger').fadeOut();
        }, 5000);
    }

    function formatPrice(price) {
        return parseFloat(price).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR');
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
<?= $this->endSection() ?>
