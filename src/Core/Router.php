<?php

namespace App\Core;

use App\Core\Http;

class Router
{
    /**
     * $routes é um array que guardará todas as rotas cadastradas, incluindo:
     * O método HTTP (GET, POST, etc.)
     * O caminho (ex: /empresas/{id})
     * A função/callback a ser executada
     */
    private $routes = [];

    /**
     * Essa função add() é usada para registrar uma nova rota
     *
     * @param string    $method     Tipo da requisição (ex: GET)
     * @param string    $pattern    O path da rota (ex: /empresas/{id})
     * @param callable  $callback   Uma função anônima ou um método de controller
     */
    public function add(string $method, string $pattern, callable $callback)
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback,
        ];
    }

    /**
     * Essa função dispatch() processa a requisição
     *
     * Ela percorre cada rota registrada e pula (continue) se o método
     *   (GET, POST, etc.) da rota não for o mesmo da requisição atual.
     *
     * @param string $requestUri        Recebe o $_SERVER['REQUEST_URI']
     * @param string $requestMethod     Recebe o $_SERVER['REQUEST_METHOD']
     */
    public function dispatch(string $requestUri, string $requestMethod)
    {
        // Remove a query string da URI
        $uriLimpa = parse_url($requestUri, PHP_URL_PATH);

        // Percorre o array de rotas registradas
        foreach ($this->routes as $route) {
            // Pula a rota se o método HTTP (GET, POST, etc.) da requisição atual não for igual
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Transformamos o path com {variáveis} em uma regex com named capture groups.
            // Exemplo: /empresas/{id} vira @^/empresas/(?P<id>[^/]+)$@

            // Isso permite extrair id automaticamente se o path for /empresas/7
            $pattern = '@^';
            $pattern .= preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['pattern']);
            $pattern .= '$@';

            // Se o requestUri bate com o padrão convertido, extraímos os parâmetros da URL
            // preg_match() preenche o array $matches, com chaves como ['id' => '7']
            if (preg_match($pattern, $uriLimpa, $matches)) {
                // call_user_func_array() executa o callback passando os parâmetros como argumentos
                return call_user_func_array(
                    // Chama o método do controller
                    $route['callback'],

                    // Remove índices numéricos, mantendo apenas os nomes dos parâmetros
                    array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY)
                );

            }
        }

        // Se nenhuma rota bateu, retorna um 404 como resposta padrão
        return [
            'status' => Http::NOT_FOUND,
            'message' => 'Not Found',
        ];
    }
}
