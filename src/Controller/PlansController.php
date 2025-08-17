<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use JornaticCore\Model\Table\PlansTable;

/**
 * Plans Controller
 *
 * Gestión completa de planes del ecosistema Jornatic
 *
 * @property \JornaticCore\Model\Table\PlansTable $Plans
 */
class PlansController extends AppController
{
    protected PlansTable $Plans;
    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        // Cargar el modelo desde el plugin
        $this->Plans = $this->getTable('JornaticCore.Plans');
        
        // Cargar componente de logging
        $this->loadComponent('Logging');

        // Skip authorization para todas las acciones (por ahora)
        $this->Authorization->skipAuthorization();
    }

    /**
     * Index method - Lista paginada de planes
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        // Registrar acceso
        $this->Logging->logView('plans_list');

        // Query base con relaciones
        $query = $this->Plans->find()
            ->contain(['Features', 'Prices', 'Subscriptions'])
            ->orderBy(['Plans.created' => 'DESC']);

        // Aplicar filtros si existen
        $filters = $this->request->getQueryParams();
        
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where([
                'OR' => [
                    'Plans.name LIKE' => $search,
                    'Plans.description LIKE' => $search,
                ]
            ]);
        }
        
        if (isset($filters['is_trial'])) {
            $query->where(['Plans.is_trial' => (bool)$filters['is_trial']]);
        }

        // Configurar paginación
        $this->paginate = [
            'limit' => 20,
            'maxLimit' => 100,
        ];

        $plans = $this->paginate($query);

        // Estadísticas
        $stats = $this->_getPlanStats();

        $this->set(compact('plans', 'filters', 'stats'));
    }

    /**
     * View method - Detalle de un plan
     *
     * @param string|null $id Plan id.
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $plan = $this->Plans->get($id, [
            'contain' => [
                'Features' => function ($q) {
                    return $q->orderBy(['Features.order' => 'ASC']);
                },
                'Prices',
                'Subscriptions' => ['Companies'],
            ],
        ]);

        // Registrar visualización
        $this->Logging->logView('plans', (int)$id);

        // Obtener estadísticas del plan
        $planStats = $this->_getSpecificPlanStats($plan);

        $this->set(compact('plan', 'planStats'));
    }

    /**
     * Add method - Crear un nuevo plan
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $plan = $this->Plans->newEmptyEntity();

        if ($this->request->is('post')) {
            $plan = $this->Plans->patchEntity($plan, $this->request->getData());
            
            if ($this->Plans->save($plan)) {
                // Registrar creación
                $this->Logging->logCreate('plans', $plan->id, [
                    'plan_name' => $plan->name,
                    'max_users' => $plan->max_users,
                    'is_trial' => $plan->is_trial
                ]);
                
                $this->Flash->success(__('_PLAN_CREADO_CORRECTAMENTE'));
                return $this->redirect(['action' => 'view', $plan->id]);
            }
            
            $this->Flash->error(__('_ERROR_AL_CREAR_PLAN'));
        }

        // Obtener features disponibles
        $Features = $this->getTable('JornaticCore.Features');
        $features = $Features->find('list')->toArray();

        $this->set(compact('plan', 'features'));
    }

    /**
     * Edit method - Editar un plan
     *
     * @param string|null $id Plan id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit($id = null)
    {
        $plan = $this->Plans->get($id, [
            'contain' => [
                'Features' => function ($q) {
                    return $q->orderBy(['Features.order' => 'ASC']);
                },
                'Prices'
            ],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            
            // Procesar features si existen en los datos
            if (isset($data['features'])) {
                $featuresData = [];
                foreach ($data['features'] as $featureId => $featureData) {
                    if (!empty($featureData['selected'])) {
                        $featuresData[] = [
                            'id' => $featureId,
                            '_joinData' => [
                                'value' => $featureData['value'] ?? null
                            ]
                        ];
                    }
                }
                $data['features'] = $featuresData;
            }
            
            $plan = $this->Plans->patchEntity($plan, $data, [
                'associated' => ['Features._joinData']
            ]);
            
            if ($this->Plans->save($plan, ['associated' => ['Features']])) {
                // Registrar actualización
                $this->Logging->logUpdate('plans', (int)$id, [
                    'updated_fields' => array_keys($this->request->getData()),
                    'plan_name' => $plan->name
                ]);
                
                $this->Flash->success(__('_PLAN_ACTUALIZADO_CORRECTAMENTE'));
                return $this->redirect(['action' => 'view', $id]);
            }
            
            $this->Flash->error(__('_ERROR_AL_ACTUALIZAR_PLAN'));
        }

        // Obtener todas las features disponibles ordenadas
        $Features = $this->getTable('JornaticCore.Features');
        $allFeatures = $Features->find()
            ->orderBy(['Features.order' => 'ASC'])
            ->toArray();

        $this->set(compact('plan', 'allFeatures'));
    }

    /**
     * Delete method - Eliminar un plan (soft delete)
     *
     * @param string|null $id Plan id.
     * @return \Cake\Http\Response|null
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        
        $plan = $this->Plans->get($id);
        
        // Verificar si el plan tiene suscripciones activas
        $Subscriptions = $this->getTable('JornaticCore.Subscriptions');
        $activeSubscriptions = $Subscriptions->find()
            ->where([
                'plan_id' => $id,
                'status IN' => ['active', 'trial']
            ])
            ->count();

        if ($activeSubscriptions > 0) {
            $this->Flash->error(__('_NO_SE_PUEDE_ELIMINAR_PLAN_CON_SUSCRIPCIONES_ACTIVAS'));
            return $this->redirect(['action' => 'view', $id]);
        }
        
        if ($this->Plans->delete($plan)) {
            // Registrar eliminación
            $this->Logging->logDelete('plans', (int)$id, [
                'plan_name' => $plan->name,
                'hard_delete' => true,
                'active_subscriptions' => $activeSubscriptions
            ]);
            
            $this->Flash->success(__('_PLAN_ELIMINADO_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_ELIMINAR_PLAN'));
        }

        return $this->redirect(['action' => 'index']);
    }


    /**
     * Prices method - Gestionar precios de un plan
     *
     * @param string|null $id Plan id.
     * @return \Cake\Http\Response|null|void
     */
    public function prices($id = null)
    {
        $plan = $this->Plans->get($id, [
            'contain' => ['Prices'],
        ]);

        // Registrar acceso a precios
        $this->Logging->logView('plan_prices', (int)$id);

        if ($this->request->is(['post', 'put', 'patch'])) {
            $pricesData = $this->request->getData('prices', []);
            
            $Prices = $this->getTable('JornaticCore.Prices');
            $success = true;
            
            foreach ($pricesData as $priceData) {
                if (!empty($priceData['id'])) {
                    // Actualizar precio existente
                    $price = $Prices->get($priceData['id']);
                    $price = $Prices->patchEntity($price, $priceData);
                } else {
                    // Crear nuevo precio
                    $priceData['plan_id'] = $id;
                    $price = $Prices->newEntity($priceData);
                }
                
                if (!$Prices->save($price)) {
                    $success = false;
                    break;
                }
            }
            
            if ($success) {
                $this->Logging->logUpdate('plans', (int)$id, [
                    'action' => 'update_prices',
                    'plan_name' => $plan->name
                ]);
                
                $this->Flash->success(__('_PRECIOS_ACTUALIZADOS_CORRECTAMENTE'));
                return $this->redirect(['action' => 'view', $id]);
            } else {
                $this->Flash->error(__('_ERROR_AL_ACTUALIZAR_PRECIOS'));
            }
        }

        $this->set(compact('plan'));
    }

    /**
     * Export method - Exportar lista de planes a CSV
     *
     * @return \Cake\Http\Response
     */
    public function export()
    {
        $this->request->allowMethod(['get']);

        // Registrar exportación
        $this->Logging->logExport('plans', [
            'format' => 'csv',
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        $plans = $this->Plans->find()
            ->contain(['Prices', 'Features'])
            ->orderBy(['Plans.order' => 'ASC'])
            ->toArray();

        // Preparar datos CSV
        $csvData = [];
        $csvData[] = [
            __('_NOMBRE'),
            __('_DESCRIPCION'),
            __('_LIMITE_USUARIOS'),
            __('_PRECIO_MENSUAL'),
            __('_PRECIO_ANUAL'),
            __('_ORDEN'),
            __('_ACTIVO'),
            __('_SUSCRIPCIONES_ACTIVAS'),
            __('_FECHA_CREACION'),
        ];

        foreach ($plans as $plan) {
            $monthlyPrice = '';
            $annualPrice = '';
            
            foreach ($plan->prices as $price) {
                if ($price->period === 'monthly') {
                    $monthlyPrice = $price->amount;
                } elseif ($price->period === 'annual') {
                    $annualPrice = $price->amount;
                }
            }
            
            $csvData[] = [
                $plan->name,
                $plan->description ?? '',
                $plan->max_users ?? '',
                $monthlyPrice,
                $annualPrice,
                $plan->order ?? 0,
                $plan->is_trial ? __('_TRIAL') : __('_NORMAL'),
                count($plan->subscriptions ?? []),
                $plan->created->format('Y-m-d'),
            ];
        }

        // Generar CSV
        $filename = 'plans_' . date('Y-m-d_H-i-s') . '.csv';
        
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
     * Obtener estadísticas generales de planes
     *
     * @return array
     */
    private function _getPlanStats(): array
    {
        $total = $this->Plans->find()->count();
        
        $withSubscriptions = $this->Plans->find()
            ->matching('Subscriptions')
            ->count();

        $Subscriptions = $this->getTable('JornaticCore.Subscriptions');
        $totalSubscriptions = $Subscriptions->find()
            ->where(['status IN' => ['active', 'trial']])
            ->count();

        return [
            'total' => $total,
            'with_subscriptions' => $withSubscriptions,
            'total_active_subscriptions' => $totalSubscriptions,
        ];
    }

    /**
     * Obtener estadísticas específicas de un plan
     *
     * @param \JornaticCore\Model\Entity\Plan $plan
     * @return array
     */
    private function _getSpecificPlanStats($plan): array
    {
        $Subscriptions = $this->getTable('JornaticCore.Subscriptions');
        
        $totalSubscriptions = count($plan->subscriptions ?? []);
        
        $activeSubscriptions = $Subscriptions->find()
            ->where([
                'plan_id' => $plan->id,
                'status IN' => ['active', 'trial']
            ])
            ->count();
            
        $monthlySubscriptions = $Subscriptions->find()
            ->where([
                'plan_id' => $plan->id,
                'period' => 'monthly',
                'status IN' => ['active', 'trial']
            ])
            ->count();
            
        $annualSubscriptions = $Subscriptions->find()
            ->where([
                'plan_id' => $plan->id,
                'period' => 'annual', 
                'status IN' => ['active', 'trial']
            ])
            ->count();

        // Calcular revenue mensual
        $monthlyRevenue = 0;
        $annualRevenue = 0;
        
        foreach ($plan->prices as $price) {
            if ($price->period === 'monthly') {
                $monthlyRevenue = $monthlySubscriptions * (float)$price->amount;
            } elseif ($price->period === 'annual') {
                $annualRevenue = $annualSubscriptions * (float)$price->amount;
            }
        }

        return [
            'total_subscriptions' => $totalSubscriptions,
            'active_subscriptions' => $activeSubscriptions,
            'monthly_subscriptions' => $monthlySubscriptions,
            'annual_subscriptions' => $annualSubscriptions,
            'monthly_revenue' => $monthlyRevenue,
            'annual_revenue' => $annualRevenue,
            'total_revenue' => $monthlyRevenue + $annualRevenue,
        ];
    }
}