<?php

namespace App\Models;

use App\Core\Database;
use Exception;
use PDO;

class EmpresaModel
{
    // Atributo público que armazena uma mensagem de erro, se ocorrer
    // Pode ser null
    public ?string $error = null;

    public function insert(array $data)
    {
        try {
            // Obtém a instância da conexão com o banco (PDO)
            $db = Database::getInstance();

            // Prepara uma query SQL com parâmetros nomeados
            $stmt = $db->prepare('INSERT INTO empresas (cnpj, razao_social) VALUES (:cnpj, :razao_social)');

            // Substitui os parâmetros da query pelos dados fornecidos
            $stmt->bindParam(':cnpj', $data['cnpj']);
            $stmt->bindParam(':razao_social', $data['razao_social']);

            if ($stmt->execute()) {
                // Se a inserção foi bem-sucedida, retorna o ID gerado
                return $db->lastInsertId();
            }
        } catch (Exception $exception) {
            // Em caso de erro, armazena a mensagem para depuração
            $this->error = $exception->getMessage();
        }

        // Se algo falhou, retorna false
        return false;
    }

    // src/Models/EmpresaModel.php
    public function findAll()
    {
        try {
            // Obtém a instância da conexão com o banco (PDO)
            $db = Database::getInstance();

            // Executa a consulta SQL que seleciona as empresas ordenadas por razão social
            // Limita o resultado a no máximo 10 registros
            $stmt = $db->query('SELECT id, razao_social, cnpj FROM empresas ORDER BY razao_social LIMIT 10');

            // Retorna todos os resultados como array associativo
            return $stmt->fetchAll();
        } catch (Exception $exception) {
            // Em caso de erro, armazena a mensagem para depuração
            $this->error = $exception->getMessage();
        }

        // Se algo falhou, retorna false
        return false;
    }

    // src/Models/EmpresaModel.php
    public function find(int $id)
    {
        try {
            // Obtém a instância da conexão com o banco (PDO)
            $db = Database::getInstance();

            // Prepara uma consulta SQL para buscar a empresa com base no ID
            $stmt = $db->prepare('SELECT id, razao_social, cnpj FROM empresas WHERE id = :id');

            // Substitui o parâmetro :id pelo valor recebido, garantindo segurança contra SQL Injection
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Executa a consulta no banco
            $stmt->execute();

            // Retorna os dados da empresa como um array associativo
            return $stmt->fetch();
        } catch (Exception $exception) {
            // Em caso de erro, armazena a mensagem para depuração
            $this->error = $exception->getMessage();
        }

        // Se algo falhou, retorna false
        return false;
    }

    // src/Models/EmpresaModel.php
    public function update(int $id, array $data)
    {
        try {
            // Obtém a instância da conexão com o banco (PDO)
            $db = Database::getInstance();

            // Prepara uma consulta SQL para atualizar a empresa
            $stmt = $db->prepare('UPDATE empresas SET cnpj = :cnpj, razao_social = :razao_social WHERE id = :id');

            // Substitui os parâmetros de forma segura contra SQL Injection
            $stmt->bindParam(':cnpj', $data['cnpj']);
            $stmt->bindParam(':razao_social', $data['razao_social']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Executa o update no banco
            return $stmt->execute();
        } catch (Exception $exception) {
            // Em caso de erro, armazena a mensagem para depuração
            $this->error = $exception->getMessage();
        }

        // Se algo falhou, retorna false
        return false;
    }

    // src/Models/EmpresaModel.php
    public function delete(int $id)
    {
        try {
            // Obtém a instância da conexão com o banco (PDO)
            $db = Database::getInstance();

            // Prepara uma consulta SQL para excluir a empresa
            $stmt = $db->prepare('DELETE FROM empresas WHERE id = :id');

            // Substitui o parâmetro :id de forma segura contra SQL Injection
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Executa o delete no banco
            return $stmt->execute();
        } catch (Exception $exception) {
            // Em caso de erro, armazena a mensagem para depuração
            $this->error = $exception->getMessage();
        }

        // Se algo falhou, retorna false
        return false;
    }
}
