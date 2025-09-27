<?php
header("Content-Type: application/json; charset=utf-8");
include 'funcoes.php';

// Pega o token
$token = trim($_GET['token'] ?? '');

// Valida o token
$validacao = validarToken($conn, $token);
if ($validacao !== true) {
    echo json_encode($validacao, JSON_UNESCAPED_UNICODE);
    exit;
}
$dados = gerarJson($conn, "itens_venda", "
    id_item,
    id_venda,
    id_produto,
    quantidade,
    preco_unitario,
    subtotal
");



echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

