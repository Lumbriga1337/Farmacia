<?php
header("Content-Type: application/json; charset=utf-8");

$cdu = trim($_GET['cdu'] ?? '');
$pwd = trim($_GET['pwd'] ?? '');

// 🔐 Autenticação
if ($cdu != "9" || $pwd != "9") {
    echo json_encode(["erro" => "Acesso negado"], JSON_UNESCAPED_UNICODE);
    exit;
}

// Conexão
$cone = mysqli_connect("localhost", "root", "", "farmacia");
mysqli_set_charset($cone, "utf8");

// Consulta categorias
$sql = "SELECT * FROM itens_venda";
$result = $cone->query($sql); // usar $cone

$linhas = [];
while ($row = $result->fetch_assoc()) { // usar $result
    $linhas[] = $row;
}

echo json_encode($linhas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
