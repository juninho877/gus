<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

verificarLogin();

// Estatísticas básicas
try {
    $pdo = conectarBD();
    
    // Total de produtos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produtos");
    $total_produtos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Produtos ativos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produtos WHERE ativo = 1");
    $produtos_ativos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Produtos com estoque baixo
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produtos WHERE estoque <= 5 AND ativo = 1");
    $estoque_baixo = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
} catch(PDOException $e) {
    $erro = "Erro ao buscar estatísticas: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Header Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-speedometer2 me-2"></i>Painel Admin
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?php echo $_SESSION['admin_usuario']; ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../index.php" target="_blank">
                            <i class="bi bi-globe me-2"></i>Ver Site
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Sair
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-white sidebar border-end vh-100">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="bi bi-house-door me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="produtos.php">
                                <i class="bi bi-box me-2"></i>Produtos
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="produtos.php" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Novo Produto
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo $erro; ?>
                    </div>
                <?php endif; ?>

                <!-- Estatísticas -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title"><?php echo $total_produtos ?? 0; ?></h4>
                                        <p class="card-text">Total de Produtos</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-box" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title"><?php echo $produtos_ativos ?? 0; ?></h4>
                                        <p class="card-text">Produtos Ativos</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title"><?php echo $estoque_baixo ?? 0; ?></h4>
                                        <p class="card-text">Estoque Baixo</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Ações Rápidas</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <a href="produtos.php" class="btn btn-outline-primary w-100 p-3">
                                            <i class="bi bi-plus-circle me-2"></i>
                                            <strong>Adicionar Produto</strong>
                                            <br>
                                            <small class="text-muted">Cadastrar novo produto no catálogo</small>
                                        </a>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <a href="produtos.php?estoque_baixo=1" class="btn btn-outline-warning w-100 p-3">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            <strong>Estoque Baixo</strong>
                                            <br>
                                            <small class="text-muted">Ver produtos com estoque baixo</small>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>