<?php 
$ds_cpf = "";   if (isset($_POST["ds_cpf"]))   { $ds_cpf   = $_POST["ds_cpf"]; }
$ds_nome = "";  if (isset($_POST["ds_nome"]))  { $ds_nome  = $_POST["ds_nome"]; }
$ds_senha = ""; if (isset($_POST["ds_senha"])) { $ds_senha = $_POST["ds_senha"]; }

if (strlen($ds_cpf)==11)
{
	$cone = mysqli_connect("localhost", "root", "","sistema"); 
	mysqli_set_charset($cone, "utf8");
	$RSS = $cone->query("Select * from user_ws where ds_cpf = '$ds_cpf' ");
	$RS = $RSS->fetch_assoc();	
	$ds_token = rand(10011001,99999998);
	if (isset($RS["ds_cpf"])) 
	{ $SQL = "update user_ws set ds_nome='$ds_nome',ds_senha='$ds_senha',ds_ip='".$_SERVER["REMOTE_ADDR"]."',ds_token='$ds_token',dt_token=DATE_ADD(now(), INTERVAL 1 HOUR) where ds_cpf='$ds_cpf'"; $cone->query($SQL); }
	else 
	{ $cone->query("insert into user_ws (ds_nome,ds_senha,ds_cpf,ds_ip,ds_token,dt_token) 	values ('$ds_nome','$ds_senha','$ds_cpf','".$_SERVER["REMOTE_ADDR"]."','$ds_token',DATE_ADD(now(), INTERVAL 1 HOUR))"); }
	echo "<center><b>$ds_nome</b> inserido. TOKEN <b>$ds_token</b></center>";
}
/*
$RSS = $cone->query($SQL);
$RS = $RSS->fetch_assoc();	
*/

echo "<html>";
echo "<body>";

echo "<form action='cadastro.php' method='post'><table>";
echo "<tr><td>CPF</td><td><input type='text' id='ds_cpf' name='ds_cpf'></td></tr>";
echo "<tr><td>Nome</td><td><input type='text' id='ds_nome' name='ds_nome'></td></tr>";
echo "<tr><td>Senha</td><td><input type='text' id='ds_senha' name='ds_senha'></td></tr>";
echo "<tr><td></td><td><input type='submit' value='Salvar'></td></tr>";
echo "</form>";
echo "</body>";
echo "</html>";
?> 