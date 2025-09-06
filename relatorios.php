<?php
include 'db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Relatórios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
        }
        .container-custom {
            max-width: 1200px;
            margin: 80px auto 30px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.08);
        }
        h2 {
            color: #0d6efd;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 5px;
        }
        th {
            background-color: #e7f1ff;
        }
        .btn-nota {
            background-color: #0d6efd;
            border-color: #0d6efd;
            font-size: 14px;
            padding: 4px 12px;
        }
        .btn-nota:hover {
            background-color: #084298;
            border-color: #084298;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container container-custom">

    <h2>📦 Relatório de Estoque</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Produto</th>
                    <th>Quantidade em Estoque</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $res_estoque = $conn->query("SELECT * FROM produtos");
            if ($res_estoque) {
                while ($row = $res_estoque->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['nome']) . "</td>
                            <td>" . (int)$row['quantidade'] . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='2'>Erro ao buscar dados de estoque: " . $conn->error . "</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    ---

    <h2>🛒 Relatório de Vendas</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID da Venda</th>
                    <th>Cliente</th>
                    <th>Data da Venda</th>
                    <th>Total da Venda</th>
                    <th>Detalhes</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php
            // SQL query to get sales information, along with the total price for each sale
            $query_vendas = "SELECT 
                                vendas.id_venda,
                                clientes.nome_cliente,
                                vendas.data_venda,
                                SUM(itens_venda.subtotal) AS total_venda
                             FROM vendas
                             JOIN clientes ON vendas.cliente_id = clientes.cliente_id
                             JOIN itens_venda ON vendas.id_venda = itens_venda.id_venda
                             GROUP BY vendas.id_venda
                             ORDER BY vendas.data_venda DESC";

            $res_vendas = $conn->query($query_vendas);

            if (!$res_vendas) {
                die("Erro na consulta SQL: " . $conn->error);
            }
            
            if ($res_vendas->num_rows > 0) {
                while ($venda = $res_vendas->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($venda['id_venda']) . "</td>";
                    echo "<td>" . htmlspecialchars($venda['nome_cliente']) . "</td>";
                    echo "<td>" . date('d/m/Y H:i', strtotime($venda['data_venda'])) . "</td>";
                    echo "<td>R$ " . number_format($venda['total_venda'], 2, ',', '.') . "</td>";
                    
                    // Fetch items for the current sale
                    $query_itens = "SELECT produtos.nome, itens_venda.quantidade, itens_venda.preco_unitario
                                    FROM itens_venda
                                    JOIN produtos ON itens_venda.id_produto = produtos.id
                                    WHERE itens_venda.id_venda = " . $venda['id_venda'];
                    $res_itens = $conn->query($query_itens);
                    
                    echo "<td>";
                    echo "<ul>";
                    while ($item = $res_itens->fetch_assoc()) {
                        echo "<li>" . htmlspecialchars($item['nome']) . " (x" . (int)$item['quantidade'] . ") - R$ " . number_format($item['preco_unitario'], 2, ',', '.') . "</li>";
                    }
                    echo "</ul>";
                    echo "</td>";

                    echo "<td>";
                    echo "<a href='nota_fiscal.php?id=" . urlencode($venda['id_venda']) . "' class='btn btn-nota btn-sm' target='_blank'>Gerar Nota</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Nenhuma venda encontrada.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>