<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\JornaticCore\Model\Entity\Company> $companies
 * @var array $filters
 * @var array $stats
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-base-content"><?= __('_GESTION_EMPRESAS') ?></h1>
            <p class="text-base-content/60 mt-1"><?= __('_ADMINISTRAR_EMPRESAS_ECOSISTEMA') ?></p>
        </div>
        
        <div class="flex gap-2">
            <?= $this->Html->link(
                $this->Icon->render('plus', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_NUEVA_EMPRESA'),
                ['action' => 'add'],
                [
                    'class' => 'btn btn-primary btn-sm',
                    'escape' => false
                ]
            ) ?>
            
            <?= $this->Html->link(
                $this->Icon->render('arrow-down-tray', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_EXPORTAR_CSV'),
                ['action' => 'export'] + $filters,
                [
                    'class' => 'btn btn-outline btn-sm',
                    'escape' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats stats-horizontal shadow w-full mb-6">
        <div class="stat">
            <div class="stat-figure text-primary">
                <?= $this->Icon->render('building-office', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TOTAL_EMPRESAS') ?></div>
            <div class="stat-value text-primary"><?= number_format($stats['total']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-success">
                <?= $this->Icon->render('check-circle', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_EMPRESAS_ACTIVAS') ?></div>
            <div class="stat-value text-success"><?= number_format($stats['active']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-info">
                <?= $this->Icon->render('users', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TOTAL_USUARIOS') ?></div>
            <div class="stat-value text-info"><?= number_format($stats['total_users']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-warning">
                <?= $this->Icon->render('chart-bar', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_MEDIA_USUARIOS_EMPRESA') ?></div>
            <div class="stat-value text-warning"><?= $stats['average_users_per_company'] ?></div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card bg-base-100 shadow-lg mb-6">
        <div class="card-body">
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'flex flex-wrap gap-4 items-end']) ?>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_BUSCAR') ?></span></label>
                <?= $this->Form->control('search', [
                    'label' => false,
                    'placeholder' => __('_BUSCAR_POR_NOMBRE_EMAIL'),
                    'value' => $filters['search'] ?? '',
                    'class' => 'input input-bordered input-sm w-80'
                ]) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_ESTADO') ?></span></label>
                <?= $this->Form->select('is_active', [
                    '' => __('_TODOS'),
                    '1' => __('_ACTIVAS'),
                    '0' => __('_INACTIVAS')
                ], [
                    'value' => $filters['is_active'] ?? '',
                    'class' => 'select select-bordered select-sm'
                ]) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_EMPLEADOS') ?></span></label>
                <?= $this->Form->select('employee_range', [
                    '' => __('_TODOS'),
                    '1-10' => '1-10 ' . __('_EMPLEADOS'),
                    '11-50' => '11-50 ' . __('_EMPLEADOS'),
                    '51-100' => '51-100 ' . __('_EMPLEADOS'),
                    '100+' => '100+ ' . __('_EMPLEADOS')
                ], [
                    'value' => $filters['employee_range'] ?? '',
                    'class' => 'select select-bordered select-sm'
                ]) ?>
            </div>
            
            <div class="flex gap-2">
                <?= $this->Form->button(__('_FILTRAR'), ['class' => 'btn btn-primary btn-sm']) ?>
                <?= $this->Html->link(__('_LIMPIAR'), ['action' => 'index'], ['class' => 'btn btn-ghost btn-sm']) ?>
            </div>
            
            <?= $this->Form->end() ?>
        </div>
    </div>

    <!-- Tabla de Empresas -->
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('name', __('_NOMBRE')) ?></th>
                            <th><?= $this->Paginator->sort('email', __('_EMAIL')) ?></th>
                            <th><?= $this->Paginator->sort('cif', __('_CIF')) ?></th>
                            <th><?= __('_EMPLEADOS') ?></th>
                            <th><?= __('_SUSCRIPCION') ?></th>
                            <th><?= $this->Paginator->sort('is_active', __('_ESTADO')) ?></th>
                            <th><?= $this->Paginator->sort('created', __('_FECHA_CREACION')) ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($companies as $company): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-neutral text-neutral-content rounded-full w-8">
                                            <span class="text-xs"><?= strtoupper(substr($company->name, 0, 2)) ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold"><?= h($company->name) ?></div>
                                        <div class="text-sm opacity-50"><?= h($company->industry ?? '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= h($company->email) ?></td>
                            <td><?= h($company->nif) ?></td>
                            <td>
                                <span class="badge badge-primary"><?= number_format(count($company->users ?? [])) ?></span>
                            </td>
                            <td>
                                <?php if (!empty($company->subscriptions)): ?>
                                    <?php $subscription = $company->active_subscription; ?>
                                    <span class="badge badge-success"><?= h($subscription->plan->name ?? '') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-error"><?= __('_SIN_SUSCRIPCION') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($company->active_subscription): ?>
                                    <span class="badge badge-success"><?= __('_ACTIVA') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-error"><?= __('_INACTIVA') ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= $company->created->format('d/m/Y') ?></td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <?= $this->Html->link(
                                        $this->Icon->render('eye', 'solid', ['class' => 'w-4 h-4']),
                                        ['action' => 'view', $company->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_VER_DETALLES')
                                        ]
                                    ) ?>
                                    
                                    <?= $this->Html->link(
                                        $this->Icon->render('pencil-square', 'solid', ['class' => 'w-4 h-4']),
                                        ['action' => 'edit', $company->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_EDITAR')
                                        ]
                                    ) ?>
                                    
                                    <?php if ($company->is_active): ?>
                                        <?= $this->Form->postLink(
                                            $this->Icon->render('x-circle', 'solid', ['class' => 'w-4 h-4']),
                                            ['action' => 'deactivate', $company->id],
                                            [
                                                'class' => 'btn btn-error btn-xs',
                                                'escape' => false,
                                                'title' => __('_DESACTIVAR'),
                                                'confirm' => __('_CONFIRMAR_DESACTIVAR_EMPRESA')
                                            ]
                                        ) ?>
                                    <?php else: ?>
                                        <?= $this->Form->postLink(
                                            $this->Icon->render('check-circle', 'solid', ['class' => 'w-4 h-4']),
                                            ['action' => 'activate', $company->id],
                                            [
                                                'class' => 'btn btn-success btn-xs',
                                                'escape' => false,
                                                'title' => __('_ACTIVAR'),
                                                'confirm' => __('_CONFIRMAR_ACTIVAR_EMPRESA')
                                            ]
                                        ) ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginación -->
    <div class="flex justify-center mt-6">
        <div class="join">
            <?= $this->Paginator->first('<<', ['class' => 'join-item btn btn-sm']) ?>
            <?= $this->Paginator->prev('<', ['class' => 'join-item btn btn-sm']) ?>
            <?= $this->Paginator->numbers(['class' => 'join-item btn btn-sm']) ?>
            <?= $this->Paginator->next('>', ['class' => 'join-item btn btn-sm']) ?>
            <?= $this->Paginator->last('>>', ['class' => 'join-item btn btn-sm']) ?>
        </div>
    </div>

    <!-- Info de paginación -->
    <div class="text-center mt-4 text-sm text-base-content/60">
        <?= $this->Paginator->counter(__('_PAGINA_{page}_DE_{pages}_MOSTRANDO_{current}_DE_{count}_TOTAL')) ?>
    </div>
</div>