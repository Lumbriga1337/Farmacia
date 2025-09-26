<?php
session_start();
include 'db.php';

// PHPMailer
require __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// =========================
// Validações básicas
// =========================
if (empty($_POST['ds_usuario']) || empty($_POST['tipo_usuario'])) {
    $_SESSION['erro'] = "Usuário e Tipo de usuário são obrigatórios.";
    header('Location: cadastro.php');
    exit();
}

$ds_usuario   = trim($_POST['ds_usuario']);
$tipo_usuario = $_POST['tipo_usuario'];
$ds_situacao  = 'ativo';

$tipos_validos = ['funcionario', 'administrador', 'cliente'];
if (!in_array($tipo_usuario, $tipos_validos)) {
    $_SESSION['erro'] = "Tipo de usuário inválido.";
    header('Location: cadastro.php');
    exit();
}

// =========================
// ADMINISTRADOR - Aprovação via e-mail
// =========================
if ($tipo_usuario === 'administrador') {
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

    // sempre SHA-256 completo
    $ds_senha = hash('sha256', $_POST['ds_senha']);
    $ip       = $_SERVER['REMOTE_ADDR'];
    $entidade = "ADM";
    $token    = bin2hex(random_bytes(16));

    // Inserir na tabela de aprovação
    $sql_pending = "INSERT INTO adm_pending (ds_usuario, ds_senha, ds_entidade, ip_atual, token) VALUES (?, ?, ?, ?, ?)";
    $stmt_pending = $conn->prepare($sql_pending);
    $stmt_pending->bind_param('sssss', $ds_usuario, $ds_senha, $entidade, $ip, $token);
    $stmt_pending->execute();
    $stmt_pending->close();

    // Enviar e-mail via PHPMailer (igual ao seu código)
    $mail = new PHPMailer(true);
    $logFile = __DIR__ . '/adm_email_log.txt';
    try {
        $mail->SMTPDebug   = 0;
        $mail->Debugoutput = 'error_log';
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'arthurappel0@gmail.com';
        $mail->Password   = 'tlkvtpnsofahkdyd';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('arthurappel0@gmail.com', 'Sistema ERP');
        $mail->addAddress('arthurappel0@gmail.com', 'Você mesmo');
        $mail->isHTML(true);
        $mail->Subject = 'Solicitação de novo administrador';
        $linkAprovacao = "http://192.168.0.209/ERP/json/aprovar_adm.php?token=$token";
        $mail->Body    = "<p>Um novo usuário administrador solicitou acesso.</p>
                          <p>Clique no link para aprovar: <a href='$linkAprovacao'>$linkAprovacao</a></p>";
        $mail->AltBody = "Um novo usuário administrador solicitou acesso.\nClique no link para aprovar: $linkAprovacao";
        $mail->send();
        $logMsg = "[".date('Y-m-d H:i:s')."] Email enviado com sucesso | Usuário: $ds_usuario | Token: $token\n";
        file_put_contents($logFile, $logMsg, FILE_APPEND);
        $_SESSION['sucesso'] = "Solicitação de administrador enviada para aprovação!";
    } catch (Exception $e) {
        $logMsg = "[".date('Y-m-d H:i:s')."] Falha ao enviar email | Usuário: $ds_usuario | Token: $token | Erro: {$mail->ErrorInfo}\n";
        file_put_contents($logFile, $logMsg, FILE_APPEND);
        $_SESSION['erro'] = "Falha ao enviar e-mail de aprovação. Verifique o log.";
    }
    header('Location: cadastro.php');
    exit();
}

// =========================
// FUNCIONÁRIO ou CLIENTE
// =========================
$required_fields = ['ds_cpf', 'ds_email', 'ds_celular', 'ds_endereco', 'dt_nascimento'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['erro'] = "Campo '$field' é obrigatório.";
        header('Location: cadastro.php');
        exit();
    }
}

$ds_cpf        = preg_replace('/[^0-9]/', '', $_POST['ds_cpf']);
$ds_email      = filter_var($_POST['ds_email'], FILTER_SANITIZE_EMAIL);
$ds_celular    = preg_replace('/[^0-9]/', '', $_POST['ds_celular']);
$ds_endereco   = trim($_POST['ds_endereco']);
$dt_nascimento = $_POST['dt_nascimento'];

// FUNCIONÁRIO → senha sempre SHA-256
$ds_senha = null;
if ($tipo_usuario === 'funcionario') {
    if (empty($_POST['ds_senha']) || empty($_POST['confirmar_senha'])) {
        $_SESSION['erro'] = "Senha e confirmação são obrigatórias para funcionário.";
        header('Location: cadastro.php');
        exit();
    }
    if ($_POST['ds_senha'] !== $_POST['confirmar_senha']) {
        $_SESSION['erro'] = "As senhas não coincidem.";
        header('Location: cadastro.php');
        exit();
    }
    $ds_senha = hash('sha256', $_POST['ds_senha']); // SHA-256 completo
}

// =========================
// Verifica duplicidade
// =========================
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

// =========================
// Inserir em usuarios
// =========================
$sql = "INSERT INTO usuarios 
        (ds_usuario, ds_cpf, ds_email, ds_celular, ds_endereco, ds_senha, dt_nascimento, ds_situacao, tipo_usuario) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
    if ($tipo_usuario === 'cliente') {
        $sql_cliente = "INSERT INTO clientes (nome_cliente, cpf_cliente, email_cliente, celular_cliente, endereco_cliente, data_nascimento) 
                        VALUES (?, ?, ?, ?, ?, ?)";
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
} else {
    $_SESSION['erro'] = "Erro ao cadastrar usuário: " . $stmt->error;
}

$stmt->close();
header('Location: cadastro.php');
exit();
