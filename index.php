<?php
include 'db.php';

// Verifica se já existe algum cadastro de funcionários ou administradores
$sql = "
    SELECT 
        (SELECT COUNT(*) FROM funcionarios) +
        (SELECT COUNT(*) FROM administradores) AS total
";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Se não houver cadastros, redireciona para cadastro.php
if ($row['total'] == 0) {
    header("Location: cadastro.php");
    exit();
}


// Consultas para os dados do dashboard
$hoje = date('Y-m-d');
$ontem = date('Y-m-d', strtotime('-1 day'));

// Vendas hoje
$sql_vendas_hoje = "SELECT COUNT(*) AS total FROM vendas WHERE DATE(data_venda) = '$hoje'";
$result_vendas_hoje = mysqli_query($conn, $sql_vendas_hoje);
$vendas_hoje = mysqli_fetch_assoc($result_vendas_hoje);

// Vendas ontem
$sql_vendas_ontem = "SELECT COUNT(*) AS total FROM vendas WHERE DATE(data_venda) = '$ontem'";
$result_vendas_ontem = mysqli_query($conn, $sql_vendas_ontem);
$vendas_ontem = mysqli_fetch_assoc($result_vendas_ontem);

// Calcular variação percentual
$variacao = 0;
if ($vendas_ontem['total'] > 0) {
    $variacao = (($vendas_hoje['total'] - $vendas_ontem['total']) / $vendas_ontem['total']) * 100;
}

// Faturamento hoje
$sql_faturamento = "SELECT SUM(valor_total) AS faturamento FROM vendas WHERE DATE(data_venda) = '$hoje'";
$result_faturamento = mysqli_query($conn, $sql_faturamento);
$faturamento = mysqli_fetch_assoc($result_faturamento);

// Meta de faturamento
$meta_faturamento = 5000;

// Produtos em falta (estoque abaixo do mínimo)
$sql_produtos_falta = "SELECT COUNT(*) AS total FROM produtos WHERE quantidade <= estoque_minimo";
$result_produtos_falta = mysqli_query($conn, $sql_produtos_falta);
$produtos_falta = mysqli_fetch_assoc($result_produtos_falta);

// Dados para o gráfico de vendas dos últimos 7 dias
$data_inicio = date('Y-m-d', strtotime('-6 days'));
$sql_vendas_semana = "SELECT DATE(data_venda) as dia, COUNT(*) as total FROM vendas WHERE DATE(data_venda) BETWEEN '$data_inicio' AND '$hoje' GROUP BY DATE(data_venda) ORDER BY dia ASC";
$result_vendas_semana = mysqli_query($conn, $sql_vendas_semana);

$vendas_por_dia = [];
$dias_semana = [];

while ($row = mysqli_fetch_assoc($result_vendas_semana)) {
    $dias_semana[] = date('D', strtotime($row['dia']));
    $vendas_por_dia[] = $row['total'];
}

// Preencher dias sem vendas com zero
$todos_dias = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $todos_dias[] = date('D', strtotime($date));
}

$vendas_completas = [];
foreach ($todos_dias as $dia) {
    $index = array_search($dia, $dias_semana);
    $vendas_completas[] = ($index !== false) ? $vendas_por_dia[$index] : 0;
}

// Dados para o gráfico de categorias mais vendidas
$sql_categorias_vendidas = "SELECT c.nome as categoria, COUNT(*) as total 
FROM itens_venda iv 
JOIN produtos p ON iv.id_produto = p.id 
JOIN categorias c ON p.categoria_id = c.id 
JOIN vendas v ON iv.id_venda = v.id_venda 
WHERE DATE(v.data_venda) BETWEEN '$data_inicio' AND '$hoje' 
GROUP BY c.nome 
ORDER BY total DESC 
LIMIT 4";

$result_categorias = mysqli_query($conn, $sql_categorias_vendidas);
$categorias = [];
$vendas_por_categoria = [];

