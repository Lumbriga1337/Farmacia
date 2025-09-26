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
    <img src="./imagens/logo4.png" alt="Manual" style="height:40px; vertical-align:middle; margin-left:10px;">
</h1>

<div class="container">
    <div class="main">
        <div class="section">
            <h2> Histórico / Log de Versões</h2>
            <table>
                <tr><th>Data</th><th>Versão</th><th>Observações</th></tr>
                <tr><td>2025-09-22</td><td>1.4</td><td>Implementado validação por Token!</td></tr>
                <tr><td>2025-09-19</td><td>1.3</td><td>Em manutenção para atualização do acesso! Adicionado no banco uma nova tabela para armazenamento dos usuarios do WS somente. Sistema de acesso mudará para a necessidade de um cadastro, mediante aprovação minha na solicitaçao, para assim poder ter acesso aos dados! (Previsão de implementação 26/09/2025)</td></tr>
                <tr><td>2025-09-12</td><td>1.2</td><td>Necessária Atualização do acesso! (login e token)</td></tr>
                <tr><td>2025-09-06</td><td>1.1</td><td>Manual completo com todas entidades</td></tr>
                <tr><td>2025-08-01</td><td>1.0</td><td>Versão inicial</td></tr>
            </table>
        </div>
        <div class="section">
            <h2>Introdução</h2>
            <p>
                Este manual de acesso tem como objetivo orientar os clientes sobre como utilizar os serviços disponíveis no sistema ERP. Cada serviço disponibilizado é acessível através de um endpoint específico, que retorna informações em formato JSON. Para acessar os dados, é necessário informar os parâmetros de autenticação fornecidos ( Token) na URL do endpoint.
            </p>
            <p>
                A seguir, detalhamos cada serviço e seu funcionamento:
            </p>
            <ul>
                <li><strong>Funcionarios:</strong> Permite consultar informações dos Funcionarios cadastrados, incluindo nome, CPF e email</li>
                <li><strong>Produtos:</strong> Lista todos os produtos disponíveis no estoque, com detalhes como nome, descrição, categoria, preço e quantidade.</li>
                <li><strong>Categorias:</strong> Exibe as categorias de produtos existentes, ajudando a organizar os itens do sistema.</li>
                <li><strong>Clientes:</strong> Mostra os clientes cadastrados, com dados pessoais e informações de cadastro no sistema.</li>
                <li><strong>Vendas:</strong> Permite acompanhar as vendas realizadas, incluindo data, cliente, valor total e status da venda.</li>
                <li><strong>Itens de Venda:</strong> Detalha os produtos de cada venda, incluindo quantidade, preço unitário e subtotal.</li>
                <li><strong>Receitas:</strong> Lista receitas médicas cadastradas, indicando paciente, médico, data e observações, com o caminho do arquivo PDF.</li>
            </ul>
            <h2>Acesso</h2>
            <p>
                Como funciona o acesso?
            </p>
                <p>
                1- Primeiramente para acessar qualquer serviço, será necessário o cadastro admiministrador,portanto solicite o seu cadastro na seguinte URL: <a href="http://192.168.0.209/ERP/cadastro.php" target="_blank" class="url">http://192.168.0.209/ERP/cadastro.php</a>
                <p>Após o cadastro deve aparecer a seguinte mensagem: "Solicitação de administrador enviada para aprovação!" Após essa confirmação, aguarde aprovação minha para liberar o acesso.</p>
                <p>
                2- Acesso liberado: Com a confirmação minha de liberação de acesso, voce devera agora clicar em login administrador, ao lado do botao cadastrar, na página de cadastro acessada anteriormente. Após acessar a página de login (<a href="http://192.168.0.209/ERP/json/loginadm.php" target="_blank" class="url">http://192.168.0.209/ERP/json/loginadm.php</a>), coloque seu usuário cadastrado e sua senha,  e deve receber um token que valerá por uma hora apenas!.</p>
                <p>
                3- Com o token em mãos acesse o endpoint que deseja obter o Json (produtos, vendas, clientes,ect) e adicione na URL o Token gerado no login admiministrador. Exemplo: "http://192.168.0.209/ERP/json/produtos_json.php?token=db0130067a131507c7e91430f2a81967493f2e5aeb6d5858a2d82611f6f97bd0"
            </p>
            <p>
                Qualquer dúvida de acesso, erro e bugs, favor mandar email para arthur.appel@aluno.unc.br.
            </p>
        </div>
        <div class="section">
            <h2> Dados de acesso</h2>
            <table>
                <tr><th>Parâmetro</th><th>EX:</th></tr<img src="./imagens/Cadastro.png" alt="Manual" ">
                <tr><td>Cadastro</td><td> Nome e senha!</td></tr>
                <tr><td>Aguarde aprovação do cadastro! </td><td> Solicitação de cadastro enviada para administrador</td></tr>
                <tr><td>Login e geração de token </td><td>EX:Insira o usuario cadastrado e façã login. Se validado  </td></tr>
                <tr><td>Cole o token na url</td><td>EX:</td></tr>
            </table>
        </div>
        <div class="section">
            <h2> Serviços disponíveis</h2>

            <div class="card">
                <div class="card-header"> Funcionarios</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/funcionarios_json.php?token=?" target="_blank" class="url">http://192.168.0.209/ERP/json/usuarios_json.php?token=?</a></p>
                    <table>
                        <tr><th>Campo</th><th>Descrição</th></tr>
                        <tr><td>cd_funcionario</td><td>ID do usuário</td></tr>
                        <tr><td>ds_funcionario</td><td>Nome</td></tr>
                        <tr><td>ds_cpf</td><td>CPF</td></tr>
                        <tr><td>ds_email</td><td>Email</td></tr>
                        <tr><td>ds_celular</td><td>Celular</td></tr>
                        <tr><td>ds_endereco</td><td>Endereço</td></tr>
                        <tr><td>ds_senha</td><td>Senha (hash)</td></tr>
                        <tr><td>dt_nascimento</td><td>Data nascimento</td></tr>
                        <tr><td>ds_situacao</td><td>Situação</td></tr>
                    </table>
                    <h4>Exemplo JSON:</h4>
                    <pre>[{"cd_usuario":"2","ds_usuario":"teste","ds_cpf":"333","ds_email":"teste@fdf","ds_celular":"","ds_endereco":"rfsresf","ds_senha":"hash","dt_nascimento":"2000-04-23","ds_situacao":"ativo","tipo_usuario":"cliente"}]</pre>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Produtos</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/produtos_json.php?token=?" target="_blank" class="url">http://192.168.0.209/ERP/json/produtos_json.php?token=?</a></p>
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

            <div class="card">
                <div class="card-header"> Categorias</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/categorias_json.php?token=?" target="_blank" class="url">http://192.168.0.209/ERP/json/categorias_json.php?token=?</a></p>
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

            <div class="card">
                <div class="card-header"> Clientes</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/clientes_json.php?token=?" target="_blank" class="url">http://192.168.0.209/ERP/json/clientes_json.php?token=?</a></p>
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

            <div class="card">
                <div class="card-header"> Vendas</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/vendas_json.php?token=?" target="_blank" class="url">http://192.168.0.209/ERP/json/vendas_json.php?token=?</a></p>
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

            <div class="card">
                <div class="card-header">Itens de Venda</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/itens_venda_json.php?token=?" target="_blank" class="url">http://192.168.0.209/ERP/json/itens_venda_json.php?token=?</a></p>
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

            <div class="card">
                <div class="card-header"> Receitas</div>
                <div class="card-content">
                    <p>Endpoint: <a href="http://192.168.0.209/ERP/json/receitas_json.php?token=?" target="_blank" class="url">http://192.168.0.209/ERP/json/receitas_json.php?token=?</a></p>
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
