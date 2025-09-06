<?php
session_start();
include 'db.php';

if (empty($_POST['ds_usuario']) || empty($_POST['ds_senha'])) {
    die("Por favor, preencha todos os campos.");
}

$usuario = mysqli_real_escape_string($conn, $_POST['ds_usuario']);
$senha = hash('sha256', $_POST['ds_senha']); // Aplica o mesmo hash usado no cadastro

$sql = "SELECT * FROM usuarios WHERE ds_usuario = ? AND ds_senha = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $usuario, $senha);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 1) {
    $_SESSION['ds_usuario'] = $usuario;
    header("Location: index.php");
    exit();
} else {
    header("Location: login.php?erro=1");
    exit();
}
?>
