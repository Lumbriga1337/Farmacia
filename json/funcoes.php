<?php
// funcoes.php
include '../db.php';

function validarToken($conn, $token) {
    if ($token === '') {
        return ["erro" => "Token não informado"];
    }

    $stmt = $conn->prepare("SELECT cd_adm, dt_token FROM administradores WHERE ds_token = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        return ["erro" => "Token inválido"];
    }

    $adm = $res->fetch_assoc();
    $dt_expira = strtotime($adm['dt_token']);
    $agora     = time();

    if ($agora > $dt_expira) {
        return ["erro" => "Token expirado"];
    }

    return true; // Token válido
}

function gerarJson($conn, $tabela, $campos = "*") {
    mysqli_set_charset($conn, "utf8");

    $sql = "SELECT $campos FROM $tabela";
    $rs = $conn->query($sql);

    $linhas = [];
    while ($row = $rs->fetch_assoc()) {
        $linhas[] = $row;
    }

    return $linhas;
}

function validar_json($json_string) {
    json_decode($json_string);
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            return true;
        case JSON_ERROR_DEPTH:
            die("❌ Erro: Profundidade máxima excedida");
        case JSON_ERROR_STATE_MISMATCH:
            die("❌ Erro: Estado inválido ou sintaxe incorreta");
        case JSON_ERROR_CTRL_CHAR:
            die("❌ Erro: Caractere de controle inesperado");
        case JSON_ERROR_SYNTAX:
            die("❌ Erro: Sintaxe inválida no JSON");
        case JSON_ERROR_UTF8:
            die("❌ Erro: Problema de codificação UTF-8");
        default:
            die("❌ Erro: JSON desconhecido");
    }
}
?>
