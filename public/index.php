<?php

use App\Core\Router;

// 1. Define o diretório raiz se ainda não foi definido
define('ROOT_PATH', dirname(__DIR__));

// 2. Executa o bootstrap da aplicação (carrega autoload e .env)
require ROOT_PATH . '/bootstrap/app.php';

// 3. Inicia a classe de roteamento
$router = new Router();

// 4. Inclui o arquivo que define as rotas e a função de tratamento
require ROOT_PATH . '/routes/api.php';

// 5. Processa a requisição atual
$response = $router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

// 6. Define o cabeçalho padrão para respostas JSON
header('Content-Type: application/json; charset=utf-8');

// 7. Define o código de status HTTP final
http_response_code($response['status']);

// 8. Envia a resposta JSON final
// O JSON_PRETTY_PRINT é bom para debug, pode ser removido em produção para economizar bytes
// JSON_UNESCAPED_UNICODE e JSON_UNESCAPED_SLASHES garantem a formatação correta de caracteres
echo json_encode(
    $response,
    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
