<?php
include 'db.php';
session_start();

// Funções para o estoque
function getProdutos($conn) {
    $sql = "SELECT p.*, c.nome as categoria_nome,
                   p.quantidade as estoque_total
            FROM produtos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            ORDER BY p.nome";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getCategorias($conn) {
    $sql = "SELECT * FROM categorias ORDER BY nome";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Processar formulários
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['adicionar_produto'])) {
        // Cadastrar novo produto
        $nome = mysqli_real_escape_string($conn, $_POST['nome']);
        $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
        $categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : NULL;
        $preco = floatval(str_replace(',', '.', $_POST['preco']));
        $quantidade = intval($_POST['quantidade']);
        $estoque_minimo = intval($_POST['estoque_minimo']);
        
        $sql = "INSERT INTO produtos (nome, descricao, categoria_id, preco, quantidade, estoque_minimo) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssidii", $nome, $descricao, $categoria_id, $preco, $quantidade, $estoque_minimo);
        mysqli_stmt_execute($stmt);
        
        $_SESSION['mensagem'] = "Produto cadastrado com sucesso!";
        header("Location: estoque.php");
        exit();
    }
    elseif(isset($_POST['atualizar_produto'])) {
        // Atualizar produto
        $id = intval($_POST['id']);
        $nome = mysqli_real_escape_string($conn, $_POST['nome']);
        $descricao = mysqli_real_escape_string($conn, $_POST['descricao']);
        $categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : NULL;
        $preco = floatval(str_replace(',', '.', $_POST['preco']));
        $quantidade = intval($_POST['quantidade']);
        $estoque_minimo = intval($_POST['estoque_minimo']);
        
        $sql = "UPDATE produtos SET 
                nome = ?, 
                descricao = ?, 
                categoria_id = ?, 
                preco = ?, 
                quantidade = ?, 
                estoque_minimo = ?
                WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssidiii", $nome, $descricao, $categoria_id, $preco, $quantidade, $estoque_minimo, $id);
        mysqli_stmt_execute($stmt);
        
        $_SESSION['mensagem'] = "Produto atualizado com sucesso!";
        header("Location: estoque.php");
        exit();
    }
    elseif(isset($_POST['excluir_produto'])) {
        // Excluir produto
        $id = intval($_POST['id']);
        
        $sql = "DELETE FROM produtos WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        
        $_SESSION['mensagem'] = "Produto excluído com sucesso!";
        header("Location: estoque.php");
        exit();
    }
    elseif(isset($_POST['adicionar_categoria'])) {
        // Adicionar nova categoria
        $nome = mysqli_real_escape_string($conn, $_POST['nome_categoria']);
        $descricao = mysqli_real_escape_string($conn, $_POST['descricao_categoria']);
        
        $sql = "INSERT INTO categorias (nome, descricao) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $nome, $descricao);
        mysqli_stmt_execute($stmt);
        
        $_SESSION['mensagem'] = "Categoria adicionada com sucesso!";
        header("Location: estoque.php");
        exit();
    }
}

// Obter dados para exibição
$produtos = getProdutos($conn);
$categorias = getCategorias($conn);
$produto_selecionado = null;

