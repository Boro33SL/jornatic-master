<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Collection\Collection;
use Cake\Database\Query;
use Exception;
use JornaticCore\Model\Entity\Company;
use JornaticCore\Model\Table\CompaniesTable;
use JornaticCore\Service\StripeService;
use Mpdf\Tag\Q;

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
     * Stripe service instance
     *
     * @var \JornaticCore\Service\StripeService
     */
    protected StripeService $stripeService;

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

        // Inicializar servicio de Stripe
        $this->stripeService = new StripeService();

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

        // Aplicar filtros de forma encapsulada
        $filters = $this->request->getQueryParams();
        $query = $this->_applyFilters($query, $filters);

        // Configurar paginación
        $this->paginate = [
            'limit' => 20,
            'maxLimit' => 100,
        ];

        $companies = $this->paginate($query);

        // Autorizar usando una nueva entidad Company si no hay resultados
        $firstCompany = $companies->items()->first();
        if ($firstCompany) {
            $this->Authorization->authorize($firstCompany);
        } else {
            $this->Authorization->authorize($this->Companies->newEmptyEntity());
        }
        
        // Estadísticas optimizadas
        $stats = $this->_getCompanyStats();

        $this->set(compact('companies', 'filters', 'stats'));
    }

    /**
     * Aplicar filtros a la query de forma encapsulada
     *
     * @param \Cake\ORM\Query $query Query base
     * @param array $filters Filtros del request
     * @return \Cake\ORM\Query Query con filtros aplicados
     */
    private function _applyFilters(Query $query, array $filters): Query
    {
        if (!empty($filters['search'])) {
            $query = $this->_applySearchFilter($query, $filters['search']);
        }

        if (!empty($filters['status'])) {
            $query = $this->_applySubscriptionStatusFilter($query, $filters['status']);
        }

        if (isset($filters['is_active'])) {
            $query = $this->_applyCompanyStatusFilter($query, $filters['is_active']);
        }

        return $query;
    }

    /**
     * Aplicar filtro de búsqueda por texto
     *
     * @param \Cake\ORM\Query $query Query base
     * @param string $searchTerm Término de búsqueda
     * @return \Cake\ORM\Query Query con filtro de búsqueda
     */
    private function _applySearchFilter(Query $query, string $searchTerm): Query
    {
        $search = '%' . $searchTerm . '%';
        return $query->where([
            'OR' => [
                'Companies.name LIKE' => $search,
                'Companies.nif LIKE' => $search,
                'Companies.email LIKE' => $search,
                'Companies.phone LIKE' => $search,
            ],
        ]);
    }

    /**
     * Aplicar filtro por estado de suscripción
     *
     * @param \Cake\ORM\Query $query Query base
     * @param string $status Estado de la suscripción
     * @return \Cake\ORM\Query Query con filtro de estado
     */
    private function _applySubscriptionStatusFilter(Query $query, string $status): Query
    {
        return $query->matching('Subscriptions', function ($q) use ($status) {
            return $q->where(['Subscriptions.status' => $status]);
        });
    }

    /**
     * Aplicar filtro por estado de la empresa
     *
     * @param \Cake\ORM\Query $query Query base
     * @param string $isActive Filtro de estado ('1' para activas, '0' para inactivas)
     * @return \Cake\ORM\Query Query con filtro de estado de empresa
     */
    private function _applyCompanyStatusFilter(Query $query, string $isActive): Query
    {
        if ($isActive === '1') {
            return $query->where(['Companies.status' => 'active']);
        } elseif ($isActive === '0') {
            return $query->where(['Companies.status' => 'inactive']);
        }
        
        return $query;
    }

    /**
     * Obtener estadísticas generales de empresas de forma optimizada
     *
     * @return array
     */
    private function _getCompanyStats(): array
    {
        // Query única con agregaciones para estadísticas de empresas
        $companiesStats = $this->Companies->find()
            ->select([
                'total' => $this->Companies->find()->func()->count('*'),
                'active' => $this->Companies->find()->func()->count(
                    $this->Companies->find()->newExpr()->case()
                        ->when(['status !=' => 'inactive'])
                        ->then(1)
                ),
                'new_this_month' => $this->Companies->find()->func()->count(
                    $this->Companies->find()->newExpr()->case()
                        ->when([
                            'MONTH(Companies.created)' => date('m'),
                            'YEAR(Companies.created)' => date('Y')
                        ])
                        ->then(1)
                ),
            ])
            ->first();

        // Solo una query adicional para total de usuarios
        $Users = $this->getTable('JornaticCore.Users');
        $totalUsers = $Users->find()->count();

        // Calcular media de usuarios por empresa (evitar división por cero)
        $total = $companiesStats->total;
        $averageUsersPerCompany = $total > 0 ? round($totalUsers / $total, 1) : 0;

        return [
            'total' => $total,
            'active' => $companiesStats->active,
            'total_users' => $totalUsers,
            'average_users_per_company' => $averageUsersPerCompany,
            'new_this_month' => $companiesStats->new_this_month,
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
        // Cargar empresa con todas las relaciones necesarias de una vez
        $company = $this->Companies->get($id, contain: [
            'Subscriptions' => ['Plans' => ['Prices']],
            'Users' => function ($q) {
                return $q->contain(['Contracts'])->orderBy(['Users.created' => 'DESC']);
            },
            'Departments',
            'Holidays',
            'CompanyGeolocationPolicies',
            'AbsenceApprovalSettings',
        ]);

        $this->Authorization->authorize($company);
        $this->Logging->logView('companies', (int)$id);

        // Generar estadísticas usando Collection API + queries mínimas necesarias
        $companyStats = $this->_generateCompanyStats($company, $id);
        
        // Obtener datos de Stripe si están disponibles
        $stripeCustomerData = $this->_getStripeCustomerData($company);
        
        // Verificar estado de contratos usando Collection API
        $contractsNotification = $this->_getContractsNotification($company);

        $this->set(compact('company', 'companyStats', 'stripeCustomerData', 'contractsNotification'));
    }

    /**
     * Generar estadísticas de la empresa de forma optimizada
     *
     * @param \JornaticCore\Model\Entity\Company $company Entidad empresa
     * @param string $companyId ID de la empresa
     * @return array Estadísticas de la empresa
     */
    private function _generateCompanyStats($company, string $companyId): array
    {
        // Usar Collection API para estadísticas calculables con datos ya cargados
        $usersCollection = new Collection($company->users ?? []);
        $departmentsCollection = new Collection($company->departments ?? []);
        
        // Solo queries necesarias para datos no cargados en las relaciones
        $Attendances = $this->getTable('JornaticCore.Attendances');
        $Absences = $this->getTable('JornaticCore.Absences');

        $todayAttendances = $Attendances->find()
            ->matching('Users', function ($q) use ($companyId) {
                return $q->where(['Users.company_id' => $companyId]);
            })
            ->where(['DATE(timestamp)' => date('Y-m-d')])
            ->count();

        $pendingAbsences = $Absences->find()
            ->where(['company_id' => $companyId, 'status' => 'pending'])
            ->count();

        return [
            'total_users' => $usersCollection->count(),
            'active_users' => $usersCollection->filter(fn($user) => $user->is_active)->count(),
            'today_attendances' => $todayAttendances,
            'pending_absences' => $pendingAbsences,
            'departments_count' => $departmentsCollection->count(),
            'attendances_today' => $todayAttendances, // Alias para el template
        ];
    }

    /**
     * Obtener datos del cliente Stripe de forma encapsulada
     *
     * @param \JornaticCore\Model\Entity\Company $company Entidad empresa
     * @return array|null Datos del cliente Stripe o null si no están disponibles
     */
    private function _getStripeCustomerData(Company $company): ?array
    {
        // Verificar condiciones previas
        if (empty($company->active_subscription) || 
            empty($company->active_subscription->stripe_customer_id) || 
            !$this->stripeService->isConfigured()) {
            return null;
        }

        try {
            $stripeCustomerId = $company->active_subscription->stripe_customer_id;
            $stripeClient = $this->stripeService->getStripeClient();

            // Obtener datos del customer y recursos relacionados en paralelo
            $stripeCustomer = $this->stripeService->getCustomer($stripeCustomerId);
            $recentInvoices = $stripeClient->invoices->all([
                'customer' => $stripeCustomerId,
                'limit' => 5,
            ]);
            $paymentMethods = $stripeClient->paymentMethods->all([
                'customer' => $stripeCustomerId,
                'type' => 'card',
            ]);

            return [
                'customer' => $stripeCustomer,
                'recent_invoices' => $recentInvoices->data ?? [],
                'payment_methods' => $paymentMethods->data ?? [],
            ];
        } catch (Exception $e) {
            // Log del error pero continuar sin datos de Stripe
            $this->Logging->logAction('STRIPE_ERROR', false, 'company_customer_view', (int)$company->id, [
                'stripe_customer_id' => $company->active_subscription->stripe_customer_id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Generar notificación de estado de contratos usando Collection API
     *
     * @param \JornaticCore\Model\Entity\Company $company Entidad empresa
     * @return array Información sobre el estado de los contratos
     */
    private function _getContractsNotification(Company $company): array
    {
        if (empty($company->users)) {
            return [
                'all_have_contracts' => true,
                'users_without_contracts' => [],
                'total_users' => 0,
                'users_with_contracts' => 0,
            ];
        }

        $usersCollection = new Collection($company->users);
        $usersWithoutContracts = $usersCollection->filter(fn($user) => $user->current_contract === null);
        
        return [
            'all_have_contracts' => $usersWithoutContracts->count() === 0,
            'users_without_contracts' => $usersWithoutContracts,
            'total_users' => $usersCollection->count(),
            'users_with_contracts' => $usersCollection->count() - $usersWithoutContracts->count(),
        ];
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
        return $this->_changeCompanyStatus($id, 'inactive', 'DESACTIVAR', 'index');
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
        return $this->_changeCompanyStatus($id, 'active', 'ACTIVAR', 'view');
    }

    /**
     * Cambiar estado de una empresa de forma unificada
     *
     * @param string|null $id ID de la empresa
     * @param string $status Nuevo estado
     * @param string $action Acción para mensajes y logging
     * @param string $redirectAction Acción de redirección
     * @return \Cake\Http\Response
     */
    private function _changeCompanyStatus(?string $id, string $status, string $action, string $redirectAction): \Cake\Http\Response
    {
        $company = $this->Companies->get($id);
        $originalStatus = $company->status;
        $company->status = $status;

        if ($this->Companies->save($company)) {
            $this->_logStatusChange($company, $action, $originalStatus);
            $this->Flash->success(__("_EMPRESA_{$action}_CORRECTAMENTE"));

            // Determinar redirección según acción
            $redirectParams = ['action' => $redirectAction];
            if ($redirectAction === 'view') {
                $redirectParams[] = $id;
            }

            return $this->redirect($redirectParams);
        }

        $this->Flash->error(__("_ERROR_AL_{$action}_EMPRESA"));
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Registrar cambio de estado en logs
     *
     * @param \JornaticCore\Model\Entity\Company $company Entidad empresa
     * @param string $action Acción realizada
     * @param string $originalStatus Estado original
     * @return void
     */
    private function _logStatusChange($company, string $action, string $originalStatus): void
    {
        $logData = [
            'company_name' => $company->name,
            'original_status' => $originalStatus,
            'new_status' => $company->status,
        ];

        if ($action === 'DESACTIVAR') {
            $logData['soft_delete'] = true;
            $this->Logging->logDelete('companies', (int)$company->id, $logData);
        } else {
            $logData['action'] = strtolower($action);
            $this->Logging->logUpdate('companies', (int)$company->id, $logData);
        }
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

        // Obtener empresas con relaciones necesarias
        $companies = $this->Companies->find()
            ->contain(['Subscriptions' => ['Plans']])
            ->orderBy(['Companies.created' => 'DESC'])
            ->toArray();

        // Generar datos CSV usando Collection API
        $csvData = $this->_generateCsvData($companies);

        // Configurar respuesta y generar archivo
        return $this->_generateCsvResponse($csvData);
    }

    /**
     * Generar datos CSV usando Collection API
     *
     * @param array $companies Array de empresas
     * @return array Datos formateados para CSV
     */
    private function _generateCsvData(array $companies): array
    {
        $companiesCollection = new Collection($companies);

        // Headers del CSV
        $csvData = [$this->_getCsvHeaders()];

        // Formatear datos usando Collection API
        $formattedRows = $companiesCollection->map(function ($company) {
            return $this->_formatCompanyForCsv($company);
        })->toArray();

        return array_merge($csvData, $formattedRows);
    }

    /**
     * Obtener headers del CSV
     *
     * @return array Headers del archivo CSV
     */
    private function _getCsvHeaders(): array
    {
        return [
            __('_NOMBRE'),
            __('_CIF'),
            __('_EMAIL'),
            __('_TELEFONO'),
            __('_SITIO_WEB'),
            __('_PLAN'),
            __('_ESTADO_SUSCRIPCION'),
            __('_FECHA_REGISTRO'),
            __('_ACTIVA'),
        ];
    }

    /**
     * Formatear una empresa para CSV
     *
     * @param \JornaticCore\Model\Entity\Company $company Entidad empresa
     * @return array Fila formateada para CSV
     */
    private function _formatCompanyForCsv($company): array
    {
        $subscription = $company->subscriptions[0] ?? null;

        return [
            $company->name,
            $company->nif ?? '',
            $company->email,
            $company->phone ?? '',
            $company->website ?? '',
            $subscription?->plan?->name ?? '',
            $subscription?->status ?? '',
            $company->created->format('Y-m-d'),
            $company->status === 'active' ? __('_SI') : __('_NO'),
        ];
    }

    /**
     * Generar respuesta CSV con headers y contenido
     *
     * @param array $csvData Datos del CSV
     * @return \Cake\Http\Response Respuesta con archivo CSV
     */
    private function _generateCsvResponse(array $csvData): \Cake\Http\Response
    {
        $filename = 'companies_' . date('Y-m-d_H-i-s') . '.csv';

        // Configurar headers de respuesta
        $this->response = $this->response
            ->withType('text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

        // Generar contenido CSV
        $output = fopen('php://output', 'w');

        // UTF-8 BOM para Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Escribir cada fila usando Collection API
        $csvCollection = new Collection($csvData);
        $csvCollection->each(function ($row) use ($output) {
            fputcsv($output, $row, ';', '"');
        });

        fclose($output);

        return $this->response;
    }
}
