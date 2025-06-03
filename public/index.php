<?php

use Dotenv\Dotenv;
use Bramus\Router\Router;

// Cria a constante ROOT_PATH com o caminho da pasta raiz do projeto
define('ROOT_PATH', dirname(__DIR__));

// Carrega o autoload do Composer
require ROOT_PATH . '/vendor/autoload.php';

// Carrega as variáveis do arquivo .env
// E as armazenam no $_ENV (ex: $_ENV['DB_HOST'])
$dotenv = Dotenv::createImmutable(ROOT_PATH);
$dotenv->load();

// Cria uma nova instância do roteador
// A partir daqui, podemos definir as rotas da API
$router = new Router();

// Define uma rota do tipo GET para a URL "/"
// Quando acessada, ela executa a função anônima que imprime 'Hello World!'
$router->get('/', function() {
    // Define o tipo de conteúdo da resposta
    header('Content-Type: application/json; charset=UTF-8');

    // Define o código de status HTTP (200 = OK)
    http_response_code(200);

    // Envia a resposta no formato JSON
    echo json_encode([
        'mensagem' => 'Hello World!'
    ]);

    // Encerra a execução
    exit;
});

// public/index.php
$router->post('empresas', '\App\Controllers\EmpresaController@criar');
$router->get('empresas', '\App\Controllers\EmpresaController@listar');
$router->get('empresas/(\d+)', '\App\Controllers\EmpresaController@buscarPorId');
$router->put('empresas/(\d+)', '\App\Controllers\EmpresaController@atualizar');
$router->delete('empresas/(\d+)', '\App\Controllers\EmpresaController@excluir');

// Inicia o roteador e processa a requisição atual
$router->run();
