<?php
header("Content-Type: application/json; charset=utf-8");
include 'funcoes.php';

// Pega o token
$token = trim($_GET['token'] ?? '');

// Valida o token e retorna os dados do admin
$adm = validarToken($conn, $token);
if (isset($adm['erro'])) {
    echo json_encode($adm, JSON_UNESCAPED_UNICODE);
    exit;
}

// Token válido → retorna funcionários
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


// Retorna os dados em JSON
echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
