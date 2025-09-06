<!-- navbar.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container-fluid">
    <!-- Logo -->
    <a class="navbar-brand" href="index.php">
      <img src="imagens/logo4.png" alt="ERP Farmácia" style="max-height: 30px;">
    </a>

    <!-- Botão toggle para mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-controls="offcanvasMenu" aria-label="Abrir menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu desktop normal -->
    <div class="collapse navbar-collapse" id="menuERP">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="produtos.php">Cadastrar Produtos</a></li>
        <li class="nav-item"><a class="nav-link" href="vendas.php">Registrar Venda</a></li>
        <li class="nav-item"><a class="nav-link" href="relatorios.php">Relatórios</a></li>
        <li class="nav-item"><a class="nav-link" href="cadastro.php">Cadastrar</a></li>
        <li class="nav-item"><a class="nav-link" href="estoque.php">Editar Estoque</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Offcanvas menu para mobile -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
  <div class="offcanvas-header bg-primary text-white">
    <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menu</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" href="produtos.php">Cadastrar Produtos</a></li>
      <li class="nav-item"><a class="nav-link" href="vendas.php">Registrar Venda</a></li>
      <li class="nav-item"><a class="nav-link" href="relatorios.php">Relatórios</a></li>
      <li class="nav-item"><a class="nav-link" href="cadastro.php">Cadastrar</a></li>
      <li class="nav-item"><a class="nav-link" href="estoque.php">Editar Estoque</a></li>
    </ul>
  </div>
</div>

<!-- CSS personalizado -->
<style>
.navbar {
  height: 50px;
  padding-right: 30px !important;
}

.navbar-nav .nav-link {
  font-size: 16px;
  font-weight: 600;
  color: #ffffffff !important;
  margin-left: 20px;
  letter-spacing: 0.5px;
  transition: color 0.2s ease-in-out;
}

.navbar-nav .nav-link:hover {
  color: #767676ff !important;
}

/* Ajustes para o offcanvas menu */
.offcanvas {
  width: 220px !important;
}

.offcanvas .nav-link {
  color: #000 !important;
  font-weight: 600;
  font-size: 18px;
  padding: 10px 0;
}

.offcanvas .nav-link:hover {
  color: #007bff !important;
}

/* Header offcanvas custom */
.offcanvas-header {
  height: 50px;
  align-items: center;
  padding-left: 1rem;
  padding-right: 1rem;
}
</style>

<!-- NÃO esqueça de incluir o JS do Bootstrap no seu projeto! -->
<!-- Exemplo (caso ainda não tenha): -->
<!-- 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
-->