while ($row = mysqli_fetch_assoc($result_categorias)) {
    $categorias[] = $row['categoria'];
    $vendas_por_categoria[] = $row['total'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<title>ERP Farmácia - Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
:root {
    --primary-color: #0d6efd;
    --secondary-color: #6c757d;
    --success-color: #198754;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --light-color: #f8f9fa;
}
body {
    background-color: #f5f5f5;
    margin-top: 96px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.navbar { height: 60px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); background-color: #fff; }
.navbar-brand img { max-height: 40px; }
.navbar-nav .nav-link { font-size: 15px; font-weight: 600; color: #333 !important; margin-left: 15px; padding: 8px 15px; border-radius: 5px; }
.navbar-nav .nav-link:hover, .navbar-nav .nav-link.active { background-color: var(--primary-color); color: white !important; }
.dashboard-card { border-radius: 10px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s; margin-bottom: 20px; height: 100%; }
.dashboard-card:hover { transform: translateY(-5px); }
.card-icon { font-size: 2rem; margin-bottom: 15px; }
.section-title { border-left: 5px solid var(--primary-color); padding-left: 10px; margin: 30px 0 20px; }
.quick-actions { margin-bottom: 30px; }
.quick-action-btn { padding: 15px; text-align: center; border-radius: 8px; background: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: all 0.3s; color: #333; text-decoration: none; display: block; }
.quick-action-btn:hover { background: var(--primary-color); color: white; transform: translateY(-3px); }
.quick-action-icon { font-size: 1.5rem; margin-bottom: 10px; }
.welcome-banner { background: linear-gradient(135deg, var(--primary-color), #0b5ed7); color: white; border-radius: 10px; padding: 20px; margin-bottom: 30px; }
.chart-container { position: relative; height: 250px; width: 100%; }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">

<!-- Banner de Boas-Vindas -->
<div class="welcome-banner">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2>Bem-vindo ao ERP Farmácia</h2>
            <p class="mb-0">Sistema completo para gestão de farmácias</p>
        </div>
        <div class="col-md-4 text-end">
            <i class="bi bi-shop" style="font-size: 3rem; opacity: 0.8;"></i>
        </div>
    </div>
</div>

<!-- Cards de Resumo -->
<div class="row">
    <div class="col-md-3">
        <div class="card dashboard-card text-white bg-primary">
            <div class="card-body">
                <div class="card-icon"><i class="bi bi-cart"></i></div>
                <h5 class="card-title">Vendas Hoje</h5>
                <h2 class="card-text"><?php echo $vendas_hoje['total']; ?></h2>
                <p class="card-text"><small><?php echo $variacao > 0 ? '+' : ''; echo number_format($variacao, 2) ?>% em relação a ontem</small></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card text-white bg-success">
            <div class="card-body">
                <div class="card-icon"><i class="bi bi-currency-dollar"></i></div>
                <h5 class="card-title">Faturamento</h5>
                <h2 class="card-text">R$ <?php echo number_format($faturamento['faturamento'] ?? 0, 2, ',', '.'); ?></h2>
                <p class="card-text"><small>Meta: R$ <?php echo number_format($meta_faturamento, 2, ',', '.'); ?></small></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card text-white bg-warning">
            <div class="card-body">
                <div class="card-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <h5 class="card-title">Produtos em Falta</h5>
                <h2 class="card-text"><?php echo $produtos_falta['total']; ?></h2>
                <p class="card-text"><small>Precisa de atenção</small></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card text-white bg-danger">
            <div class="card-body">
                <div class="card-icon"><i class="bi bi-calendar-x"></i></div>
                <h5 class="card-title">Vencimentos</h5>
                <h2 class="card-text">0</h2>
                <p class="card-text"><small>Esta semana</small></p>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<h3 class="section-title">Ações Rápidas</h3>
<div class="row quick-actions">
    <div class="col-md-2 col-6 mb-3">
        <a href="vendas.php" class="quick-action-btn">
            <i class="bi bi-cart-plus quick-action-icon"></i> Nova Venda
        </a>
    </div>
    <div class="col-md-2 col-6 mb-3">
        <a href="produtos.php" class="quick-action-btn">
            <i class="bi bi-plus-circle quick-action-icon"></i> Cadastrar Produto
        </a>
    </div>
    <div class="col-md-2 col-6 mb-3">
        <a href="relatorios.php" class="quick-action-btn">
            <i class="bi bi-graph-up quick-action-icon"></i> Relatórios
        </a>
    </div>
    <div class="col-md-2 col-6 mb-3">
        <a href="estoque.php" class="quick-action-btn">
            <i class="bi bi-box-seam quick-action-icon"></i> Estoque
        </a>
    </div>
    <div class="col-md-2 col-6 mb-3">
        <a href="farmacia.clientes.php" class="quick-action-btn">
            <i class="bi bi-people quick-action-icon"></i> Clientes
        </a>
    </div>
    <div class="col-md-2 col-6 mb-3">
        <a href="receitas.php" class="quick-action-btn">
            <i class="bi bi-file-earmark-medical quick-action-icon"></i> Receitas
        </a>
    </div>
</div>

<!-- Gráficos -->
<h3 class="section-title">Desempenho Recente</h3>
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-bar-chart"></i> Vendas Diárias (7 dias)</h5>
                <div class="chart-container"><canvas id="vendasChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card dashboard-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-pie-chart"></i> Categorias Mais Vendidas</h5>
                <div class="chart-container"><canvas id="categoriasChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<!-- Produtos com Estoque Baixo -->
<h3 class="section-title">Produtos Abaixo do Estoque Mínimo</h3>
<div class="card dashboard-card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Código</th>
                        <th>Estoque Atual</th>
                        <th>Mínimo</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_baixo_estoque = "SELECT * FROM produtos WHERE quantidade <= estoque_minimo LIMIT 5";
                    $result_baixo_estoque = mysqli_query($conn, $sql_baixo_estoque);
                    if (mysqli_num_rows($result_baixo_estoque) > 0) {
                        while ($produto = mysqli_fetch_assoc($result_baixo_estoque)):
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                        <td><?php echo htmlspecialchars($produto['id']); ?></td>
                        <td class="text-danger fw-bold"><?php echo $produto['quantidade']; ?></td>
                        <td><?php echo $produto['estoque_minimo']; ?></td>
                        <td>
                            <a href="estoque.php?id=<?php echo $produto['id']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Repor
                            </a>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    } else {
                        echo '<tr><td colspan="5" class="text-center">Nenhum produto com estoque baixo</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Últimas Vendas -->
<h3 class="section-title">Últimas Vendas</h3>
<div class="card dashboard-card mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Cliente</th>
                        <th>Valor Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_ultimas_vendas = "SELECT v.*, c.nome_cliente as cliente FROM vendas v LEFT JOIN clientes c ON v.cliente_id = c.cliente_id ORDER BY v.data_venda DESC LIMIT 5";
                    $result_ultimas_vendas = mysqli_query($conn, $sql_ultimas_vendas);
                    if (mysqli_num_rows($result_ultimas_vendas) > 0) {
                        while ($venda = mysqli_fetch_assoc($result_ultimas_vendas)):
                    ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($venda['data_venda'])); ?></td>
                        <td><?php echo $venda['cliente'] ? htmlspecialchars($venda['cliente']) : 'Consumidor Final'; ?></td>
                        <td>R$ <?php echo number_format($venda['valor_total'], 2, ',', '.'); ?></td>
                        <td><span class="badge bg-success">Concluída</span></td>
                    </tr>
                    <?php
                        endwhile;
                    } else {
                        echo '<tr><td colspan="4" class="text-center">Nenhuma venda registrada</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dados para os gráficos (PHP -> JS)
const vendasData = {
    labels: <?php echo json_encode($todos_dias); ?>,
    datasets: [{
        label: 'Vendas',
        data: <?php echo json_encode($vendas_completas); ?>,
        backgroundColor: 'rgba(13, 110, 253, 0.2)',
        borderColor: 'rgba(13, 110, 253, 1)',
        tension: 0.3,
        fill: true
    }]
};

const categoriasData = {
    labels: <?php echo json_encode($categorias); ?>,
    datasets: [{
        data: <?php echo json_encode($vendas_por_categoria); ?>,
        backgroundColor: [
            'rgba(13, 110, 253, 0.7)',
            'rgba(25, 135, 84, 0.7)',
            'rgba(255, 193, 7, 0.7)',
            'rgba(220, 53, 69, 0.7)'
        ],
        borderWidth: 1
    }]
};

// Renderizar gráficos
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('vendasChart'), {
        type: 'line',
        data: vendasData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    new Chart(document.getElementById('categoriasChart'), {
        type: 'doughnut',
        data: categoriasData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
</body>
</html>
