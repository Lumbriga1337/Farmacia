<?php 
if (isset($_REQUEST["token"])) 	 
{ 
	$token = $_REQUEST["token"]; 
	if (strlen($token)!=8) { echo "Token INVALIDO!"; exit();  }
	$cone = mysqli_connect("localhost", "root", "","farmacia"); 
	mysqli_set_charset($cone, "utf8");
	$SQL = "Select *,TIMESTAMPDIFF(MINUTE,now(),dt_token) as tempotoken from user_ws where ds_token = '".$_REQUEST["token"]."' ";
	$RSS = $cone->query($SQL);
	$RS = $RSS->fetch_assoc();	
	if (!isset($RS["cd_uws"])) { echo "Token Invalido."; exit(); }	
//	if ($RS["tempotoken"]<0) { echo "Token Expirado. Faça novo token."; exit(); }	
}
else
{
	if (!isset($_REQUEST["ds_cpf"])) 	 { echo "Precisa informar o CPF!"; exit(); }
	if (strlen($_REQUEST["ds_cpf"])!=11) { echo "CPF deve ter 11 digitos!"; exit();  }
	if (strlen($_REQUEST["ds_senha"])<3) { echo "Senha com mais de 3 digitos!"; exit();  }
	if ( (strlen($_REQUEST["ds_cpf"])!=11) || (strlen($_REQUEST["ds_senha"])<2) ) { echo "Usuario / Senha invalido"; exit(); }
	$cone = mysqli_connect("localhost", "root", "","farmacia"); 
	mysqli_set_charset($cone, "utf8");
	$SQL = "Select * from user_arthur where ds_cpf = '".$_REQUEST["ds_cpf"]."' and ds_senha = '".$_REQUEST["ds_senha"]."'";
	$RSS = $cone->query($SQL);
	$RS = $RSS->fetch_assoc();	
	if (!isset($RS["cd_uws"])) { echo "Usuario / Senha ERRADO	"; exit(); }
}
?> 