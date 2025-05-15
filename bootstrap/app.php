<?php

use Dotenv\Dotenv;

try {
    // 1. Verifica se o autoload existe antes de incluí-lo
    $autoloader = ROOT_PATH . '/vendor/autoload.php';
    if (!file_exists($autoloader)) {
        throw new Exception('Arquivo autoloader não encontrado.');
    }

    // 2. Carrega o autoload do Composer
    require $autoloader;

    // 3. Carrega as variáveis de ambiente (.env)
    $dotenv = Dotenv::createImmutable(ROOT_PATH);
    $dotenv->load();

    // 4. Valida se as variáveis essenciais para o DB existem
    $dotenv->required([
        'DB_HOST',
        'DB_PORT',
        'DB_DATABASE',
        'DB_USERNAME',
        'DB_PASSWORD'
    ]);
} catch (Throwable $e) {
    // 5. Grava log de erro durante o bootstrap da aplicação
    error_log('Erro crítico: ' . $e->getMessage());

    // 6. Define o cabeçalho padrão para respostas JSON
    header('Content-Type: application/json; charset=utf-8', true, 500);

    // 8. Envia a resposta JSON final
    // O JSON_PRETTY_PRINT é bom para debug, pode ser removido em produção para economizar bytes
    // JSON_UNESCAPED_UNICODE e JSON_UNESCAPED_SLASHES garantem a formatação correta de caracteres
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro crítico no servidor.'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    // 9. Garante que a execução seja finalizada
    exit;
}
