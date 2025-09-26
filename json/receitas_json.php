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

$dados = gerarJson($conn, "receitas", "
    id,
    cliente_id,
    paciente,
    medico,
    data_receita,
    arquivo_path
    observacoes,
    data_cadastro
");

echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
