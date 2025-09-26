<?php
session_start();

// Conexão com o banco
$cone = mysqli_connect("localhost", "root", "", "farmacia");
mysqli_set_charset($cone, "utf8");

// Função para gerar token
function gerarToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}

$erro = "";
$sucesso = "";

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($login === '' || $senha === '') {
        $erro = "Login e senha são obrigatórios.";
    } else {
        // SHA-256 da senha
        $senhaHash = hash("sha256", $senha);

        $stmt = $cone->prepare("SELECT cd_adm FROM administradores WHERE ds_nome = ? AND ds_senha = ? LIMIT 1");
        $stmt->bind_param("ss", $login, $senhaHash);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            $erro = "Login ou senha incorretos.";
        } else {
            $adm = $res->fetch_assoc();

            // Gera token válido por 1 hora
            $token = gerarToken(64);
            $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

            $upd = $cone->prepare("UPDATE administradores SET ds_token = ?, dt_token = ? WHERE cd_adm = ?");
            $upd->bind_param("ssi", $token, $expira, $adm['cd_adm']);
            $upd->execute();

            $sucesso = "Login ok! Seu token (válido 1 hora):<br><br><code>$token</code>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login Administrador - ERP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #7c85f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            width: 850px;
            max-width: 100%;
            display: flex;
            flex-direction: row;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .left,
        .right {
            flex: 1;
        }

        .left {
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .left img {
            width: 320px;
            max-width: 100%;
            height: auto;
        }

        .right {
            background-color: #f3f3f3;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h2 {
            margin-bottom: 30px;
            font-size: 24px;
            color: #333;
            text-align: center;
        }

        label {
            margin-top: 10px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="password"] {
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 100%;
        }

        button {
            margin-top: 25px;
            padding: 12px;
            width: 100%;
            background-color: #6c6a89;
            border: none;
            color: white;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #007bff;
            transition: background-color 0.3s ease;
        }

        .message {
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
        }

        .error {
            color: #dc3545;
        }

        .success {
            color: #28a745;
            word-wrap: break-word;
        }

        code {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 10px;
            background: #eee;
            border-radius: 8px;
            font-size: 14px;
            word-break: break-all;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left,
            .right {
                width: 100%;
                padding: 30px 20px;
            }

            h2 {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left">
            <img src="../imagens/logo.png" alt="Logo">
        </div>
        <div class="right">
            <form method="POST" action="">
                <h2>LOGIN ADMINISTRADOR</h2>

                <label for="login">Usuário:</label>
                <input type="text" name="login" required>

                <label for="senha">Senha:</label>
                <input type="password" name="senha" required>

                <button type="submit">Gerar Token</button>

                <?php if (!empty($erro)): ?>
                    <p class="message error"><?= $erro ?></p>
                <?php endif; ?>

                <?php if (!empty($sucesso)): ?>
                    <p class="message success"><?= $sucesso ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
