<?php

namespace App\Core;

class Requests
{
    /**
     * Faz uma requisição HTTP usando cURL para qualquer API externa (por enquanto suporta apenas GET)
     *
     * @TODO Implementar suporte para outros métodos HTTP
     *
     * @param string $method O método HTTP (GET, POST, PUT, DELETE, etc.)
     * @param string $url    A URL da API externa
     *
     * @return array Retorna um array com 'status', 'body' e 'error' (se houver)
     */
    public static function send(string $method, string $url): array
    {
        // Inicia o cURL
        $ch = curl_init();

        // Define a URL de destino
        curl_setopt($ch, CURLOPT_URL, $url);

        // Define o método HTTP
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        // Define que queremos capturar a resposta em vez de exibir diretamente
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Desativa a verificação de SSL (localhost não tem HTTPS por padrão)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Executa a requisição
        $responseBody = curl_exec($ch);

        // Captura status HTTP
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Captura erros de cURL (ex: falha de conexão ou timeout)
        $error = curl_error($ch);

        // Encerra a sessão cURL
        curl_close($ch);

        if ($error) {
            return [
                'status' => null,
                'body' => null,
                'error' => $error,
            ];
        }

        // Retorna um array com status e corpo
        return [
            'status' => $httpStatus,
            'body' => json_decode($responseBody, true), // tenta decodificar se for JSON
            'error' => null,
        ];
    }
}
