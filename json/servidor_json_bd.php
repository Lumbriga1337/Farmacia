<?php 
$cone = mysqli_connect("localhost", "root", "","farmacia"); 
mysqli_set_charset($cone, "utf8");

$linhas = array();
$rs = $cone->query("Select * from usuarios");
while($row = $rs->fetch_array()) 
{ 
  $cd_usuarios	= $row['cd_usuario']; 
  $ds_usuarios	= $row['ds_usuario']; 
  $ds_email	= $row['ds_email']; 
  $ds_cpf	= $row['ds_cpf']; 
  $linhas[] = array('cd_usuario'=> $cd_usuarios, 'ds_usuarios'=> $ds_usuarios, 'ds_email'=> $ds_email, 'ds_cpf'=> $ds_cpf);
} 

$json_string = json_encode($linhas);
echo $json_string;
?> 