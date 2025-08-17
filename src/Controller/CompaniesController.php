<?php
declare(strict_types=1);

namespace App\Controller;

use JornaticCore\Model\Table\CompaniesTable;

/**
 * Companies Controller
 * Gestión completa de empresas del ecosistema Jornatic
 *
 * @property \JornaticCore\Model\Table\CompaniesTable $Companies
 */
class CompaniesController extends AppController
{
    /**
     * Companies table instance
     *
     * @var \JornaticCore\Model\Table\CompaniesTable
     */
    protected CompaniesTable $Companies;

    /**
     * Función de inicialización
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Cargar el modelo desde el plugin
        $this->Companies = $this->getTable('JornaticCore.Companies');

        // Cargar componente de logging
        $this->loadComponent('Logging');
    }

    /**
     * Función index - Lista paginada de empresas
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        // Registrar acceso
        $this->Logging->logView('companies_list');

        // Query base con relaciones
        $query = $this->Companies->find()
            ->contain([
                'Subscriptions' => ['Plans'],
                'Users',
            ])
            ->orderBy(['Companies.created' => 'DESC']);

        // Aplicar filtros si existen
        $filters = $this->request->getQueryParams();

        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where([
                'OR' => [
                    'Companies.name LIKE' => $search,
                    'Companies.legal_name LIKE' => $search,
                    'Companies.nif LIKE' => $search,
                    'Companies.email LIKE' => $search,
                ],
            ]);
        }

        if (!empty($filters['status'])) {
            $query->matching('Subscriptions', function ($q) use ($filters) {
                return $q->where(['Subscriptions.status' => $filters['status']]);
            });
        }

        // Configurar paginación
        $this->paginate = [
            'limit' => 20,
            'maxLimit' => 100,
        ];

        $companies = $this->paginate($query);

        $this->Authorization->authorize($companies->items()->first());
        // Estadísticas
        $stats = $this->_getCompanyStats();

        $this->set(compact('companies', 'filters', 'stats'));
    }

    /**
     * Obtener estadísticas generales de empresas
     *
     * @return array
     */
    private function _getCompanyStats(): array
    {
        $total = $this->Companies->find()->count();

        $active = $this->Companies->find()
            ->where(['status !=' => 'inactive'])
            ->count();

        $createdThisMonth = $this->Companies->find()
            ->where([
                'MONTH(Companies.created)' => date('m'),
                'YEAR(Companies.created)' => date('Y'),
            ])
            ->count();

        // Obtener total de usuarios de todas las empresas
        $Users = $this->getTable('JornaticCore.Users');
        $totalUsers = $Users->find()->count();

        // Calcular media de usuarios por empresa (evitar división por cero)
        $averageUsersPerCompany = $total > 0
            ? round($totalUsers / $total, 1)
            : 0;

        return [
            'total' => $total,
            'active' => $active,
            'total_users' => $totalUsers,
            'average_users_per_company' => $averageUsersPerCompany,
            'new_this_month' => $createdThisMonth,
        ];
    }

    /**
     * Función view - Detalle de una empresa
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null|void
     */
    public function view(?string $id = null)
    {
        $company = $this->Companies->get($id, [
            'contain' => [
                'Subscriptions' => ['Plans' => ['Prices']],
                'Users' => function ($q) {
                    return $q->limit(10)->orderBy(['Users.created' => 'DESC']);
                },
                'Departments',
                'Holidays',
                'CompanyGeolocationPolicies',
                'AbsenceApprovalSettings',
            ],
        ]);
        $this->Authorization->authorize($company);
        // Registrar visualización
        $this->Logging->logView('companies', (int)$id);

        // Obtener estadísticas de la empresa
        $Users = $this->getTable('JornaticCore.Users');
        $Attendances = $this->getTable('JornaticCore.Attendances');
        $Absences = $this->getTable('JornaticCore.Absences');
        $Departments = $this->getTable('JornaticCore.Departments');

        $totalUsers = $Users->find()
            ->where(['company_id' => $id])
            ->count();

        $activeUsers = $Users->find()
            ->where([
                'company_id' => $id,
                'is_active' => true,
            ])
            ->count();

        $todayAttendances = $Attendances->find()
            ->matching('Users', function ($q) use ($id) {
                return $q->where(['Users.company_id' => $id]);
            })
            ->where([
                'DATE(timestamp)' => date('Y-m-d'),
            ])
            ->count();

        $pendingAbsences = $Absences->find()
            ->where([
                'company_id' => $id,
                'status' => 'pending',
            ])
            ->count();

        $departmentsCount = $Departments->find()
            ->where(['company_id' => $id])
            ->count();

        $companyStats = [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'today_attendances' => $todayAttendances,
            'pending_absences' => $pendingAbsences,
            'departments_count' => $departmentsCount,
            'attendances_today' => $todayAttendances, // Alias para el template
        ];

        $this->set(compact('company', 'companyStats'));
    }

