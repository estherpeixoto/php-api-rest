<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\EmpresaModel;

class EmpresaController
{
    // POST /empresas
    public function criar()
    {
        // Lê e converte o corpo da requisição (JSON) em array associativo
        $dadosJson = Request::getJson();

        // Validação básica dos dados
        if (empty($dadosJson['cnpj'])) {
            return Response::json([
                'status' => 400,
                'message' => 'O campo {cnpj} é obrigatório',
            ]);
        }

        // Consumindo uma API REST
        $url = "https://brasilapi.com.br/api/cnpj/v1/{$dadosJson['cnpj']}";

        $ch = curl_init(); // Inicia sessão cURL

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,             // Define a URL
            CURLOPT_RETURNTRANSFER => true,  // Captura a resposta como string
            CURLOPT_SSL_VERIFYPEER => false, // Desativa SSL (útil para localhost)
        ]);

        $response = curl_exec($ch); // Executa a requisição
        curl_close($ch);            // Encerra a sessão cURL

        // Converte a resposta da API de string para array associativo
        $response = json_decode($response, true);

        if (!isset($response['razao_social'])) {
            // Se a resposta não contiver a razão social, retorna erro
            return Response::json([
                'status' => 500,
                'message' => "Não foi possível buscar a razão social da empresa {{$dadosJson['cnpj']}}",
            ]);
        }

        // Instancia o model responsável por lidar com o banco de dados
        $model = new EmpresaModel();

        // Insere a nova empresa no banco com o CNPJ enviado e a razão social retornada da API
        $empresaId = $model->insert([
            'cnpj' => $dadosJson['cnpj'],
            'razao_social' => $response['razao_social'],
        ]);

        if ($empresaId === false) {
            // Se falhar ao inserir no banco, retorna erro do PDO
            return Response::json([
                'status' => 500,
                'message' => $model->error,
            ]);
        }

        // Retorna sucesso com o ID da nova empresa inserida
        return Response::json([
            'status' => 201,
            'message' => 'Empresa criada com sucesso',
            'data' => [
                'id' => $empresaId,
            ],
        ]);
    }

    // src/Controllers/EmpresaController.php
    public function listar()
    {
        // Instancia o model responsável por lidar com o banco de dados
        $model = new EmpresaModel();

        // Chama o método do model que retorna todas as empresas
        $empresas = $model->findAll();

        // Verifica se houve erro na consulta
        if ($empresas === false) {
            return Response::json([
                'status' => 500,
                'message' => $model->error,
            ]);
        }

        // Retorna as empresas com status 200 e mensagem de sucesso
        return Response::json([
            'status' => 200,
            'message' => 'Sucesso',
            'data' => $empresas,
        ]);
    }

    // src/Controllers/EmpresaController.php
    public function buscarPorId(int $id)
    {
        // Instancia o model responsável por lidar com o banco de dados
        $model = new EmpresaModel();

        // Busca uma empresa específica pelo ID fornecido
        $empresa = $model->find($id);

        if ($empresa === false) {
            // Se não encontrar a empresa, retorna status 404 (não encontrado)
            return Response::json([
                'status' => 404,
                'message' => 'Empresa não existe',
            ]);
        }

        // Se a empresa for encontrada, retorna os dados com status 200
        return Response::json([
            'status' => 200,
            'message' => 'Sucesso',
            'data' => $empresa,
        ]);
    }

    // src/Controllers/EmpresaController.php
    public function atualizar(int $id)
    {
        // Lê e converte o corpo da requisição (JSON) em array associativo
        $dadosJson = Request::getJson();

        // Validação básica dos dados
        if (empty($dadosJson['cnpj']) || empty($dadosJson['razao_social'])) {
            return Response::json([
                'status' => 400,
                'message' => 'Os campos {cnpj} e {razao_social} são obrigatórios',
            ]);
        }

        // Instancia o model responsável por lidar com o banco de dados
        $model = new EmpresaModel();

        // Verifica se existe uma empresa com o ID fornecido
        $empresa = $model->find($id);

        if ($empresa === false) {
            // Se não encontrar a empresa, retorna status 404 (não encontrado)
            return Response::json([
                'status' => 404,
                'message' => 'Empresa não existe',
            ]);
        }

        if ($model->update($id, $dadosJson)) {
            // Retorna mensagem de sucesso e status 200
            return Response::json([
                'status' => 200,
                'message' => 'Empresa atualizada com sucesso',
            ]);
        }

        // Se falhar ao atualizar no banco, retorna erro do PDO
        return Response::json([
            'status' => 500,
            'message' => $model->error,
        ]);
    }

    // src/Controllers/EmpresaController.php
    public function excluir(int $id): array
    {
        // Instancia o model responsável por lidar com o banco de dados
        $model = new EmpresaModel();

        // Verifica se existe uma empresa com o ID fornecido
        $empresa = $model->find($id);

        if ($empresa === false) {
            // Se não encontrar a empresa, retorna status 404 (não encontrado)
            return Response::json([
                'status' => 404,
                'message' => 'Empresa não existe',
            ]);
        }

        if ($model->delete($id)) {
            // Retorna mensagem de sucesso e status 200
            return Response::json([
                'status' => 200,
                'message' => 'Empresa excluída com sucesso',
            ]);
        }

        // Se falhar ao excluir no banco, retorna erro do PDO
        return Response::json([
            'status' => 500,
            'message' => $model->error,
        ]);
    }
}
