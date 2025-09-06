<?php
include 'db.php';

// Determinar o filtro selecionado
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'alfabetico';

// Construir a consulta SQL baseada no filtro
switch ($filtro) {
    case 'alfabetico':
        $sql = "SELECT * FROM clientes ORDER BY nome_cliente ASC";
        $titulo_filtro = "Ordem Alfabética (A-Z)";
        break;
    case 'alfabetico_desc':
        $sql = "SELECT * FROM clientes ORDER BY nome_cliente DESC";
        $titulo_filtro = "Ordem Alfabética (Z-A)";
        break;
    case 'recentes':
        $sql = "SELECT * FROM clientes ORDER BY data_cadastro DESC";
        $titulo_filtro = "Cadastrados Recentemente";
        break;
    case 'antigos':
        $sql = "SELECT * FROM clientes ORDER BY data_cadastro ASC";
        $titulo_filtro = "Cadastrados Há Mais Tempo";
        break;
    case 'ativos':
        $sql = "SELECT * FROM clientes WHERE data_nascimento IS NOT NULL ORDER BY data_nascimento DESC";
        $titulo_filtro = "Clientes Ativos";
        break;
    case 'inativos':
        $sql = "SELECT * FROM clientes WHERE data_nascimento IS NULL ORDER BY nome_cliente ASC";
        $titulo_filtro = "Clientes Inativos";
        break;
    default:
        $sql = "SELECT * FROM clientes ORDER BY nome_cliente ASC";
        $titulo_filtro = "Ordem Alfabética (A-Z)";
}

// Executar a consulta
$result = $conn->query($sql);
if (!$result) {
    die("Erro ao consultar clientes: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Estilos consistentes com a navbar fixa */
        body {
            padding-top: 70px;
        }
        .navbar {
            height: 50px;
        }
        .navbar-nav .nav-link {
            font-size: 16px;
            font-weight: 600;
            color: #ffffff !important;
            margin-left: 20px;
            letter-spacing: 0.5px;
        }
        .navbar-nav .nav-link:hover {
            color: #ffd700 !important;
        }
        .client-table th {
            background-color: #f8f9fa;
        }
        .filtro-ativo {
            font-weight: bold;
            color: #0d6efd !important;
        }
        .badge-status {
            font-size: 0.8em;
        }
        .active-badge {
            background-color: #198754;
        }
        .inactive-badge {
            background-color: #6c757d;
        }
        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-4">
    <h2><i class="bi bi-people-fill"></i> Lista de Clientes</h2>
    
    <div class="d-flex justify-content-between mb-4">
        <div class="btn-group">
            <a href="?filtro=alfabetico" class="btn btn-outline-secondary <?= $filtro == 'alfabetico' ? 'filtro-ativo' : '' ?>">
                <i class="bi bi-sort-alpha-down"></i> A-Z
            </a>
            <a href="?filtro=alfabetico_desc" class="btn btn-outline-secondary <?= $filtro == 'alfabetico_desc' ? 'filtro-ativo' : '' ?>">
                <i class="bi bi-sort-alpha-down-alt"></i> Z-A
            </a>
            <a href="?filtro=recentes" class="btn btn-outline-secondary <?= $filtro == 'recentes' ? 'filtro-ativo' : '' ?>">
                <i class="bi bi-clock-history"></i> Recentes
            </a>
            <a href="?filtro=antigos" class="btn btn-outline-secondary <?= $filtro == 'antigos' ? 'filtro-ativo' : '' ?>">
                <i class="bi bi-clock"></i> Antigos
            </a>
            <a href="?filtro=ativos" class="btn btn-outline-secondary <?= $filtro == 'ativos' ? 'filtro-ativo' : '' ?>">
                <i class="bi bi-check-circle"></i> Ativos
            </a>
            <a href="?filtro=inativos" class="btn btn-outline-secondary <?= $filtro == 'inativos' ? 'filtro-ativo' : '' ?>">
                <i class="bi bi-dash-circle"></i> Inativos
            </a>
        </div>
        
        <a href="cadastro.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Cliente
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped client-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Endereço</th>
                    <th>Status</th>
                    <th>Cadastro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($cliente = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($cliente['cliente_id']) ?></td>
                    <td><?= htmlspecialchars($cliente['nome_cliente']) ?></td>
                    <td><?= htmlspecialchars($cliente['celular_cliente']) ?></td>
                    <td><?= htmlspecialchars($cliente['email_cliente']) ?></td>
                    <td><?= htmlspecialchars(substr($cliente['endereco_cliente'], 0, 30)) . (strlen($cliente['endereco_cliente']) > 30 ? '...' : '') ?></td>
                    <td>
                        <span class="badge rounded-pill badge-status <?= $cliente['data_nascimento'] ? 'active-badge' : 'inactive-badge' ?>">
                            <?= $cliente['data_nascimento'] ? 'Ativo' : 'Inativo' ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($cliente['data_cadastro'])) ?></td>
                    <td>
                        <a href="editar_cliente.php?id=<?= $cliente['cliente_id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            Mostrando <?= $result->num_rows ?> cliente(s)
        </div>
        <div>
            <span class="fw-bold">Filtro atual:</span> <?= $titulo_filtro ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>