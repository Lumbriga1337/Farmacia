<?php
session_start();
include('db.php');

// Campos obrigatórios
$required_fields = ['ds_usuario', 'ds_cpf', 'ds_email', 'ds_celular', 'ds_endereco', 'dt_nascimento', 'tipo_usuario'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['erro'] = "Campo '$field' não foi submetido.";
        header('Location: cadastro.php');
        exit();
    }
}

// Coletar dados
$ds_usuario    = trim($_POST['ds_usuario']);
$ds_cpf        = preg_replace('/[^0-9]/', '', $_POST['ds_cpf']);
$ds_email      = filter_var($_POST['ds_email'], FILTER_SANITIZE_EMAIL);
$ds_celular    = preg_replace('/[^0-9]/', '', $_POST['ds_celular']);
$ds_endereco   = trim($_POST['ds_endereco']);
$dt_nascimento = $_POST['dt_nascimento'];
$tipo_usuario  = $_POST['tipo_usuario'];
$ds_situacao   = 'ativo';

// Validar tipo
$tipos_validos = ['funcionario', 'administrador', 'cliente'];
if (!in_array($tipo_usuario, $tipos_validos)) {
    $_SESSION['erro'] = "Tipo de usuário inválido.";
    header('Location: cadastro.php');
    exit();
}

// Senha opcional
$ds_senha = null;
if ($tipo_usuario !== 'cliente') {
    if (empty($_POST['ds_senha']) || empty($_POST['confirmar_senha'])) {
        $_SESSION['erro'] = "Senha e confirmação são obrigatórias.";
        header('Location: cadastro.php');
        exit();
    }
    if ($_POST['ds_senha'] !== $_POST['confirmar_senha']) {
        $_SESSION['erro'] = "As senhas não coincidem.";
        header('Location: cadastro.php');
        exit();
    }
    $ds_senha = hash('sha256', $_POST['ds_senha']);
}

// Verifica usuário ou CPF existente
$sql = "SELECT cd_usuario FROM usuarios WHERE ds_usuario = ? OR ds_cpf = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $ds_usuario, $ds_cpf);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    $_SESSION['erro'] = "Usuário ou CPF já cadastrado.";
    header('Location: cadastro.php');
    exit();
}
$stmt->close();

// Inserir usuário
$sql = "INSERT INTO usuarios (ds_usuario, ds_cpf, ds_email, ds_celular, ds_endereco, ds_senha, dt_nascimento, ds_situacao, tipo_usuario) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    'sssssssss',
    $ds_usuario,
    $ds_cpf,
    $ds_email,
    $ds_celular,
    $ds_endereco,
    $ds_senha,
    $dt_nascimento,
    $ds_situacao,
    $tipo_usuario
);

if ($stmt->execute()) {
    // Se for cliente, cadastra também na tabela clientes
    if ($tipo_usuario === 'cliente') {
        $sql_cliente = "INSERT INTO clientes (nome_cliente, cpf_cliente, email_cliente, celular_cliente, endereco_cliente, data_nascimento) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_cliente = $conn->prepare($sql_cliente);
        $stmt_cliente->bind_param(
            'ssssss',
            $ds_usuario,
            $ds_cpf,
            $ds_email,
            $ds_celular,
            $ds_endereco,
            $dt_nascimento
        );
        $stmt_cliente->execute();
        $stmt_cliente->close();
    }

    $_SESSION['sucesso'] = "Usuário cadastrado com sucesso!";
    header('Location: cadastro.php');
    exit();
} else {
    $_SESSION['erro'] = "Erro ao cadastrar usuário: " . $stmt->error;
    header('Location: cadastro.php');
    exit();
}
