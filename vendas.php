<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #2e59d9;
            --text-color: #5a5c69;
        }
        body { background-color: #f8f9fc; padding-top: 5px; color: var(--text-color); }
        .card { border-radius: 0.35rem; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
        .card-header { background-color: var(--primary-color); color: white; font-weight: 600; }
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: var(--accent-color); border-color: var(--accent-color); }
        .product-list { max-height: 400px; overflow-y: auto; margin: 20px 0; border: 1px solid #e3e6f0; border-radius: 0.35rem; padding: 15px; background-color: white; }
        .selected-product { background-color: var(--secondary-color); border-radius: 0.25rem; padding: 15px; margin-bottom: 10px; border-left: 0.25rem solid var(--primary-color); }
        .total-container { background-color: white; padding: 20px; border-radius: 0.35rem; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1); margin-top: 20px; }
        .form-control, .form-select { padding: 0.75rem 1rem; border: 1px solid #d1d3e2; border-radius: 0.35rem; }
        .form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25); }
        .quantity-input { max-width: 80px; text-align: center; }
        .alert-fixed { position: fixed; top: 15px; right: 20px; z-index: 1050; min-width: 300px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_id = (int)$_POST['cliente_id'];
    $data_venda = $conn->real_escape_string($_POST['data_venda']);
    $status = $conn->real_escape_string($_POST['status']);
    $produtos = json_decode($_POST['produtos_json'], true);
    
    $valor_total = 0;
    foreach ($produtos as $produto) {
        $valor_total += $produto['preco'] * $produto['quantidade'];
    }

    $conn->begin_transaction();

    try {
        $sqlVenda = "INSERT INTO vendas (cliente_id, data_venda, valor_total, status) 
                     VALUES ($cliente_id, '$data_venda', $valor_total, '$status')";
        $conn->query($sqlVenda);
        $id_venda = $conn->insert_id;

        foreach ($produtos as $produto) {
            $id_produto = (int)$produto['id'];
            $quantidade = (int)$produto['quantidade'];
            $preco_unitario = (float)$produto['preco'];

            $sqlItem = "INSERT INTO itens_venda (id_venda, id_produto, quantidade, preco_unitario)
                        VALUES ($id_venda, $id_produto, $quantidade, $preco_unitario)";
            $conn->query($sqlItem);

            $sqlEstoque = "UPDATE produtos SET quantidade = quantidade - $quantidade 
                           WHERE id = $id_produto";
            $conn->query($sqlEstoque);
        }

        $conn->commit();
        
        echo '<div class="alert alert-success alert-dismissible fade show auto-dismiss alert-fixed">
                <i class="bi bi-check-circle-fill"></i> Venda registrada com sucesso! Número: '.$id_venda.'
                <a href="recibo.php?id_venda='.$id_venda.'" target="_blank" class="btn btn-sm btn-light ms-2">
                    <i class="bi bi-printer"></i> Imprimir Recibo
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
    } catch (Exception $e) {
        $conn->rollback();
        echo '<div class="alert alert-danger alert-dismissible fade show auto-dismiss alert-fixed">
                <i class="bi bi-exclamation-triangle-fill"></i> Erro ao registrar venda: '.$e->getMessage().'
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <i class="bi bi-cart-plus me-2"></i>Registrar Nova Venda
                </div>
                <div class="card-body">
                    <form id="vendaForm" method="POST">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Cliente</label>
                                <select name="cliente_id" class="form-select" required>
                                    <option value="">Selecione um cliente</option>
                                    <?php
                                    $clientes = $conn->query("SELECT * FROM clientes");
                                    while ($cliente = $clientes->fetch_assoc()) {
                                        echo "<option value='{$cliente['cliente_id']}'>{$cliente['nome_cliente']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Data da Venda</label>
                                <input type="datetime-local" class="form-control" name="data_venda" value="<?= date('Y-m-d\TH:i') ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="Pendente">Pendente</option>
                                    <option value="Pago">Pago</option>
                                    <option value="Cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="mb-3 fw-bold text-primary">
                            <i class="bi bi-box-seam me-2"></i>Produtos
                        </h5>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <select id="produtoSelect" class="form-select">
                                    <option value="">Selecione um produto</option>
                                    <?php
                                    $produtos = $conn->query("SELECT * FROM produtos WHERE quantidade > 0");
                                    while ($produto = $produtos->fetch_assoc()) {
                                        echo "<option value='{$produto['id']}' 
                                              data-preco='{$produto['preco']}' 
                                              data-estoque='{$produto['quantidade']}'
                                              data-nome='{$produto['nome']}'>
                                              {$produto['nome']} - R$ " . number_format($produto['preco'], 2, ',', '.') . " (Estoque: {$produto['quantidade']})
                                              </option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" id="quantidadeProduto" class="form-control quantity-input" min="1" value="1">
                            </div>
                            <div class="col-md-4">
                                <button type="button" id="addProduto" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle me-2"></i>Adicionar
                                </button>
                            </div>
                        </div>

                        <div class="product-list">
                            <div id="produtosSelecionados" class="text-center py-3">
                                <p class="text-muted mb-0">
                                    <i class="bi bi-cart-x fs-4"></i><br>
                                    Nenhum produto adicionado
                                </p>
                            </div>
                        </div>

                        <div class="total-container d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0 fw-bold">Total da Venda:</h5>
                                <small class="text-muted">Incluindo todos os produtos</small>
                            </div>
                            <div class="text-end">
                                <h3 class="mb-0 fw-bold text-primary" id="totalVenda">R$ 0,00</h3>
                                <button type="submit" class="btn btn-success mt-2 px-4 py-2">
                                    <i class="bi bi-check-circle me-2"></i>Finalizar Venda
                                </button>
                            </div>
                        </div>

                        <input type="hidden" name="produtos_json" id="produtos_json">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const produtosSelecionados = [];
    const produtoSelect = document.getElementById('produtoSelect');
    const quantidadeProduto = document.getElementById('quantidadeProduto');
    const addProdutoBtn = document.getElementById('addProduto');
    const produtosContainer = document.getElementById('produtosSelecionados');
    const totalSpan = document.getElementById('totalVenda');
    const produtosJsonInput = document.getElementById('produtos_json');
    const vendaForm = document.getElementById('vendaForm');

    addProdutoBtn.addEventListener('click', function() {
        const produtoId = produtoSelect.value;
        const produtoOption = produtoSelect.options[produtoSelect.selectedIndex];
        if (!produtoId) return;

        const preco = parseFloat(produtoOption.dataset.preco);
        const estoque = parseInt(produtoOption.dataset.estoque);
        const nome = produtoOption.dataset.nome;
        const quantidade = parseInt(quantidadeProduto.value) || 1;

        if (quantidade > estoque) {
            alert(`Quantidade maior que estoque disponível (${estoque})`);
            return;
        }

        const index = produtosSelecionados.findIndex(p => p.id === produtoId);
        if (index !== -1) {
            produtosSelecionados[index].quantidade += quantidade;
        } else {
            produtosSelecionados.push({ id: produtoId, nome, preco, quantidade });
        }

        renderProdutosSelecionados();
        produtoSelect.value = '';
        quantidadeProduto.value = 1;
    });

    function renderProdutosSelecionados() {
        produtosContainer.innerHTML = '';
        let total = 0;

        if (produtosSelecionados.length === 0) {
            produtosContainer.innerHTML = `<p class="text-muted mb-0"><i class="bi bi-cart-x fs-4"></i><br>Nenhum produto adicionado</p>`;
            totalSpan.textContent = 'R$ 0,00';
            produtosJsonInput.value = '';
            return;
        }

        produtosSelecionados.forEach((produto, index) => {
            const subtotal = produto.preco * produto.quantidade;
            total += subtotal;

            const produtoDiv = document.createElement('div');
            produtoDiv.className = 'selected-product d-flex justify-content-between align-items-center';
            produtoDiv.innerHTML = `
                <div>
                    <h6 class="mb-1 fw-bold">${produto.nome}</h6>
                    <small class="text-muted">${produto.quantidade} x R$ ${produto.preco.toFixed(2).replace('.', ',')}</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="fw-bold me-3">R$ ${subtotal.toFixed(2).replace('.', ',')}</span>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removerProduto(${index})"><i class="bi bi-trash"></i></button>
                </div>
            `;
            produtosContainer.appendChild(produtoDiv);
        });

        totalSpan.textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
        produtosJsonInput.value = JSON.stringify(produtosSelecionados);
    }

    window.removerProduto = function(index) {
        produtosSelecionados.splice(index, 1);
        renderProdutosSelecionados();
    };

    vendaForm.addEventListener('submit', function(e) {
        if (produtosSelecionados.length === 0) {
            e.preventDefault();
            alert("Adicione pelo menos um produto à venda!");
        }
    });

    document.querySelectorAll('.auto-dismiss').forEach(alert => {
        setTimeout(() => alert.remove(), 5000);
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
