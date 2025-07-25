<?php
$page_title = 'Catálogo';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Conectar ao banco
$pdo = conectarBD();

// Buscar produtos ativos
try {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE ativo = 1 ORDER BY nome ASC");
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $erro = "Erro ao buscar produtos: " . $e->getMessage();
    $produtos = [];
}

require_once 'includes/header.php';
?>

<div class="container my-4">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="jumbotron bg-light p-5 rounded text-center">
                <h1 class="display-4 fw-bold text-primary">
                    <i class="bi bi-droplet-fill me-3"></i>
                    Produtos de Limpeza
                </h1>
                <p class="lead">Encontre os melhores produtos para deixar sua casa sempre limpa e perfumada!</p>
                <a href="#produtos" class="btn btn-primary btn-lg">
                    <i class="bi bi-cart-plus me-2"></i>Ver Produtos
                </a>
            </div>
        </div>
    </div>

    <!-- Produtos -->
    <div id="produtos">
        <h2 class="mb-4">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>Nossos Produtos
        </h2>

        <?php if (isset($erro)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <?php if (empty($produtos)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        Nenhum produto encontrado.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($produtos as $produto): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 shadow-sm produto-card">
                            <div class="position-relative">
                                <?php if ($produto['imagem'] && file_exists('assets/images/produtos/' . $produto['imagem'])): ?>
                                    <img src="assets/images/produtos/<?php echo htmlspecialchars($produto['imagem']); ?>" 
                                         class="card-img-top" 
                                         alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                                         style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($produto['estoque'] <= 5): ?>
                                    <span class="badge bg-warning position-absolute top-0 end-0 m-2">
                                        Últimas unidades
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title fw-bold mb-2">
                                    <?php echo htmlspecialchars($produto['nome']); ?>
                                </h6>
                                
                                <?php if ($produto['categoria']): ?>
                                    <small class="text-muted mb-2">
                                        <i class="bi bi-tag me-1"></i>
                                        <?php echo htmlspecialchars($produto['categoria']); ?>
                                    </small>
                                <?php endif; ?>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="h5 text-primary mb-0 fw-bold">
                                            <?php echo formatarPreco($produto['preco']); ?>
                                        </span>
                                        <small class="text-muted">
                                            Estoque: <?php echo $produto['estoque']; ?>
                                        </small>
                                    </div>
                                    
                                    <div class="input-group mb-3">
                                        <button class="btn btn-outline-secondary btn-quantidade" 
                                                type="button" 
                                                data-action="diminuir" 
                                                data-produto="<?php echo $produto['id']; ?>">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input type="number" 
                                               class="form-control text-center quantidade-input" 
                                               value="1" 
                                               min="1" 
                                               max="<?php echo $produto['estoque']; ?>"
                                               data-produto="<?php echo $produto['id']; ?>">
                                        <button class="btn btn-outline-secondary btn-quantidade" 
                                                type="button" 
                                                data-action="aumentar" 
                                                data-produto="<?php echo $produto['id']; ?>">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                    
                                    <button class="btn btn-primary w-100 btn-adicionar-carrinho" 
                                            data-id="<?php echo $produto['id']; ?>"
                                            data-nome="<?php echo htmlspecialchars($produto['nome']); ?>"
                                            data-preco="<?php echo $produto['preco']; ?>"
                                            <?php echo $produto['estoque'] <= 0 ? 'disabled' : ''; ?>>
                                        <i class="bi bi-cart-plus me-2"></i>
                                        <?php echo $produto['estoque'] <= 0 ? 'Indisponível' : 'Adicionar'; ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loading-spinner" class="position-fixed top-50 start-50 translate-middle d-none">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>