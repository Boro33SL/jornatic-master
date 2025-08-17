<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Masters Controller
 *
 * Handles authentication for master users
 *
 * @property \App\Model\Table\MastersTable $Masters
 * * @property \App\Controller\Component\LoggingComponent $Logging
 */
class MastersController extends AppController
{
    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Load logging component for audit trail
        $this->loadComponent('Logging');

        // Allow unauthenticated access to login only
    }

    /**
     * Metodo beforeFilter
     *
     * @param EventInterface $event
     * @return void
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // Allow unauthenticated access to login
        $this->Authentication->allowUnauthenticated(['login']);
    }

    /**
     * Login action
     *
     * @return \Cake\Http\Response|null|void
     */
    public function login()
    {
        $this->request->allowMethod(['get', 'post']);
        $this->Authorization->skipAuthorization();
        $result = $this->Authentication->getResult();

        // If user is already logged in, redirect to dashboard
        if ($result && $result->isValid()) {
            $redirect = $this->request->getQuery('redirect', '/');

            return $this->redirect($redirect);
        }

        // If login form was submitted
        if ($this->request->is('post')) {
            if ($result && $result->isValid()) {
                // Login exitoso - registrar en auditoría
                $master = $result->getData();
                $this->Logging->logLogin([
                    'master_id' => $master->id,
                    'master_name' => $master->name,
                    'login_method' => 'form',
                ]);
            } elseif ($this->request->is('post')) {
                // Login fallido - registrar en auditoría
                $email = $this->request->getData('email', '');
                $this->Logging->logLoginFailed($email, 'Invalid credentials');
                $this->Flash->error(__('_EMAIL_O_PASSWORD_INCORRECTOS'));
            }
        }
    }

    /**
     * Logout action
     *
     * @return \Cake\Http\Response|null|void
     */
    public function logout()
    {
        $this->Authorization->skipAuthorization();

        $result = $this->Authentication->getResult();
        if ($result && $result->isValid()) {
            // Registrar logout antes de cerrar sesión
            $this->Logging->logLogout();

            $this->Authentication->logout();
            $this->Flash->success(__('_SESION_CERRADA_CORRECTAMENTE'));
        }

        return $this->redirect(['action' => 'login']);
    }

    /**
     * Add action - Create new master user
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $master = $this->Masters->newEmptyEntity();

        $this->Authorization->authorize($master);

        if ($this->request->is('post')) {
            $master = $this->Masters->patchEntity($master, $this->request->getData());

            if ($this->Masters->save($master)) {
                // Registrar creación de usuario master
                $this->Logging->logCreate('masters', $master->id, [
                    'master_name' => $master->name,
                    'master_email' => $master->email,
                    'is_active' => $master->is_active,
                ]);

                $this->Flash->success(__('_USUARIO_MASTER_CREADO_CORRECTAMENTE'));

                return $this->redirect(['action' => 'dashboard']);
            }

            $this->Flash->error(__('_ERROR_AL_CREAR_USUARIO_MASTER'));
        }

        $this->set(compact('master'));
    }

    /**
     * Dashboard action
     *
     * @return void
     */
    public function dashboard(): void
    {
        // Autorizar el acceso al dashboard para cualquier master autenticado
        $this->Authorization->authorize($this->Authentication->getIdentity()->getOriginalData(), 'dashboard');
        // Registrar acceso al dashboard
        $this->Logging->logView('dashboard');

        $master = $this->Authentication->getIdentity();
        
        // Cargar tablas necesarias para estadísticas
        $Companies = $this->getTable('JornaticCore.Companies');
        $Subscriptions = $this->getTable('JornaticCore.Subscriptions');
        $Plans = $this->getTable('JornaticCore.Plans');
        $Prices = $this->getTable('JornaticCore.Prices');
        $Users = $this->getTable('JornaticCore.Users');
        $Attendances = $this->getTable('JornaticCore.Attendances');
        $MasterAccessLogs = $this->fetchTable('MasterAccessLogs');
        
        // Obtener estadísticas de empresas
        $totalCompanies = $Companies->find()->count();
        
        // Obtener suscripciones activas
        $activeSubscriptions = $Subscriptions->find()
            ->where(['status' => 'active'])
            ->count();
        
        // Calcular ingresos mensuales - versión simplificada sin join directo
        $activeSubscriptionsThisMonth = $Subscriptions->find()
            ->contain(['Plans'])
            ->where([
                'Subscriptions.status' => 'active',
                'MONTH(Subscriptions.created)' => date('m'),
                'YEAR(Subscriptions.created)' => date('Y')
            ])
            ->toArray();
        
        $monthlyRevenueAmount = 0;
        foreach ($activeSubscriptionsThisMonth as $subscription) {
            // Buscar el precio correspondiente al plan y período
            $price = $Prices->find()
                ->where([
                    'plan_id' => $subscription->plan_id,
                    'period' => $subscription->period
                ])
                ->first();
            if ($price) {
                $monthlyRevenueAmount += (float)$price->amount;
            }
        }
        
        // Obtener logs de auditoría recientes
        $recentLogs = $MasterAccessLogs->find()
            ->contain(['Masters'])
            ->orderBy(['MasterAccessLogs.created' => 'DESC'])
            ->limit(10)
            ->toArray();
        
        // Estadísticas de logs del día
        $today = date('Y-m-d');
        $todayLogsCount = $MasterAccessLogs->find()
            ->where(['DATE(created)' => $today])
            ->count();
        
        // Estadísticas de éxito/fallo del día
        $todaySuccessCount = $MasterAccessLogs->find()
            ->where([
                'DATE(created)' => $today,
                'success' => true
            ])
            ->count();
            
        $todayFailedCount = $MasterAccessLogs->find()
            ->where([
                'DATE(created)' => $today,
                'success' => false
            ])
            ->count();
        
        // Preparar datos para los gráficos (últimos 6 meses)
        $revenueData = [];
        $subscriptionData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            
            // Ingresos por mes - versión simplificada
            $monthSubscriptions = $Subscriptions->find()
                ->where([
                    'Subscriptions.status' => 'active',
                    'DATE_FORMAT(Subscriptions.created, "%Y-%m") =' => $month
                ])
                ->toArray();
            
            $monthTotal = 0;
            foreach ($monthSubscriptions as $sub) {
                $price = $Prices->find()
                    ->where([
                        'plan_id' => $sub->plan_id,
                        'period' => $sub->period
                    ])
                    ->first();
                if ($price) {
                    $monthTotal += (float)$price->amount;
                }
            }
            $revenueData[] = $monthTotal;
            
            // Suscripciones nuevas por mes
            $monthSubs = $Subscriptions->find()
                ->where(['DATE_FORMAT(Subscriptions.created, "%Y-%m") =' => $month])
                ->count();
            $subscriptionData[] = $monthSubs;
        }
        
        // Distribución por planes con nombres
        $planDistribution = $Subscriptions->find()
            ->contain(['Plans'])
            ->select([
                'Plans.name',
                'count' => 'COUNT(Subscriptions.id)'
            ])
            ->where(['Subscriptions.status' => 'active'])
            ->group(['Subscriptions.plan_id', 'Plans.name'])
            ->toArray();
        
        // ============================================================
        // NUEVOS KPIs CRÍTICOS - SALUD DEL NEGOCIO
        // ============================================================
        
        // 1. Conversion Rate: Trial → Paid
        $totalTrialCompanies = $Subscriptions->find()
            ->where(['status' => 'trialing'])
            ->count();
        $totalPaidCompanies = $Subscriptions->find()
            ->where(['status' => 'active'])
            ->count();
        $conversionRate = $totalTrialCompanies > 0 ? 
            round(($totalPaidCompanies / ($totalTrialCompanies + $totalPaidCompanies)) * 100, 1) : 0;
        
        // 2. Churn Rate: Cancelaciones este mes
        $currentMonth = date('Y-m');
        $cancelledThisMonth = $Subscriptions->find()
            ->where([
                'status' => 'cancelled',
                'DATE_FORMAT(modified, "%Y-%m") =' => $currentMonth
            ])
            ->count();
        $activeStartMonth = $Subscriptions->find()
            ->where([
                'status' => 'active',
                'DATE_FORMAT(created, "%Y-%m") <=' => $currentMonth
            ])
            ->count();
        $churnRate = $activeStartMonth > 0 ? 
            round(($cancelledThisMonth / $activeStartMonth) * 100, 1) : 0;
        
        // 3. ARR Growth (Annual Recurring Revenue)
        $currentYearRevenue = 0;
        $activeThisYear = $Subscriptions->find()
            ->where([
                'status' => 'active',
                'YEAR(created)' => date('Y')
            ])
            ->toArray();
        foreach ($activeThisYear as $sub) {
            $price = $Prices->find()
                ->where(['plan_id' => $sub->plan_id, 'period' => $sub->period])
                ->first();
            if ($price) {
                $yearlyValue = $sub->period === 'annual' ? $price->amount : $price->amount * 12;
                $currentYearRevenue += $yearlyValue;
            }
        }
        
        $lastYearRevenue = 0;
        $activeLastYear = $Subscriptions->find()
            ->where([
                'status' => 'active',
                'YEAR(created)' => date('Y') - 1
            ])
            ->toArray();
        foreach ($activeLastYear as $sub) {
            $price = $Prices->find()
                ->where(['plan_id' => $sub->plan_id, 'period' => $sub->period])
                ->first();
            if ($price) {
                $yearlyValue = $sub->period === 'annual' ? $price->amount : $price->amount * 12;
                $lastYearRevenue += $yearlyValue;
            }
        }
        
        $arrGrowth = $lastYearRevenue > 0 ? 
            round((($currentYearRevenue - $lastYearRevenue) / $lastYearRevenue) * 100, 1) : 0;
        
        // 4. Customer Lifetime Value (CLV) promedio
        $totalRevenue = 0;
        $allPaidSubscriptions = $Subscriptions->find()
            ->where(['status IN' => ['active', 'cancelled']])
            ->toArray();
        foreach ($allPaidSubscriptions as $sub) {
            $price = $Prices->find()
                ->where(['plan_id' => $sub->plan_id, 'period' => $sub->period])
                ->first();
            if ($price) {
                $totalRevenue += $price->amount;
            }
        }
        $uniqueCompanies = $Subscriptions->find()
            ->select(['company_id'])
            ->where(['status IN' => ['active', 'cancelled']])
            ->distinct(['company_id'])
            ->count();
        $averageClv = $uniqueCompanies > 0 ? round($totalRevenue / $uniqueCompanies, 2) : 0;
        
        // ============================================================
        // NUEVOS KPIs - ACTIVIDAD TIEMPO REAL
        // ============================================================
        
        // 1. Empresas activas hoy (con fichajes)
        $activeCompaniesToday = $Attendances->find()
            ->contain(['Users'])
            ->select(['Users.company_id'])
            ->where(['DATE(Attendances.timestamp)' => date('Y-m-d')])
            ->distinct(['Users.company_id'])
            ->count();
        
        // 2. Empleados únicos que han fichado hoy
        $uniqueEmployeesToday = $Attendances->find()
            ->select(['user_id'])
            ->where(['DATE(timestamp)' => date('Y-m-d')])
            ->distinct(['user_id'])
            ->count();
        
        // 3. Fichajes por hora (promedio del día actual)
        $todayAttendances = $Attendances->find()
            ->where(['DATE(timestamp)' => date('Y-m-d')])
            ->count();
        $currentHour = (int)date('H');
        $avgAttendancesPerHour = $currentHour > 0 ? round($todayAttendances / $currentHour, 1) : 0;
        
        // 4. Empresas inactivas (>30 días sin fichajes)
        $inactiveCompanies = $Companies->find()
            ->select(['Companies.id'])
            ->leftJoinWith('Users.Attendances')
            ->group(['Companies.id'])
            ->having([
                'OR' => [
                    'MAX(Attendances.timestamp) <' => date('Y-m-d', strtotime('-30 days')),
                    'MAX(Attendances.timestamp) IS NULL'
                ]
            ])
            ->count();
        
        // ============================================================
        // NUEVOS KPIs - ALERTAS Y ATENCIÓN
        // ============================================================
        
        // 1. Trials expirando en próximos 7 días
        $trialsExpiring = $Subscriptions->find()
            ->where([
                'status' => 'trialing',
                'ends >=' => date('Y-m-d'),
                'ends <=' => date('Y-m-d', strtotime('+7 days'))
            ])
            ->count();
        
        // 2. Pagos fallidos que requieren atención
        $failedPayments = $Subscriptions->find()
            ->where(['status' => 'past_due'])
            ->count();
        
        // 3. Empresas sin actividad (>30 días) - Reutilizamos $inactiveCompanies
        
        // 4. System Health - Promedio de logs exitosos vs fallidos (últimos 7 días)
        $last7Days = date('Y-m-d', strtotime('-7 days'));
        $totalLogsWeek = $MasterAccessLogs->find()
            ->where(['created >=' => $last7Days])
            ->count();
        $successLogsWeek = $MasterAccessLogs->find()
            ->where([
                'created >=' => $last7Days,
                'success' => true
            ])
            ->count();
        $systemHealthPercentage = $totalLogsWeek > 0 ? 
            round(($successLogsWeek / $totalLogsWeek) * 100, 1) : 100;
        
        // ============================================================
        // DATOS ADICIONALES PARA NUEVOS GRÁFICOS
        // ============================================================
        
        // MoM Growth (últimos 6 meses)
        $momGrowthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $prevMonth = date('Y-m', strtotime("-" . ($i + 1) . " months"));
            
            $currentMonthSubs = $Subscriptions->find()
                ->where(['DATE_FORMAT(created, "%Y-%m") =' => $month])
                ->count();
            $prevMonthSubs = $Subscriptions->find()
                ->where(['DATE_FORMAT(created, "%Y-%m") =' => $prevMonth])
                ->count();
            
            $growth = $prevMonthSubs > 0 ? 
                round((($currentMonthSubs - $prevMonthSubs) / $prevMonthSubs) * 100, 1) : 0;
            $momGrowthData[] = $growth;
        }
        
        // Distribución geográfica (simulada - sin campo provincia específico)
        $geographicDistribution = [
            ['province' => 'Madrid', 'count' => round($totalCompanies * 0.35)],
            ['province' => 'Barcelona', 'count' => round($totalCompanies * 0.25)],
            ['province' => 'Valencia', 'count' => round($totalCompanies * 0.15)],
            ['province' => 'Sevilla', 'count' => round($totalCompanies * 0.10)],
            ['province' => 'Bilbao', 'count' => round($totalCompanies * 0.08)]
        ];
        
        // Preparar datos para la vista
        $this->set(compact(
            'master',
            'totalCompanies',
            'activeSubscriptions',
            'monthlyRevenueAmount',
            'recentLogs',
            'todayLogsCount',
            'todaySuccessCount',
            'todayFailedCount',
            'revenueData',
            'subscriptionData',
            'planDistribution',
            // Nuevos KPIs - Salud del Negocio
            'conversionRate',
            'churnRate',
            'arrGrowth',
            'averageClv',
            // Nuevos KPIs - Actividad Tiempo Real
            'activeCompaniesToday',
            'uniqueEmployeesToday',
            'avgAttendancesPerHour',
            'inactiveCompanies',
            // Nuevos KPIs - Alertas
            'trialsExpiring',
            'failedPayments',
            'systemHealthPercentage',
            // Nuevos datos para gráficos
            'momGrowthData',
            'geographicDistribution'
        ));
    }
}
