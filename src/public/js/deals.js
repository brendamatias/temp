$(document).ready(function() {
    
    // Controle dos campos de oferta baseado no tipo
    $('#bid_type').on('change', function() {
        const valueSection = $('#bidValueSection');
        const tradeSection = $('#bidTradeSection');
        const valueInput = $('#bid_value');
        const tradeInput = $('#bid_trade_for');
        
        if (this.value === '1') { // Compra
            valueSection.show();
            tradeSection.hide();
            valueInput.prop('required', true);
            tradeInput.prop('required', false);
            tradeInput.val('');
        } else if (this.value === '2') { // Troca
            valueSection.hide();
            tradeSection.show();
            valueInput.prop('required', false);
            tradeInput.prop('required', true);
            valueInput.val('');
        } else {
            valueSection.hide();
            tradeSection.hide();
            valueInput.prop('required', false);
            tradeInput.prop('required', false);
        }
    });

    // Envio de mensagem via AJAX
    $('#messageForm').on('submit', function(e) {
        e.preventDefault();
        
        const message = $('#message').val().trim();
        if (!message) {
            showAlert('Por favor, digite uma mensagem.', 'warning');
            return false;
        }

        const formData = new FormData(this);
        const dealId = $(this).data('deal-id');
        
        // Mostra loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: `/deals/${dealId}/message`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                if (data.success) {
                    showAlert(data.message, 'success');
                    // Fecha o modal
                    $('#messageModal').modal('hide');
                    // Limpa o formulário
                    $('#messageForm')[0].reset();
                    // Recarrega a página para mostrar a nova mensagem
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert(data.message || 'Erro ao enviar mensagem.', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro:', error);
                showAlert('Erro ao enviar mensagem. Tente novamente.', 'danger');
            },
            complete: function() {
                // Restaura o botão
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Envio de proposta via AJAX
    $('#bidForm').on('submit', function(e) {
        e.preventDefault();
        
        const type = $('#bid_type').val();
        const value = $('#bid_value').val();
        const tradeFor = $('#bid_trade_for').val();
        
        if (type === '1' && !value) {
            showAlert('Por favor, informe o valor da oferta.', 'warning');
            $('#bid_value').focus();
            return false;
        }
        
        if (type === '2' && !tradeFor.trim()) {
            showAlert('Por favor, informe o que você oferece em troca.', 'warning');
            $('#bid_trade_for').focus();
            return false;
        }

        const formData = new FormData(this);
        const dealId = $(this).data('deal-id');
        
        // Mostra loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: `/deals/${dealId}/bid`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data) {
                if (data.success) {
                    showAlert(data.message, 'success');
                    // Fecha o modal
                    $('#bidModal').modal('hide');
                    // Limpa o formulário
                    $('#bidForm')[0].reset();
                    // Recarrega a página para mostrar a nova proposta
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert(data.message || 'Erro ao enviar proposta.', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro:', error);
                showAlert('Erro ao enviar proposta. Tente novamente.', 'danger');
            },
            complete: function() {
                // Restaura o botão
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });

    // Funções para aceitar/rejeitar ofertas via AJAX
    window.acceptBid = function(bidId) {
        if (confirm('Tem certeza que deseja aceitar esta oferta?')) {
            const dealId = $('[data-deal-id]').data('deal-id');
            const btn = $(`button[onclick="acceptBid(${bidId})"]`);
            const originalText = btn.html();
            
            // Mostra loading
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processando...');
            btn.prop('disabled', true);

            $.ajax({
                url: `/deals/${dealId}/bid/${bidId}/accept`,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Recarrega a página para atualizar o status
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert(data.message || 'Erro ao aceitar oferta.', 'danger');
                        // Restaura o botão
                        btn.html(originalText);
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro:', error);
                    showAlert('Erro ao aceitar oferta. Tente novamente.', 'danger');
                    // Restaura o botão
                    btn.html(originalText);
                    btn.prop('disabled', false);
                }
            });
        }
    };

    window.rejectBid = function(bidId) {
        if (confirm('Tem certeza que deseja recusar esta oferta?')) {
            const dealId = $('[data-deal-id]').data('deal-id');
            const btn = $(`button[onclick="rejectBid(${bidId})"]`);
            const originalText = btn.html();
            
            // Mostra loading
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processando...');
            btn.prop('disabled', true);

            $.ajax({
                url: `/deals/${dealId}/bid/${bidId}/reject`,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        // Recarrega a página para atualizar o status
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert(data.message || 'Erro ao recusar oferta.', 'danger');
                        // Restaura o botão
                        btn.html(originalText);
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro:', error);
                    showAlert('Erro ao recusar oferta. Tente novamente.', 'danger');
                    // Restaura o botão
                    btn.html(originalText);
                    btn.prop('disabled', false);
                }
            });
        }
    };

    // Função para mostrar alertas
    function showAlert(message, type = 'info') {
        let alertContainer = $('#alertContainer');
        if (alertContainer.length === 0) {
            // Cria o container se não existir
            alertContainer = $('<div>', {
                id: 'alertContainer',
                css: {
                    position: 'fixed',
                    top: '20px',
                    right: '20px',
                    zIndex: '9999',
                    maxWidth: '400px'
                }
            }).appendTo('body');
        }

        const alertDiv = $(`
            <div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

        alertContainer.append(alertDiv);

        // Remove o alerta após 5 segundos
        setTimeout(() => {
            alertDiv.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Limpa campos quando o modal é fechado
    $('#messageModal').on('hidden.bs.modal', function() {
        $('#messageForm')[0].reset();
    });

    $('#bidModal').on('hidden.bs.modal', function() {
        $('#bidForm')[0].reset();
        // Esconde os campos condicionais
        $('#bidValueSection').hide();
        $('#bidTradeSection').hide();
    });

    // Adiciona efeitos visuais extras
    $('.card').hover(
        function() {
            $(this).addClass('shadow-lg').css('transform', 'translateY(-2px)');
        },
        function() {
            $(this).removeClass('shadow-lg').css('transform', 'translateY(0)');
        }
    );

    // Melhora a experiência do usuário com tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Adiciona animação suave aos botões
    $('.btn').on('click', function() {
        $(this).addClass('btn-clicked');
        setTimeout(() => {
            $(this).removeClass('btn-clicked');
        }, 200);
    });

    // Validação em tempo real para campos obrigatórios
    $('input[required], textarea[required], select[required]').on('blur', function() {
        const field = $(this);
        const value = field.val().trim();
        
        if (!value) {
            field.addClass('is-invalid');
            if (!field.next('.invalid-feedback').length) {
                field.after('<div class="invalid-feedback">Este campo é obrigatório.</div>');
            }
        } else {
            field.removeClass('is-invalid');
            field.next('.invalid-feedback').remove();
        }
    });

    // Remove validação quando o usuário começa a digitar
    $('input[required], textarea[required], select[required]').on('input', function() {
        const field = $(this);
        if (field.hasClass('is-invalid')) {
            field.removeClass('is-invalid');
            field.next('.invalid-feedback').remove();
        }
    });

    // Adiciona efeito de loading aos botões de ação
    $('.btn[onclick*="acceptBid"], .btn[onclick*="rejectBid"]').on('click', function() {
        const btn = $(this);
        const originalText = btn.html();
        
        btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processando...');
        btn.prop('disabled', true);
        
        // Restaura o botão após um tempo (caso a página não recarregue)
        setTimeout(() => {
            btn.html(originalText);
            btn.prop('disabled', false);
        }, 3000);
    });

    // Melhora a responsividade dos modais
    $(window).on('resize', function() {
        if ($(window).width() < 768) {
            $('.modal-dialog').addClass('modal-fullscreen-sm-down');
        } else {
            $('.modal-dialog').removeClass('modal-fullscreen-sm-down');
        }
    });

    // Inicializa responsividade dos modais
    $(window).trigger('resize');
});
