<?php
include 'db.php';

$id_venda = (int)$_GET['id_venda'];
$venda = $conn->query("SELECT v.*, c.nome_cliente 
                       FROM vendas v 
                       JOIN clientes c ON v.cliente_id = c.cliente_id 
                       WHERE v.id_venda = $id_venda")->fetch_assoc();

$itens = $conn->query("SELECT i.*, p.nome 
                       FROM itens_venda i 
                       JOIN produtos p ON i.id_produto = p.id 
                       WHERE i.id_venda = $id_venda");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Recibo da Venda #<?= $id_venda ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body onload="window.print()">
<div class="container mt-5">
    <h2>Recibo da Venda #<?= $id_venda ?></h2>
    <p><b>Cliente:</b> <?= $venda['nome_cliente'] ?></p>
    <p><b>Data:</b> <?= date("d/m/Y H:i", strtotime($venda['data_venda'])) ?></p>
    <p><b>Status:</b> <?= $venda['status'] ?></p>
    <table class="table table-bordered">
        <thead>
            <tr><th>Produto</th><th>Qtd</th><th>Preço</th><th>Total</th></tr>
        </thead>
        <tbody>
            <?php 
            while($item = $itens->fetch_assoc()) {
                $subtotal = $item['quantidade'] * $item['preco_unitario'];
                echo "<tr>
                        <td>{$item['nome']}</td>
                        <td>{$item['quantidade']}</td>
                        <td>R$ ".number_format($item['preco_unitario'],2,',','.')."</td>
                        <td>R$ ".number_format($subtotal,2,',','.')."</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
    <h4>Total: R$ <?= number_format($venda['valor_total'],2,',','.') ?></h4>
</div>
</body>
</html>
