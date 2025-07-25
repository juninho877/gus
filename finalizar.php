<?php
$page_title = 'Finalizar Pedido';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Verificar se há itens no carrinho
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    header('Location: carrinho.php');
    exit;
}

$carrinho = $_SESSION['carrinho'];
$total_geral = calcularTotalCarrinho();
$erro = '';
$sucesso = '';

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitizar($_POST['nome'] ?? '');
    $endereco = sanitizar($_POST['endereco'] ?? '');
    $pagamento = sanitizar($_POST['pagamento'] ?? '');
    
    // Validações
    if (empty($nome)) {
        $erro = 'O nome é obrigatório';
    } elseif (empty($endereco)) {
        $erro = 'O endereço é obrigatório';
    } elseif (empty($pagamento)) {
        $erro = 'A forma de pagamento é obrigatória';
    } else {
        // Gerar mensagem para WhatsApp
        $dados_cliente = [
            'nome' => $nome,
            'endereco' => $endereco,
            'pagamento' => $pagamento
        ];
        
        $mensagem = gerarMensagemWhatsApp($carrinho, $dados_cliente);
        $mensagem_encoded = urlencode($mensagem);
        $whatsapp_url = "https://wa.me/" . WHATSAPP_NUMBER . "?text=" . $mensagem_encoded;
        
        // Limpar carrinho após finalizar
        limparCarrinho();
        
        // Redirecionar para WhatsApp
        header("Location: " . $whatsapp_url);
        exit;
    }
}

require_once 'includes/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-whatsapp me-2"></i>Finalizar Pedido
                    </h4>
                </div>
                <div class="card-body">
                    <?php if ($erro): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php echo $erro; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="form-finalizar">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Dados do Cliente</h5>
                                
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome Completo *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nome" 
                                           name="nome" 
                                           required
                                           value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="endereco" class="form-label">Endereço Completo *</label>
                                    <textarea class="form-control" 
                                              id="endereco" 
                                              name="endereco" 
                                              rows="3" 
                                              required
                                              placeholder="Rua, número, bairro, cidade..."><?php echo isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco']) : ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="pagamento" class="form-label">Forma de Pagamento *</label>
                                    <select class="form-select" id="pagamento" name="pagamento" required>
                                        <option value="">Selecione...</option>
                                        <option value="Dinheiro" <?php echo (isset($_POST['pagamento']) && $_POST['pagamento'] === 'Dinheiro') ? 'selected' : ''; ?>>Dinheiro</option>
                                        <option value="PIX" <?php echo (isset($_POST['pagamento']) && $_POST['pagamento'] === 'PIX') ? 'selected' : ''; ?>>PIX</option>
                                        <option value="Cartão de Débito" <?php echo (isset($_POST['pagamento']) && $_POST['pagamento'] === 'Cartão de Débito') ? 'selected' : ''; ?>>Cartão de Débito</option>
                                        <option value="Cartão de Crédito" <?php echo (isset($_POST['pagamento']) && $_POST['pagamento'] === 'Cartão de Crédito') ? 'selected' : ''; ?>>Cartão de Crédito</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="mb-3">Resumo do Pedido</h5>
                                
                                <div class="bg-light p-3 rounded mb-3">
                                    <?php foreach ($carrinho as $item): ?>
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>
                                                <strong><?php echo htmlspecialchars($item['nome']); ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo $item['quantidade']; ?>x <?php echo formatarPreco($item['preco']); ?>
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <strong><?php echo formatarPreco($item['preco'] * $item['quantidade']); ?></strong>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <strong>Total Geral:</strong>
                                        <strong class="text-primary"><?php echo formatarPreco($total_geral); ?></strong>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>Importante:</strong> Após clicar em "Enviar Pedido", você será redirecionado para o WhatsApp com todos os dados do pedido preenchidos.
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="carrinho.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Voltar ao Carrinho
                            </a>
                            
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-whatsapp me-2"></i>Enviar Pedido
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>