    /**
     * Función edit - Editar una empresa
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit(?string $id = null)
    {
        $company = $this->Companies->get($id, [
            'contain' => ['Subscriptions'],
        ]);
        $this->Authorization->authorize($company);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $company = $this->Companies->patchEntity($company, $this->request->getData());

            if ($this->Companies->save($company)) {
                // Registrar actualización
                $this->Logging->logUpdate('companies', (int)$id, [
                    'updated_fields' => array_keys($this->request->getData()),
                ]);

                $this->Flash->success(__('_EMPRESA_ACTUALIZADA_CORRECTAMENTE'));

                return $this->redirect(['action' => 'view', $id]);
            }

            $this->Flash->error(__('_ERROR_AL_ACTUALIZAR_EMPRESA'));
        }

        $this->set(compact('company'));
    }

    /**
     * Función delete - Eliminar una empresa (soft delete)
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null
     */
    public function delete(?string $id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $company = $this->Companies->get($id);

        // En lugar de eliminar, marcar como inactiva
        $company->status = 'inactive';

        if ($this->Companies->save($company)) {
            // Registrar eliminación lógica
            $this->Logging->logDelete('companies', (int)$id, [
                'company_name' => $company->name,
                'soft_delete' => true,
            ]);

            $this->Flash->success(__('_EMPRESA_DESACTIVADA_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_DESACTIVAR_EMPRESA'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Función activate - Activar una empresa desactivada
     *
     * @param string|null $id Company id.
     * @return \Cake\Http\Response|null
     */
    public function activate(?string $id = null)
    {
        $this->request->allowMethod(['post']);

        $company = $this->Companies->get($id);
        $company->status = 'active';

        if ($this->Companies->save($company)) {
            // Registrar activación
            $this->Logging->logUpdate('companies', (int)$id, [
                'action' => 'activate',
                'company_name' => $company->name,
            ]);

            $this->Flash->success(__('_EMPRESA_ACTIVADA_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_ACTIVAR_EMPRESA'));
        }

        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Función export - Exportar lista de empresas a CSV
     *
     * @return \Cake\Http\Response
     */
    public function export()
    {
        $this->request->allowMethod(['get']);

        // Registrar exportación
        $this->Logging->logExport('companies', [
            'format' => 'csv',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);

        $companies = $this->Companies->find()
            ->contain(['Subscriptions' => ['Plans']])
            ->orderBy(['Companies.created' => 'DESC'])
            ->toArray();

        // Preparar datos CSV
        $csvData = [];
        $csvData[] = [
            __('_NOMBRE'),
            __('_NOMBRE_LEGAL'),
            __('_CIF'),
            __('_EMAIL'),
            __('_TELEFONO'),
            __('_PLAN'),
            __('_ESTADO_SUSCRIPCION'),
            __('_FECHA_REGISTRO'),
            __('_ACTIVA'),
        ];

        foreach ($companies as $company) {
            $subscription = $company->subscriptions[0] ?? null;
            $csvData[] = [
                $company->name,
                $company->legal_name ?? '',
                $company->cif ?? '',
                $company->email,
                $company->phone ?? '',
                $subscription
                    ? $subscription->plan->name
                    : '',
                $subscription
                    ? $subscription->status
                    : '',
                $company->created->format('Y-m-d'),
                $company->status === 'active'
                    ? __('_SI')
                    : __('_NO'),
            ];
        }

        // Generar CSV
        $filename = 'companies_' . date('Y-m-d_H-i-s') . '.csv';

        $this->response = $this->response->withType('text/csv');
        $this->response =
            $this->response->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

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
