<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\JornaticCore\Model\Entity\User> $users
 * @var array $filters
 * @var array $stats
 * @var array $companies
 * @var array $departments
 */
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-base-content"><?= __('_GESTION_USUARIOS') ?></h1>
            <p class="text-base-content/60 mt-1"><?= __('_ADMINISTRAR_EMPLEADOS_SISTEMA') ?></p>
        </div>
        <div class="flex gap-2">
            <?= $this->Html->link(
                $this->Icon->render('plus', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_NUEVO_USUARIO'),
                ['action' => 'add'],
                [
                    'class' => 'btn btn-primary btn-sm',
                    'escape' => false
                ]
            ) ?>
            <?= $this->Html->link(
                $this->Icon->render('arrow-down-tray', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_EXPORTAR_CSV'),
                ['action' => 'export'],
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
                <?= $this->Icon->render('users', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TOTAL_USUARIOS') ?></div>
            <div class="stat-value text-primary"><?= number_format($stats['total']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-success">
                <?= $this->Icon->render('check-circle', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_USUARIOS_ACTIVOS') ?></div>
            <div class="stat-value text-success"><?= number_format($stats['active']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-info">
                <?= $this->Icon->render('document-text', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_CON_CONTRATOS') ?></div>
            <div class="stat-value text-info"><?= number_format($stats['with_contracts']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-warning">
                <?= $this->Icon->render('plus-circle', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_NUEVOS_ESTE_MES') ?></div>
            <div class="stat-value text-warning"><?= number_format($stats['new_this_month']) ?></div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-lg mb-6">
        <div class="card-body">
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'flex flex-wrap gap-4 items-end']) ?>
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_BUSCAR') ?></span></label>
                <?= $this->Form->control('search', ['label' => false, 'placeholder' => __('_BUSCAR_NOMBRE_EMAIL'), 'value' => $filters['search'] ?? '', 'class' => 'input input-bordered input-sm w-80']) ?>
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_EMPRESA') ?></span></label>
                <?= $this->Form->select('company_id', ['' => __('_TODAS')] + $companies, ['value' => $filters['company_id'] ?? '', 'class' => 'select select-bordered select-sm']) ?>
            </div>
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_DEPARTAMENTO') ?></span></label>
                <?= $this->Form->select('department_id', ['' => __('_TODOS')] + $departments, ['value' => $filters['department_id'] ?? '', 'class' => 'select select-bordered select-sm']) ?>
            </div>
            <div class="flex gap-2">
                <?= $this->Form->button(__('_FILTRAR'), ['class' => 'btn btn-primary btn-sm']) ?>
                <?= $this->Html->link(__('_LIMPIAR'), ['action' => 'index'], ['class' => 'btn btn-ghost btn-sm']) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th><?= __('_USUARIO') ?></th>
                            <th><?= __('_EMAIL') ?></th>
                            <th><?= __('_EMPRESA') ?></th>
                            <th><?= __('_DEPARTAMENTO') ?></th>
                            <th><?= __('_ROL') ?></th>
                            <th><?= __('_ESTADO') ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-neutral text-neutral-content rounded-full w-8">
                                            <span class="text-xs"><?= strtoupper(substr($user->name, 0, 1) . substr($user->lastname, 0, 1)) ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold"><?= h($user->name . ' ' . $user->lastname) ?></div>
                                        <div class="text-sm opacity-50"><?= h($user->dni_nie ?? '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= h($user->email) ?></td>
                            <td><?= h($user->company->name ?? '') ?></td>
                            <td><?= h($user->department->name ?? __('_SIN_DEPARTAMENTO')) ?></td>
                            <td><?= h($user->role->name ?? __('_SIN_ROL')) ?></td>
                            <td>
                                <?php if ($user->is_active): ?>
                                    <span class="badge badge-success"><?= __('_ACTIVO') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-error"><?= __('_INACTIVO') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <?= $this->Html->link(
                                        $this->Icon->render('eye', 'solid', ['class' => 'w-4 h-4']),
                                        ['action' => 'view', $user->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_VER_DETALLES')
                                        ]
                                    ) ?>
                                    <?= $this->Html->link(
                                        $this->Icon->render('pencil-square', 'solid', ['class' => 'w-4 h-4']),
                                        ['action' => 'edit', $user->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_EDITAR')
                                        ]
                                    ) ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>