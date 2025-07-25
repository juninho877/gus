<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

verificarLogin();

$pdo = conectarBD();
$erro = '';
$sucesso = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'criar':
            $nome = sanitizar($_POST['nome']);
            $preco = (float)$_POST['preco'];
            $categoria = sanitizar($_POST['categoria']);
            $estoque = (int)$_POST['estoque'];
            
            if (empty($nome) || $preco <= 0) {
                $erro = 'Nome e preço são obrigatórios';
            } else {
                try {
                    $imagem = '';
                    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                        $upload = uploadImagem($_FILES['imagem']);
                        if ($upload['success']) {
                            $imagem = $upload['filename'];
                        } else {
                            $erro = $upload['message'];
                        }
                    }
                    
                    if (!$erro) {
                        $stmt = $pdo->prepare("INSERT INTO produtos (nome, preco, imagem, categoria, estoque) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$nome, $preco, $imagem, $categoria, $estoque]);
                        $sucesso = 'Produto criado com sucesso!';
                    }
                } catch(PDOException $e) {
                    $erro = 'Erro ao criar produto: ' . $e->getMessage();
                }
            }
            break;
            
        case 'editar':
            $id = (int)$_POST['id'];
            $nome = sanitizar($_POST['nome']);
            $preco = (float)$_POST['preco'];
            $categoria = sanitizar($_POST['categoria']);
            $estoque = (int)$_POST['estoque'];
            $ativo = isset($_POST['ativo']) ? 1 : 0;
            
            if (empty($nome) || $preco <= 0) {
                $erro = 'Nome e preço são obrigatórios';
            } else {
                try {
                    $imagem_query = '';
                    $params = [$nome, $preco, $categoria, $estoque, $ativo];
                    
                    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                        $upload = uploadImagem($_FILES['imagem']);
                        if ($upload['success']) {
                            $imagem_query = ', imagem = ?';
                            $params[] = $upload['filename'];
                        } else {
                            $erro = $upload['message'];
                        }
                    }
                    
                    if (!$erro) {
                        $params[] = $id;
                        $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, preco = ?, categoria = ?, estoque = ?, ativo = ?" . $imagem_query . " WHERE id = ?");
                        $stmt->execute($params);
                        $sucesso = 'Produto atualizado com sucesso!';
                    }
                } catch(PDOException $e) {
                    $erro = 'Erro ao atualizar produto: ' . $e->getMessage();
                }
            }
            break;
            
        case 'excluir':
            $id = (int)$_POST['id'];
            try {
                $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
                $stmt->execute([$id]);
                $sucesso = 'Produto excluído com sucesso!';
            } catch(PDOException $e) {
                $erro = 'Erro ao excluir produto: ' . $e->getMessage();
            }
            break;
    }
}

