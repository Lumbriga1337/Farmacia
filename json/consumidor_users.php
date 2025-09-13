<?php
include "funcoes.php";

// Simulação de leitura do JSON vindo do webservice
$json_string = file_get_contents("http://localhost/farmacia/json/usuarios.json");

// Valida o JSON antes de usar
if (validar_json($json_string)) {
    $obj = json_decode($json_string);

    echo "<h2>Lista de Usuários</h2>";
    echo "<table border=1 cellpadding=5>";
    echo "<tr><th>ID</th><th>Usuário</th><th>Email</th><th>CPF</th></tr>";

    foreach ($obj as $linha) {
        echo "<tr>";
        echo "<td>".$linha->cd_usuario."</td>";
        echo "<td>".$linha->ds_usuario."</td>";
        echo "<td>".$linha->ds_email."</td>";
        echo "<td>".$linha->ds_cpf."</td>";
        echo "</tr>";
    }

    echo "</table>";
}
?>
