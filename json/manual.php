<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Manual de Acesso - WebService</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: azure;
    margin: 20px;
    color: #333;
}
h1, h2, h3 {
    color: #1a73bc;
}
h1 {
    text-align: center;
    margin-bottom: 30px;
}
.container {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}
.main { 
    flex: 2; 
}

.section {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.card {
    border-radius: 8px;
    margin-bottom: 15px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}
.card:hover { transform: translateY(-2px); }
.card-header {
    padding: 12px 15px;
    cursor: pointer;
    color: #000000ff;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 10px;
    background-color: #ffffffff;
}
.card-content {
    display: none;
    padding: 15px;
    background: #ecf0f1;
    font-size: 14px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
    background-color: #eaf3fc;
}
th, td {
    border: 1px solid #1a73bc;
    padding: 6px;
    text-align: left;
}
th { background-color: #1a73bc; color: white; }
pre {
    background: #fff;
    padding: 10px;
    border-radius: 5px;
    overflow-x: auto;
    font-size: 13px;
}
a.url { color: #1a73bc; font-weight: bold; text-decoration: none; }
a.url:hover { text-decoration: underline; }
</style>
</head>
<body>

<h1>
    Manual de Acesso á
    <img src="../imagens/logo4.png" alt="Manual" style="height:40px; vertical-align:middle; margin-left:10px;">
</h1>

<div class="container">
    <!-- Conteúdo principal -->
    <div class="main">
        <!-- Histórico -->
        <div class="section">
            <h2>📄 Histórico / Log de Versões</h2>
            <table>
                <tr><th>Data</th><th>Versão</th><th>Observações</th></tr>
                <tr><td>2025-09-06</td><td>1.5</td><td>Manual completo com todas entidades</td></tr>
                <tr><td>2025-08-01</td><td>1.0</td><td>Versão inicial</td></tr>
            </table>
        </div>

        <!-- Dados de acesso -->
        <div class="section">
            <h2>🔐 Dados de acesso</h2>
            <table>
                <tr><th>Parâmetro</th><th>Valor</th></tr>
                <tr><td>CDU</td><td>9</td></tr>
                <tr><td>Senha</td><td>9</td></tr>
            </table>
        </div>

        <!-- Serviços disponíveis -->
        <div class="section">
            <h2>🌐 Serviços disponíveis</h2>

            <!-- Usuários -->
            <div class="card">
                <div class="card-header">👤 Usuários</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/usuarios_json.php?cdu=9&pwd=9" target="_blank" class="url">http://192.168.0.209/ERP/json/usuarios_json.php?cdu=9&pwd=9</a></p>
                    <table>
                        <tr><th>Campo</th><th>Descrição</th></tr>
                        <tr><td>cd_usuario</td><td>ID do usuário</td></tr>
                        <tr><td>ds_usuario</td><td>Nome</td></tr>
                        <tr><td>ds_cpf</td><td>CPF</td></tr>
                        <tr><td>ds_email</td><td>Email</td></tr>
                        <tr><td>ds_celular</td><td>Celular</td></tr>
                        <tr><td>ds_endereco</td><td>Endereço</td></tr>
                        <tr><td>ds_senha</td><td>Senha (hash)</td></tr>
                        <tr><td>dt_nascimento</td><td>Data nascimento</td></tr>
                        <tr><td>ds_situacao</td><td>Situação</td></tr>
                        <tr><td>tipo_usuario</td><td>Tipo</td></tr>
                    </table>
                    <h4>Exemplo JSON:</h4>
                    <pre>[{"cd_usuario":"2","ds_usuario":"teste","ds_cpf":"333","ds_email":"teste@fdf","ds_celular":"","ds_endereco":"rfsresf","ds_senha":"hash","dt_nascimento":"2000-04-23","ds_situacao":"ativo","tipo_usuario":"cliente"}]</pre>
                </div>
            </div>

            <!-- Produtos -->
            <div class="card">
                <div class="card-header">💊 Produtos</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/produtos_json.php?cdu=9&pwd=9" target="_blank" class="url">http://192.168.0.209/ERP/json/produtos_json.php?cdu=9&pwd=9</a></p>
                    <table>
                        <tr><th>Campo</th><th>Descrição</th></tr>
                        <tr><td>id</td><td>ID do produto</td></tr>
                        <tr><td>nome</td><td>Nome</td></tr>
                        <tr><td>descricao</td><td>Descrição</td></tr>
                        <tr><td>categoria_id</td><td>ID da categoria</td></tr>
                        <tr><td>preco</td><td>Preço unitário</td></tr>
                        <tr><td>quantidade</td><td>Quantidade em estoque</td></tr>
                        <tr><td>estoque_minimo</td><td>Estoque mínimo</td></tr>
                        <tr><td>data_cadastro</td><td>Data de cadastro</td></tr>
                    </table>
                    <h4>Exemplo JSON:</h4>
                    <pre>[{"id":"1","nome":"Dipirona","descricao":"Analgésico","categoria_id":"2","preco":"5.50","quantidade":"100","estoque_minimo":"10","data_cadastro":"2025-09-06 10:00:00"}]</pre>
                </div>
            </div>

            <!-- Categorias -->
            <div class="card">
                <div class="card-header">🏷️ Categorias</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/categorias_json.php?cdu=9&pwd=9" target="_blank" class="url">http://192.168.0.209/ERP/json/categorias_json.php?cdu=9&pwd=9</a></p>
                    <table>
                        <tr><th>Campo</th><th>Descrição</th></tr>
                        <tr><td>id</td><td>ID da categoria</td></tr>
                        <tr><td>nome</td><td>Nome da categoria</td></tr>
                        <tr><td>descricao</td><td>Descrição</td></tr>
                    </table>
                    <h4>Exemplo JSON:</h4>
                    <pre>[{"id":"1","nome":"Analgesicos","descricao":"Medicamentos para dor"}]</pre>
                </div>
            </div>

            <!-- Clientes -->
            <div class="card">
                <div class="card-header">👥 Clientes</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/clientes_json.php?cdu=9&pwd=9" target="_blank" class="url">http://192.168.0.209/ERP/json/clientes_json.php?cdu=9&pwd=9</a></p>
                    <table>
                        <tr><th>Campo</th><th>Descrição</th></tr>
                        <tr><td>id</td><td>ID do cliente</td></tr>
                        <tr><td>nome_cliente</td><td>Nome do cliente</td></tr>
                        <tr><td>cpf_cliente</td><td>CPF</td></tr>
                        <tr><td>email_cliente</td><td>Email</td></tr>
                        <tr><td>celular_cliente</td><td>Telefone celular</td></tr>
                        <tr><td>endereco_cliente</td><td>Endereço</td></tr>
                        <tr><td>data_nascimento_cliente</td><td>Data de nascimento</td></tr>
                        <tr><td>data_cadastro</td><td>Data de cadastro</td></tr>
                        <tr><td>cd_usuario</td><td>ID do usuário relacionado</td></tr>
                    </table>
                    <h4>Exemplo JSON:</h4>
                    <pre>[{"id":"1","nome_cliente":"João da Silva","cpf_cliente":"12345678900","email_cliente":"teste@gmail.com","celular_cliente":"11999999999","endereco_cliente":"Rua A, 123","data_nascimento_cliente":"2000-05-24","data_cadastro":"2025-09-05","cd_usuario":"06"}]</pre>
                </div>
            </div>

            <!-- Vendas -->
            <div class="card">
                <div class="card-header">🛒 Vendas</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/vendas_json.php?cdu=9&pwd=9" target="_blank" class="url">http://192.168.0.209/ERP/json/vendas_json.php?cdu=9&pwd=9</a></p>
                    <table>
                        <tr><th>Campo</th><th>Descrição</th></tr>
                        <tr><td>id_venda</td><td>ID da venda</td></tr>
                        <tr><td>cliente_id</td><td>ID do cliente</td></tr>
                        <tr><td>data_venda</td><td>Data da venda</td></tr>
                        <tr><td>valor_total</td><td>Valor total da venda</td></tr>
                        <tr><td>status</td><td>Status da venda (Pago, Pendente, Cancelado)</td></tr>
                    </table>
                    <h4>Exemplo JSON:</h4>
                    <pre>[{"id_venda":"10","cliente_id":"1","data_venda":"2025-09-06","valor_total":"150.00","status":"Pago"}]</pre>
                </div>
            </div>

            <!-- Itens de Venda -->
            <div class="card">
                <div class="card-header">📦 Itens de Venda</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/itens_venda_json.php?cdu=9&pwd=9" target="_blank" class="url">http://192.168.0.209/ERP/json/itens_venda_json.php?cdu=9&pwd=9</a></p>
                    <table>
                        <tr><th>Campo</th><th>Descrição</th></tr>
                        <tr><td>id_item</td><td>ID do item</td></tr>
                        <tr><td>id_venda</td><td>ID da venda relacionada</td></tr>
                        <tr><td>id_produto</td><td>ID do produto</td></tr>
                        <tr><td>quantidade</td><td>Quantidade vendida</td></tr>
                        <tr><td>preco_unitario</td><td>Preço unitário</td></tr>
                        <tr><td>subtotal</td><td>Subtotal do item</td></tr>
                    </table>
                    <h4>Exemplo JSON:</h4>
                    <pre>[{"id_item":"1","id_venda":"10","id_produto":"1","quantidade":"2","preco_unitario":"5.50","subtotal":"11.00"}]</pre>
                </div>
            </div>

            <!-- Receitas -->
            <div class="card">
                <div class="card-header">📄 Receitas</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/receitas_json.php?cdu=9&pwd=9" target="_blank" class="url">http://192.168.0.209/ERP/json/receitas_json.php?cdu=9&pwd=9</a></p>
                    <table>
                        <tr><th>Campo</th><th>Descrição</th></tr>
                        <tr><td>id</td><td>ID da receita</td></tr>
                        <tr><td>cliente_id</td><td>ID do cliente</td></tr>
                        <tr><td>paciente</td><td>Nome do paciente</td></tr>
                        <tr><td>medico</td><td>Nome do médico</td></tr>
                        <tr><td>data_receita</td><td>Data da receita</td></tr>
                        <tr><td>arquivo_path</td><td>Caminho do arquivo PDF</td></tr>
                        <tr><td>observacoes</td><td>Observações</td></tr>
                        <tr><td>data_cadastro</td><td>Data de cadastro</td></tr>
                    </table>
                    <h4>Exemplo JSON:</h4>
                    <pre>[{"id":"1","cliente_id":"1","paciente":"Arthur","medico":"Dr. José","data_receita":"2025-09-01","arquivo_path":"receitas/receita_685a2cfcdf61f5.15222338.pdf","observacoes":"teste pdf","data_cadastro":"2025-06-24 01:43:40"}]</pre>
                </div>
            </div>

        </div>
    </div>
<script>
const headers = document.querySelectorAll('.card-header');
headers.forEach(header => {
    header.addEventListener('click', () => {
        const content = header.nextElementSibling;
        content.style.display = content.style.display === 'block' ? 'none' : 'block';
    });
});
</script>

</body>
</html>
