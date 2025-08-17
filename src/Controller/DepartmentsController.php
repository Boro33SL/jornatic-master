<?php
declare(strict_types=1);

namespace App\Controller;

use JornaticCore\Model\Entity\Department;

/**
 * Departments Controller
 * Gestión completa de departamentos del ecosistema Jornatic
 *
 * @property \JornaticCore\Model\Table\DepartmentsTable $Departments
 */
class DepartmentsController extends AppController
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
        $this->Departments = $this->getTable('JornaticCore.Departments');

        // Cargar componente de logging
        $this->loadComponent('Logging');

        // Skip authorization para todas las acciones (por ahora)
        $this->Authorization->skipAuthorization();
    }

    /**
     * Función index - Lista paginada de departamentos
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        // Registrar acceso
        $this->Logging->logView('departments_list');

        // Query base con relaciones
        $query = $this->Departments->find()
            ->contain(['Companies', 'Users'])
            ->orderBy(['Departments.created' => 'DESC']);

        // Aplicar filtros si existen
        $filters = $this->request->getQueryParams();

        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where([
                'OR' => [
                    'Departments.name LIKE' => $search,
                    'Departments.description LIKE' => $search,
                ],
            ]);
        }

        if (!empty($filters['company_id'])) {
            $query->where(['Departments.company_id' => $filters['company_id']]);
        }

        if (isset($filters['is_active'])) {
            $query->where(['Departments.active' => (bool)$filters['is_active']]);
        }

        // Configurar paginación
        $this->paginate = [
            'limit' => 20,
            'maxLimit' => 100,
        ];

        $departments = $this->paginate($query);

        // Estadísticas
        $stats = $this->_getDepartmentStats();

        // Obtener empresas para filtros
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        $this->set(compact('departments', 'filters', 'stats', 'companies'));
    }

    /**
     * Obtener estadísticas generales de departamentos
     *
     * @return array
     */
    private function _getDepartmentStats(): array
    {
        $total = $this->Departments->find()->count();

        $active = $this->Departments->find()
            ->where(['active' => true])
            ->count();

        $withUsers = $this->Departments->find()
            ->matching('Users')
            ->count();

        $thisMonth = $this->Departments->find()
            ->where([
                'MONTH(Departments.created)' => date('m'),
                'YEAR(Departments.created)' => date('Y'),
            ])
            ->count();

        // Estadísticas por empresa
        $byCompany = $this->Departments->find()
            ->contain(['Companies'])
            ->select([
                'Companies.name',
                'count' => 'COUNT(Departments.id)',
            ])
            ->group(['Departments.company_id', 'Companies.name'])
            ->toArray();

        return [
            'total' => $total,
            'active' => $active,
            'with_users' => $withUsers,
            'new_this_month' => $thisMonth,
            'by_company' => $byCompany,
        ];
    }

    /**
     * Función view - Detalle de un departamento
     *
     * @param string|null $id Department id.
     * @return \Cake\Http\Response|null|void
     */
    public function view(?string $id = null)
    {
        $department = $this->Departments->get($id, [
            'contain' => [
                'Companies',
                'Users' => function ($q) {
                    return $q->contain(['Roles'])->orderBy(['Users.name' => 'ASC']);
                },
                'CompanySchedules' => function ($q) {
                    return $q->orderBy(['CompanySchedules.created' => 'DESC']);
                },
            ],
        ]);

        // Registrar visualización
        $this->Logging->logView('departments', (int)$id);

        // Obtener estadísticas del departamento
        $departmentStats = $this->_getSpecificDepartmentStats($department);

        $this->set(compact('department', 'departmentStats'));
    }

    /**
     * Obtener estadísticas específicas de un departamento
     *
     * @param \JornaticCore\Model\Entity\Department $department
     * @return array
     */
    private function _getSpecificDepartmentStats(Department $department): array
    {
        $totalUsers = count($department->users ?? []);

        $activeUsers = count(array_filter($department->users ?? [], function ($user) {
            return $user->is_active;
        }));

        // Obtener asistencias del departamento en el mes actual
        $Attendances = $this->getTable('JornaticCore.Attendances');
        $thisMonthAttendances = $Attendances->find()
            ->matching('Users', function ($q) use ($department) {
                return $q->where(['Users.department_id' => $department->id]);
            })
            ->where([
                'MONTH(Attendances.datetime)' => date('m'),
                'YEAR(Attendances.datetime)' => date('Y'),
            ])
            ->count();

        // Obtener ausencias pendientes del departamento
        $Absences = $this->getTable('JornaticCore.Absences');
        $pendingAbsences = $Absences->find()
            ->matching('Users', function ($q) use ($department) {
                return $q->where(['Users.department_id' => $department->id]);
            })
            ->where(['Absences.status' => 'pending'])
            ->count();

        // Obtener contratos activos del departamento
        $Contracts = $this->getTable('JornaticCore.Contracts');
        $activeContracts = $Contracts->find()
            ->matching('Users', function ($q) use ($department) {
                return $q->where(['Users.department_id' => $department->id]);
            })
            ->where(['Contracts.is_active' => true])
            ->count();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $totalUsers - $activeUsers,
            'this_month_attendances' => $thisMonthAttendances,
            'pending_absences' => $pendingAbsences,
            'active_contracts' => $activeContracts,
        ];
    }

    /**
     * Función add - Crear un nuevo departamento
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $department = $this->Departments->newEmptyEntity();

        if ($this->request->is('post')) {
            $department = $this->Departments->patchEntity($department, $this->request->getData());

            if ($this->Departments->save($department)) {
                // Registrar creación
                $this->Logging->logCreate('departments', $department->id, [
                    'department_name' => $department->name,
                    'company_id' => $department->company_id,
                    'is_active' => $department->active,
                ]);

                $this->Flash->success(__('_DEPARTAMENTO_CREADO_CORRECTAMENTE'));

                return $this->redirect(['action' => 'view', $department->id]);
            }

            $this->Flash->error(__('_ERROR_AL_CREAR_DEPARTAMENTO'));
        }

        // Obtener empresas para el formulario
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        $this->set(compact('department', 'companies'));
    }

    /**
     * Función edit - Editar un departamento
     *
     * @param string|null $id Department id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit(?string $id = null)
    {
        $department = $this->Departments->get($id, [
            'contain' => ['Companies'],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $department = $this->Departments->patchEntity($department, $this->request->getData());

            if ($this->Departments->save($department)) {
                // Registrar actualización
                $this->Logging->logUpdate('departments', (int)$id, [
                    'updated_fields' => array_keys($this->request->getData()),
                    'department_name' => $department->name,
                    'company_name' => $department->company->name ?? '',
                ]);

                $this->Flash->success(__('_DEPARTAMENTO_ACTUALIZADO_CORRECTAMENTE'));

                return $this->redirect(['action' => 'view', $id]);
            }

            $this->Flash->error(__('_ERROR_AL_ACTUALIZAR_DEPARTAMENTO'));
        }

        // Obtener empresas para el formulario
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        $this->set(compact('department', 'companies'));
    }

    /**
     * Función delete - Eliminar un departamento (soft delete)
     *
     * @param string|null $id Department id.
     * @return \Cake\Http\Response|null
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $department = $this->Departments->get($id, ['contain' => ['Companies', 'Users']]);

        // Verificar si el departamento tiene usuarios activos
        $activeUsers = count(array_filter($department->users ?? [], function ($user) {
            return $user->is_active;
        }));

        if ($activeUsers > 0) {
            $this->Flash->error(__('_NO_SE_PUEDE_ELIMINAR_DEPARTAMENTO_CON_USUARIOS_ACTIVOS'));

            return $this->redirect(['action' => 'view', $id]);
        }

        // Marcar como inactivo en lugar de eliminar
        $department->active = false;

        if ($this->Departments->save($department)) {
            // Registrar eliminación lógica
            $this->Logging->logDelete('departments', (int)$id, [
                'department_name' => $department->name,
                'company_name' => $department->company->name ?? '',
                'users_count' => count($department->users ?? []),
                'soft_delete' => true,
            ]);

            $this->Flash->success(__('_DEPARTAMENTO_DESACTIVADO_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_DESACTIVAR_DEPARTAMENTO'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Función activate - Activar un departamento desactivado
     *
     * @param string|null $id Department id.
     * @return \Cake\Http\Response|null
     */
    public function activate(?string $id = null)
    {
        $this->request->allowMethod(['post']);

        $department = $this->Departments->get($id, ['contain' => ['Companies']]);
        $department->active = true;

        if ($this->Departments->save($department)) {
            // Registrar activación
            $this->Logging->logUpdate('departments', (int)$id, [
                'action' => 'activate',
                'department_name' => $department->name,
                'company_name' => $department->company->name ?? '',
            ]);

            $this->Flash->success(__('_DEPARTAMENTO_ACTIVADO_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_ACTIVAR_DEPARTAMENTO'));
        }

        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Función users - Ver usuarios de un departamento
     *
     * @param string|null $id Department id.
     * @return \Cake\Http\Response|null|void
     */
    public function users(?string $id = null)
    {
        $department = $this->Departments->get($id, ['contain' => ['Companies']]);

        // Registrar acceso a usuarios del departamento
        $this->Logging->logView('department_users', (int)$id);

        $Users = $this->getTable('JornaticCore.Users');

        $query = $Users->find()
            ->contain(['Roles', 'Contracts'])
            ->where(['department_id' => $id])
            ->orderBy(['Users.name' => 'ASC']);

        // Aplicar filtros si existen
        $filters = $this->request->getQueryParams();

        if (isset($filters['is_active'])) {
            $query->where(['Users.is_active' => (bool)$filters['is_active']]);
        }

        if (!empty($filters['role_id'])) {
            $query->where(['Users.role_id' => $filters['role_id']]);
        }

        $this->paginate = ['limit' => 25];
        $users = $this->paginate($query);

        // Obtener roles para filtros
        $Roles = $this->getTable('JornaticCore.Roles');
        $roles = $Roles->find('list')->toArray();

        $this->set(compact('department', 'users', 'filters', 'roles'));
    }

    /**
     * Función export - Exportar lista de departamentos a CSV
     *
     * @return \Cake\Http\Response
     */
    public function export()
    {
        $this->request->allowMethod(['get']);

        // Registrar exportación
        $this->Logging->logExport('departments', [
            'format' => 'csv',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);

        $departments = $this->Departments->find()
            ->contain(['Companies', 'Users'])
            ->orderBy(['Departments.created' => 'DESC'])
            ->toArray();

        // Preparar datos CSV
        $csvData = [];
        $csvData[] = [
            __('_NOMBRE'),
            __('_DESCRIPCION'),
            __('_EMPRESA'),
            __('_USUARIOS_TOTAL'),
            __('_USUARIOS_ACTIVOS'),
            __('_ACTIVO'),
            __('_FECHA_CREACION'),
        ];

        foreach ($departments as $department) {
            $totalUsers = count($department->users ?? []);
            $activeUsers = count(array_filter($department->users ?? [], function ($user) {
                return $user->is_active;
            }));

            $csvData[] = [
                $department->name,
                $department->description ?? '',
                $department->company->name ?? '',
                $totalUsers,
                $activeUsers,
                $department->active
                    ? __('_SI')
                    : __('_NO'),
                $department->created->format('Y-m-d'),
            ];
        }

        // Generar CSV
        $filename = 'departments_' . date('Y-m-d_H-i-s') . '.csv';

        $this->response = $this->response->withType('text/csv');
        $this->response =
            $this->response
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
