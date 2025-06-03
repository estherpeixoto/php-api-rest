<?php

namespace App\Core;

class Request
{
    // Método responsável por ler o corpo da requisição e retornar os dados como array
    public static function getJson(): array
    {
        // Lê os dados brutos enviados no corpo da requisição
        $input = file_get_contents('php://input');

        // Converte o JSON em um array associativo
        $data = json_decode($input, true);

        // Se a conversão deu certo, retorna os dados
        // Caso contrário, retorna um array vazio
        return is_array($data) ? $data : [];
    }
}
