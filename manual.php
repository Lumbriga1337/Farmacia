<?php
// manual.php

header("Content-Type: application/json; charset=UTF-8");

// Dados do manual
$data = [
    "Manual" => "Biqueira ERP",
    "Historico" => [
        [
            "Dia" => "22/08/2025 19:24",
            "Acao" => "Criação da versão 1",
            "Usuario" => "Biqueira"
        ],
        [
            "Dia" => "29/08/2025 21:24",
            "Acao" => "Melhorias",
            "Usuario" => "Biqueira"
        ]
    ],
    "Servidores" => [
        [
            "cdu" => 9,
            "Servidor" => "Arthur",
            "Endereco" => "192.168.0.209"
        ]
    ],
    "Sobre" => "Este documento é um manual de acesso e informativo para a conexão da Biqueira.",
    "Entidades" => [
        "Clientes",
        "Categorias",
        "Itens_venda",
        "Produtos",
        "Receitas",
        "Usuarios",
        "Vendas"
    ],
    "Funcionalidades" => [
        "Cadastro de usuários para acesso",
        "Controle de acesso por usuários",
        "Controle de acesso por token",
        "Controle de log",
        "Gestão de CRUD",
        "Relatórios de acesso"
    ]
];

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
