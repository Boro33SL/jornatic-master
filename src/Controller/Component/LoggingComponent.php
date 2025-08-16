<?php
declare(strict_types=1);

namespace App\Controller\Component;

use App\Model\Table\MasterAccessLogsTable;
use Cake\Controller\Component;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;

/**
 * Logging component
 *
 * Componente para registrar automáticamente todas las acciones de los usuarios master
 */
class LoggingComponent extends Component
{
    /**
     * Constantes de acciones para auditoría
     */
    public const ACTION_LOGIN = 'LOGIN';
    public const ACTION_LOGOUT = 'LOGOUT';
    public const ACTION_CREATE = 'CREATE';
    public const ACTION_UPDATE = 'UPDATE';
    public const ACTION_DELETE = 'DELETE';
    public const ACTION_VIEW = 'VIEW';
    public const ACTION_EXPORT = 'EXPORT';
    public const ACTION_LOGIN_FAILED = 'LOGIN_FAILED';

    /**
     * @var \App\Model\Table\MasterAccessLogsTable
     */
    protected MasterAccessLogsTable $MasterAccessLogs;

    /**
     * Initialize method
     *
     * @param array $config The config data.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->MasterAccessLogs = TableRegistry::getTableLocator()->get('MasterAccessLogs');
    }

    /**
     * Registrar una acción de auditoría
     *
     * @param string $action Acción realizada (usar constantes ACTION_*)
     * @param bool $success Si la operación fue exitosa
     * @param string|null $resource Recurso afectado (opcional)
     * @param int|null $resourceId ID del recurso (opcional)
     * @param array|string|null $details Detalles adicionales (opcional)
     * @return bool
     */
    public function logAction(string $action, bool $success = true, ?string $resource = null, ?int $resourceId = null, array|string|null $details = null): bool
    {
        $controller = $this->getController();
        $request = $controller->getRequest();

        // Obtener usuario master autenticado
        $master = $request->getAttribute('identity');
        if (!$master) {
            // Si no hay usuario autenticado, intentar obtenerlo de otra forma o registrar como anónimo
            return false;
        }

        // Preparar detalles como JSON si es un array
        $detailsJson = null;
        if ($details !== null) {
            if (is_array($details)) {
                $detailsJson = json_encode($details, JSON_UNESCAPED_UNICODE);
            } else {
                $detailsJson = (string)$details;
            }
        }

        // Crear registro de log
        $logData = [
            'master_id' => $master->id,
            'ip_address' => $this->_getClientIp($request),
            'user_agent' => $request->getHeaderLine('User-Agent'),
            'action' => $action,
            'resource' => $resource,
            'resource_id' => $resourceId,
            'details' => $detailsJson,
            'success' => $success,
        ];

        $log = $this->MasterAccessLogs->newEntity($logData);

        return (bool)$this->MasterAccessLogs->save($log);
    }

    /**
     * Registrar login exitoso
     *
     * @param array $details Detalles adicionales del login
     * @return bool
     */
    public function logLogin(array $details = []): bool
    {
        return $this->logAction(self::ACTION_LOGIN, true, 'authentication', null, $details);
    }

    /**
     * Registrar login fallido
     *
     * @param string $email Email del intento fallido
     * @param string $reason Razón del fallo
     * @return bool
     */
    public function logLoginFailed(string $email, string $reason = 'Invalid credentials'): bool
    {
        // Para logins fallidos, necesitamos crear el log sin master_id
        $controller = $this->getController();
        $request = $controller->getRequest();

        $logData = [
            'master_id' => null, // No hay usuario autenticado en login fallido
            'ip_address' => $this->_getClientIp($request),
            'user_agent' => $request->getHeaderLine('User-Agent'),
            'action' => self::ACTION_LOGIN_FAILED,
            'resource' => 'authentication',
            'resource_id' => null,
            'details' => json_encode([
                'email' => $email,
                'reason' => $reason,
                'timestamp' => date('Y-m-d H:i:s'),
            ], JSON_UNESCAPED_UNICODE),
            'success' => false,
        ];

        // Necesitamos modificar temporalmente la validación para permitir master_id null
        $log = $this->MasterAccessLogs->newEntity($logData, ['validate' => false]);

        return (bool)$this->MasterAccessLogs->save($log, ['validate' => false]);
    }

    /**
     * Registrar logout
     *
     * @return bool
     */
    public function logLogout(): bool
    {
        return $this->logAction(self::ACTION_LOGOUT, true, 'authentication');
    }

    /**
     * Registrar creación de recurso
     *
     * @param string $resource Tipo de recurso creado
     * @param int $resourceId ID del recurso creado
     * @param array $details Detalles del recurso creado
     * @return bool
     */
    public function logCreate(string $resource, int $resourceId, array $details = []): bool
    {
        return $this->logAction(self::ACTION_CREATE, true, $resource, $resourceId, $details);
    }

    /**
     * Registrar actualización de recurso
     *
     * @param string $resource Tipo de recurso actualizado
     * @param int $resourceId ID del recurso actualizado
     * @param array $details Detalles de los cambios
     * @return bool
     */
    public function logUpdate(string $resource, int $resourceId, array $details = []): bool
    {
        return $this->logAction(self::ACTION_UPDATE, true, $resource, $resourceId, $details);
    }

    /**
     * Registrar eliminación de recurso
     *
     * @param string $resource Tipo de recurso eliminado
     * @param int $resourceId ID del recurso eliminado
     * @param array $details Detalles del recurso eliminado
     * @return bool
     */
    public function logDelete(string $resource, int $resourceId, array $details = []): bool
    {
        return $this->logAction(self::ACTION_DELETE, true, $resource, $resourceId, $details);
    }

    /**
     * Registrar visualización de recurso
     *
     * @param string $resource Tipo de recurso visualizado
     * @param int|null $resourceId ID del recurso visualizado
     * @return bool
     */
    public function logView(string $resource, ?int $resourceId = null): bool
    {
        return $this->logAction(self::ACTION_VIEW, true, $resource, $resourceId);
    }

    /**
     * Registrar exportación de datos
     *
     * @param string $resource Tipo de recurso exportado
     * @param array $details Detalles de la exportación (filtros, cantidad, etc.)
     * @return bool
     */
    public function logExport(string $resource, array $details = []): bool
    {
        return $this->logAction(self::ACTION_EXPORT, true, $resource, null, $details);
    }

    /**
     * Obtener la IP real del cliente
     *
     * @param \Cake\Http\ServerRequest $request
     * @return string
     */
    private function _getClientIp(ServerRequest $request): string
    {
        // Verificar headers de proxy/load balancer
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR',                // Standard
        ];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];

                // Si hay múltiples IPs (proxy chain), tomar la primera
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }

                $ip = trim($ip);

                // Validar que sea una IP válida
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Fallback a REMOTE_ADDR incluso si es IP privada
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
