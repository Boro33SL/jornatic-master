<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * MasterAccessLogs Controller
 * Sistema de auditoría y logging para usuarios master
 *
 * @property \App\Model\Table\MasterAccessLogsTable $MasterAccessLogs
 */
class MasterAccessLogsController extends AppController
{
    /**
     * Función de inicialización
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Skip authorization for all actions (will be handled by authentication)
        $this->Authorization->skipAuthorization();
    }

    /**
     * Función index - Lista paginada de logs con filtros
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        // Construir query base con Master asociado
        $query = $this->MasterAccessLogs->find()
            ->contain(['Masters'])
            ->orderBy(['MasterAccessLogs.created' => 'DESC']);

        // Aplicar filtros si existen
        $filters = $this->request->getQueryParams();

        if (!empty($filters['master_id'])) {
            $query->where(['MasterAccessLogs.master_id' => $filters['master_id']]);
        }

        if (!empty($filters['action'])) {
            $query->where(['MasterAccessLogs.action' => $filters['action']]);
        }

        if (!empty($filters['success'])) {
            $query->where(['MasterAccessLogs.success' => $filters['success'] === '1']);
        }

        if (!empty($filters['ip_address'])) {
            $query->where(['MasterAccessLogs.ip_address LIKE' => '%' . $filters['ip_address'] . '%']);
        }

        if (!empty($filters['date_from'])) {
            $query->where(['MasterAccessLogs.created >=' => $filters['date_from'] . ' 00:00:00']);
        }

        if (!empty($filters['date_to'])) {
            $query->where(['MasterAccessLogs.created <=' => $filters['date_to'] . ' 23:59:59']);
        }

        // Configurar paginación
        $this->paginate = [
            'limit' => 25,
            'maxLimit' => 100,
        ];

        $masterAccessLogs = $this->paginate($query);

        // Obtener datos para filtros
        $masters = $this->MasterAccessLogs->Masters->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->toArray();

        $actions = $this->MasterAccessLogs->find()
            ->select(['action'])
            ->distinct(['action'])
            ->orderBy(['action' => 'ASC'])
            ->toArray();

        // Calcular estadísticas del día actual
        $todayStats = $this->_getTodayStats();

        $this->set(compact('masterAccessLogs', 'masters', 'actions', 'filters', 'todayStats'));
    }

    /**
     * Obtener estadísticas del día actual
     *
     * @return array
     */
    private function _getTodayStats(): array
    {
        $today = date('Y-m-d');

        $total = $this->MasterAccessLogs->find()
            ->where(['DATE(created)' => $today])
            ->count();

        $successful = $this->MasterAccessLogs->find()
            ->where([
                'DATE(created)' => $today,
                'success' => true,
            ])
            ->count();

        $failed = $this->MasterAccessLogs->find()
            ->where([
                'DATE(created)' => $today,
                'success' => false,
            ])
            ->count();

        $uniqueIps = $this->MasterAccessLogs->find()
            ->select(['ip_address'])
            ->distinct(['ip_address'])
            ->where(['DATE(created)' => $today])
            ->count();

        return [
            'total' => $total,
            'successful' => $successful,
            'failed' => $failed,
            'unique_ips' => $uniqueIps,
        ];
    }

    /**
     * Función view - Detalle de un log específico
     *
     * @param string|null $id Master Access Log id.
     * @return \Cake\Http\Response|null|void
     */
    public function view(?string $id = null)
    {
        $masterAccessLog = $this->MasterAccessLogs->get($id, [
            'contain' => ['Masters'],
        ]);

        // Obtener logs relacionados del mismo master en el mismo día
        $relatedLogs = $this->MasterAccessLogs->find()
            ->contain(['Masters'])
            ->where([
                'MasterAccessLogs.master_id' => $masterAccessLog->master_id,
                'MasterAccessLogs.id !=' => $id,
                'DATE(MasterAccessLogs.created)' => $masterAccessLog->created->format('Y-m-d'),
            ])
            ->orderBy(['MasterAccessLogs.created' => 'DESC'])
            ->limit(10)
            ->toArray();

        $this->set(compact('masterAccessLog', 'relatedLogs'));
    }

    /**
     * Función export - Exportar logs a CSV
     *
     * @return \Cake\Http\Response
     */
    public function export()
    {
        $this->request->allowMethod(['get']);

        // Aplicar los mismos filtros que en index
        $filters = $this->request->getQueryParams();

        $query = $this->MasterAccessLogs->find()
            ->contain(['Masters'])
            ->orderBy(['MasterAccessLogs.created' => 'DESC']);

        // Aplicar filtros (mismo código que en index)
        if (!empty($filters['master_id'])) {
            $query->where(['MasterAccessLogs.master_id' => $filters['master_id']]);
        }

        if (!empty($filters['action'])) {
            $query->where(['MasterAccessLogs.action' => $filters['action']]);
        }

        if (!empty($filters['success'])) {
            $query->where(['MasterAccessLogs.success' => $filters['success'] === '1']);
        }

        if (!empty($filters['ip_address'])) {
            $query->where(['MasterAccessLogs.ip_address LIKE' => '%' . $filters['ip_address'] . '%']);
        }

        if (!empty($filters['date_from'])) {
            $query->where(['MasterAccessLogs.created >=' => $filters['date_from'] . ' 00:00:00']);
        }

        if (!empty($filters['date_to'])) {
            $query->where(['MasterAccessLogs.created <=' => $filters['date_to'] . ' 23:59:59']);
        }

        // Limitar a 5000 registros para evitar problemas de memoria
        $logs = $query->limit(5000)->toArray();

        // Preparar datos CSV
        $csvData = [];
        $csvData[] = [
            __('_FECHA'),
            __('_USUARIO_MASTER'),
            __('_ACCION'),
            __('_RECURSO'),
            __('_RECURSO_ID'),
            __('_IP_ADDRESS'),
            __('_USER_AGENT'),
            __('_EXITO'),
            __('_DETALLES'),
        ];

        foreach ($logs as $log) {
            $csvData[] = [
                $log->created->format('Y-m-d H:i:s'),
                $log->master->name ?? '',
                $log->action,
                $log->resource ?? '',
                $log->resource_id ?? '',
                $log->ip_address,
                $log->user_agent ?? '',
                $log->success
                    ? __('_SI')
                    : __('_NO'),
                $log->details ?? '',
            ];
        }

        // Generar CSV
        $filename = 'master_access_logs_' . date('Y-m-d_H-i-s') . '.csv';

        $this->response = $this->response->withType('text/csv');
        $this->response = $this->response
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // Crear contenido CSV
        $output = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($output, $row, ';', '"');
        }
        fclose($output);

        return $this->response;
    }
}
