<?php

namespace App\Controllers;

use App\Core\Http;
use App\Core\Requests;
use App\Models\EmpresaModel;

class EmpresaController
{
    private EmpresaModel $model;

    public function __construct()
    {
        $this->model = new EmpresaModel;
    }

    public function listar(): array
    {
        // Exemplo de como pegar o parâmetro 'cnpj' da query string
        $cnpj = $_GET['cnpj'] ?? null;

        // Validação simples: se existir e for string não vazia
        if (!is_null($cnpj) && is_string($cnpj)) {
            $cnpj = preg_replace('/[%_]/', '', $cnpj);

            if (trim($cnpj) !== '') {
                // Sanitiza: remove espaços desnecessários, % e _
                $cnpj = preg_replace('/[%_]/', '', trim($cnpj));

                // Chama o model com o cnpj
                $empresas = $this->model->findByCNPJ($cnpj);
            } else {
                return [
                    'statusCode' => Http::BAD_REQUEST,
                    'message' => 'O campo "cnpj" é obrigatório',
                ];
            }
        } else {
            $empresas = $this->model->findAll();
        }

        if ($empresas === false) {
            return [
                'statusCode' => Http::INTERNAL_SERVER_ERROR,
                'message' => 'Erro ao listar empresas',
            ];
        }

        if (count($empresas) > 0) {
            return [
                'statusCode' => Http::OK,
                'message' => 'Sucesso',
                'data' => $empresas,
            ];
        }

        return ['statusCode' => Http::NO_CONTENT];
    }

    public function buscarPorId(int $id): array
    {
        $empresa = $this->model->find($id);

        if ($empresa === false) {
            return [
                'statusCode' => Http::NOT_FOUND,
                'message' => 'Empresa não existe',
            ];
        }

        return [
            'statusCode' => Http::OK,
            'data' => $empresa,
        ];
    }

    public function criar(array $data): array
    {
        // Validação básica dos dados
        // @TODO Implementar validação de CNPJ
        if (empty($data['cnpj'])) {
            return [
                'statusCode' => Http::BAD_REQUEST,
                'message' => 'O campo "cnpj" é obrigatório',
            ];
        }

        // Verifica se o CNPJ já está cadastrado
        $empresas = $this->model->findByCNPJ($data['cnpj']);

        if (count($empresas) > 0) {
            return [
                'statusCode' => Http::CONFLICT,
                'message' => "A empresa {{$data['cnpj']}} já está cadastrada",
            ];
        }

        // Buscar a razão social da empresa usando uma API externa
        $response = Requests::send('GET', "https://brasilapi.com.br/api/cnpj/v1/{$data['cnpj']}");

        if (!isset($response['body']['razao_social'])) {
            return [
                'statusCode' => Http::INTERNAL_SERVER_ERROR,
                'message' => $response['error'] ?? 'Falha ao buscar dados da empresa',
            ];
        }

        $data['razao_social'] = $response['body']['razao_social'];

        $empresaId = $this->model->insert($data);

        if ($empresaId === false) {
            return [
                'statusCode' => Http::INTERNAL_SERVER_ERROR,
                'message' => 'Não foi possível criar a empresa',
            ];
        }

        return [
            'statusCode' => Http::CREATED,
            'message' => 'Empresa criada com sucesso',
            'data' => [
                'id' => $empresaId,
            ],
        ];
    }

    public function atualizar(int $id, array $data): array
    {
        // Validação básica dos dados
        if (empty($data['cnpj'])) {
            return [
                'statusCode' => Http::BAD_REQUEST,
                'message' => 'O campo "cnpj" é obrigatório',
            ];
        }

        $empresa = $this->model->find($id);

        if ($empresa === false) {
            return [
                'statusCode' => Http::NOT_FOUND,
                'message' => 'Empresa não existe',
            ];
        }

        if ($this->model->update($id, $data)) {
            return [
                'statusCode' => Http::OK,
                'message' => 'Empresa atualizada com sucesso',
            ];
        }

        return [
            'statusCode' => Http::INTERNAL_SERVER_ERROR,
            'message' => 'Não foi possível atualizar a empresa',
        ];
    }

    public function excluir(int $id): array
    {
        $empresa = $this->model->find($id);

        if ($empresa === false) {
            return [
                'statusCode' => Http::NOT_FOUND,
                'message' => 'Empresa não existe',
            ];
        }

        if ($this->model->delete($id)) {
            return [
                'statusCode' => Http::OK,
                'message' => 'Empresa excluída com sucesso',
            ];
        }

        return [
            'statusCode' => Http::INTERNAL_SERVER_ERROR,
            'message' => 'Erro ao tentar excluir a empresa',
        ];
    }
}
