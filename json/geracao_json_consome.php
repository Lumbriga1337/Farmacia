<?php 

echo "Passo 1 - conectando ao banco<br><br>";

$cone = mysqli_connect("localhost", "root", "","farmacia"); 
mysqli_set_charset($cone, "utf8");

echo "Passo 2 - montando a variavel json<br><br>";

$linhas = array();
$rs = $cone->query("Select * from usuarios");
while($row = $rs->fetch_array()) 
{ 
  $cd_usuario	= $row['cd_usuario']; 
  $ds_usuario	= $row['ds_usuario']; 
  $ds_email	= $row['ds_email']; 
  $ds_cpf	= $row['ds_cpf']; 
  $linhas[] = array('cd_usuario'=> $cd_usuario, 'ds_usuario'=> $ds_usuario, 'ds_email'=> $ds_email, 'ds_cpf'=> $ds_cpf);
} 

echo "Passo 3 - salvando o arquivo json - só para salvar<br><br>";

$fp = fopen('cadastros.json', 'w');
fwrite($fp, json_encode($linhas));
fclose($fp);

echo "Passo 4 - codificando a variavel json<br><br>";

$json_string = json_encode($linhas);

echo "Passo 5 - JSON em analise e decodificação<br><br>";

$obj = json_decode($json_string);
if (json_last_error() == 0) {
    echo '- Nao houve erro! O parsing foi perfeito';
}
else {
    echo 'OCORREU UM ERRO!</br>';
	switch (json_last_error()) {

        case JSON_ERROR_DEPTH:
            echo ' - profundidade maxima excedida';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Erro de sintaxe genérico';
        break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Caracter de controle encontrado';
        break;
        case JSON_ERROR_SYNTAX:
            echo ' - Erro de sintaxe! String JSON mal-formatado!';
        break;
        case JSON_ERROR_UTF8:
            echo ' - Erro na codificação UTF-8';
        break;
        default:
            echo ' – Erro desconhecido';
        break;
    }
}

echo "<br><br>Passo 6 - Mostando os dados recebido no json<br>";

$i=0;
echo "<br>Dados do Json: ".count($obj)."<br><table>";
while ($i<count($obj))
{
	echo "<tr><td>".$obj[$i]->cd_usuario."</td>";
	echo "<td>".$obj[$i]->ds_usuario."</td>";
	echo "<td>".$obj[$i]->ds_email."</td>";
	echo "<td>".$obj[$i]->ds_cpf."</td></tr>";
	$i++;
}
echo "</table>";
?> 