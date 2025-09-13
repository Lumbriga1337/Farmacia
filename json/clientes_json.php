<?php
header("Content-Type: application/json; charset=utf-8");

$cdu = trim($_GET['cdu'] ?? '');
$pwd = trim($_GET['pwd'] ?? '');

// 🔐 Autenticação
if ($cdu != "9" || $pwd != "9") {
    echo json_encode(["erro" => "Acesso negado"], JSON_UNESCAPED_UNICODE);
    exit;
}

$cone = mysqli_connect("localhost", "root", "", "farmacia");
mysqli_set_charset($cone, "utf8");

// Consulta clientes
$sql = "SELECT 
            cliente_id, 
            nome_cliente, 
            cpf_cliente, 
            email_cliente, 
            celular_cliente, 
            endereco_cliente, 
            data_nascimento, 
            data_cadastro, 
            cd_usuario 
        FROM clientes";
$rs = $cone->query($sql);

$linhas = [];
while ($row = $rs->fetch_assoc()) {
    $linhas[] = $row;
}

echo json_encode($linhas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
