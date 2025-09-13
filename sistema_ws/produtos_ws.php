<?php 
header('Content-type:Application/json');

include "autoriza.php";

if (isset($RS["cd_uws"]))
{
	$cone->query("update user_ws set nr_acesso = nr_acesso + 1, dt_acesso = now(),ip_atual = '".$_SERVER["REMOTE_ADDR"]."',ds_entidade='PRODUTOS' where cd_uws = ".$RS["cd_uws"]);
} else { echo "Erro de autorização ";  exit(); } 

$linhas = array();
$rs = $cone->query("Select * from produtos");
while($row = $rs->fetch_array()) 
{ 
  $cd_produto	= $row['cd_produto']; 
  $ds_produto	= $row['ds_produto']; 
  $ds_unidade	= $row['ds_unidade']; 
  $vl_custo		= $row['vl_custo']; 
  $linhas[] = array('cd_produto'=> $cd_produto, 'ds_produto'=> $ds_produto, 'ds_unidade'=> $ds_unidade, 'vl_custo'=> $vl_custo);
} 
$json_string = json_encode($linhas);
echo $json_string;
?> 