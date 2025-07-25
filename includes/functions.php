<?php
/**
 * Funções auxiliares do sistema
 */

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Formatar preço para exibição
 */
function formatarPreco($preco) {
    return 'R$ ' . number_format($preco, 2, ',', '.');
}

/**
 * Sanitizar dados de entrada
 */
function sanitizar($dados) {
    $dados = trim($dados);
    $dados = stripslashes($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}

/**
 * Verificar se usuário está logado (admin)
 */
function verificarLogin() {
    if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Contar itens no carrinho
 */
function contarItensCarrinho() {
    if (!isset($_SESSION['carrinho'])) {
        return 0;
    }
    
    $total = 0;
    foreach ($_SESSION['carrinho'] as $item) {
        $total += $item['quantidade'];
    }
    
    return $total;
}

/**
 * Calcular total do carrinho
 */
function calcularTotalCarrinho() {
    if (!isset($_SESSION['carrinho'])) {
        return 0;
    }
    
    $total = 0;
    foreach ($_SESSION['carrinho'] as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }
    
    return $total;
}

/**
 * Adicionar produto ao carrinho
 */
function adicionarAoCarrinho($id, $nome, $preco, $quantidade = 1) {
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = array();
    }
    
    // Verificar se o produto já existe no carrinho
    if (isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id]['quantidade'] += $quantidade;
    } else {
        $_SESSION['carrinho'][$id] = array(
            'id' => $id,
            'nome' => $nome,
            'preco' => $preco,
            'quantidade' => $quantidade
        );
    }
    
    return true;
}

/**
 * Remover produto do carrinho
 */
function removerDoCarrinho($id) {
    if (isset($_SESSION['carrinho'][$id])) {
        unset($_SESSION['carrinho'][$id]);
        return true;
    }
    return false;
}

/**
 * Limpar carrinho
 */
function limparCarrinho() {
    unset($_SESSION['carrinho']);
    return true;
}

/**
 * Upload de imagem
 */
function uploadImagem($arquivo, $pasta = 'assets/images/produtos/') {
    $extensoes_permitidas = array('jpg', 'jpeg', 'png', 'gif');
    $tamanho_maximo = MAX_FILE_SIZE; // 5MB
    
    // Verificar se foi enviado um arquivo
    if (!isset($arquivo) || $arquivo['error'] !== UPLOAD_ERR_OK) {
        return array('success' => false, 'message' => 'Erro no upload do arquivo');
    }
    
    // Verificar tamanho
    if ($arquivo['size'] > $tamanho_maximo) {
        return array('success' => false, 'message' => 'Arquivo muito grande. Máximo 5MB');
    }
    
    // Verificar extensão
    $info = pathinfo($arquivo['name']);
    $extensao = strtolower($info['extension']);
    
    if (!in_array($extensao, $extensoes_permitidas)) {
        return array('success' => false, 'message' => 'Extensão não permitida');
    }
    
    // Gerar nome único
    $nome_arquivo = uniqid() . '.' . $extensao;
    $caminho_destino = $pasta . $nome_arquivo;
    
    // Criar pasta se não existir
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }
    
    // Mover arquivo
    if (move_uploaded_file($arquivo['tmp_name'], $caminho_destino)) {
        return array('success' => true, 'filename' => $nome_arquivo);
    } else {
        return array('success' => false, 'message' => 'Erro ao salvar arquivo');
    }
}

/**
 * Gerar mensagem do WhatsApp
 */
function gerarMensagemWhatsApp($carrinho, $dados_cliente) {
    $mensagem = "🛒 *NOVO PEDIDO - " . SITE_NAME . "*\n\n";
    $mensagem .= "👤 *Cliente:* " . $dados_cliente['nome'] . "\n";
    $mensagem .= "📍 *Endereço:* " . $dados_cliente['endereco'] . "\n";
    $mensagem .= "💳 *Pagamento:* " . $dados_cliente['pagamento'] . "\n\n";
    $mensagem .= "📦 *PRODUTOS:*\n";
    
    $total_geral = 0;
    foreach ($carrinho as $item) {
        $subtotal = $item['preco'] * $item['quantidade'];
        $total_geral += $subtotal;
        
        $mensagem .= "• " . $item['nome'] . "\n";
        $mensagem .= "  Qtd: " . $item['quantidade'] . " | Valor: " . formatarPreco($item['preco']) . "\n";
        $mensagem .= "  Subtotal: " . formatarPreco($subtotal) . "\n\n";
    }
    
    $mensagem .= "💰 *TOTAL GERAL: " . formatarPreco($total_geral) . "*\n\n";
    $mensagem .= "Obrigado pela preferência! 😊";
    
    return $mensagem;
}
?>