// Buscar produtos
$filtro_estoque = isset($_GET['estoque_baixo']) ? ' WHERE estoque <= 5' : '';
$stmt = $pdo->query("SELECT * FROM produtos" . $filtro_estoque . " ORDER BY nome ASC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Produto para edição
$produto_edit = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$edit_id]);
    $produto_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - <?php echo SITE_NAME; ?></title>
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
                            <a class="nav-link" href="dashboard.php">
                                <i class="bi bi-house-door me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="produtos.php">
                                <i class="bi bi-box me-2"></i>Produtos
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <?php echo $produto_edit ? 'Editar Produto' : 'Produtos'; ?>
                    </h1>
                    
                    <?php if (!$produto_edit): ?>
                        <div class="btn-toolbar mb-2 mb-md-0">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProduto">
                                <i class="bi bi-plus-circle me-1"></i>Novo Produto
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($erro): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <?php echo $erro; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($sucesso): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>
                        <?php echo $sucesso; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($produto_edit): ?>
                    <!-- Formulário de Edição -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Editar Produto: <?php echo htmlspecialchars($produto_edit['nome']); ?></h5>
                            <a href="produtos.php" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x me-1"></i>Cancelar
                            </a>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="editar">
                                <input type="hidden" name="id" value="<?php echo $produto_edit['id']; ?>">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Nome do Produto *</label>
                                            <input type="text" class="form-control" name="nome" 
                                                   value="<?php echo htmlspecialchars($produto_edit['nome']); ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Preço *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">R$</span>
                                                <input type="number" class="form-control" name="preco" 
                                                       value="<?php echo $produto_edit['preco']; ?>" 
                                                       step="0.01" min="0" required>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Categoria</label>
                                            <input type="text" class="form-control" name="categoria" 
                                                   value="<?php echo htmlspecialchars($produto_edit['categoria']); ?>">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Estoque</label>
                                            <input type="number" class="form-control" name="estoque" 
                                                   value="<?php echo $produto_edit['estoque']; ?>" min="0">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Imagem</label>
                                            <input type="file" class="form-control" name="imagem" accept="image/*">
                                            <?php if ($produto_edit['imagem']): ?>
                                                <small class="text-muted">Imagem atual: <?php echo $produto_edit['imagem']; ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="ativo" 
                                                       <?php echo $produto_edit['ativo'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label">Produto Ativo</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-lg me-2"></i>Salvar Alterações
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Lista de Produtos -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            Lista de Produtos 
                            <?php if (isset($_GET['estoque_baixo'])): ?>
                                <span class="badge bg-warning">Estoque Baixo</span>
                            <?php endif; ?>
                        </h5>
                        
                        <?php if (isset($_GET['estoque_baixo'])): ?>
                            <a href="produtos.php" class="btn btn-outline-primary btn-sm">
                                Ver Todos
                            </a>
                        <?php else: ?>
                            <a href="produtos.php?estoque_baixo=1" class="btn btn-outline-warning btn-sm">
                                <i class="bi bi-exclamation-triangle me-1"></i>Estoque Baixo
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Preço</th>
                                    <th>Estoque</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($produtos)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="bi bi-inbox text-muted mb-2" style="font-size: 2rem;"></i>
                                            <br>Nenhum produto encontrado
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($produtos as $produto): ?>
                                        <tr>
                                            <td><?php echo $produto['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($produto['nome']); ?></strong>
                                                <?php if ($produto['imagem']): ?>
                                                    <br><small class="text-muted">
                                                        <i class="bi bi-image me-1"></i>Com imagem
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo $produto['categoria'] ? htmlspecialchars($produto['categoria']) : '-'; ?>
                                            </td>
                                            <td><?php echo formatarPreco($produto['preco']); ?></td>
                                            <td>
                                                <span class="badge <?php echo ($produto['estoque'] <= 5) ? 'bg-warning' : 'bg-success'; ?>">
                                                    <?php echo $produto['estoque']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $produto['ativo'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo $produto['ativo'] ? 'Ativo' : 'Inativo'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="produtos.php?edit=<?php echo $produto['id']; ?>" 
                                                       class="btn btn-outline-primary btn-sm" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger btn-sm" 
                                                            title="Excluir"
                                                            onclick="confirmarExclusao(<?php echo $produto['id']; ?>, '<?php echo addslashes($produto['nome']); ?>')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Novo Produto -->
    <div class="modal fade" id="modalProduto" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="criar">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nome do Produto *</label>
                                    <input type="text" class="form-control" name="nome" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Preço *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control" name="preco" 
                                               step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Categoria</label>
                                    <input type="text" class="form-control" name="categoria">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Estoque</label>
                                    <input type="number" class="form-control" name="estoque" value="0" min="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Imagem</label>
                            <input type="file" class="form-control" name="imagem" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Criar Produto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form para exclusão -->
    <form id="formExcluir" method="POST" style="display: none;">
        <input type="hidden" name="action" value="excluir">
        <input type="hidden" name="id" id="idExcluir">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarExclusao(id, nome) {
            if (confirm('Tem certeza que deseja excluir o produto "' + nome + '"?')) {
                document.getElementById('idExcluir').value = id;
                document.getElementById('formExcluir').submit();
            }
        }
    </script>
</body>
</html>