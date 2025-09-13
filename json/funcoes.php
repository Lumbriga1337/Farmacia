<?php
// funcoes.php

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
