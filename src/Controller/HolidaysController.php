<?php
declare(strict_types=1);

namespace App\Controller;

use DateTime;
use JornaticCore\Model\Entity\Holiday;

/**
 * Holidays Controller
 *
 * Gestión completa de festivos del ecosistema Jornatic
 *
 * @property \JornaticCore\Model\Table\HolidaysTable $Holidays
 */
class HolidaysController extends AppController
{
    /**
     * Función de inicialización
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Cargar el modelo desde el plugin
        $this->Holidays = $this->getTable('JornaticCore.Holidays');

        // Cargar componente de logging
        $this->loadComponent('Logging');

        // Skip authorization para todas las acciones (por ahora)
        $this->Authorization->skipAuthorization();
    }

    /**
     * Función index - Lista paginada de festivos
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        // Registrar acceso
        $this->Logging->logView('holidays_list');

        // Aplicar filtros si existen
        $filters = $this->request->getQueryParams();

        // Filtrar por año actual por defecto
        $currentYear = date('Y');
        $year = $filters['year'] ?? $currentYear;

        // Query base con relaciones y filtro de año
        $query = $this->Holidays->find()
            ->contain(['Companies'])
            ->where(['YEAR(Holidays.date)' => $year])
            ->orderBy(['Holidays.date' => 'ASC']);

        // Filtro por estado (activo por defecto si no se especifica)
        if (isset($filters['is_active'])) {
            $query->where(['Holidays.is_active' => (bool)$filters['is_active']]);
        } else {
            // Solo mostrar festivos activos por defecto
            $query->where(['Holidays.is_active' => true]);
        }

        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where([
                'OR' => [
                    'Holidays.name LIKE' => $search,
                ],
            ]);
        }

        if (!empty($filters['company_id'])) {
            $query->where(['Holidays.company_id' => $filters['company_id']]);
        }

        if (!empty($filters['date'])) {
            $query->where(['DATE(Holidays.date)' => $filters['date']]);
        }

        if (!empty($filters['type'])) {
            $query->where(['Holidays.type' => $filters['type']]);
        }

        // Configurar paginación
        $this->paginate = [
            'limit' => 25,
            'maxLimit' => 100,
        ];

        $holidays = $this->paginate($query);

        // Estadísticas del año actual
        $stats = $this->_getHolidayStats($year, $filters);

        // Obtener opciones para filtros
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        // Años disponibles
        $years = $this->Holidays->find()
            ->select(['year' => 'YEAR(date)'])
            ->group(['YEAR(date)'])
            ->orderBy(['year' => 'DESC'])
            ->toArray();

        // Extraer solo los años
        $years = array_column($years, 'year');

        // Asegurar que el año actual se pase en los filtros
        $filters['year'] = $year;

        $this->set(compact('holidays', 'filters', 'stats', 'companies', 'years'));
    }

    /**
     * Función view - Detalle de un festivo
     *
     * @param string|null $id Holiday id.
     * @return \Cake\Http\Response|null|void
     */
    public function view(?string $id = null)
    {
        $holiday = $this->Holidays->get($id, [
            'contain' => ['Companies'],
        ]);

        // Registrar visualización
        $this->Logging->logView('holidays', (int)$id);

        // Obtener festivos relacionados (mismo año y empresa)
        $relatedHolidays = $this->Holidays->find()
            ->where([
                'company_id' => $holiday->company_id,
                'YEAR(date)' => $holiday->date->format('Y'),
                'id !=' => $id,
            ])
            ->orderBy(['date' => 'ASC'])
            ->limit(10)
            ->toArray();

        // Verificar si hay empleados que trabajan en esta fecha
        $conflictingUsers = $this->_getConflictingUsers($holiday);

        $this->set(compact('holiday', 'relatedHolidays', 'conflictingUsers'));
    }

