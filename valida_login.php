<?php
session_start();
include 'db.php';

if (empty($_POST['ds_usuario']) || empty($_POST['ds_senha'])) {
    die("Por favor, preencha todos os campos.");
}

$usuario = mysqli_real_escape_string($conn, $_POST['ds_usuario']);
$senha   = hash('sha256', $_POST['ds_senha']); // mesma hash usada no cadastro

// ==========================
// Função para verificar login
// ==========================
function validarLogin($conn, $tabela, $col_usuario, $col_senha, $usuario, $senha) {
    $sql = "SELECT * FROM $tabela WHERE $col_usuario = ? AND $col_senha = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $usuario, $senha);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) == 1) {
            $dados = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return $dados;
        }
        mysqli_stmt_close($stmt);
    }
    return false;
}

// ==========================
// Tenta Administrador
// ==========================
$adm = validarLogin($conn, 'administradores', 'ds_nome', 'ds_senha', $usuario, $senha);
if ($adm) {
    $_SESSION['ds_usuario'] = $usuario;
    $_SESSION['tipo_usuario'] = 'administrador';
    $_SESSION['cd_usuario'] = $adm['id_administrador']; // id do administrador
    header("Location: index.php");
    exit();
}

// ==========================
// Tenta Funcionário
// ==========================
$func = validarLogin($conn, 'funcionarios', 'ds_funcionario', 'ds_senha', $usuario, $senha);
if ($func) {
    $_SESSION['ds_usuario'] = $usuario;
    $_SESSION['tipo_usuario'] = 'funcionario';
    $_SESSION['cd_funcionario'] = $func['id_funcionario']; // id do funcionário
    header("Location: index.php");
    exit();
}

// ==========================
// Usuário não encontrado
// ==========================
header("Location: login.php?erro=1");
exit();
?>
