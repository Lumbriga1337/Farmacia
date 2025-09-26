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
$dados = gerarJson($conn, "funcionarios", "
    cd_funcionario, 
    ds_funcionario, 
    ds_cpf, 
    ds_email, 
    ds_celular, 
    ds_endereco, 
    ds_senha, 
    dt_nascimento,
    ds_situacao
");

echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);