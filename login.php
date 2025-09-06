<?php
session_start();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - ERP</title>
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

        input[type="submit"] {
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

        input[type="submit"]:hover {
            background-color: #007bff;
            transition: background-color 0.3s ease;
        }

        .register {
            margin-top: 15px;
            text-align: center;
        }

        .register a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .error {
            margin-top: 10px;
            color: #dc3545;
            font-weight: bold;
            text-align: center;
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
            <img src="imagens/logo.png" alt="Logo">
        </div>
        <div class="right">
            <form action="valida_login.php" method="post">
                <h2>LOGIN</h2>

                <label for="ds_usuario">Usuário:</label>
                <input type="text" name="ds_usuario" required>

                <label for="ds_senha">Senha:</label>
                <input type="password" name="ds_senha" required>

                <input type="submit" value="Entrar">

                <div class="register">
                    <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
                </div>

                <?php
                if (isset($_GET['erro'])) {
                    echo "<p class='error'>Usuário ou senha inválidos.</p>";
                }
                ?>
            </form>
        </div>
    </div>
</body>
</html>
