<?php 
session_start(); 
include 'db.php'; 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #7c85f3;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .card-header {
            background-color: #f3f3f3;
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            padding: 20px;
            border-bottom: none;
        }
        .btn-primary {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #007bff;
            transition: 0.3s;
        }
        .form-label {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="card w-100" style="max-width: 700px;">
        <div class="card-header">
            Cadastro de Usuário
        </div>
        <div class="card-body p-4">

            <?php if (isset($_SESSION['sucesso'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['sucesso']; 
                        unset($_SESSION['sucesso']);
                    ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['erro'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        echo $_SESSION['erro']; 
                        unset($_SESSION['erro']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="processa_cadastro.php" method="POST">
                <div class="mb-3">
                    <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
                    <select name="tipo_usuario" id="tipo_usuario" class="form-select" required>
                        <option value="" disabled selected hidden>Selecione</option>
                        <option value="cliente">Cliente</option>
                        <option value="funcionario">Funcionário</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="ds_usuario" class="form-label">Nome de Usuário</label>
                    <input type="text" name="ds_usuario" id="ds_usuario" class="form-control" required>
                </div>

                <div id="grupo-contato">
                    <div class="mb-3">
                        <label for="ds_email" class="form-label">Email</label>
                        <input type="email" name="ds_email" id="ds_email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="ds_celular" class="form-label">Celular</label>
                        <input type="text" name="ds_celular" id="ds_celular" class="form-control">
                    </div>
                </div>

                <div id="grupo-padrao">
                    <div class="mb-3">
                        <label for="ds_cpf" class="form-label">CPF</label>
                        <input type="text" name="ds_cpf" id="ds_cpf" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="ds_endereco" class="form-label">Endereço</label>
                        <input type="text" name="ds_endereco" id="ds_endereco" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="dt_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="date" name="dt_nascimento" id="dt_nascimento" class="form-control">
                    </div>
                </div>

                <div id="grupo-senha" style="display:none;">
                    <div class="mb-3">
                        <label for="ds_senha" class="form-label">Senha</label>
                        <input type="password" name="ds_senha" id="ds_senha" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                        <input type="password" name="confirmar_senha" id="confirmar_senha" class="form-control">
                    </div>
                </div>

                <div id="grupo-adm" style="display:none;"></div>

                <!-- Linha com os dois botões -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                    <div>
                        <a href="login.php" class="btn btn-primary me-2">Login Cliente / Funcionário</a>
                        <a href="./json/loginadm.php" class="btn btn-primary">Login Administrador</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    const tipoUsuario   = document.getElementById("tipo_usuario");
    const grupoPadrao   = document.getElementById("grupo-padrao");
    const grupoSenha    = document.getElementById("grupo-senha");
    const grupoAdm      = document.getElementById("grupo-adm");
    const grupoContato  = document.getElementById("grupo-contato");

    tipoUsuario.addEventListener("change", () => {
        const valor = tipoUsuario.value;

        if (valor === "cliente") {
            grupoContato.style.display = "block";
            grupoPadrao.style.display  = "block";
            grupoSenha.style.display   = "none";
            grupoAdm.style.display     = "none";
        } 
        else if (valor === "funcionario") {
            grupoContato.style.display = "block";
            grupoPadrao.style.display  = "block";
            grupoSenha.style.display   = "block";
            grupoAdm.style.display     = "none";
        } 
        else if (valor === "administrador") {
            grupoContato.style.display = "none";
            grupoPadrao.style.display  = "none";
            grupoSenha.style.display   = "block";
            grupoAdm.style.display     = "block";
        }
    });
    </script>
</body>
</html>
