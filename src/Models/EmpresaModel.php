<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

// @TODO Criar uma forma de buscar mensagens de erro do model
class EmpresaModel
{
    public PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findAll()
    {
        try {
            $stmt = $this->db->query('SELECT id, razao_social, cnpj FROM empresas ORDER BY razao_social LIMIT 10');
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Erro PDO: ' . $e->getMessage());
            return false;
        }
    }

    public function findByCNPJ($cnpj)
    {
        try {
            $stmt = $this->db->prepare('SELECT id, razao_social, cnpj FROM empresas WHERE cnpj = :cnpj ORDER BY razao_social');
            $stmt->bindParam(':cnpj', $cnpj);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Erro PDO: ' . $e->getMessage());
            return false;
        }
    }

    public function find(int $id)
    {
        try {
            $stmt = $this->db->prepare('SELECT id, razao_social, cnpj FROM empresas WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Erro PDO: ' . $e->getMessage());
            return false;
        }
    }

    public function insert(array $data)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO empresas (cnpj, razao_social) VALUES (:cnpj, :razao_social)');
            $stmt->bindParam(':cnpj', $data['cnpj']);
            $stmt->bindParam(':razao_social', $data['razao_social']);

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log('Erro PDO: ' . $e->getMessage());
            return false;
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $stmt = $this->db->prepare('UPDATE empresas SET cnpj = :cnpj, razao_social = :razao_social WHERE id = :id');
            $stmt->bindParam(':cnpj', $data['cnpj']);
            $stmt->bindParam(':razao_social', $data['razao_social']);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Erro PDO: ' . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id)
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM empresas WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Erro PDO: ' . $e->getMessage());
            return false;
        }
    }
}
