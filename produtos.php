<?php
include 'db.php';

// Processar cadastro de categoria (se enviado via AJAX)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['categoria_nome'])) {
    $categoria_nome = $conn->real_escape_string($_POST['categoria_nome']);
    $categoria_descricao = $conn->real_escape_string($_POST['categoria_descricao']);
    
    $sql = "INSERT INTO categorias (nome, descricao) VALUES ('$categoria_nome', '$categoria_descricao')";
    
    if ($conn->query($sql)) {
        $nova_categoria_id = $conn->insert_id;
        $response = [
            'success' => true,
            'message' => 'Categoria cadastrada com sucesso!',
            'categoria_id' => $nova_categoria_id,
            'categoria_nome' => $categoria_nome
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Erro ao cadastrar categoria: ' . $conn->error
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Processar exclusão de categoria (se enviado via AJAX)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_category_id'])) {
    $category_id = intval($_POST['delete_category_id']);
    
    // Verificar se a categoria está sendo usada por algum produto
    $check = $conn->query("SELECT COUNT(*) as total FROM produtos WHERE categoria_id = $category_id");
    $result = $check->fetch_assoc();
    
    if ($result['total'] > 0) {
        $response = [
            'success' => false,
            'message' => 'Esta categoria está em uso por algum produto e não pode ser excluída.'
        ];
    } else {
        $sql = "DELETE FROM categorias WHERE id = $category_id";
        if ($conn->query($sql)) {
            $response = [
                'success' => true,
                'message' => 'Categoria excluída com sucesso!'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Erro ao excluir categoria: ' . $conn->error
            ];
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Processar cadastro de produto (apenas se não for uma requisição AJAX)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nome']) && !isset($_POST['categoria_nome'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $categoria_id = !empty($_POST['categoria_id']) ? intval($_POST['categoria_id']) : NULL;
    $preco = str_replace(',', '.', $conn->real_escape_string($_POST['preco']));
    $quantidade = intval($_POST['quantidade']);
    $estoque_minimo = isset($_POST['estoque_minimo']) ? intval($_POST['estoque_minimo']) : 5;
    
    $sql = "INSERT INTO produtos (nome, descricao, categoria_id, preco, quantidade, estoque_minimo) 
            VALUES ('$nome', '$descricao', " . ($categoria_id ? $categoria_id : 'NULL') . ", '$preco', '$quantidade', '$estoque_minimo')";
    
    if ($conn->query($sql)) {
        $success_message = 'Produto cadastrado com sucesso!';
    } else {
        $error_message = 'Erro ao cadastrar produto: ' . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
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
        .product-table th {
            background-color: #f8f9fa;
        }
        .badge-category {
            background-color: #6c757d;
            color: white;
        }
        .modal-category {
            max-width: 500px;
        }
        .delete-category-btn {
            color: #dc3545;
            background: none;
            border: none;
            padding: 0;
            font-size: 0.8rem;
        }
        .delete-category-btn:hover {
            color: #bb2d3b;
        }
        .category-section {
            background: #e2e3e4;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5 pt-5">
    <h2><i class="bi bi-box-seam"></i> Cadastro de Produtos</h2>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nome" class="form-label">Nome do Produto</label>
                <input type="text" name="nome" id="nome" placeholder="Digite o nome do produto" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="categoria_id" class="form-label">Categoria</label>
                <div class="input-group">
                    <select name="categoria_id" id="categoria_id" class="form-select">
                        <option value="">Selecione uma categoria</option>
                        <?php
                        $categorias = $conn->query("SELECT * FROM categorias ORDER BY nome");
                        while ($cat = $categorias->fetch_assoc()) {
                            echo "<option value='{$cat['id']}'>{$cat['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" placeholder="Descrição do produto" class="form-control" rows="2"></textarea>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="preco" class="form-label">Preço (R$)</label>
                <input type="text" name="preco" id="preco" placeholder="0,00" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="quantidade" class="form-label">Quantidade</label>
                <input type="number" name="quantidade" id="quantidade" placeholder="Quantidade em estoque" class="form-control" min="0" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="estoque_minimo" class="form-label">Estoque Mínimo</label>
                <input type="number" name="estoque_minimo" id="estoque_minimo" placeholder="Mínimo desejado" class="form-control" min="0" value="5">
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Salvar Produto
        </button>
    </form>
    
    <!-- Seção de Categorias -->
    <div class="category-section">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5><i class="bi bi-tags"></i> Gerenciar Categorias</h5>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                    <i class="bi bi-plus-circle"></i> Nova Categoria
                </button>
                <div class="btn-group ms-2">
                    <button class="btn btn-sm btn-outline-danger dropdown-toggle" type="button" id="deleteCategoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="deleteCategoryDropdown">
                        <?php
                        $categorias = $conn->query("SELECT * FROM categorias ORDER BY nome");
                        while ($cat = $categorias->fetch_assoc()) {
                            echo '<li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-id="'.$cat['id'].'">
                                        '.$cat['nome'].'
                                        <button class="delete-category-btn" data-id="'.$cat['id'].'">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </a>
                                  </li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Produtos -->
    <h3 class="mt-4"><i class="bi bi-list-ul"></i> Lista de Produtos</h3>
    <div class="table-responsive">
        <table class="table table-striped product-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT p.*, c.nome as categoria_nome 
                                       FROM produtos p 
                                       LEFT JOIN categorias c ON p.categoria_id = c.id 
                                       ORDER BY p.nome");
                
                while ($row = $result->fetch_assoc()) {
                    $estoque_class = ($row['quantidade'] < ($row['estoque_minimo'] ?? 5)) ? 'text-danger fw-bold' : '';
                    
                    echo "<tr>
                            <td>{$row['nome']}</td>
                            <td>" . ($row['descricao'] ? $row['descricao'] : '-') . "</td>
                            <td>" . 
                                ($row['categoria_nome'] ? 
                                 '<span class="badge badge-category">'.$row['categoria_nome'].'</span>' : 
                                 '<span class="text-muted">Sem categoria</span>') . 
                            "</td>
                            <td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>
                            <td class='{$estoque_class}'>
                                {$row['quantidade']} uni." . 
                                (($row['quantidade'] < ($row['estoque_minimo'] ?? 5)) ? 
                                 '<br><small class="text-danger">Mín: '.($row['estoque_minimo'] ?? 5).'</small>' : '') . 
                            "</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para adicionar nova categoria -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-category">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel"><i class="bi bi-tag"></i> Nova Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <div class="mb-3">
                        <label for="categoria_nome" class="form-label">Nome da Categoria</label>
                        <input type="text" class="form-control" id="categoria_nome" name="categoria_nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoria_descricao" class="form-label">Descrição (Opcional)</label>
                        <textarea class="form-control" id="categoria_descricao" name="categoria_descricao" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveCategoryBtn">Salvar Categoria</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmação -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                <h3 class="mt-3" id="successMessage">Operação realizada com sucesso!</h3>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="confirmSuccessBtn">OK</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Formatação do preço
    document.getElementById('preco').addEventListener('blur', function() {
        let value = this.value.replace('.', '').replace(',', '.');
        if(!isNaN(value) && value !== '') {
            this.value = parseFloat(value).toFixed(2).replace('.', ',');
        }
    });

    // Adicionar nova categoria via AJAX
    $('#saveCategoryBtn').click(function() {
        const nome = $('#categoria_nome').val().trim();
        const descricao = $('#categoria_descricao').val().trim();
        
        if (!nome) {
            alert('Por favor, informe o nome da categoria');
            return;
        }
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            dataType: 'json',
            data: {
                categoria_nome: nome,
                categoria_descricao: descricao
            },
            success: function(data) {
                if (data.success) {
                    // Adiciona a nova categoria ao select
                    const select = $('#categoria_id');
                    select.append($('<option>', {
                        value: data.categoria_id,
                        text: data.categoria_nome,
                        selected: true
                    }));

                    // Fecha o modal e limpa o formulário
                    $('#categoryModal').modal('hide');
                    $('#categoryForm')[0].reset();
                    
                    // Exibe modal de sucesso
                    $('#successMessage').text(data.message);
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                } else {
                    alert(data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro detalhado:", xhr.responseText);
                alert('Erro ao comunicar com o servidor. Verifique o console para detalhes.');
            }
        });
    });

    // Excluir categoria via AJAX
    $(document).on('click', '.delete-category-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const categoryId = $(this).data('id');
        const categoryName = $(this).closest('a').text().trim();
        
        if (confirm(`Tem certeza que deseja excluir a categoria "${categoryName}"?`)) {
            $.ajax({
                url: window.location.href,
                type: 'POST',
                dataType: 'json',
                data: {
                    delete_category_id: categoryId
                },
                success: function(data) {
                    if (data.success) {
                        // Remove a categoria do select
                        $(`#categoria_id option[value="${categoryId}"]`).remove();
                        
                        // Exibe modal de sucesso
                        $('#successMessage').text(data.message);
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                        
                        // Atualiza o dropdown de exclusão
                        updateDeleteDropdown();
                    } else {
                        alert(data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erro detalhado:", xhr.responseText);
                    alert('Erro ao comunicar com o servidor. Verifique o console para detalhes.');
                }
            });
        }
    });

    // Fechar modal de sucesso
    $('#confirmSuccessBtn').click(function() {
        // Não recarrega mais a página automaticamente
        // A atualização é feita via JavaScript
    });

    // Função para atualizar o dropdown de exclusão via AJAX
    function updateDeleteDropdown() {
        $.ajax({
            url: window.location.href,
            type: 'GET',
            success: function(data) {
                // Extrai apenas a parte do dropdown do HTML retornado
                const dropdownContent = $(data).find('.dropdown-menu').html();
                $('.dropdown-menu').html(dropdownContent);
            },
            error: function() {
                console.log('Erro ao atualizar dropdown de categorias');
            }
        });
    }
</script>
</body>
</html>