<?php
$page_title = 'Carrinho de Compras';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Processar ações do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'adicionar':
                $id = (int)$_POST['id'];
                $nome = sanitizar($_POST['nome']);
                $preco = (float)$_POST['preco'];
                $quantidade = (int)$_POST['quantidade'];
                
                adicionarAoCarrinho($id, $nome, $preco, $quantidade);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'itens' => contarItensCarrinho()]);
                exit;
                break;
                
            case 'remover':
                $id = (int)$_POST['id'];
                removerDoCarrinho($id);
                header('Location: carrinho.php');
                exit;
                break;
                
            case 'limpar':
                limparCarrinho();
                header('Location: carrinho.php');
                exit;
                break;
        }
    }
}

$carrinho = isset($_SESSION['carrinho']) ? $_SESSION['carrinho'] : [];
$total_geral = calcularTotalCarrinho();

require_once 'includes/header.php';
?>

<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">
                <i class="bi bi-cart3 me-2"></i>Carrinho de Compras
            </h2>
            
            <?php if (empty($carrinho)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-cart-x text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">Seu carrinho está vazio</h4>
                    <p class="text-muted mb-4">Adicione alguns produtos para continuar</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-2"></i>Continuar Comprando
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Itens do Carrinho</h5>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja limpar o carrinho?')">
                                    <input type="hidden" name="action" value="limpar">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash me-1"></i>Limpar Carrinho
                                    </button>
                                </form>
                            </div>
                            <div class="card-body p-0">
                                <?php foreach ($carrinho as $item): ?>
                                    <div class="border-bottom p-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($item['nome']); ?></h6>
                                                <small class="text-muted">Preço unitário: <?php echo formatarPreco($item['preco']); ?></small>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <span class="badge bg-primary">Qtd: <?php echo $item['quantidade']; ?></span>
                                            </div>
                                            <div class="col-md-3 text-end">
                                                <span class="fw-bold text-primary">
                                                    <?php echo formatarPreco($item['preco'] * $item['quantidade']); ?>
                                                </span>
                                            </div>
                                            <div class="col-md-1">
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="action" value="remover">
                                                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Remover item">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="index.php" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-2"></i>Continuar Comprando
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Resumo do Pedido</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span><?php echo formatarPreco($total_geral); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Frete:</span>
                                    <span class="text-success">A combinar</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total:</strong>
                                    <strong class="text-primary"><?php echo formatarPreco($total_geral); ?></strong>
                                </div>
                                
                                <a href="finalizar.php" class="btn btn-success w-100 mb-2">
                                    <i class="bi bi-whatsapp me-2"></i>Finalizar via WhatsApp
                                </a>
                                
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Você será redirecionado para o WhatsApp para confirmar o pedido
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>