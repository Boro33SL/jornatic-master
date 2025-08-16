<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Contracts Controller
 *
 * Gestión completa de contratos del ecosistema Jornatic
 *
 * @property \JornaticCore\Model\Table\ContractsTable $Contracts
 */
class ContractsController extends AppController
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
        $this->Contracts = $this->getTable('JornaticCore.Contracts');
        
        // Cargar componente de logging
        $this->loadComponent('Logging');

        // Skip authorization para todas las acciones (por ahora)
        $this->Authorization->skipAuthorization();
    }

    /**
     * Index method - Lista paginada de contratos
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        // Registrar acceso
        $this->Logging->logView('contracts_list');

        // Query base con relaciones
        $query = $this->Contracts->find()
            ->contain(['Users' => ['Companies', 'Departments'], 'ProfessionalCategories'])
            ->orderBy(['Contracts.created_at' => 'DESC']);

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
        
        if (!empty($filters['professional_category_id'])) {
            $query->where(['Contracts.professional_category_id' => $filters['professional_category_id']]);
        }
        
        if (isset($filters['is_active'])) {
            $query->where(['Contracts.is_active' => (bool)$filters['is_active']]);
        }
        
        if (!empty($filters['type'])) {
            $query->where(['Contracts.type' => $filters['type']]);
        }

        // Configurar paginación
        $this->paginate = [
            'limit' => 25,
            'maxLimit' => 100,
        ];

        $contracts = $this->paginate($query);

        // Estadísticas
        $stats = $this->_getContractStats();

        // Obtener opciones para filtros
        $Companies = $this->getTable('JornaticCore.Companies');
        $companies = $Companies->find('list')->toArray();
        
        $ProfessionalCategories = $this->getTable('JornaticCore.ProfessionalCategories');
        $professionalCategories = $ProfessionalCategories->find('list')->toArray();

        $this->set(compact('contracts', 'filters', 'stats', 'companies', 'professionalCategories'));
    }

    /**
     * View method - Detalle de un contrato
     *
     * @param string|null $id Contract id.
     * @return \Cake\Http\Response|null|void
     */
    public function view($id = null)
    {
        $contract = $this->Contracts->get($id, [
            'contain' => [
                'Users' => ['Companies', 'Departments', 'Roles'],
                'ProfessionalCategories',
            ],
        ]);

        // Registrar visualización
        $this->Logging->logView('contracts', (int)$id);

        // Obtener estadísticas del contrato
        $contractStats = $this->_getSpecificContractStats($contract);

        $this->set(compact('contract', 'contractStats'));
    }

    /**
     * Add method - Crear un nuevo contrato
     *
     * @return \Cake\Http\Response|null|void
     */
    public function add()
    {
        $contract = $this->Contracts->newEmptyEntity();

        if ($this->request->is('post')) {
            $contract = $this->Contracts->patchEntity($contract, $this->request->getData());
            
            if ($this->Contracts->save($contract)) {
                // Registrar creación
                $this->Logging->logCreate('contracts', $contract->id, [
                    'user_id' => $contract->user_id,
                    'professional_category_id' => $contract->professional_category_id,
                    'start_date' => $contract->start_date ? $contract->start_date->format('Y-m-d') : null,
                    'is_active' => $contract->is_active
                ]);
                
                $this->Flash->success(__('_CONTRATO_CREADO_CORRECTAMENTE'));
                return $this->redirect(['action' => 'view', $contract->id]);
            }
            
            $this->Flash->error(__('_ERROR_AL_CREAR_CONTRATO'));
        }

        // Obtener opciones para el formulario
        $Users = $this->getTable('JornaticCore.Users');
        $users = $Users->find()
            ->contain(['Companies'])
            ->combine('id', function($user) {
                return $user->name . ' ' . $user->lastname . ' (' . ($user->company->name ?? '') . ')';
            })
            ->toArray();
        
        $ProfessionalCategories = $this->getTable('JornaticCore.ProfessionalCategories');
        $professionalCategories = $ProfessionalCategories->find('list')->toArray();

        $this->set(compact('contract', 'users', 'professionalCategories'));
    }

    /**
     * Edit method - Editar un contrato
     *
     * @param string|null $id Contract id.
     * @return \Cake\Http\Response|null|void
     */
    public function edit($id = null)
    {
        $contract = $this->Contracts->get($id, [
            'contain' => ['Users' => ['Companies'], 'ContractTypes', 'Studies'],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $contract = $this->Contracts->patchEntity($contract, $this->request->getData());
            
            if ($this->Contracts->save($contract)) {
                // Registrar actualización
                $this->Logging->logUpdate('contracts', (int)$id, [
                    'updated_fields' => array_keys($this->request->getData()),
                    'user_name' => $contract->user->name . ' ' . $contract->user->lastname,
                    'professional_category' => $contract->professional_category->name ?? ''
                ]);
                
                $this->Flash->success(__('_CONTRATO_ACTUALIZADO_CORRECTAMENTE'));
                return $this->redirect(['action' => 'view', $id]);
            }
            
            $this->Flash->error(__('_ERROR_AL_ACTUALIZAR_CONTRATO'));
        }

        // Obtener opciones para el formulario
        $Users = $this->getTable('JornaticCore.Users');
        $users = $Users->find()
            ->contain(['Companies'])
            ->combine('id', function($user) {
                return $user->name . ' ' . $user->lastname . ' (' . ($user->company->name ?? '') . ')';
            })
            ->toArray();
        
        $ProfessionalCategories = $this->getTable('JornaticCore.ProfessionalCategories');
        $professionalCategories = $ProfessionalCategories->find('list')->toArray();

        $this->set(compact('contract', 'users', 'professionalCategories'));
    }

    /**
     * Delete method - Eliminar un contrato (soft delete)
     *
     * @param string|null $id Contract id.
     * @return \Cake\Http\Response|null
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        
        $contract = $this->Contracts->get($id, [
            'contain' => ['Users' => ['Companies'], 'ProfessionalCategories']
        ]);
        
        // Marcar como inactivo en lugar de eliminar
        $contract->is_active = false;
        $contract->end_date = new \DateTime();
        
        if ($this->Contracts->save($contract)) {
            // Registrar eliminación lógica
            $this->Logging->logDelete('contracts', (int)$id, [
                'user_name' => $contract->user->name . ' ' . $contract->user->lastname,
                'company_name' => $contract->user->company->name ?? '',
                'professional_category' => $contract->professional_category->name ?? '',
                'soft_delete' => true
            ]);
            
            $this->Flash->success(__('_CONTRATO_FINALIZADO_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_FINALIZAR_CONTRATO'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Activate method - Activar un contrato desactivado
     *
     * @param string|null $id Contract id.
     * @return \Cake\Http\Response|null
     */
    public function activate($id = null)
    {
        $this->request->allowMethod(['post']);
        
        $contract = $this->Contracts->get($id, [
            'contain' => ['Users' => ['Companies'], 'ProfessionalCategories']
        ]);
        
        $contract->is_active = true;
        $contract->end_date = null;
        
        if ($this->Contracts->save($contract)) {
            // Registrar activación
            $this->Logging->logUpdate('contracts', (int)$id, [
                'action' => 'activate',
                'user_name' => $contract->user->name . ' ' . $contract->user->lastname,
                'company_name' => $contract->user->company->name ?? ''
            ]);
            
            $this->Flash->success(__('_CONTRATO_ACTIVADO_CORRECTAMENTE'));
        } else {
            $this->Flash->error(__('_ERROR_AL_ACTIVAR_CONTRATO'));
        }

        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Terminate method - Finalizar un contrato
     *
     * @param string|null $id Contract id.
     * @return \Cake\Http\Response|null|void
     */
    public function terminate($id = null)
    {
        $contract = $this->Contracts->get($id, [
            'contain' => ['Users' => ['Companies'], 'ProfessionalCategories']
        ]);

        if ($this->request->is(['post', 'put', 'patch'])) {
            $endDate = $this->request->getData('end_date');
            $reason = $this->request->getData('termination_reason', '');
            
            $contract->is_active = false;
            $contract->end_date = new \DateTime($endDate);
            $contract->termination_reason = $reason;
            
            if ($this->Contracts->save($contract)) {
                // Registrar finalización
                $this->Logging->logUpdate('contracts', (int)$id, [
                    'action' => 'terminate',
                    'user_name' => $contract->user->name . ' ' . $contract->user->lastname,
                    'end_date' => $endDate,
                    'reason' => $reason
                ]);
                
                $this->Flash->success(__('_CONTRATO_FINALIZADO_CORRECTAMENTE'));
                return $this->redirect(['action' => 'view', $id]);
            }
            
            $this->Flash->error(__('_ERROR_AL_FINALIZAR_CONTRATO'));
        }

        $this->set(compact('contract'));
    }

    /**
     * Export method - Exportar lista de contratos a CSV
     *
     * @return \Cake\Http\Response
     */
    public function export()
    {
        $this->request->allowMethod(['get']);

        // Registrar exportación
        $this->Logging->logExport('contracts', [
            'format' => 'csv',
            'timestamp' => date('Y-m-d H:i:s')
        ]);

        $contracts = $this->Contracts->find()
            ->contain(['Users' => ['Companies'], 'ProfessionalCategories'])
            ->orderBy(['Contracts.created_at' => 'DESC'])
            ->toArray();

        // Preparar datos CSV
        $csvData = [];
        $csvData[] = [
            __('_EMPLEADO'),
            __('_EMAIL'),
            __('_EMPRESA'),
            __('_CATEGORIA_PROFESIONAL'),
            __('_FECHA_INICIO'),
            __('_FECHA_FIN'),
            __('_SALARIO'),
            __('_PORCENTAJE_HORAS'),
            __('_TIPO'),
            __('_ACTIVO'),
            __('_FECHA_CREACION'),
        ];

        foreach ($contracts as $contract) {
            $csvData[] = [
                $contract->user->name . ' ' . $contract->user->lastname,
                $contract->user->email,
                $contract->user->company->name ?? '',
                $contract->professional_category->name ?? '',
                $contract->start_date ? $contract->start_date->format('Y-m-d') : '',
                $contract->end_date ? $contract->end_date->format('Y-m-d') : '',
                $contract->salary ?? '',
                $contract->percentage_hours ?? '',
                $contract->type ?? '',
                $contract->is_active ? __('_SI') : __('_NO'),
                $contract->created_at->format('Y-m-d'),
            ];
        }

        // Generar CSV
        $filename = 'contracts_' . date('Y-m-d_H-i-s') . '.csv';
        
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
     * Obtener estadísticas generales de contratos
     *
     * @return array
     */
    private function _getContractStats(): array
    {
        $total = $this->Contracts->find()->count();
        
        $active = $this->Contracts->find()
            ->where(['is_active' => true])
            ->count();
            
        $terminated = $this->Contracts->find()
            ->where(['is_active' => false])
            ->count();
            
        $thisMonth = $this->Contracts->find()
            ->where([
                'MONTH(Contracts.created_at)' => date('m'),
                'YEAR(Contracts.created_at)' => date('Y')
            ])
            ->count();

        // Contratos por tipo
        $byType = $this->Contracts->find()
            ->select([
                'type',
                'count' => 'COUNT(Contracts.id)'
            ])
            ->where(['Contracts.is_active' => true])
            ->group(['Contracts.type'])
            ->toArray();

        // Contratos que expiran pronto (próximos 30 días)
        $expiringSoon = $this->Contracts->find()
            ->where([
                'is_active' => true,
                'end_date IS NOT NULL',
                'end_date <=' => (new \DateTime())->modify('+30 days')->format('Y-m-d')
            ])
            ->count();

        return [
            'total' => $total,
            'active' => $active,
            'terminated' => $terminated,
            'new_this_month' => $thisMonth,
            'by_type' => $byType,
            'expiring_soon' => $expiringSoon,
        ];
    }

    /**
     * Obtener estadísticas específicas de un contrato
     *
     * @param \JornaticCore\Model\Entity\Contract $contract
     * @return array
     */
    private function _getSpecificContractStats($contract): array
    {
        // Calcular duración del contrato
        $duration = null;
        $daysRemaining = null;
        
        if ($contract->start_date) {
            $now = new \DateTime();
            $startDate = $contract->start_date;
            
            if ($contract->end_date) {
                $endDate = $contract->end_date;
                $diff = $startDate->diff($endDate);
                $duration = $diff->days;
                
                if ($endDate > $now) {
                    $remaining = $now->diff($endDate);
                    $daysRemaining = $remaining->days;
                }
            } else {
                // Contrato indefinido
                $diff = $startDate->diff($now);
                $duration = $diff->days;
            }
        }

        // Obtener asistencias del usuario desde el inicio del contrato
        $Attendances = $this->getTable('JornaticCore.Attendances');
        $attendancesCount = 0;
        
        if ($contract->start_date) {
            $attendancesCount = $Attendances->find()
                ->where([
                    'user_id' => $contract->user_id,
                    'DATE(datetime) >=' => $contract->start_date->format('Y-m-d')
                ])
                ->count();
        }

        // Obtener ausencias del usuario desde el inicio del contrato
        $Absences = $this->getTable('JornaticCore.Absences');
        $absencesCount = 0;
        
        if ($contract->start_date) {
            $absencesCount = $Absences->find()
                ->where([
                    'user_id' => $contract->user_id,
                    'DATE(start_date) >=' => $contract->start_date->format('Y-m-d')
                ])
                ->count();
        }

        return [
            'duration_days' => $duration,
            'days_remaining' => $daysRemaining,
            'attendances_count' => $attendancesCount,
            'absences_count' => $absencesCount,
            'is_expiring_soon' => $daysRemaining !== null && $daysRemaining <= 30,
        ];
    }
}