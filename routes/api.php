<?php

use App\Controllers\EmpresaController;

$controller = new EmpresaController;

// CREATE - Criar empresa
$router->add('POST', '/empresas', function () use ($controller) {
    $jsonInput = json_decode(file_get_contents('php://input'), true);
    return $controller->criar($jsonInput, true);
});

// READ - Listar empresas
$router->add('GET', '/empresas', fn() => $controller->listar());

// READ - Buscar empresa pelo ID
$router->add('GET', '/empresas/{id}', fn($id) => $controller->buscarPorId($id));

// UPDATE - Atualizar empresa
$router->add('PUT', '/empresas/{id}', function ($id) use ($controller) {
    $jsonInput = json_decode(file_get_contents('php://input'), true);
    return $controller->atualizar($id, $jsonInput);
});

// DELETE - Excluir empresa
$router->add('DELETE', '/empresas/{id}', fn($id) => $controller->excluir($id));
