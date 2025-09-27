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

    // ✅ Atualiza contagem de acessos se o token for válido
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $stmtUpdate = $conn->prepare("
        UPDATE administradores 
        SET nr_acesso = nr_acesso + 1, dt_acesso = NOW(), ip_atual = ? 
        WHERE cd_adm = ?
    ");
    $stmtUpdate->bind_param("si", $ip, $adm['cd_adm']);
    $stmtUpdate->execute();
    $stmtUpdate->close();

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

function registrarLogSaida($conn, $adm, $nr_registros) {
    // Valida o array do admin
    if (!is_array($adm) || !isset($adm['cd_adm'], $adm['ds_nome'], $adm['ds_entidade'])) {
        error_log("Admin inválido, log não registrado");
        return false;
    }

    // IP do usuário que acessou
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    // Prepara o statement
    $stmt = $conn->prepare("
        INSERT INTO log_saida 
        (cd_codigo, dt_log, ds_entidade, ds_nome, ip_atual, nr_registros) 
        VALUES (?, NOW(), ?, ?, ?, ?)
    ");

    if (!$stmt) {
        error_log("Erro ao preparar statement: " . $conn->error);
        return false;
    }

    // Associa os parâmetros
    $stmt->bind_param(
        "isssi",
        $adm['cd_adm'],
        $adm['ds_entidade'],
        $adm['ds_nome'],
        $ip,
        $nr_registros
    );

    // Executa e fecha
    if (!$stmt->execute()) {
        error_log("Erro ao executar statement: " . $stmt->error);
        return false;
    }

    $stmt->close();
    return true;
}