    /**
     * Función add - Crear un nuevo festivo
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $holiday = $this->Holidays->newEmptyEntity();

        if ($this->request->is('post')) {
            $holiday = $this->Holidays->patchEntity($holiday, $this->request->getData());

            if ($this->Holidays->save($holiday)) {
                // Registrar creación
                $this->Logging->logCreate('holidays', $holiday->id, [
                    'holiday_name' => $holiday->name,
                    'company_id' => $holiday->company_id,
                    'date' => $holiday->date->format('Y-m-d'),
                    'type' => $holiday->type,
                    'is_active' => $holiday->is_active,
                ]);

                $this->Flash->success(__('_FESTIVO_CREADO_CORRECTAMENTE'));

                return $this->redirect(['action' => 'view', $holiday->id]);
            }

            $this->Flash->error(__('_ERROR_AL_CREAR_FESTIVO'));
        }

        // Obtener empresas para el formulario
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        $this->set(compact('holiday', 'companies'));
    }

    /**
     * Función edit - Editar un festivo
     *
     * @param string|null $id Holiday id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit(?string $id = null)
    {
        $holiday = $this->Holidays->get($id, [
            'contain' => ['Companies'],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $holiday = $this->Holidays->patchEntity($holiday, $this->request->getData());

            if ($this->Holidays->save($holiday)) {
                // Registrar actualización
                $this->Logging->logUpdate('holidays', (int)$id, [
                    'updated_fields' => array_keys($this->request->getData()),
                    'holiday_name' => $holiday->name,
                    'company_name' => $holiday->company->name ?? '',
                ]);

                $this->Flash->success(__('_FESTIVO_ACTUALIZADO_CORRECTAMENTE'));

                return $this->redirect(['action' => 'view', $id]);
            }

            $this->Flash->error(__('_ERROR_AL_ACTUALIZAR_FESTIVO'));
        }

        // Obtener empresas para el formulario
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        $this->set(compact('holiday', 'companies'));
    }

    /**
     * Función delete - Eliminar un festivo
     *
     * @param string|null $id Holiday id.
     * @return \Cake\Http\Response|null
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $holiday = $this->Holidays->get($id, ['contain' => ['Companies']]);

        if ($this->Holidays->delete($holiday)) {
            // Registrar eliminación
            $this->Logging->logDelete('holidays', (int)$id, [
                'holiday_name' => $holiday->name,
                'company_name' => $holiday->company->name ?? '',
                'date' => $holiday->date->format('Y-m-d'),
                'hard_delete' => true,
            ]);

            $this->Flash->success(__('_FESTIVO_ELIMINADO_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_ELIMINAR_FESTIVO'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Función calendar - Vista de calendario de festivos
     *
     * @return \Cake\Http\Response|null|void
     */
    public function calendar()
    {
        // Registrar acceso
        $this->Logging->logView('holidays_calendar');

        $year = $this->request->getQuery('year', date('Y'));
        $companyId = $this->request->getQuery('company_id');

        $query = $this->Holidays->find()
            ->contain(['Companies'])
            ->where(['YEAR(date)' => $year])
            ->orderBy(['date' => 'ASC']);

        if ($companyId) {
            $query->where(['company_id' => $companyId]);
        }

        $holidays = $query->toArray();

        // Agrupar por mes
        $holidaysByMonth = [];
        foreach ($holidays as $holiday) {
            $month = $holiday->date->format('n');
            if (!isset($holidaysByMonth[$month])) {
                $holidaysByMonth[$month] = [];
            }
            $holidaysByMonth[$month][] = $holiday;
        }

        // Obtener empresas para filtros
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        // Estadísticas del año
        $yearStats = $this->_getYearStats($year, $companyId);

        $this->set(compact('holidaysByMonth', 'year', 'companyId', 'companies', 'yearStats'));
    }

    /**
     * Función bulk - Creación masiva de festivos
     *
     * @return \Cake\Http\Response|null|void
     */
    public function bulk()
    {
        if ($this->request->is('post')) {
            $companyId = $this->request->getData('company_id');
            $year = $this->request->getData('year');
            $holidayType = $this->request->getData('type', 'national');

            if ($companyId && $year) {
                $created = $this->_createNationalHolidays($companyId, $year, $holidayType);

                if ($created > 0) {
                    // Registrar creación masiva
                    $this->Logging->logCreate('holidays', 0, [
                        'action' => 'bulk_create',
                        'company_id' => $companyId,
                        'year' => $year,
                        'type' => $holidayType,
                        'created_count' => $created,
                    ]);

                    $this->Flash->success(__('_FESTIVOS_CREADOS_CORRECTAMENTE', $created));
                } else {
                    $this->Flash->warning(__('_NO_SE_CREARON_FESTIVOS_NUEVOS'));
                }

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('_ERROR_DATOS_OBLIGATORIOS'));
        }

        // Obtener empresas para el formulario
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        $this->set(compact('companies'));
    }

