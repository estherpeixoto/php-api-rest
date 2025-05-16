<?php

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    // Usando Singleton Pattern:
    // Garante que uma classe tenha apenas uma única instância durante a execução da aplicação
    // E fornece um ponto de acesso global a essa instância
    private static ?PDO $instance = null;

    /**
     * Impede a instanciação direta da classe.
     */
    private function __construct() {}

    /**
     * Impede a clonagem da instância.
     */
    private function __clone() {}

    /**
     * Cria uma conexão ou retorna a conexão atual
     *
     * @return PDO
     * @throws RuntimeException Se a conexão falhar ou variáveis de ambiente não estiverem definidas
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            // Busca as credenciais das variáveis de ambiente
            $host = $_ENV['DB_HOST'];
            $port = $_ENV['DB_PORT'];
            $dbName = $_ENV['DB_DATABASE'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASSWORD'];

            // Validação básica das variáveis
            if (!$host || !$port || !$dbName || !$username) {
                throw new RuntimeException('Erro: Variáveis de ambiente do banco de dados não configuradas corretamente no arquivo .env');
            }

            $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lança exceções em erros
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retorna resultados como array associativo
                PDO::ATTR_EMULATE_PREPARES => false, // Usa prepared statements nativos
            ];

            try {
                self::$instance = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                error_log('Erro de Conexão: ' . $e->getMessage());

                throw new RuntimeException(
                    'Erro ao conectar ao banco de dados: ' . $e->getMessage(),
                    (int) $e->getCode()
                );
            }
        }

        return self::$instance;
    }
}
