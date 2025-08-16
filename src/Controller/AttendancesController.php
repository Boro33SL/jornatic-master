<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Attendances Controller
 *
 * Gestión completa de asistencias del ecosistema Jornatic
 *
 * @property \JornaticCore\Model\Table\AttendancesTable $Attendances
 */
class AttendancesController extends AppController
{
    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Cargar el modelo desde el plugin
        $this->Attendances = $this->getTable('JornaticCore.Attendances');
        
        // Cargar componente de logging
        $this->loadComponent('Logging');

        // Skip authorization para todas las acciones (por ahora)
        $this->Authorization->skipAuthorization();
    }

    /**
     * Index method - Lista paginada de asistencias
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        // Registrar acceso
        $this->Logging->logView('attendances_list');

        // Query base con relaciones
        $query = $this->Attendances->find()
            ->contain(['Users' => ['Companies', 'Departments']])
            ->orderBy(['Attendances.timestamp' => 'DESC']);

        // Aplicar filtros si existen
        $filters = $this->request->getQueryParams();
        
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->matching('Users', function($q) use ($search) {
                return $q->where([
                    'OR' => [
                        'Users.name LIKE' => $search,
                        'Users.lastname LIKE' => $search,
                        'Users.email LIKE' => $search,
                        'Users.dni_nie LIKE' => $search,
                    ]
                ]);
            });
        }
        
        if (!empty($filters['company_id'])) {
            $query->matching('Users', function($q) use ($filters) {
                return $q->where(['Users.company_id' => $filters['company_id']]);
            });
        }
        
        if (!empty($filters['company_id'])) {
            $query->matching('Users', function($q) use ($filters) {
                return $q->where(['Users.company_id' => $filters['company_id']]);
            });
        }
        
        if (!empty($filters['user_id'])) {
            $query->where(['Attendances.user_id' => $filters['user_id']]);
        }
        
        if (!empty($filters['type'])) {
            $query->where(['Attendances.type' => $filters['type']]);
        }
        
        if (!empty($filters['date_from'])) {
            $query->where(['DATE(Attendances.timestamp) >=' => $filters['date_from']]);
        }
        
        if (!empty($filters['date_to'])) {
            $query->where(['DATE(Attendances.timestamp) <=' => $filters['date_to']]);
        }

        // Configurar paginación
        $this->paginate = [
            'limit' => 50,
            'maxLimit' => 200,
        ];

        $attendances = $this->paginate($query);

        // Estadísticas
        $stats = $this->_getAttendanceStats();

        // Obtener opciones para filtros
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();
        
        // Si hay una empresa seleccionada, obtener sus usuarios
        $users = [];
        if (!empty($filters['company_id'])) {
            $Users = $this->getTable('JornaticCore.Users');
            $users = $Users->find('list', [
                'keyField' => 'id',
                'valueField' => function ($user) {
                    return $user->name . ' ' . $user->lastname;
                }
            ])
            ->where(['company_id' => $filters['company_id']])
            ->toArray();
        }

        $this->set(compact('attendances', 'filters', 'stats', 'companies', 'users'));
    }

    /**
     * View method - Detalle de una asistencia
     *
     * @param string|null $id Attendance id.
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $attendance = $this->Attendances->get($id, [
            'contain' => [
                'Users' => ['Companies', 'Departments', 'Roles'],
            ],
        ]);

        // Registrar visualización
        $this->Logging->logView('attendances', (int)$id);

        // Obtener asistencias relacionadas del mismo día
        $relatedAttendances = $this->Attendances->find()
            ->contain(['Users'])
            ->where([
                'Attendances.user_id' => $attendance->user_id,
                'DATE(Attendances.timestamp)' => $attendance->timestamp->format('Y-m-d'),
                'Attendances.id !=' => $id
            ])
            ->orderBy(['Attendances.timestamp' => 'ASC'])
            ->toArray();

        // Obtener estadísticas del día
        $dayStats = $this->_getDayAttendanceStats($attendance);

        // Debug temporal para verificar datos
        $this->log('DayStats: ' . json_encode($dayStats), 'debug');
        $this->log('RelatedAttendances count: ' . count($relatedAttendances), 'debug');

        $this->set(compact('attendance', 'relatedAttendances', 'dayStats'));
    }

    /**
     * Edit method - Editar una asistencia
     *
     * @param string|null $id Attendance id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit($id = null)
    {
        $attendance = $this->Attendances->get($id, [
            'contain' => ['Users' => ['Companies']],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $oldData = [
                'datetime' => $attendance->timestamp->format('Y-m-d H:i:s'),
                'type' => $attendance->type,
                'location' => $attendance->location,
            ];
            
            $attendance = $this->Attendances->patchEntity($attendance, $this->request->getData());
            
            if ($this->Attendances->save($attendance)) {
                // Registrar actualización
                $this->Logging->logUpdate('attendances', (int)$id, [
                    'updated_fields' => array_keys($this->request->getData()),
                    'user_name' => $attendance->user->name . ' ' . $attendance->user->lastname,
                    'old_data' => $oldData,
                    'new_datetime' => $attendance->timestamp->format('Y-m-d H:i:s'),
                    'new_type' => $attendance->type
                ]);
                
                $this->Flash->success(__('_ASISTENCIA_ACTUALIZADA_CORRECTAMENTE'));
                return $this->redirect(['type' => 'view', $id]);
            }
            
            $this->Flash->error(__('_ERROR_AL_ACTUALIZAR_ASISTENCIA'));
        }

        $this->set(compact('attendance'));
    }

    /**
     * Delete method - Eliminar una asistencia
     *
     * @param string|null $id Attendance id.
     * @return \Cake\Http\Response|null
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        
        $attendance = $this->Attendances->get($id, [
            'contain' => ['Users' => ['Companies']]
        ]);
        
        if ($this->Attendances->delete($attendance)) {
            // Registrar eliminación
            $this->Logging->logDelete('attendances', (int)$id, [
                'user_name' => $attendance->user->name . ' ' . $attendance->user->lastname,
                'company_name' => $attendance->user->company->name ?? '',
                'datetime' => $attendance->timestamp->format('Y-m-d H:i:s'),
                'type' => $attendance->type,
                'hard_delete' => true
            ]);
            
            $this->Flash->success(__('_ASISTENCIA_ELIMINADA_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_ELIMINAR_ASISTENCIA'));
        }

        return $this->redirect(['type' => 'index']);
    }

    /**
     * Daily method - Ver asistencias por día
     *
     * @return \Cake\Http\Response|null|void
     */
    public function daily()
    {
        // Registrar acceso
        $this->Logging->logView('attendances_daily');

        $date = $this->request->getQuery('date', date('Y-m-d'));
        $companyId = $this->request->getQuery('company_id');

        $query = $this->Attendances->find()
            ->contain(['Users' => ['Companies', 'Departments']])
            ->where(['DATE(Attendances.timestamp)' => $date])
            ->orderBy(['Attendances.timestamp' => 'ASC']);

        if ($companyId) {
            $query->matching('Users', function($q) use ($companyId) {
                return $q->where(['Users.company_id' => $companyId]);
            });
        }

        $attendances = $query->toArray();

        // Agrupar por usuario
        $attendancesByUser = [];
        foreach ($attendances as $attendance) {
            $userId = $attendance->user_id;
            if (!isset($attendancesByUser[$userId])) {
                $attendancesByUser[$userId] = [
                    'user' => $attendance->user,
                    'attendances' => []
                ];
            }
            $attendancesByUser[$userId]['attendances'][] = $attendance;
        }

        // Estadísticas del día
        $dailyStats = $this->_getDailyStats($date, $companyId);

        // Obtener empresas para filtros
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        $this->set(compact('attendancesByUser', 'date', 'companyId', 'dailyStats', 'companies'));
    }

    /**
     * Reports method - Reportes de asistencias
     *
     * @return \Cake\Http\Response|null|void
     */
    public function reports()
    {
        // Registrar acceso
        $this->Logging->logView('attendances_reports');

        $filters = $this->request->getQueryParams();
        $dateFrom = $filters['date_from'] ?? date('Y-m-01'); // Primer día del mes
        $dateTo = $filters['date_to'] ?? date('Y-m-t'); // Último día del mes
        $companyId = $filters['company_id'] ?? null;

        // Resumen por usuario
        $userSummary = $this->_getUserAttendanceSummary($dateFrom, $dateTo, $companyId);

        // Estadísticas del período
        $periodStats = $this->_getPeriodStats($dateFrom, $dateTo, $companyId);

        // Obtener empresas para filtros
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        $this->set(compact('userSummary', 'periodStats', 'filters', 'companies'));
    }

    /**
     * Export method - Exportar asistencias a CSV
     *
     * @return \Cake\Http\Response
     */
    public function export()
    {
        $this->request->allowMethod(['get']);

        // Registrar exportación
        $this->Logging->logExport('attendances', [
            'format' => 'csv',
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        $filters = $this->request->getQueryParams();
        
        $query = $this->Attendances->find()
            ->contain(['Users' => ['Companies', 'Departments']])
            ->orderBy(['Attendances.timestamp' => 'DESC']);

        // Aplicar filtros
        if (!empty($filters['date_from'])) {
            $query->where(['DATE(Attendances.timestamp) >=' => $filters['date_from']]);
        }
        
        if (!empty($filters['date_to'])) {
            $query->where(['DATE(Attendances.timestamp) <=' => $filters['date_to']]);
        }
        
        if (!empty($filters['company_id'])) {
            $query->matching('Users', function($q) use ($filters) {
                return $q->where(['Users.company_id' => $filters['company_id']]);
            });
        }

        $attendances = $query->toArray();

        // Preparar datos CSV
        $csvData = [];
        $csvData[] = [
            __('_EMPLEADO'),
            __('_EMAIL'),
            __('_EMPRESA'),
            __('_DEPARTAMENTO'),
            __('_FECHA_HORA'),
            __('_ACCION'),
            __('_UBICACION'),
            __('_DISPOSITIVO'),
            __('_IP'),
        ];

        foreach ($attendances as $attendance) {
            $csvData[] = [
                $attendance->user->name . ' ' . $attendance->user->lastname,
                $attendance->user->email,
                $attendance->user->company->name ?? '',
                $attendance->user->department->name ?? '',
                $attendance->timestamp->format('Y-m-d H:i:s'),
                $this->_getActionLabel($attendance->type),
                $attendance->location ?? '',
                $attendance->device_info ?? '',
                $attendance->ip_address ?? '',
            ];
        }

        // Generar CSV
        $filename = 'attendances_' . date('Y-m-d_H-i-s') . '.csv';
        
        $this->response = $this->response->withType('text/csv');
        $this->response = $this->response->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // Crear contenido CSV
        $output = fopen('php://output', 'w');
        // UTF-8 BOM para Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        foreach ($csvData as $row) {
            fputcsv($output, $row, ';', '"');
        }
        fclose($output);

        return $this->response;
    }

    /**
     * Obtener estadísticas generales de asistencias
     *
     * @return array
     */
    private function _getAttendanceStats(): array
    {
        $today = date('Y-m-d');
        
        // Total de asistencias
        $totalAttendances = $this->Attendances->find()->count();
        
        // Total de empresas con asistencias
        $totalCompanies = $this->Attendances->find()
            ->matching('Users.Companies')
            ->select(['Companies.id'])
            ->distinct(['Companies.id'])
            ->count();
            
        // Media de asistencias por empresa
        $avgPerCompany = $totalCompanies > 0 ? round($totalAttendances / $totalCompanies, 1) : 0;
        
        // Asistencias de hoy
        $todayTotal = $this->Attendances->find()
            ->where(['DATE(timestamp)' => $today])
            ->count();
            
        $todayCheckIns = $this->Attendances->find()
            ->where([
                'DATE(timestamp)' => $today,
                'type' => 'in'
            ])
            ->count();
            
        $todayCheckOuts = $this->Attendances->find()
            ->where([
                'DATE(timestamp)' => $today,
                'type' => 'out'
            ])
            ->count();
            
        // Este mes
        $thisMonth = $this->Attendances->find()
            ->where([
                'MONTH(timestamp)' => date('m'),
                'YEAR(timestamp)' => date('Y')
            ])
            ->count();

        return [
            'total' => $totalAttendances,
            'total_companies' => $totalCompanies,
            'avg_per_company' => $avgPerCompany,
            'today_total' => $todayTotal,
            'today_ins' => $todayCheckIns,
            'today_outs' => $todayCheckOuts,
            'this_month_total' => $thisMonth,
        ];
    }

    /**
     * Obtener estadísticas de asistencias de un día específico
     *
     * @param \JornaticCore\Model\Entity\Attendance $attendance
     * @return array
     */
    private function _getDayAttendanceStats($attendance): array
    {
        $date = $attendance->timestamp->format('Y-m-d');
        $userId = $attendance->user_id;

        $dayAttendances = $this->Attendances->find()
            ->where([
                'Attendances.user_id' => $userId,
                'DATE(Attendances.timestamp)' => $date
            ])
            ->orderBy(['Attendances.timestamp' => 'ASC'])
            ->toArray();

        // Debug temporal
        $this->log("Date filter: $date, User ID: $userId", 'debug');
        $this->log('Day attendances found: ' . count($dayAttendances), 'debug');
        foreach ($dayAttendances as $att) {
            $this->log("Attendance type: {$att->type}, time: {$att->timestamp->format('H:i:s')}", 'debug');
        }

        $checkIns = array_filter($dayAttendances, function($att) {
            return $att->type === 'in';
        });
        
        $checkOuts = array_filter($dayAttendances, function($att) {
            return $att->type === 'out';
        });
        
        $breakStarts = array_filter($dayAttendances, function($att) {
            return $att->type === 'break_start';
        });
        
        $breakEnds = array_filter($dayAttendances, function($att) {
            return $att->type === 'break_end';
        });

        // Calcular horas trabajadas descontando descansos
        $hoursWorked = 0;
        $totalHoursFormatted = null;
        
        if (count($checkIns) > 0 && count($checkOuts) > 0) {
            // Ordenar todas las asistencias por tiempo
            usort($dayAttendances, function($a, $b) {
                return $a->timestamp <=> $b->timestamp;
            });
            
            $workingMinutes = 0;
            $isWorking = false;
            $lastTimestamp = null;
            
            foreach ($dayAttendances as $attendance) {
                switch ($attendance->type) {
                    case 'in':
                    case 'break_end':
                        // Inicio de período de trabajo
                        if (!$isWorking) {
                            $isWorking = true;
                            $lastTimestamp = $attendance->timestamp;
                        }
                        break;
                        
                    case 'break_start':
                    case 'out':
                        // Fin de período de trabajo
                        if ($isWorking && $lastTimestamp) {
                            $diff = $lastTimestamp->diff($attendance->timestamp);
                            $workingMinutes += ($diff->h * 60) + $diff->i;
                            $isWorking = false;
                            $lastTimestamp = null;
                        }
                        break;
                }
            }
            
            // Convertir minutos trabajados a formato "X horas Y minutos"
            $hours = intval($workingMinutes / 60);
            $minutes = $workingMinutes % 60;
            
            $totalHoursFormatted = null;
            if ($workingMinutes > 0) {
                if ($hours > 0 && $minutes > 0) {
                    $totalHoursFormatted = $hours . ' horas ' . $minutes . ' minutos';
                } elseif ($hours > 0) {
                    $totalHoursFormatted = $hours . ' horas';
                } else {
                    $totalHoursFormatted = $minutes . ' minutos';
                }
            }
            
            $this->log("Working minutes calculated: $workingMinutes (= $hours hours, $minutes minutes)", 'debug');
        }

        return [
            'total_attendances' => count($dayAttendances),
            'ins' => count($checkIns),
            'outs' => count($checkOuts),
            'breaks' => count($breakStarts), // Usar break_start como referencia para contar descansos
            'total_hours' => $totalHoursFormatted,
            'first_in' => count($checkIns) > 0 ? reset($checkIns)->timestamp->format('H:i') : null,
            'last_out' => count($checkOuts) > 0 ? end($checkOuts)->timestamp->format('H:i') : null,
        ];
    }

    /**
     * Obtener estadísticas diarias
     *
     * @param string $date
     * @param int|null $companyId
     * @return array
     */
    private function _getDailyStats($date, $companyId = null): array
    {
        $query = $this->Attendances->find()
            ->where(['DATE(timestamp)' => $date]);

        if ($companyId) {
            $query->matching('Users', function($q) use ($companyId) {
                return $q->where(['Users.company_id' => $companyId]);
            });
        }

        $total = $query->count();
        $checkIns = $query->where(['type' => 'in'])->count();
        $checkOuts = $query->where(['type' => 'out'])->count();
        
        // Usuarios únicos que ficharon
        $uniqueUsers = $this->Attendances->find()
            ->select(['user_id'])
            ->where(['DATE(timestamp)' => $date])
            ->group(['user_id'])
            ->count();

        return [
            'total' => $total,
            'ins' => $checkIns,
            'outs' => $checkOuts,
            'unique_users' => $uniqueUsers,
        ];
    }

    /**
     * Obtener resumen de asistencias por usuario en un período
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @param int|null $companyId
     * @return array
     */
    private function _getUserAttendanceSummary($dateFrom, $dateTo, $companyId = null): array
    {
        $query = $this->Attendances->find()
            ->contain(['Users' => ['Companies']])
            ->where([
                'DATE(Attendances.timestamp) >=' => $dateFrom,
                'DATE(Attendances.timestamp) <=' => $dateTo
            ]);

        if ($companyId) {
            $query->matching('Users', function($q) use ($companyId) {
                return $q->where(['Users.company_id' => $companyId]);
            });
        }

        $attendances = $query->toArray();
        
        $summary = [];
        foreach ($attendances as $attendance) {
            $userId = $attendance->user_id;
            if (!isset($summary[$userId])) {
                $summary[$userId] = [
                    'user' => $attendance->user,
                    'total_attendances' => 0,
                    'ins' => 0,
                    'outs' => 0,
                    'unique_days' => [],
                ];
            }
            
            $summary[$userId]['total_attendances']++;
            if ($attendance->type === 'in') {
                $summary[$userId]['ins']++;
            } elseif ($attendance->type === 'out') {
                $summary[$userId]['outs']++;
            }
            
            $day = $attendance->timestamp->format('Y-m-d');
            $summary[$userId]['unique_days'][$day] = true;
        }
        
        // Convertir unique_days a count
        foreach ($summary as &$userSummary) {
            $userSummary['unique_days'] = count($userSummary['unique_days']);
        }

        return $summary;
    }

    /**
     * Obtener estadísticas de un período
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @param int|null $companyId
     * @return array
     */
    private function _getPeriodStats($dateFrom, $dateTo, $companyId = null): array
    {
        $query = $this->Attendances->find()
            ->where([
                'DATE(timestamp) >=' => $dateFrom,
                'DATE(timestamp) <=' => $dateTo
            ]);

        if ($companyId) {
            $query->matching('Users', function($q) use ($companyId) {
                return $q->where(['Users.company_id' => $companyId]);
            });
        }

        $total = $query->count();
        $checkIns = $query->where(['type' => 'in'])->count();
        $checkOuts = $query->where(['type' => 'out'])->count();

        return [
            'total' => $total,
            'ins' => $checkIns,
            'outs' => $checkOuts,
            'period_from' => $dateFrom,
            'period_to' => $dateTo,
        ];
    }

    /**
     * Obtener etiqueta de acción traducida
     *
     * @param string $action
     * @return string
     */
    private function _getActionLabel($action): string
    {
        return match($action) {
            'in' => __('_ENTRADA'),
            'out' => __('_SALIDA'),
            'break_start' => __('_INICIO_DESCANSO'),
            'break_end' => __('_FIN_DESCANSO'),
            default => ucfirst($action)
        };
    }
}