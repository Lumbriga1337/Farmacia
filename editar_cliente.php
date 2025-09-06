<?php
include 'db.php';

// Verifica se foi solicitada a edição de um cliente
if (isset($_GET['id'])) {
    $cliente_id = intval($_GET['id']);
    $query = "SELECT * FROM clientes WHERE cliente_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cliente = $result->fetch_assoc();
    
    if (!$cliente) {
        die("Cliente não encontrado");
    }
}

// Processa a atualização se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cliente_id'])) {
    $cliente_id = intval($_POST['cliente_id']);
    $nome = $conn->real_escape_string($_POST['nome_cliente']);
    $cpf = $conn->real_escape_string($_POST['cpf_cliente']);
    $email = $conn->real_escape_string($_POST['email_cliente']);
    $telefone = $conn->real_escape_string($_POST['celular_cliente']);
    $endereco = $conn->real_escape_string($_POST['endereco_cliente']);
    $formul = $conn->real_escape_string($_POST['data_nascimento']);
    $data_atualizacao = date('Y-m-d H:i:s');
    
    $sql = "UPDATE clientes SET 
            nome_cliente = '$nome',
            cpf_cliente = '$cpf',
            email_cliente = '$email',
            celular_cliente = '$telefone',
            endereco_cliente = '$endereco',
            data_nascimento = '$formul',
            data_cadastro = '$data_atualizacao'
            WHERE cliente_id = $cliente_id";
    
    if ($conn->query($sql)) {
        $success = "Cliente atualizado com sucesso!";
        // Recarrega os dados atualizados
        $query = "SELECT * FROM clientes WHERE cliente_id = $cliente_id";
        $result = $conn->query($query);
        $cliente = $result->fetch_assoc();
    } else {
        $error = "Erro ao atualizar cliente: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            padding-top: 70px;
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            font-weight: 600;
        }
        .btn-save {
            min-width: 120px;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person-lines-fill"></i> Editar Cliente</h2>
        <a href="lista_clientes.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST">
            <input type="hidden" name="cliente_id" value="<?= $cliente['cliente_id'] ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nome_cliente" class="form-label">Nome do Cliente</label>
                        <input type="text" class="form-control" id="nome_cliente" name="nome_cliente" 
                               value="<?= htmlspecialchars($cliente['nome_cliente']) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cpf_cliente" class="form-label">CPF Cliente</label>
                        <input type="text" class="form-control" id="cpf_cliente" name="cpf_cliente" 
                               value="<?= htmlspecialchars($cliente['cpf_cliente']) ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email_cliente" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email_cliente" name="email_cliente" 
                               value="<?= htmlspecialchars($cliente['email_cliente']) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="celular_cliente" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="celular_cliente" name="celular_cliente" 
                               value="<?= htmlspecialchars($cliente['celular_cliente']) ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="endereco_cliente" class="form-label">Endereço</label>
                <textarea class="form-control" id="endereco_cliente" name="endereco_cliente" 
                          rows="3"><?= htmlspecialchars($cliente['endereco_cliente']) ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                        <input type="text" class="form-control" id="data_nascimento" name="data_nascimento" 
                               value="<?= htmlspecialchars($cliente['data_nascimento']) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Data de Cadastro</label>
                        <input type="text" class="form-control" 
                               value="<?= date('d/m/Y H:i', strtotime($cliente['data_cadastro'])) ?>" readonly>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <button type="submit" class="btn btn-primary btn-save">
                    <i class="bi bi-save"></i> Salvar Alterações
                </button>
                
                <a href="farmacia.clientes.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Máscara para telefone
    document.getElementById('celular_cliente').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            value = value.match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
            e.target.value = !value[2] ? value[1] : '(' + value[1] + ') ' + value[2] + (value[3] ? '-' + value[3] : '');
        }
    });
</script>
</body>
</html>