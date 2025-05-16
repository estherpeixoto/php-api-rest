<?php

use App\Controllers\EmpresaController;
use App\Core\Http;
use Dotenv\Dotenv;

// Executa o bootstrap da aplicação (carrega autoload e .env)
try {
    // Define o diretório raiz se ainda não foi definido
    define('ROOT_PATH', dirname(__DIR__));

    // Verifica se o autoload existe antes de incluí-lo
    $caminhoAutoloader = ROOT_PATH . '/vendor/autoload.php';

    if (!file_exists($caminhoAutoloader)) {
        http_response_code(500); // Define o status HTTP
        header('Content-Type: text/plain; charset=utf-8'); // Define o tipo de conteúdo
        die("ERRO CRÍTICO: Arquivo autoloader não encontrado em '{$caminhoAutoloader}'. Execute 'composer install'.");
    }

    // Carrega o autoload do Composer
    require $caminhoAutoloader;

    // Carrega as variáveis de ambiente (.env)
    $dotenv = Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();

    // Valida se as variáveis essenciais para o DB existem
    $dotenv->required([
        'DB_HOST',
        'DB_PORT',
        'DB_DATABASE',
        'DB_USERNAME',
        'DB_PASSWORD'
    ]);

    // Instância o controller
    $controller = new EmpresaController;

    // Obter método e path da requisição
    $metodoHTTP = $_SERVER['REQUEST_METHOD'];
    $rota = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Define as rotas
    if ($rota === '/empresas') {
        if ($metodoHTTP === 'POST') {
            $corpoRequisicao = json_decode(file_get_contents('php://input'), true);
            $resposta = $controller->criar($corpoRequisicao); // CREATE - Criar empresa
        } else if ($metodoHTTP === 'GET') {
            $resposta = $controller->listar(); // READ - Listar empresas
        } else {
            $resposta = [
                'statusCode' => Http::METHOD_NOT_ALLOWED,
                'message' => "Método $metodoHTTP não permitido para o endpoint /empresas",
            ];
        }
    } else if (preg_match('/^\/empresas\/(\d+)$/', $rota, $matches)) {
        $empresaId = (int) $matches[1];

        if ($metodoHTTP === 'GET') {
            $resposta = $controller->buscarPorId($empresaId); // READ - Buscar empresa pelo ID
        } else if ($metodoHTTP === 'PUT') {
            $corpoRequisicao = json_decode(file_get_contents('php://input'), true);
            $resposta = $controller->atualizar($empresaId, $corpoRequisicao); // UPDATE - Atualizar empresa
        } else if ($metodoHTTP === 'DELETE') {
            $resposta = $controller->excluir($empresaId); // DELETE - Excluir empresa
        } else {
            $resposta = [
                'statusCode' => Http::METHOD_NOT_ALLOWED,
                'message' => "Método $metodoHTTP não permitido para o endpoint /empresas",
            ];
        }
    } else {
        $resposta = [
            'statusCode' => Http::NOT_FOUND,
            'message' => "Rota inexistente {$rota}",
        ];
    }

    // Seta no cabeçalho da resposta o statusCode definido no roteamento/controller
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($resposta['statusCode']);
    }

    // Se o statusCode for 204 No Content, o corpo da resposta DEVE ser vazio.
    if ($resposta['statusCode'] === Http::NO_CONTENT) {
        echo '';
    } else {
        // Envia a resposta JSON final
        // O JSON_PRETTY_PRINT é bom para debug, pode ser removido em produção para economizar bytes
        // JSON_UNESCAPED_UNICODE e JSON_UNESCAPED_SLASHES garantem a formatação correta de caracteres
        echo json_encode($resposta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    exit;
} catch (Throwable $e) {
    // Grava log de erro detalhado
    $logMessage = sprintf(
        "Erro crítico: %s no arquivo %s na linha %d\nTrace: %s",
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );

    error_log($logMessage);

    if (!headers_sent()) {
        // Boa prática verificar se headers já foram enviados
        header('Content-Type: application/json; charset=utf-8', true, Http::INTERNAL_SERVER_ERROR);
    }

    echo json_encode([
        'statusCode' => Http::INTERNAL_SERVER_ERROR,
        'message' => 'Erro crítico no servidor. Consulte os logs para mais detalhes.'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    exit;
}
