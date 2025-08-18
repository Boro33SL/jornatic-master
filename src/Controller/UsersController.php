<?php
declare(strict_types=1);

namespace App\Controller;

use JornaticCore\Model\Entity\User;
use JornaticCore\Model\Table\UsersTable;

/**
 * Users Controller
 * Gestión completa de usuarios del ecosistema Jornatic
 *
 * @property \JornaticCore\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * Users table instance
     *
     * @var \JornaticCore\Model\Table\UsersTable
     */
    protected UsersTable $Users;

    /**
     * Función de inicialización
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Cargar el modelo desde el plugin
        $this->Users = $this->getTable('JornaticCore.Users');

        // Cargar componente de logging
        $this->loadComponent('Logging');

        // Skip authorization para todas las acciones (por ahora)
        $this->Authorization->skipAuthorization();
    }

    /**
     * Función index - Lista paginada de usuarios
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        // Registrar acceso
        $this->Logging->logView('users_list');

        // Query base con relaciones
        $query = $this->Users->find()
            ->contain(['Companies', 'Departments', 'Roles'])
            ->orderBy(['Users.created' => 'DESC']);

        // Aplicar filtros si existen
        $filters = $this->request->getQueryParams();

        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where([
                'OR' => [
                    'Users.name LIKE' => $search,
                    'Users.lastname LIKE' => $search,
                    'Users.email LIKE' => $search,
                    'Users.dni_nie LIKE' => $search,
                ],
            ]);
        }

        if (!empty($filters['company_id'])) {
            $query->where(['Users.company_id' => $filters['company_id']]);
        }

        if (!empty($filters['department_id'])) {
            $query->where(['Users.department_id' => $filters['department_id']]);
        }

        if (!empty($filters['role_id'])) {
            $query->where(['Users.role_id' => $filters['role_id']]);
        }

        if (isset($filters['is_active'])) {
            $query->where(['Users.is_active' => (bool)$filters['is_active']]);
        }

        // Configurar paginación
        $this->paginate = [
            'limit' => 25,
            'maxLimit' => 100,
        ];

        $users = $this->paginate($query);

        // Estadísticas
        $stats = $this->_getUserStats();

        // Obtener opciones para filtros
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        $Departments = $this->getTable('JornaticCore.Departments');
        $departments = $Departments->find('list')->toArray();

        $Roles = $this->getTable('JornaticCore.Roles');
        $roles = $Roles->find('list')->toArray();

        $this->set(compact('users', 'filters', 'stats', 'companies', 'departments', 'roles'));
    }

    /**
     * Obtener estadísticas generales de usuarios
     *
     * @return array
     */
    private function _getUserStats(): array
    {
        $total = $this->Users->find()->count();

        $active = $this->Users->find()
            ->where(['is_active' => true])
            ->count();

        $thisMonth = $this->Users->find()
            ->where([
                'MONTH(Users.created)' => date('m'),
                'YEAR(Users.created)' => date('Y'),
            ])
            ->count();

        $withContracts = $this->Users->find()
            ->matching('Contracts')
            ->count();

        return [
            'total' => $total,
            'active' => $active,
            'new_this_month' => $thisMonth,
            'with_contracts' => $withContracts,
        ];
    }

    /**
     * Función view - Detalle de un usuario
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void
     */
    public function view(?string $id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [
                'Companies',
                'Departments',
                'Roles',
                'Contracts' => function ($q) {
                    return $q->orderBy(['Contracts.start_date' => 'DESC']);
                },
                'Attendances' => function ($q) {
                    return $q->orderBy(['Attendances.timestamp' => 'DESC'])->limit(10);
                },
                'Absences' => function ($q) {
                    return $q->orderBy(['Absences.created' => 'DESC'])->limit(10);
                },
            ],
        ]);

        // Obtener contrato activo específicamente
        $activeContract = null;
        if (!empty($user->contracts)) {
            foreach ($user->contracts as $contract) {
                if ($contract->is_active) {
                    $activeContract = $contract;
                    break;
                }
            }
        }

        // Registrar visualización
        $this->Logging->logView('users', (int)$id);

        // Obtener estadísticas del usuario
        $userStats = $this->_getSpecificUserStats($user);

        $this->set(compact('user', 'userStats', 'activeContract'));
    }

    /**
     * Obtener estadísticas específicas de un usuario
     *
     * @param \JornaticCore\Model\Entity\User $user
     * @return array
     */
    private function _getSpecificUserStats(User $user): array
    {
        $Attendances = $this->getTable('JornaticCore.Attendances');
        $Absences = $this->getTable('JornaticCore.Absences');
        $Contracts = $this->getTable('JornaticCore.Contracts');

        // Asistencias del mes actual
        $thisMonthAttendances = $Attendances->find()
            ->where([
                'user_id' => $user->id,
                'MONTH(timestamp)' => date('m'),
                'YEAR(timestamp)' => date('Y'),
            ])
            ->count();

        // Ausencias pendientes
        $pendingAbsences = $Absences->find()
            ->where([
                'user_id' => $user->id,
                'status' => 'pending',
            ])
            ->count();

        // Contratos activos
        $activeContracts = $Contracts->find()
            ->where([
                'user_id' => $user->id,
                'is_active' => true,
            ])
            ->count();

        // Último fichaje
        $lastAttendance = $Attendances->find()
            ->where(['user_id' => $user->id])
            ->orderBy(['timestamp' => 'DESC'])
            ->first();

        return [
            'this_month_attendances' => $thisMonthAttendances,
            'pending_absences' => $pendingAbsences,
            'active_contracts' => $activeContracts,
            'last_attendance' => $lastAttendance,
        ];
    }

    /**
     * Función edit - Editar un usuario
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit(?string $id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Companies', 'Departments', 'Roles'],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());

            if ($this->Users->save($user)) {
                // Registrar actualización
                $this->Logging->logUpdate('users', (int)$id, [
                    'updated_fields' => array_keys($this->request->getData()),
                    'user_name' => $user->name . ' ' . $user->lastname,
                    'company_name' => $user->company->name ?? '',
                ]);

                $this->Flash->success(__('_USUARIO_ACTUALIZADO_CORRECTAMENTE'));

                return $this->redirect(['action' => 'view', $id]);
            }

            $this->Flash->error(__('_ERROR_AL_ACTUALIZAR_USUARIO'));
        }

        // Obtener opciones para el formulario
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        $Departments = $this->getTable('JornaticCore.Departments');
        $departments = $Departments->find('list')->toArray();

        $Roles = $this->getTable('JornaticCore.Roles');
        $roles = $Roles->find('list')->toArray();

        $this->set(compact('user', 'companies', 'departments', 'roles'));
    }

    /**
     * Función delete - Eliminar un usuario (soft delete)
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $user = $this->Users->get($id, contain: ['Companies']);

        // Marcar como inactivo en lugar de eliminar
        $user->is_active = false;

        if ($this->Users->save($user)) {
            // Registrar eliminación lógica
            $this->Logging->logDelete('users', (int)$id, [
                'user_name' => $user->name . ' ' . $user->lastname,
                'company_name' => $user->company->name ?? '',
                'soft_delete' => true,
            ]);

            $this->Flash->success(__('_USUARIO_DESACTIVADO_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_DESACTIVAR_USUARIO'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Función activate - Activar un usuario desactivado
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     */
    public function activate(?string $id = null)
    {
        $this->request->allowMethod(['post']);

        $user = $this->Users->get($id, contain: ['Companies']);
        $user->is_active = true;

        if ($this->Users->save($user)) {
            // Registrar activación
            $this->Logging->logUpdate('users', (int)$id, [
                'action' => 'activate',
                'user_name' => $user->name . ' ' . $user->lastname,
                'company_name' => $user->company->name ?? '',
            ]);

            $this->Flash->success(__('_USUARIO_ACTIVADO_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_ACTIVAR_USUARIO'));
        }

        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Función attendances - Ver asistencias de un usuario
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void
     */
    public function attendances(?string $id = null)
    {
        $user = $this->Users->get($id, contain: ['Companies']);

        // Registrar acceso a asistencias
        $this->Logging->logView('user_attendances', (int)$id);

        $Attendances = $this->getTable('JornaticCore.Attendances');

        $query = $Attendances->find()
            ->where(['user_id' => $id])
            ->orderBy(['Attendances.timestamp' => 'DESC']);

        // Aplicar filtros de fecha si existen
        $filters = $this->request->getQueryParams();

        if (!empty($filters['date_from'])) {
            $query->where(['DATE(Attendances.timestamp) >=' => $filters['date_from']]);
        }

        if (!empty($filters['date_to'])) {
            $query->where(['DATE(Attendances.timestamp) <=' => $filters['date_to']]);
        }

        $this->paginate = ['limit' => 50];
        $attendances = $this->paginate($query);

        // Estadísticas de asistencias
        $attendanceStats = $this->_getUserAttendanceStats((int)$id, $filters);

        $this->set(compact('user', 'attendances', 'filters', 'attendanceStats'));
    }

    /**
     * Obtener estadísticas de asistencias de un usuario
     *
     * @param int $userId
     * @param array $filters
     * @return array
     */
    private function _getUserAttendanceStats(int $userId, array $filters = []): array
    {
        $Attendances = $this->getTable('JornaticCore.Attendances');

        $query = $Attendances->find()->where(['user_id' => $userId]);

        if (!empty($filters['date_from'])) {
            $query->where(['DATE(timestamp) >=' => $filters['date_from']]);
        }

        if (!empty($filters['date_to'])) {
            $query->where(['DATE(timestamp) <=' => $filters['date_to']]);
        }

        $total = $query->count();

        $checkIns = $query->where(['action' => 'check_in'])->count();
        $checkOuts = $query->where(['action' => 'check_out'])->count();
        $breaks = $query->where(['action' => 'break_start'])->count();

        return [
            'total' => $total,
            'check_ins' => $checkIns,
            'check_outs' => $checkOuts,
            'breaks' => $breaks,
        ];
    }

    /**
     * Función absences - Ver ausencias de un usuario
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void
     */
    public function absences(?string $id = null)
    {
        $user = $this->Users->get($id, contain: ['Companies']);

        // Registrar acceso a ausencias
        $this->Logging->logView('user_absences', (int)$id);

        $Absences = $this->getTable('JornaticCore.Absences');

        $query = $Absences->find()
            ->contain(['AbsenceTypes'])
            ->where(['user_id' => $id])
            ->orderBy(['Absences.created' => 'DESC']);

        // Aplicar filtros si existen
        $filters = $this->request->getQueryParams();

        if (!empty($filters['status'])) {
            $query->where(['Absences.status' => $filters['status']]);
        }

        if (!empty($filters['absence_type_id'])) {
            $query->where(['Absences.absence_type_id' => $filters['absence_type_id']]);
        }

        $this->paginate = ['limit' => 25];
        $absences = $this->paginate($query);

        // Obtener tipos de ausencia para filtros
        $AbsenceTypes = $this->getTable('JornaticCore.AbsenceTypes');
        $absenceTypes = $AbsenceTypes->find('list')->toArray();

        $this->set(compact('user', 'absences', 'filters', 'absenceTypes'));
    }

    /**
     * Función export - Exportar lista de usuarios a CSV
     *
     * @return \Cake\Http\Response
     */
    public function export()
    {
        $this->request->allowMethod(['get']);

        // Registrar exportación
        $this->Logging->logExport('users', [
            'format' => 'csv',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);

        $users = $this->Users->find()
            ->contain(['Companies', 'Departments', 'Roles'])
            ->orderBy(['Users.created' => 'DESC'])
            ->toArray();

        // Preparar datos CSV
        $csvData = [];
        $csvData[] = [
            __('_NOMBRE'),
            __('_APELLIDOS'),
            __('_EMAIL'),
            __('_DNI_NIE'),
            __('_TELEFONO'),
            __('_EMPRESA'),
            __('_DEPARTAMENTO'),
            __('_ROL'),
            __('_ACTIVO'),
            __('_FECHA_REGISTRO'),
        ];

        foreach ($users as $user) {
            $csvData[] = [
                $user->name,
                $user->lastname ?? '',
                $user->email,
                $user->dni_nie ?? '',
                $user->phone ?? '',
                $user->company->name ?? '',
                $user->department->name ?? '',
                $user->role->name ?? '',
                $user->is_active
                    ? __('_SI')
                    : __('_NO'),
                $user->created->format('Y-m-d'),
            ];
        }

        // Generar CSV
        $filename = 'users_' . date('Y-m-d_H-i-s') . '.csv';

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
}
