<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Cadastro de Usuário</h2>

<form action="processa_cadastro.php" method="POST">
    <!-- Tipo de usuário -->
    <div class="mb-3">
        <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
        <select name="tipo_usuario" id="tipo_usuario" class="form-select" required>
            <option value="" disabled selected hidden>Selecione</option>
            <option value="cliente">Cliente</option>
            <option value="funcionario">Funcionário</option>
            <option value="administrador">Administrador</option>
        </select>
    </div>

    <!-- Campo comum -->
    <div class="mb-3">
        <label for="ds_usuario" class="form-label">Usuário</label>
        <input type="text" name="ds_usuario" id="ds_usuario" class="form-control" required>
    </div>

    <!-- Email e Celular (não aparecem para admin) -->
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

    <!-- Campos só para cliente/funcionário -->
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

    <!-- Senha: aparece para funcionário e admin -->
    <div class="mb-3" id="grupo-senha" style="display:none;">
        <label for="ds_senha" class="form-label">Senha</label>
        <input type="password" name="ds_senha" id="ds_senha" class="form-control">
    </div>

    <!-- Campos extras do administrador -->
    <div id="grupo-adm" style="display:none;">
    </div>

    <button type="submit" class="btn btn-primary">Cadastrar</button>
</form>

<script>
const tipoUsuario = document.getElementById("tipo_usuario");
const grupoPadrao = document.getElementById("grupo-padrao");
const grupoSenha  = document.getElementById("grupo-senha");
const grupoAdm    = document.getElementById("grupo-adm");
const grupoContato= document.getElementById("grupo-contato");

tipoUsuario.addEventListener("change", () => {
    if (tipoUsuario.value === "cliente") {
        grupoContato.style.display = "block";
        grupoPadrao.style.display  = "block";
        grupoSenha.style.display   = "none";
        grupoAdm.style.display     = "none";
    }
    else if (tipoUsuario.value === "funcionario") {
        grupoContato.style.display = "block";
        grupoPadrao.style.display  = "block";
        grupoSenha.style.display   = "block";
        grupoAdm.style.display     = "none";
    }
    else if (tipoUsuario.value === "administrador") {
        grupoContato.style.display = "none"; // tira email e celular
        grupoPadrao.style.display  = "none"; // tira cpf/endereco/nascimento
        grupoSenha.style.display   = "block";
        grupoAdm.style.display     = "block";
    }
});
</script>

</body>
</html>
