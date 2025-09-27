<?php
header("Content-Type: application/json; charset=utf-8");

// Inclui conexão e funções      // Certifique-se que define $conn (mysqli)
include 'funcoes.php';

// Pega o token
$token = trim($_GET['token'] ?? '');

// Valida o token e recebe os dados do admin
$adm = validarToken($conn, $token); // $adm deve conter cd_adm, ds_nome e ds_entidade
if (isset($adm['erro'])) {
    echo json_encode($adm, JSON_UNESCAPED_UNICODE);
    exit;
}

// Consulta categorias
$dados = gerarJson($conn, "categorias", "
    id,
    nome,
    descricao
");

// Retorna os dados em JSON
echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