if(isset($_GET['produto_id'])) {
    $produto_id = intval($_GET['produto_id']);
    foreach($produtos as $produto) {
        if($produto['id'] == $produto_id) {
            $produto_selecionado = $produto;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>ERP Farmácia - Controle de Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .search-box {
            position: relative;
        }
        .search-box i {
            position: absolute;
            left: 10px;
            top: 10px;
            color: #6c757d;
        }
        .search-box input {
            padding-left: 35px;
        }
        .badge-estoque {
            font-size: 0.85em;
        }
        .btn-action {
            width: 30px;
            height: 30px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .estoque-baixo {
            background-color: #fff3cd;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>
 .navbar {
            height: 50px;
        }
        .navbar-nav .nav-link {
            font-size: 16px;
            font-weight: 600;
            color: #000000ff !important;
            margin-left: 20px;
            letter-spacing: 0.5px;
        }
        .navbar-nav .nav-link:hover {
            color: #ffffffff !important;
        }

<div class="container-fluid mt-3">
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Início</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Estoque</li>
                </ol>
            </nav>
            <h2 class="mb-0">Controle de Estoque</h2>
        </div>
    </div>

    <?php if(isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?= strpos($_SESSION['mensagem'], 'sucesso') !== false ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['mensagem'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <div class="row">
        <!-- Lista de Produtos -->
        <div class="col-md-<?= $produto_selecionado ? '6' : '12' ?>">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Produtos em Estoque</h5>
                    <div>
                        <button class="btn btn-sm btn-light me-2" data-bs-toggle="modal" data-bs-target="#modalAdicionarCategoria">
                            <i class="bi bi-tag"></i> Nova Categoria
                        </button>
                        <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#modalAdicionarProduto">
                            <i class="bi bi-plus-lg"></i> Novo Produto
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="search-box mb-3">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" id="buscaProduto" placeholder="Buscar produto...">
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Categoria</th>
                                    <th>Preço</th>
                                    <th>Estoque</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($produtos as $produto): 
                                    $estoque = $produto['quantidade'] ?? 0;
                                    $estoque_minimo = $produto['estoque_minimo'] ?? 0;
                                    $estoque_baixo = $estoque < $estoque_minimo;
                                ?>
                                <tr class="<?= $estoque_baixo ? 'estoque-baixo' : '' ?> <?= $produto_selecionado && $produto_selecionado['id'] == $produto['id'] ? 'table-active' : '' ?>">
                                    <td>
                                        <strong><?= htmlspecialchars($produto['nome']) ?></strong>
                                        <?php if(!empty($produto['descricao'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($produto['descricao']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $produto['categoria_nome'] ?? 'Sem categoria' ?></td>
                                    <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?= $estoque_baixo ? 'bg-warning' : 'bg-success' ?> badge-estoque">
                                            <?= $estoque ?> uni.
                                        </span>
                                        <?php if($estoque_baixo): ?>
                                            <br><small class="text-danger">Mín: <?= $estoque_minimo ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="estoque.php?produto_id=<?= $produto['id'] ?>" class="btn btn-sm btn-primary btn-action" title="Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-info btn-action" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarProduto" 
                                                data-id="<?= $produto['id'] ?>" 
                                                data-nome="<?= htmlspecialchars($produto['nome']) ?>" 
                                                data-descricao="<?= htmlspecialchars($produto['descricao']) ?>" 
                                                data-categoria="<?= $produto['categoria_id'] ?>" 
                                                data-preco="<?= number_format($produto['preco'], 2, ',', '.') ?>" 
                                                data-quantidade="<?= $produto['quantidade'] ?>" 
                                                data-estoque-minimo="<?= $produto['estoque_minimo'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-action" title="Excluir" data-bs-toggle="modal" data-bs-target="#modalExcluirProduto" 
                                                data-id="<?= $produto['id'] ?>" 
                                                data-nome="<?= htmlspecialchars($produto['nome']) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalhes do Produto -->
        <?php if($produto_selecionado): ?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Detalhes: <?= htmlspecialchars($produto_selecionado['nome']) ?></h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Descrição:</strong> <?= !empty($produto_selecionado['descricao']) ? htmlspecialchars($produto_selecionado['descricao']) : 'N/A' ?></p>
                            <p><strong>Categoria:</strong> <?= $produto_selecionado['categoria_nome'] ?? 'Sem categoria' ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Preço:</strong> R$ <?= number_format($produto_selecionado['preco'], 2, ',', '.') ?></p>
                            <p><strong>Estoque Atual:</strong> <?= $produto_selecionado['quantidade'] ?> unidades</p>
                            <p><strong>Estoque Mínimo:</strong> <?= $produto_selecionado['estoque_minimo'] ?> unidades</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Adicionar Produto -->
<div class="modal fade" id="modalAdicionarProduto" tabindex="-1" aria-labelledby="modalAdicionarProdutoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAdicionarProdutoLabel">Adicionar Novo Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="categoria_id" class="form-label">Categoria</label>
                        <select class="form-select" id="categoria_id" name="categoria_id">
                            <option value="">Selecione uma categoria...</option>
                            <?php foreach($categorias as $categoria): ?>
                                <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="preco" class="form-label">Preço (R$)</label>
                            <input type="text" class="form-control" id="preco" name="preco" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quantidade" class="form-label">Quantidade em Estoque</label>
                            <input type="number" class="form-control" id="quantidade" name="quantidade" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="estoque_minimo" class="form-label">Estoque Mínimo</label>
                        <input type="number" class="form-control" id="estoque_minimo" name="estoque_minimo" min="0" value="5" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="adicionar_produto" class="btn btn-primary">Salvar Produto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Produto -->
<div class="modal fade" id="modalEditarProduto" tabindex="-1" aria-labelledby="modalEditarProdutoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="id" id="editar_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarProdutoLabel">Editar Produto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editar_nome" class="form-label">Nome do Produto</label>
                        <input type="text" class="form-control" id="editar_nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="editar_descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="editar_descricao" name="descricao" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editar_categoria_id" class="form-label">Categoria</label>
                        <select class="form-select" id="editar_categoria_id" name="categoria_id">
                            <option value="">Selecione uma categoria...</option>
                            <?php foreach($categorias as $categoria): ?>
                                <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editar_preco" class="form-label">Preço (R$)</label>
                            <input type="text" class="form-control" id="editar_preco" name="preco" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editar_quantidade" class="form-label">Quantidade em Estoque</label>
                            <input type="number" class="form-control" id="editar_quantidade" name="quantidade" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editar_estoque_minimo" class="form-label">Estoque Mínimo</label>
                        <input type="number" class="form-control" id="editar_estoque_minimo" name="estoque_minimo" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="atualizar_produto" class="btn btn-primary">Atualizar Produto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Adicionar Categoria -->
<div class="modal fade" id="modalAdicionarCategoria" tabindex="-1" aria-labelledby="modalAdicionarCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAdicionarCategoriaLabel">Adicionar Nova Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome_categoria" class="form-label">Nome da Categoria</label>
                        <input type="text" class="form-control" id="nome_categoria" name="nome_categoria" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao_categoria" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao_categoria" name="descricao_categoria" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="adicionar_categoria" class="btn btn-primary">Salvar Categoria</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Excluir Produto -->
<div class="modal fade" id="modalExcluirProduto" tabindex="-1" aria-labelledby="modalExcluirProdutoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="id" id="excluir_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalExcluirProdutoLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o produto <strong id="excluir_nome"></strong>?</p>
                    <p class="text-danger">Esta ação não pode ser desfeita!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" name="excluir_produto" class="btn btn-danger">Confirmar Exclusão</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Inicializar modal de edição
    $('#modalEditarProduto').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        
        modal.find('#editar_id').val(button.data('id'));
        modal.find('#editar_nome').val(button.data('nome'));
        modal.find('#editar_descricao').val(button.data('descricao'));
        modal.find('#editar_categoria_id').val(button.data('categoria'));
        modal.find('#editar_preco').val(button.data('preco'));
        modal.find('#editar_quantidade').val(button.data('quantidade'));
        modal.find('#editar_estoque_minimo').val(button.data('estoque-minimo'));
    });
    
    // Inicializar modal de exclusão
    $('#modalExcluirProduto').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        
        modal.find('#excluir_id').val(button.data('id'));
        modal.find('#excluir_nome').text(button.data('nome'));
    });
    
    // Busca de produtos
    $('#buscaProduto').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Formatação de preço
    $('#preco, #editar_preco').on('blur', function() {
        var value = $(this).val().replace('.', '').replace(',', '.');
        if(!isNaN(value) && value != '') {
            $(this).val(parseFloat(value).toFixed(2).replace('.', ','));
        }
    });
</script>
</body>
</html>