    /**
     * Función export - Exportar lista de festivos a CSV
     *
     * @return \Cake\Http\Response
     */
    public function export()
    {
        $this->request->allowMethod(['get']);

        // Registrar exportación
        $this->Logging->logExport('holidays', [
            'format' => 'csv',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);

        $filters = $this->request->getQueryParams();

        $query = $this->Holidays->find()
            ->contain(['Companies'])
            ->orderBy(['Holidays.date' => 'ASC']);

        // Aplicar filtros
        if (!empty($filters['company_id'])) {
            $query->where(['company_id' => $filters['company_id']]);
        }

        if (!empty($filters['year'])) {
            $query->where(['YEAR(date)' => $filters['year']]);
        }

        $holidays = $query->toArray();

        // Preparar datos CSV
        $csvData = [];
        $csvData[] = [
            __('_NOMBRE'),
            __('_FECHA'),
            __('_EMPRESA'),
            __('_TIPO'),
            __('_RECURRENTE'),
            __('_ACTIVO'),
            __('_FECHA_CREACION'),
        ];

        foreach ($holidays as $holiday) {
            $csvData[] = [
                $holiday->name,
                $holiday->date->format('Y-m-d'),
                $holiday->company->name ?? '',
                $this->_getTypeLabel($holiday->type),
                $holiday->recurring ? __('_SI') : __('_NO'),
                $holiday->is_active ? __('_SI') : __('_NO'),
                $holiday->created->format('Y-m-d'),
            ];
        }

        // Generar CSV
        $filename = 'holidays_' . date('Y-m-d_H-i-s') . '.csv';

        $this->response = $this->response->withType('text/csv');
        $this->response = $this->response
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // Crear contenido CSV
        $output = fopen('php://output', 'w');
        // UTF-8 BOM para Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        foreach ($csvData as $row) {
            fputcsv($output, $row, ';', '"');
        }
        fclose($output);

        return $this->response;
    }

    /**
     * Obtener estadísticas generales de festivos
     *
     * @param int|null $year Año específico, si no se pasa se usa el actual
     * @param array $filters Filtros adicionales (is_active, company_id, etc
     * @return array
     */
    private function _getHolidayStats(?int $year = null, array $filters = []): array
    {
        $year = $year ?? date('Y');

        // Total de festivos del año
        $totalQuery = $this->Holidays->find()
            ->where(['YEAR(date)' => $year]);

        // Aplicar filtro de estado
        if (isset($filters['is_active'])) {
            $totalQuery->where(['is_active' => (bool)$filters['is_active']]);
        } else {
            $totalQuery->where(['is_active' => true]);
        }

        $total = $totalQuery->count();

        // Total de empresas con festivos en el año
        $companiesWithHolidays = $this->Holidays->find()
            ->select(['company_id'])
            ->where([
                'YEAR(date)' => $year,
                'company_id IS NOT NULL',
            ])
            ->distinct(['company_id'])
            ->count();

        // Media de festivos por empresa
        $avgPerCompany = $companiesWithHolidays > 0 ? round($total / $companiesWithHolidays, 1) : 0;

        // Festivos por tipo del año
        $byTypeQuery = $this->Holidays->find()
            ->select([
                'type',
                'count' => 'COUNT(Holidays.id)',
            ])
            ->where(['YEAR(date)' => $year]);

        if (isset($filters['is_active'])) {
            $byTypeQuery->where(['is_active' => (bool)$filters['is_active']]);
        } else {
            $byTypeQuery->where(['is_active' => true]);
        }

        $byType = $byTypeQuery->group(['type'])->toArray();

        // Próximos festivos del año (desde hoy)
        $upcomingQuery = $this->Holidays->find()
            ->where([
                'YEAR(date)' => $year,
                'date >=' => date('Y-m-d'),
            ]);

        if (isset($filters['is_active'])) {
            $upcomingQuery->where(['is_active' => (bool)$filters['is_active']]);
        } else {
            $upcomingQuery->where(['is_active' => true]);
        }

        $upcoming = $upcomingQuery->count();

        return [
            'total' => $total,
            'total_companies' => $companiesWithHolidays,
            'avg_per_company' => $avgPerCompany,
            'by_type' => $byType,
            'upcoming' => $upcoming,
            'year' => $year,
        ];
    }

