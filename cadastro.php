<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: rgba(3, 20, 255, 0.53);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: whitesmoke;
            width: 900px;
            max-width: 95%;
            display: flex;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .img-box {
            flex: 1;
            background-color: rgb(255, 255, 255);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1px;
        }
        .img-box img {
            max-width: 200%;
            max-height: 700px;
            object-fit: contain;
        }
        .form-box {
            flex: 1;
            padding: 40px;
            position: relative;
        }
        .form-box h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .form-box a.btn-voltar {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: rgb(107, 107, 129);
            color: white;
            padding: 5px 15px;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .form-box a.btn-voltar:hover {
            background-color: rgb(0, 4, 245);
        }
        .form-group {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .form-group input {
            flex: 1;
        }
        label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-size: 16px;
        }
        input:focus,
        select:focus {
            border-color: rgb(0, 8, 255);
            box-shadow: 0 0 5px rgba(106, 13, 173, 0.3);
            outline: none;
        }
        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: white;
            cursor: pointer;
        }
        .form-group div {
            position: relative;
            flex: 1;
        }
        .form-group div::after {
            content: "▼";
            font-size: 12px;
            color: #555;
            position: absolute;
            right: 15px;
            top: 42px;
            pointer-events: none;
        }
        option {
            padding: 8px;
        }
        option:disabled {
            color: #999;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: rgb(107, 107, 129);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-submit:hover {
            background-color: rgb(17, 0, 255);
            transform: translateY(-2px);
        }
        .msg {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }
        .msg.success {
            color: green;
        }
        .msg.error {
            color: red;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .img-box {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="img-box">
        <img src="imagens/logo.png" alt="Cadastro">
    </div>

    <div class="form-box">
        <a href="login.php" class="btn-voltar">Login</a>
        <h2>CADASTRE-SE</h2>

        <!-- Mensagens de erro/sucesso -->
        <?php
        if(isset($_SESSION['erro'])) {
            echo "<p class='msg error'>".$_SESSION['erro']."</p>";
            unset($_SESSION['erro']);
        }
        if(isset($_SESSION['sucesso'])) {
            echo "<p class='msg success'>".$_SESSION['sucesso']."</p>";
            unset($_SESSION['sucesso']);
        }
        ?>

        <form action="processa_cadastro.php" method="post">

            <div class="form-group">
                <div>
                    <label for="ds_usuario">Usuário</label>
                    <input type="text" name="ds_usuario" id="ds_usuario" required>
                </div>
                <div>
                    <label for="ds_cpf">CPF</label>
                    <input type="text" name="ds_cpf" id="ds_cpf" required>
                </div>
            </div>

            <div class="form-group">
                <div>
                    <label for="tipo_usuario">Tipo de Usuário</label>
                    <select name="tipo_usuario" id="tipo_usuario" required>
                        <option value="" disabled selected hidden>Selecione o tipo de usuário</option>
                        <option value="funcionario">Funcionário</option>
                        <option value="administrador">Administrador</option>
                        <option value="cliente">Cliente</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div>
                    <label for="ds_email">Email</label>
                    <input type="email" name="ds_email" id="ds_email" required>
                </div>
                <div>
                    <label for="ds_celular">Celular</label>
                    <input type="text" name="ds_celular" id="ds_celular" required>
                </div>
            </div>

            <div class="form-group">
                <div>
                    <label for="ds_endereco">Endereço</label>
                    <input type="text" name="ds_endereco" id="ds_endereco" required>
                </div>
                <div>
                    <label for="dt_nascimento">Nascimento</label>
                    <input type="date" name="dt_nascimento" id="dt_nascimento" required>
                </div>
            </div>

            <!-- Grupo de senha -->
            <div class="form-group" id="senha-group">
                <div>
                    <label for="ds_senha">Senha</label>
                    <input type="password" name="ds_senha" id="ds_senha">
                </div>
                <div>
                    <label for="confirmar_senha">Confirmar Senha</label>
                    <input type="password" name="confirmar_senha" id="confirmar_senha">
                </div>
            </div>

            <input class="btn-submit" type="submit" value="Continuar">

        </form>
    </div>
</div>

<script>
const tipoUsuario = document.getElementById("tipo_usuario");
const senhaGroup = document.getElementById("senha-group");
const senha = document.getElementById("ds_senha");
const confirmarSenha = document.getElementById("confirmar_senha");

// Guardar os names originais
const nomeSenhaOriginal = senha.getAttribute("name");
const nomeConfirmarSenhaOriginal = confirmarSenha.getAttribute("name");

function verificarTipoUsuario() {
    if (tipoUsuario.value === "cliente") {
        senhaGroup.style.display = "none";
        senha.removeAttribute("required");
        confirmarSenha.removeAttribute("required");
        senha.removeAttribute("name");
        confirmarSenha.removeAttribute("name");
    } else {
        senhaGroup.style.display = "flex";
        senha.setAttribute("required", "true");
        confirmarSenha.setAttribute("required", "true");
        senha.setAttribute("name", nomeSenhaOriginal);
        confirmarSenha.setAttribute("name", nomeConfirmarSenhaOriginal);
    }
}

// Executar ao carregar
verificarTipoUsuario();

// Executar ao alterar o tipo
tipoUsuario.addEventListener("change", verificarTipoUsuario);
</script>

</body>
</html>
