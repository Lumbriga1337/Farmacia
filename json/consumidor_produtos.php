<?php
echo "<h2>Consumidor de WebService - Produtos</h2>";

$url = "http://192.168.0.200/json/produtos_ws.php?cdu=1&pwd=senha";

$json = file_get_contents($url);
$obj = json_decode($json_string);

if (json_last_error() != 0) {
    echo "OCORREU UM ERRO!</br>";

    switch (json_last_error()) {
        case JSON_ERROR_DEPTH:
            echo " - Profundidade máxima excedida";
            break;
        case JSON_ERROR_STATE_MISMATCH:
            echo " - Erro de sintaxe genérico ou modos incompatíveis";
            break;
        case JSON_ERROR_CTRL_CHAR:
            echo " - Caractere de controle inesperado encontrado";
            break;
        case JSON_ERROR_SYNTAX:
            echo " - Erro de sintaxe! String JSON mal-formatada!";
            break;
        case JSON_ERROR_UTF8:
            echo " - Erro na codificação UTF-8";
            break;
        default:
            echo " - Erro desconhecido";
            break;
    }
} else {
    echo "- Não houve erro! O parsing foi perfeito.";
}


if (count($obj) > 0) {
    $cone = mysqli_connect("localhost", "root", "", "farmacia");
    mysqli_set_charset($cone, "utf8");

    echo "<br>Dados recebidos: " . count($obj) . "<br><table border='1'>";
    foreach ($obj as $produto) {
        echo "<tr>
                <td>{$produto->id}</td>
                <td>{$produto->nome}</td>
                <td>{$produto->descricao}</td>
                <td>{$produto->categoria_id}</td>
                <td>{$produto->preco}</td>
                <td>{$produto->quantidade}</td>
                <td>{$produto->estoque_minimo}</td>
                <td>{$produto->data_cadastro}</td>
              </tr>";

        $sql  = "INSERT INTO produtos 
                (id, nome, descricao, categoria_id, preco, quantidade, estoque_minimo, data_cadastro) VALUES (";
        $sql .= "'{$produto->id}',";
        $sql .= "'{$produto->nome}',";
        $sql .= "'{$produto->descricao}',";
        $sql .= "'{$produto->categoria_id}',";
        $sql .= "'{$produto->preco}',";
        $sql .= "'{$produto->quantidade}',";
        $sql .= "'{$produto->estoque_minimo}',";
        $sql .= "'{$produto->data_cadastro}')";
        $cone->query($sql);
    }
    echo "</table>";
    echo "<br>Importação concluída.";
} else {
    echo "Nenhum dado recebido.";
}
?>