    /**
     * Obtener estadísticas de un año específico
     *
     * @param int $year
     * @param int|null $companyId
     * @return array
     */
    private function _getYearStats(int $year, ?int $companyId = null): array
    {
        $query = $this->Holidays->find()
            ->where(['YEAR(date)' => $year]);

        if ($companyId) {
            $query->where(['company_id' => $companyId]);
        }

        $total = $query->count();
        $active = $query->where(['is_active' => true])->count();

        // Por tipo
        $byType = $this->Holidays->find()
            ->select([
                'type',
                'count' => 'COUNT(Holidays.id)',
            ])
            ->where(['YEAR(date)' => $year])
            ->group(['type'])
            ->toArray();

        return [
            'total' => $total,
            'active' => $active,
            'by_type' => $byType,
            'year' => $year,
        ];
    }

    /**
     * Verificar usuarios que podrían tener conflictos con el festivo
     *
     * @param \JornaticCore\Model\Entity\Holiday $holiday
     * @return array
     */
    private function _getConflictingUsers(Holiday $holiday): array
    {
        // Buscar asistencias en la fecha del festivo
        $Attendances = $this->getTable('JornaticCore.Attendances');

        $attendances = $Attendances->find()
            ->contain(['Users'])
            ->matching('Users', function ($q) use ($holiday) {
                return $q->where(['Users.company_id' => $holiday->company_id]);
            })
            ->where(['DATE(timestamp)' => $holiday->date->format('Y-m-d')])
            ->toArray();

        return $attendances;
    }

    /**
     * Crear festivos nacionales automáticamente
     *
     * @param int $companyId
     * @param int $year
     * @param string $type
     * @return int Número de festivos creados
     */
    private function _createNationalHolidays(int $companyId, int $year, string $type): int
    {
        // Festivos nacionales españoles básicos
        $nationalHolidays = [
            '01-01' => 'Año Nuevo',
            '01-06' => 'Epifanía del Señor',
            '05-01' => 'Día del Trabajador',
            '08-15' => 'Asunción de la Virgen',
            '10-12' => 'Fiesta Nacional de España',
            '11-01' => 'Todos los Santos',
            '12-06' => 'Día de la Constitución Española',
            '12-08' => 'Inmaculada Concepción',
            '12-25' => 'Navidad',
        ];

        $created = 0;

        foreach ($nationalHolidays as $dateStr => $name) {
            $date = new DateTime("$year-$dateStr");

            // Verificar si ya existe
            $exists = $this->Holidays->find()
                ->where([
                    'company_id' => $companyId,
                    'date' => $date->format('Y-m-d'),
                ])
                ->count() > 0;

            if (!$exists) {
                $holiday = $this->Holidays->newEntity([
                    'company_id' => $companyId,
                    'name' => $name,
                    'date' => $date,
                    'type' => $type,
                    'is_active' => true,
                ]);

                if ($this->Holidays->save($holiday)) {
                    $created++;
                }
            }
        }

        return $created;
    }

    /**
     * Obtener etiqueta de tipo traducida
     *
     * @param string $type
     * @return string
     */
    private function _getTypeLabel(string $type): string
    {
        return match ($type) {
            'national' => __('_NACIONAL'),
            'regional' => __('_REGIONAL'),
            'company' => __('_EMPRESA'),
            default => ucfirst($type)
        };
    }
}
