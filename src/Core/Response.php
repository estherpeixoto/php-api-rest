<?php

namespace App\Core;

class Response
{
    // Envia uma resposta JSON padronizada
    public static function json(array $response)
    {
        // Define o código de status HTTP
        http_response_code($response['status']);

        // Define o tipo de conteúdo da resposta
        header('Content-Type: application/json; charset=UTF-8');

        // Converte os dados para JSON e envia a resposta
        echo json_encode(
            $response,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        // Encerra a execução
        exit;
    }
}
