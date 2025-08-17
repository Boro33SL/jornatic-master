<?php
declare(strict_types=1);

namespace App\Controller;

use Exception;
use JornaticCore\Model\Entity\Subscription;
use JornaticCore\Model\Table\SubscriptionsTable;
use JornaticCore\Service\StripeService;

/**
 * Subscriptions Controller
 *
 * Gestión completa de suscripciones del ecosistema Jornatic
 *
 * @property \JornaticCore\Model\Table\SubscriptionsTable $Subscriptions
 */
class SubscriptionsController extends AppController
{
    /**
     * Subscriptions table instance
     *
     * @var \JornaticCore\Model\Table\SubscriptionsTable
     */
    protected SubscriptionsTable $Subscriptions;

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
        $this->Subscriptions = $this->getTable('JornaticCore.Subscriptions');

        // Inicializar servicio de Stripe
        $this->stripeService = new StripeService();

        // Cargar componente de logging
        $this->loadComponent('Logging');

        // Skip authorization para todas las acciones (por ahora)
        $this->Authorization->skipAuthorization();
    }

    /**
     * Función index - Lista paginada de suscripciones
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        // Registrar acceso
        $this->Logging->logView('subscriptions_list');

        // Query base con relaciones
        $query = $this->Subscriptions->find()
            ->contain(['Companies', 'Plans'])
            ->orderBy(['Subscriptions.created' => 'DESC']);

        // Aplicar filtros si existen
        $filters = $this->request->getQueryParams();

        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->matching('Companies', function ($q) use ($search) {
                return $q->where([
                    'OR' => [
                        'Companies.name LIKE' => $search,
                        'Companies.email LIKE' => $search,
                    ],
                ]);
            });
        }

        if (!empty($filters['status'])) {
            $query->where(['Subscriptions.status' => $filters['status']]);
        }

        if (!empty($filters['period'])) {
            $query->where(['Subscriptions.period' => $filters['period']]);
        }

        if (!empty($filters['plan_id'])) {
            $query->where(['Subscriptions.plan_id' => $filters['plan_id']]);
        }

        if (!empty($filters['company_id'])) {
            $query->where(['Subscriptions.company_id' => $filters['company_id']]);
        }

        // Configurar paginación
        $this->paginate = [
            'limit' => 20,
            'maxLimit' => 100,
        ];

        $subscriptions = $this->paginate($query);

        // Estadísticas
        $stats = $this->_getSubscriptionStats();

        // Obtener planes para filtros
        $Plans = $this->getTable('JornaticCore.Plans');
        $plans = $Plans->find('list')->toArray();

        // Obtener empresas para filtros
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();

        $this->set(compact('subscriptions', 'filters', 'stats', 'plans', 'companies'));
    }

    /**
     * Función view - Detalle de una suscripción
     *
     * @param string|null $id Subscription id.
     * @return \Cake\Http\Response|null|void
     */
    public function view(?string $id = null)
    {
        $subscription = $this->Subscriptions->get($id, [
            'contain' => [
                'Companies' => ['Users'],
                'Plans' => ['Features', 'Prices'],
            ],
        ]);

        // Obtener datos de Stripe si existe stripe_subscription_id
        $stripeData = null;
        if (!empty($subscription->stripe_subscription_id) && $this->stripeService->isConfigured()) {
            try {
                // Obtener datos de suscripción desde Stripe
                $stripeSubscription = $this->stripeService->getSubscription($subscription->stripe_subscription_id);

                // Obtener datos del customer si existe
                $stripeCustomer = null;
                if ($stripeSubscription->customer) {
                    $stripeCustomer = $this->stripeService->getCustomer($stripeSubscription->customer);
                }

                // Obtener facturas recientes
                $stripeClient = $this->stripeService->getStripeClient();
                $recentInvoices = $stripeClient->invoices->all([
                    'subscription' => $subscription->stripe_subscription_id,
                    'limit' => 3,
                ]);

                // Estructurar datos para el template
                $stripeData = [
                    'subscription' => $stripeSubscription,
                    'customer' => $stripeCustomer,
                    'recent_invoices' => $recentInvoices->data ?? [],
                ];
            } catch (Exception $e) {
                // Log del error pero continuar sin datos de Stripe
                $this->Logging->logAction('STRIPE_ERROR', false, 'subscription_view', (int)$id, [
                    'stripe_subscription_id' => $subscription->stripe_subscription_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Registrar visualización
        $this->Logging->logView('subscriptions', (int)$id);

        $this->set(compact('subscription', 'stripeData'));
    }

    /**
     * Función edit - Editar una suscripción
     *
     * @param string|null $id Subscription id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit(?string $id = null)
    {
        $subscription = $this->Subscriptions->get($id, [
            'contain' => ['Companies', 'Plans'],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $oldStatus = $subscription->status;

            $subscription = $this->Subscriptions->patchEntity($subscription, $this->request->getData());

            if ($this->Subscriptions->save($subscription)) {
                // Registrar actualización
                $this->Logging->logUpdate('subscriptions', (int)$id, [
                    'updated_fields' => array_keys($this->request->getData()),
                    'old_status' => $oldStatus,
                    'new_status' => $subscription->status,
                ]);

                $this->Flash->success(__('_SUSCRIPCION_ACTUALIZADA_CORRECTAMENTE'));

                return $this->redirect(['action' => 'view', $id]);
            }

            $this->Flash->error(__('_ERROR_AL_ACTUALIZAR_SUSCRIPCION'));
        }

        // Obtener planes para el formulario
        $Plans = $this->getTable('JornaticCore.Plans');
        $plans = $Plans->find('list')->toArray();

        $this->set(compact('subscription', 'plans'));
    }

    /**
     * Función cancel - Cancelar una suscripción
     *
     * @param string|null $id Subscription id.
     * @return \Cake\Http\Response|null
     */
    public function cancel(?string $id = null)
    {
        $this->request->allowMethod(['post']);

        $subscription = $this->Subscriptions->get($id);
        $oldStatus = $subscription->status;

        // Marcar como cancelada
        $subscription->status = 'cancelled';
        $subscription->cancel_at_period_end = true;

        if ($this->Subscriptions->save($subscription)) {
            // Registrar cancelación
            $this->Logging->logUpdate('subscriptions', (int)$id, [
                'action' => 'cancel',
                'old_status' => $oldStatus,
                'company_name' => $subscription->company->name ?? '',
            ]);

            $this->Flash->success(__('_SUSCRIPCION_CANCELADA_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_CANCELAR_SUSCRIPCION'));
        }

        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Función reactivate - Reactivar una suscripción cancelada
     *
     * @param string|null $id Subscription id.
     * @return \Cake\Http\Response|null
     */
    public function reactivate(?string $id = null)
    {
        $this->request->allowMethod(['post']);

        $subscription = $this->Subscriptions->get($id);
        $oldStatus = $subscription->status;

        // Reactivar suscripción
        $subscription->status = 'active';
        $subscription->cancel_at_period_end = false;

        if ($this->Subscriptions->save($subscription)) {
            // Registrar reactivación
            $this->Logging->logUpdate('subscriptions', (int)$id, [
                'action' => 'reactivate',
                'old_status' => $oldStatus,
                'company_name' => $subscription->company->name ?? '',
            ]);

            $this->Flash->success(__('_SUSCRIPCION_REACTIVADA_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_REACTIVAR_SUSCRIPCION'));
        }

        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Función export - Exportar lista de suscripciones a CSV
     *
     * @return \Cake\Http\Response
     */
    public function export()
    {
        $this->request->allowMethod(['get']);

        // Registrar exportación
        $this->Logging->logExport('subscriptions', [
            'format' => 'csv',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);

        $subscriptions = $this->Subscriptions->find()
            ->contain(['Companies', 'Plans'])
            ->orderBy(['Subscriptions.created' => 'DESC'])
            ->toArray();

        // Preparar datos CSV
        $csvData = [];
        $csvData[] = [
            __('_EMPRESA'),
            __('_PLAN'),
            __('_ESTADO'),
            __('_PERIODO'),
            __('_INICIO'),
            __('_FIN'),
            __('_USUARIOS_PERMITIDOS'),
            __('_STRIPE_ID'),
            __('_FECHA_CREACION'),
        ];

        foreach ($subscriptions as $subscription) {
            $csvData[] = [
                $subscription->company->name ?? '',
                $subscription->plan->name ?? '',
                $subscription->getStatusLabel(),
                $subscription->getPeriodLabel(),
                $subscription->starts ? $subscription->starts->format('Y-m-d') : '',
                $subscription->ends ? $subscription->ends->format('Y-m-d') : '',
                $subscription->users_allowed,
                $subscription->stripe_subscription_id ?? '',
                $subscription->created->format('Y-m-d'),
            ];
        }

        // Generar CSV
        $filename = 'subscriptions_' . date('Y-m-d_H-i-s') . '.csv';

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
     * Obtener estadísticas generales de suscripciones
     *
     * @return array
     */
    private function _getSubscriptionStats(): array
    {
        $total = $this->Subscriptions->find()->count();

        $active = $this->Subscriptions->find()
            ->where(['status' => 'active'])
            ->count();

        $trial = $this->Subscriptions->find()
            ->where(['status' => 'trial'])
            ->count();

        $cancelled = $this->Subscriptions->find()
            ->where(['status' => 'cancelled'])
            ->count();

        $monthly = $this->Subscriptions->find()
            ->where(['period' => 'monthly'])
            ->count();

        $annual = $this->Subscriptions->find()
            ->where(['period' => 'annual'])
            ->count();

        $thisMonth = $this->Subscriptions->find()
            ->where([
                'MONTH(Subscriptions.created)' => date('m'),
                'YEAR(Subscriptions.created)' => date('Y'),
            ])
            ->count();

        // Calcular ingresos mensuales estimados de suscripciones activas
        $monthlyRevenue = 0;
        $activeSubscriptions = $this->Subscriptions->find()
            ->contain(['Plans' => ['Prices']])
            ->where(['status' => 'active'])
            ->toArray();

        foreach ($activeSubscriptions as $subscription) {
            if (!empty($subscription->plan->prices)) {
                foreach ($subscription->plan->prices as $price) {
                    if ($price->period === $subscription->period) {
                        if ($subscription->period === 'monthly') {
                            $monthlyRevenue += (float)$price->amount;
                        } elseif ($subscription->period === 'annual') {
                            // Convertir precio anual a mensual
                            $monthlyRevenue += (float)$price->amount / 12;
                        }
                        break;
                    }
                }
            }
        }

        return [
            'total' => $total,
            'active' => $active,
            'trial' => $trial,
            'cancelled' => $cancelled,
            'monthly' => $monthly,
            'annual' => $annual,
            'new_this_month' => $thisMonth,
            'monthly_revenue' => $monthlyRevenue,
        ];
    }

    /**
     * Obtener métricas específicas de una suscripción
     *
     * @param \JornaticCore\Model\Entity\Subscription $subscription
     * @return array
     */
    private function _getSubscriptionMetrics(Subscription $subscription): array
    {
        // Calcular revenue total generado
        $Prices = $this->getTable('JornaticCore.Prices');
        $price = $Prices->find()
            ->where([
                'plan_id' => $subscription->plan_id,
                'period' => $subscription->period,
            ])
            ->first();

        $monthlyRevenue = $price ? (float)$price->amount : 0;

        // Calcular meses activos
        $monthsActive = 0;
        if ($subscription->starts && $subscription->ends) {
            $diff = $subscription->starts->diff($subscription->ends);
            $monthsActive = ($diff->y * 12) + $diff->m;
        }

        $totalRevenue = $monthlyRevenue * $monthsActive;

        // Usuarios activos de la empresa
        $Users = $this->getTable('JornaticCore.Users');
        $activeUsers = $Users->find()
            ->where([
                'company_id' => $subscription->company_id,
                'is_active' => true,
            ])
            ->count();

        return [
            'monthly_revenue' => $monthlyRevenue,
            'total_revenue' => $totalRevenue,
            'months_active' => $monthsActive,
            'active_users' => $activeUsers,
            'usage_percentage' => $subscription->users_allowed > 0 ?
                round($activeUsers / $subscription->users_allowed * 100, 1) : 0,
        ];
    }
}
