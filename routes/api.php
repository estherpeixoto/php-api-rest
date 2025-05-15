<?php

use App\Controllers\EmpresaController;

$controller = new EmpresaController;

// CREATE - Criar empresa
$router->add('POST', '/empresas', fn() => $controller->criar());

// READ - Listar empresas
$router->add('GET', '/empresas', fn() => $controller->listar());

// READ - Buscar empresa pelo ID
$router->add('GET', '/empresas/{id}', fn($id) => $controller->buscarPorId($id));

// UPDATE - Atualizar empresa
$router->add('PUT', '/empresas/{id}', fn($id) => $controller->atualizar($id));

// DELETE - Excluir empresa
$router->add('DELETE', '/empresas/{id}', fn($id) => $controller->excluir($id));
