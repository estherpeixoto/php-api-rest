<?php

namespace App\Core;

use PDO;

class Database
{
    // Atributo estático que guarda a instância da conexão com o banco
    // O "?" indica que pode ser null
    private static ?PDO $instance = null;

    // Função pública que retorna a instância única de conexão com o banco
    public static function getInstance(): PDO
    {
        // Se ainda não existe conexão, criamos uma
        if (self::$instance === null) {
            // Pegamos as configurações do banco definidas no .env
            $host     = $_ENV['DB_HOST'];
            $port     = $_ENV['DB_PORT'];
            $dbName   = $_ENV['DB_DATABASE'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASSWORD'];

            // Monta o DSN (Data Source Name) para o MySQL
            $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";

            // Tenta criar a conexão com o banco usando o PDO
            self::$instance = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Erros lançam exceção
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Retorna resultados como array associativo
                PDO::ATTR_EMULATE_PREPARES => false, // Usa queries preparadas do MySQL
            ]);
        }

        // Retorna a instância da conexão
        return self::$instance;
    }
}
