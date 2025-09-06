<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf(['enable_remote' => true]);

// Exemplo de dados (substituir pela busca no banco)
$venda = [
    'numero' => '000123',
    'serie' => '1',
    'data' => '25/06/2025 00:00',
    'empresa' => [
        'nome' => 'Farmácia BIQUEIRA',
        'cnpj' => '00.000.000/0001-00',
        'ie' => '123456789',
        'endereco' => 'Rua Exemplo, 123 - Centro - Cidade - UF',
        'fone' => '(11) 99999-9999'
    ],
    'cliente' => [
        'nome' => 'João da Silva',
        'cpf' => '000.000.000-00',
        'endereco' => 'Rua Cliente, 456 - Bairro - Cidade - UF',
        'fone' => '(11) 98888-8888'
    ],
    'produtos' => [
        ['codigo' => '001', 'descricao' => 'XAROPE', 'ncm' => '30049099', 'cfop' => '5102', 'qtd' => 4, 'unitario' => 12.00],
        ['codigo' => '002', 'descricao' => 'PARACETAMOL', 'ncm' => '30045090', 'cfop' => '5102', 'qtd' => 2, 'unitario' => 8.50]
    ]
];

$total = 0;
foreach ($venda['produtos'] as $p) {
    $total += $p['qtd'] * $p['unitario'];
}

$html = '
<style>
body { font-family: Arial, sans-serif; font-size: 10pt; }
table { border-collapse: collapse; width: 100%; }
th, td { border: 1px solid #000; padding: 4px; }
.titulo { font-size: 16pt; font-weight: bold; text-align: center; }
.subtitulo { font-size: 9pt; text-align: center; }
.bloco { margin-top: 5px; }
</style>

<div class="titulo">DANFE</div>
<div class="subtitulo">Documento Auxiliar da Nota Fiscal Eletrônica - Não é válido como documento fiscal</div>

<!-- Cabeçalho -->
<table>
    <tr>
        <td style="width: 50%;"><b>'.$venda['empresa']['nome'].'</b><br>
            CNPJ: '.$venda['empresa']['cnpj'].'<br>
            IE: '.$venda['empresa']['ie'].'<br>
            Endereço: '.$venda['empresa']['endereco'].'<br>
            Fone: '.$venda['empresa']['fone'].'
        </td>
        <td style="width: 50%;">
            <b>Nº NF:</b> '.$venda['numero'].'<br>
            <b>Série:</b> '.$venda['serie'].'<br>
            <b>Data Emissão:</b> '.$venda['data'].'
        </td>
    </tr>
</table>

<!-- Destinatário -->
<div class="bloco">
    <table>
        <tr>
            <td><b>Destinatário:</b> '.$venda['cliente']['nome'].'</td>
            <td><b>CPF:</b> '.$venda['cliente']['cpf'].'</td>
        </tr>
        <tr>
            <td colspan="2"><b>Endereço:</b> '.$venda['cliente']['endereco'].' - Fone: '.$venda['cliente']['fone'].'</td>
        </tr>
    </table>
</div>

<!-- Produtos -->
<div class="bloco">
    <table>
        <tr>
            <th>Cód</th>
            <th>Descrição</th>
            <th>NCM</th>
            <th>CFOP</th>
            <th>Qtd</th>
            <th>Valor Unit.</th>
            <th>Subtotal</th>
        </tr>';

foreach ($venda['produtos'] as $p) {
    $html .= '<tr>
        <td>'.$p['codigo'].'</td>
        <td>'.$p['descricao'].'</td>
        <td>'.$p['ncm'].'</td>
        <td>'.$p['cfop'].'</td>
        <td>'.$p['qtd'].'</td>
        <td>R$ '.number_format($p['unitario'], 2, ',', '.').'</td>
        <td>R$ '.number_format($p['qtd'] * $p['unitario'], 2, ',', '.').'</td>
    </tr>';
}

$html .= '
    </table>
</div>

<!-- Totais -->
<div class="bloco">
    <table>
        <tr>
            <td><b>Total Produtos:</b> R$ '.number_format($total, 2, ',', '.').'</td>
            <td><b>ICMS:</b> R$ 0,00</td>
            <td><b>IPI:</b> R$ 0,00</td>
            <td><b>Total da Nota:</b> R$ '.number_format($total, 2, ',', '.').'</td>
        </tr>
    </table>
</div>

<!-- Rodapé -->
<div class="bloco" style="font-size:8pt;">
    Informações Complementares: Documento gerado para fins de controle interno.  
    Este DANFE é apenas ilustrativo e não substitui a NF-e autorizada pela SEFAZ.
</div>
';

// Carregar HTML no Dompdf
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('nota_fiscal.pdf', ['Attachment' => false]);
