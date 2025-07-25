// Sistema de Catálogo de Produtos - JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Elementos DOM
    const contadorCarrinho = document.getElementById('contador-carrinho');
    const loadingSpinner = document.getElementById('loading-spinner');
    const toast = new bootstrap.Toast(document.getElementById('toast-carrinho'));
    const toastMessage = document.getElementById('toast-message');
    
    // Botões de quantidade
    document.querySelectorAll('.btn-quantidade').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.dataset.action;
            const produtoId = this.dataset.produto;
            const input = document.querySelector(`input[data-produto="${produtoId}"]`);
            const currentValue = parseInt(input.value);
            
            if (action === 'aumentar') {
                const maxValue = parseInt(input.getAttribute('max'));
                if (currentValue < maxValue) {
                    input.value = currentValue + 1;
                }
            } else if (action === 'diminuir') {
                if (currentValue > 1) {
                    input.value = currentValue - 1;
                }
            }
            
            // Adicionar efeito visual
            input.style.transform = 'scale(1.1)';
            setTimeout(() => {
                input.style.transform = 'scale(1)';
            }, 150);
        });
    });
    
    // Validação dos inputs de quantidade
    document.querySelectorAll('.quantidade-input').forEach(input => {
        input.addEventListener('change', function() {
            const min = parseInt(this.getAttribute('min')) || 1;
            const max = parseInt(this.getAttribute('max')) || 999;
            let value = parseInt(this.value);
            
            if (isNaN(value) || value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
                mostrarToast('Quantidade máxima: ' + max, 'warning');
            }
        });
        
        // Prevenir caracteres não numéricos
        input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
                e.preventDefault();
            }
        });
    });
    
    // Adicionar ao carrinho
    document.querySelectorAll('.btn-adicionar-carrinho').forEach(button => {
        button.addEventListener('click', function() {
            const produtoId = this.dataset.id;
            const produtoNome = this.dataset.nome;
            const produtoPreco = this.dataset.preco;
            const quantidadeInput = document.querySelector(`input[data-produto="${produtoId}"]`);
            const quantidade = parseInt(quantidadeInput.value);
            
            if (quantidade <= 0) {
                mostrarToast('Quantidade deve ser maior que zero', 'danger');
                return;
            }
            
            // Mostrar loading
            mostrarLoading(true);
            
            // Desabilitar botão temporariamente
            this.disabled = true;
            const originalText = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adicionando...';
            
            // Enviar dados via AJAX
            const formData = new FormData();
            formData.append('action', 'adicionar');
            formData.append('id', produtoId);
            formData.append('nome', produtoNome);
            formData.append('preco', produtoPreco);
            formData.append('quantidade', quantidade);
            
            fetch('carrinho.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar contador do carrinho
                    contadorCarrinho.textContent = data.itens;
                    
                    // Animar contador
                    contadorCarrinho.style.transform = 'scale(1.5)';
                    setTimeout(() => {
                        contadorCarrinho.style.transform = 'scale(1)';
                    }, 200);
                    
                    // Resetar quantidade para 1
                    quantidadeInput.value = 1;
                    
                    // Mostrar toast de sucesso
                    mostrarToast(`${produtoNome} adicionado ao carrinho!`, 'success');
                    
                    // Efeito visual no botão
                    this.style.backgroundColor = '#28a745';
                    this.innerHTML = '<i class="bi bi-check-lg me-2"></i>Adicionado!';
                    
                    setTimeout(() => {
                        this.style.backgroundColor = '';
                        this.innerHTML = originalText;
                    }, 1500);
                } else {
                    mostrarToast('Erro ao adicionar produto', 'danger');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mostrarToast('Erro de conexão', 'danger');
            })
            .finally(() => {
                // Ocultar loading e reabilitar botão
                mostrarLoading(false);
                this.disabled = false;
                
                // Restaurar texto original se ainda não foi alterado
                if (this.innerHTML.includes('Adicionando')) {
                    this.innerHTML = originalText;
                }
            });
        });
    });
    
    // Função para mostrar loading
    function mostrarLoading(show) {
        if (loadingSpinner) {
            if (show) {
                loadingSpinner.classList.remove('d-none');
            } else {
                loadingSpinner.classList.add('d-none');
            }
        }
    }
    
    // Função para mostrar toast
    function mostrarToast(message, type = 'success') {
        if (toastMessage && toast) {
            toastMessage.textContent = message;
            
            // Alterar cor do toast baseado no tipo
            const toastElement = document.getElementById('toast-carrinho');
            toastElement.className = `toast show`;
            
            const toastHeader = toastElement.querySelector('.toast-header');
            const icon = toastHeader.querySelector('i');
            
            // Resetar classes
            icon.className = 'me-2';
            toastHeader.className = 'toast-header';
            
            switch (type) {
                case 'success':
                    icon.classList.add('bi', 'bi-check-circle-fill', 'text-success');
                    break;
                case 'warning':
                    icon.classList.add('bi', 'bi-exclamation-triangle-fill', 'text-warning');
                    break;
                case 'danger':
                    icon.classList.add('bi', 'bi-x-circle-fill', 'text-danger');
                    break;
                default:
                    icon.classList.add('bi', 'bi-info-circle-fill', 'text-info');
            }
            
            toast.show();
        }
    }
    
    // Validação do formulário de finalização
    const formFinalizar = document.getElementById('form-finalizar');
    if (formFinalizar) {
        formFinalizar.addEventListener('submit', function(e) {
            const nome = document.getElementById('nome').value.trim();
            const endereco = document.getElementById('endereco').value.trim();
            const pagamento = document.getElementById('pagamento').value;
            
            if (!nome || !endereco || !pagamento) {
                e.preventDefault();
                mostrarToast('Preencha todos os campos obrigatórios', 'danger');
                return false;
            }
            
            // Mostrar loading durante envio
            mostrarLoading(true);
            
            // Desabilitar botão submit
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        });
    }
    
    // Smooth scroll para produtos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Animação de entrada dos cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observar cards de produtos
    document.querySelectorAll('.produto-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
    
    // Auto-hide toast após 5 segundos
    document.getElementById('toast-carrinho').addEventListener('shown.bs.toast', function() {
        setTimeout(() => {
            if (toast._element.classList.contains('show')) {
                toast.hide();
            }
        }, 5000);
    });
    
    // Formatação de preços em tempo real (para formulários admin)
    document.querySelectorAll('input[name="preco"]').forEach(input => {
        input.addEventListener('blur', function() {
            const value = parseFloat(this.value);
            if (!isNaN(value)) {
                this.value = value.toFixed(2);
            }
        });
    });
    
    // Confirmação antes de sair da página com formulário preenchido
    let formChanged = false;
    document.querySelectorAll('form input, form textarea, form select').forEach(field => {
        field.addEventListener('change', function() {
            formChanged = true;
        });
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    
    // Marcar formulário como salvo ao submeter
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            formChanged = false;
        });
    });
    
    console.log('Sistema de Catálogo de Produtos carregado com sucesso!');
});

// Função global para atualizar contador do carrinho (usada por outras páginas)
function atualizarContadorCarrinho(quantidade) {
    const contador = document.getElementById('contador-carrinho');
    if (contador) {
        contador.textContent = quantidade;
        
        // Animação
        contador.style.transform = 'scale(1.5)';
        setTimeout(() => {
            contador.style.transform = 'scale(1)';
        }, 200);
    }
}

// Função para formatar preço
function formatarPreco(valor) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(valor);
}

// Service Worker para cache (opcional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        // Registrar service worker para cache offline (implementação futura)
        console.log('Service Worker ready for implementation');
    });
}