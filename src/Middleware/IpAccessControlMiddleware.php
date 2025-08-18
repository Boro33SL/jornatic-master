<?php
declare(strict_types=1);

namespace App\Middleware;

use Cake\Core\Configure;
use Cake\Http\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware de Control de Acceso por IP para Panel Master
 * Restringe el acceso al panel master solo a IPs permitidas.
 * Redirige a jornatic.es para IPs no autorizadas.
 */
class IpAccessControlMiddleware implements MiddlewareInterface
{
    /**
     * Process method - valida la IP del cliente
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The request handler.
     * @return \Psr\Http\Message\ResponseInterface A response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Obtener IPs permitidas desde variables de entorno
        $allowedIpsEnv = env('MASTER_ALLOWED_IPS', '');
        $allowedIps = [];

        if (!empty($allowedIpsEnv)) {
            // Convertir string separado por comas en array
            $allowedIps = array_map('trim', explode(',', $allowedIpsEnv));
        }

        // Si no hay IPs configuradas o estamos en modo debug, siempre permitir localhost
        if (empty($allowedIps) || Configure::read('debug')) {
            $allowedIps[] = '127.0.0.1';
            $allowedIps[] = '::1';
        }

        // Obtener la IP real del cliente
        $clientIp = $this->getClientIp($request);

        // Verificar si la IP está permitida
        if (!in_array($clientIp, $allowedIps)) {
            // Redirigir a jornatic.es para IPs no autorizadas
            $response = new Response();
            $response = $response->withStatus(302)
                ->withHeader('Location', 'https://jornatic.es');

            return $response;
        }

        // IP permitida, continuar con la petición
        return $handler->handle($request);
    }

    /**
     * Obtiene la IP real del cliente considerando proxies
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return string
     */
    private function getClientIp(ServerRequestInterface $request): string
    {
        // Prioridad de headers para obtener IP real
        $headers = [
            'HTTP_CF_CONNECTING_IP',// Cloudflare
            'HTTP_X_FORWARDED_FOR',// Proxy estándar
            'HTTP_X_REAL_IP',// Nginx
            'HTTP_CLIENT_IP',// Otros proxies
            'REMOTE_ADDR',// IP directa
        ];

        $serverParams = $request->getServerParams();

        foreach ($headers as $header) {
            if (!empty($serverParams[$header])) {
                // X-Forwarded-For puede contener múltiples IPs
                if ($header === 'HTTP_X_FORWARDED_FOR') {
                    $ips = explode(',', $serverParams[$header]);

                    return trim($ips[0]);
                }

                return $serverParams[$header];
            }
        }

        return '0.0.0.0';
    }
}
