<?php

namespace App\Core;

/**
 * Define constantes para códigos de status HTTP comuns
 *
 * Isso melhora a legibilidade e a manutenção do código, evitando "números mágicos"
 * Baseado nos padrões RFC 7231, RFC 6585, etc
 *
 * Obs: 'final' impede que a classe seja estendida
 */
final class Http
{
    // 1xx Informational
    // Não é comum em APIs REST

    // 2xx Success
    public const OK         = 200; // Requisição bem-sucedida
    public const CREATED    = 201; // Recurso criado com sucesso
    public const NO_CONTENT = 204; // Requisição bem-sucedida, sem conteúdo para retornar ou nenhum registro encontrado

    // 3xx Redirection
    // Não é comum em APIs REST, e sim em projetos web tradicionais

    // 4xx Client Error
    public const BAD_REQUEST        = 400; // Requisição inválida (sintaxe, dados faltando, etc)
    public const NOT_FOUND          = 404; // Recurso não existe
    public const METHOD_NOT_ALLOWED = 405; // Recurso existe, mas não para o método HTTP informado
    public const CONFLICT           = 409; // Conflito com o estado atual do recurso (ex: duplicidade)

    // 5xx Server Error
    public const INTERNAL_SERVER_ERROR = 500; // Erro genérico no servidor

    /**
     * Impede a instanciação da classe, já que ela só contém constantes
     */
    private function __construct() {}
}
