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

$sql = "SELECT id, nome, descricao, categoria_id, preco, quantidade, estoque_minimo, data_cadastro FROM produtos";
$rs = $cone->query($sql);

$linhas = [];
while ($row = $rs->fetch_assoc()) {
    $linhas[] = $row;
}

echo json_encode($linhas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
