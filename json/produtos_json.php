<?php
header("Content-Type: application/json; charset=utf-8");
include 'funcoes.php';


// Pega o token
$token = trim($_GET['token'] ?? '');

// Valida o token (usando $cone que vem do db.php)
$validacao = validarToken($conn, $token);
if ($validacao !== true) {
    echo json_encode($validacao, JSON_UNESCAPED_UNICODE);
    exit;
}

$dados = gerarJson($conn, "produtos", "
    id,
    nome,
    descricao,
    categoria_id,
    preco,
    quantidade,
    estoque_minimo,
    data_cadastro
");


echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
