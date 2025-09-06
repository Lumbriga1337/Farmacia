<?php 

$json = file_get_contents("http://192.168.0.200/json/produtos_ws.php?cdu=1&pwd=senha");
$obj = json_decode($json);

if (json_last_error() != 0) {
    echo 'OCORREU UM ERRO!</br>';
	switch (json_last_error()) {

        case JSON_ERROR_DEPTH:
            echo ' - profundidade maxima excedida';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Erro de sintaxe genÃ©rico';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Caracter de controle encontrado';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Erro de sintaxe! String JSON mal-formatado!';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Erro na codificaÃ§Ã£o UTF-8';
        break;
        default:
            echo ' â€“ Erro desconhecido';
        break;
    }
}

if (count($obj)>0)
{
	$cone = mysqli_connect("localhost", "root", "","sistema"); 
	mysqli_set_charset($cone, "utf8");
	$i=0;
	echo "<br>Dados do Json: ".count($obj)."<br><table>";
	while ($i<count($obj))
	{
		echo "<tr><td>".$obj[$i]->cd_produto."</td>";
		echo "<td>".$obj[$i]->ds_produto."</td>";
		echo "<td>".$obj[$i]->ds_unidade."</td>";
		echo "<td>".$obj[$i]->vl_custo."</td>";
		
		$SQL  = "Insert into produtos (cd_codigo,ds_produto,ds_unidade,vl_custo,ds_origem) values (";
		$SQL .= "'".$obj[$i]->cd_produto."',";
		$SQL .= "'".$obj[$i]->ds_produto."',";
		$SQL .= "'".$obj[$i]->ds_unidade."',";
		$SQL .= "'".$obj[$i]->vl_custo."',";
		$SQL .= "'Max')";
		$cone->query($SQL);
		echo "<td>$SQL</td></tr>";
		$i++;
	}
	echo "</table>";
	echo "$i inseridos";
}
else
{
	echo "Json Vazio ou com Problema";
}
?> 