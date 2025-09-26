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

// Token válido → retorna clientes
$dados = gerarJson($conn, "clientes", "
    cliente_id, 
    nome_cliente, 
    cpf_cliente, 
    email_cliente, 
    celular_cliente, 
    endereco_cliente, 
    data_nascimento, 
    data_cadastro
");

echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
