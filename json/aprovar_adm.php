<?php
session_start();
include __DIR__ . '/../db.php';


if(isset($_GET['token'])) {
    $token = $_GET['token'];

    // Busca solicitação pendente
    $stmt = $conn->prepare("SELECT * FROM adm_pending WHERE token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Insere no user_arthur
        $stmt_insert = $conn->prepare("INSERT INTO administradores (ds_nome, ds_senha, ds_entidade, dt_acesso, nr_acesso, ip_atual) VALUES (?, ?, ?, NOW(), 0, ?)");
        $stmt_insert->bind_param('ssss', $row['ds_usuario'], $row['ds_senha'], $row['ds_entidade'], $row['ip_atual']);
        $stmt_insert->execute();
        $stmt_insert->close();

        // Remove da tabela pending
        $stmt_del = $conn->prepare("DELETE FROM adm_pending WHERE token = ?");
        $stmt_del->bind_param('s', $token);
        $stmt_del->execute();
        $stmt_del->close();

        echo "Administrador aprovado e cadastrado com sucesso!";
    } else {
        echo "Token inválido ou já utilizado.";
    }
} else {
    echo "Token não fornecido.";
}
?>
