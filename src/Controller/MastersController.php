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
        $this->Authorization->skipAuthorization(['login']);
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
        $this->Authorization->skipAuthorization();

        $master = $this->Masters->newEmptyEntity();

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
    public function dashboard()
    {
        $this->Authorization->skipAuthorization();

        // Registrar acceso al dashboard
        $this->Logging->logView('dashboard');

        $master = $this->Authentication->getIdentity();
        
        // Cargar tablas necesarias para estadísticas
        $Companies = $this->getTable('JornaticCore.Companies');
        $Subscriptions = $this->getTable('JornaticCore.Subscriptions');
        $Plans = $this->getTable('JornaticCore.Plans');
        $Prices = $this->getTable('JornaticCore.Prices');
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
            'planDistribution'
        ));
    }
}
