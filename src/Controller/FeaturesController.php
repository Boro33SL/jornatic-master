<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Features Controller
 *
 * Gestión completa de características del ecosistema Jornatic
 *
 * @property \JornaticCore\Model\Table\FeaturesTable $Features
 */
class FeaturesController extends AppController
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
        $this->Features = $this->getTable('JornaticCore.Features');
        
        // Cargar componente de logging
        $this->loadComponent('Logging');

        // Skip authorization para todas las acciones (por ahora)
        $this->Authorization->skipAuthorization();
    }

    /**
     * Index method - Lista paginada de características
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        // Registrar acceso
        $this->Logging->logView('features_list');

        // Query base con relaciones
        $query = $this->Features->find()
            ->contain(['Plans'])
            ->orderBy(['Features.position' => 'ASC', 'Features.created' => 'DESC']);

        // Aplicar filtros si existen
        $filters = $this->request->getQueryParams();
        
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where([
                'OR' => [
                    'Features.name LIKE' => $search,
                    'Features.code LIKE' => $search,
                ]
            ]);
        }
        
        if (!empty($filters['data_type'])) {
            $query->where(['Features.data_type' => $filters['data_type']]);
        }

        // Configurar paginación
        $this->paginate = [
            'limit' => 25,
            'maxLimit' => 100,
        ];

        $features = $this->paginate($query);

        // Estadísticas
        $stats = $this->_getFeatureStats();

        // Obtener tipos de datos para filtros
        $dataTypes = $this->Features->find()
            ->select(['data_type'])
            ->group(['data_type'])
            ->extract('data_type')
            ->toArray();

        $this->set(compact('features', 'filters', 'stats', 'dataTypes'));
    }

    /**
     * View method - Detalle de una característica
     *
     * @param string|null $id Feature id.
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $feature = $this->Features->get($id, [
            'contain' => [
                'Plans' => ['Subscriptions' => ['Companies']],
            ],
        ]);

        // Registrar visualización
        $this->Logging->logView('features', (int)$id);

        // Obtener estadísticas de la característica
        $featureStats = $this->_getSpecificFeatureStats($feature);

        $this->set(compact('feature', 'featureStats'));
    }

    /**
     * Add method - Crear una nueva característica
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $feature = $this->Features->newEmptyEntity();

        if ($this->request->is('post')) {
            $feature = $this->Features->patchEntity($feature, $this->request->getData());
            
            if ($this->Features->save($feature)) {
                // Registrar creación
                $this->Logging->logCreate('features', $feature->id, [
                    'feature_name' => $feature->name,
                    'feature_code' => $feature->code,
                    'data_type' => $feature->data_type,
                    'position' => $feature->position
                ]);
                
                $this->Flash->success(__('_CARACTERISTICA_CREADA_CORRECTAMENTE'));
                return $this->redirect(['action' => 'view', $feature->id]);
            }
            
            $this->Flash->error(__('_ERROR_AL_CREAR_CARACTERISTICA'));
        }

        // Obtener planes disponibles
        $Plans = $this->getTable('JornaticCore.Plans');
        $plans = $Plans->find('list')->toArray();

        $this->set(compact('feature', 'plans'));
    }

    /**
     * Edit method - Editar una característica
     *
     * @param string|null $id Feature id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit($id = null)
    {
        $feature = $this->Features->get($id, [
            'contain' => ['Plans'],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $feature = $this->Features->patchEntity($feature, $this->request->getData());
            
            if ($this->Features->save($feature)) {
                // Registrar actualización
                $this->Logging->logUpdate('features', (int)$id, [
                    'updated_fields' => array_keys($this->request->getData()),
                    'feature_name' => $feature->name,
                    'feature_code' => $feature->code
                ]);
                
                $this->Flash->success(__('_CARACTERISTICA_ACTUALIZADA_CORRECTAMENTE'));
                return $this->redirect(['action' => 'view', $id]);
            }
            
            $this->Flash->error(__('_ERROR_AL_ACTUALIZAR_CARACTERISTICA'));
        }

        // Obtener planes disponibles
        $Plans = $this->getTable('JornaticCore.Plans');
        $plans = $Plans->find('list')->toArray();

        $this->set(compact('feature', 'plans'));
    }

    /**
     * Delete method - Eliminar una característica (soft delete)
     *
     * @param string|null $id Feature id.
     * @return \Cake\Http\Response|null
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        
        $feature = $this->Features->get($id, ['contain' => ['Plans']]);
        
        // Verificar si la característica está siendo usada en planes
        $plansUsing = count($feature->plans ?? []);

        if ($plansUsing > 0) {
            $this->Flash->error(__('_NO_SE_PUEDE_ELIMINAR_CARACTERISTICA_EN_USO'));
            return $this->redirect(['action' => 'view', $id]);
        }
        
        if ($this->Features->delete($feature)) {
            // Registrar eliminación
            $this->Logging->logDelete('features', (int)$id, [
                'feature_name' => $feature->name,
                'feature_code' => $feature->code,
                'plans_using' => count($feature->plans ?? []),
                'hard_delete' => true
            ]);
            
            $this->Flash->success(__('_CARACTERISTICA_ELIMINADA_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_ELIMINAR_CARACTERISTICA'));
        }

        return $this->redirect(['action' => 'index']);
    }


    /**
     * Usage method - Ver uso de características por plan
     *
     * @return \Cake\Http\Response|null|void
     */
    public function usage()
    {
        // Registrar acceso
        $this->Logging->logView('features_usage');

        // Obtener todas las características con sus planes
        $features = $this->Features->find()
            ->contain(['Plans' => ['Subscriptions']])
            ->orderBy(['Features.position' => 'ASC'])
            ->toArray();

        // Calcular estadísticas de uso
        $usageStats = [];
        foreach ($features as $feature) {
            $totalPlans = count($feature->plans ?? []);
            $activePlans = count(array_filter($feature->plans ?? [], function($plan) {
                return $plan->is_active;
            }));
            
            $totalSubscriptions = 0;
            $activeSubscriptions = 0;
            
            foreach ($feature->plans as $plan) {
                $totalSubscriptions += count($plan->subscriptions ?? []);
                $activeSubscriptions += count(array_filter($plan->subscriptions ?? [], function($sub) {
                    return in_array($sub->status, ['active', 'trial']);
                }));
            }
            
            $usageStats[] = [
                'feature' => $feature,
                'total_plans' => $totalPlans,
                'active_plans' => $activePlans,
                'total_subscriptions' => $totalSubscriptions,
                'active_subscriptions' => $activeSubscriptions,
            ];
        }

        $this->set(compact('usageStats'));
    }

    /**
     * Export method - Exportar lista de características a CSV
     *
     * @return \Cake\Http\Response
     */
    public function export()
    {
        $this->request->allowMethod(['get']);

        // Registrar exportación
        $this->Logging->logExport('features', [
            'format' => 'csv',
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        $features = $this->Features->find()
            ->contain(['Plans'])
            ->orderBy(['Features.position' => 'ASC'])
            ->toArray();

        // Preparar datos CSV
        $csvData = [];
        $csvData[] = [
            __('_NOMBRE'),
            __('_CODIGO'),
            __('_TIPO_DATO'),
            __('_POSICION'),
            __('_PLANES_ASOCIADOS'),
            __('_FECHA_CREACION'),
        ];

        foreach ($features as $feature) {
            $plansNames = array_map(function($plan) {
                return $plan->name;
            }, $feature->plans ?? []);
            
            $csvData[] = [
                $feature->name,
                $feature->code ?? '',
                $feature->data_type ?? '',
                $feature->position ?? 0,
                implode(', ', $plansNames),
                $feature->created->format('Y-m-d'),
            ];
        }

        // Generar CSV
        $filename = 'features_' . date('Y-m-d_H-i-s') . '.csv';
        
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
     * Obtener estadísticas generales de características
     *
     * @return array
     */
    private function _getFeatureStats(): array
    {
        $total = $this->Features->find()->count();
        
        $withPlans = $this->Features->find()
            ->matching('Plans')
            ->count();

        // Características por tipo de dato
        $byDataType = $this->Features->find()
            ->select([
                'data_type',
                'count' => 'COUNT(Features.id)'
            ])
            ->group(['Features.data_type'])
            ->toArray();

        // Características más usadas (por número de planes)
        $mostUsed = $this->Features->find()
            ->contain(['Plans'])
            ->toArray();

        // Ordenar por número de planes
        usort($mostUsed, function($a, $b) {
            return count($b->plans ?? []) - count($a->plans ?? []);
        });

        return [
            'total' => $total,
            'with_plans' => $withPlans,
            'by_data_type' => $byDataType,
            'most_used' => array_slice($mostUsed, 0, 5), // Top 5
        ];
    }

    /**
     * Obtener estadísticas específicas de una característica
     *
     * @param \JornaticCore\Model\Entity\Feature $feature
     * @return array
     */
    private function _getSpecificFeatureStats($feature): array
    {
        $totalPlans = count($feature->plans ?? []);
        
        $activePlans = count($feature->plans ?? []);

        $totalSubscriptions = 0;
        $activeSubscriptions = 0;
        $totalCompanies = [];

        foreach ($feature->plans as $plan) {
            $planSubscriptions = $plan->subscriptions ?? [];
            $totalSubscriptions += count($planSubscriptions);
            
            foreach ($planSubscriptions as $subscription) {
                if (in_array($subscription->status, ['active', 'trial'])) {
                    $activeSubscriptions++;
                }
                
                // Recopilar empresas únicas
                if (!empty($subscription->company)) {
                    $totalCompanies[$subscription->company->id] = $subscription->company;
                }
            }
        }

        return [
            'total_plans' => $totalPlans,
            'active_plans' => $activePlans,
            'total_subscriptions' => $totalSubscriptions,
            'active_subscriptions' => $activeSubscriptions,
            'total_companies' => count($totalCompanies),
            'usage_percentage' => $totalPlans > 0 ? round(($activePlans / $totalPlans) * 100, 1) : 0,
        ];
    }
}