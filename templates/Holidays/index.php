<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\JornaticCore\Model\Entity\Holiday> $holidays
 */
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-base-content"><?= __('_GESTION_FESTIVOS') ?></h1>
        <div class="flex gap-2">
            <?= $this->Html->link(__('_NUEVO_FESTIVO'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= $this->Html->link(__('_CALENDARIO'), ['action' => 'calendar'], ['class' => 'btn btn-secondary btn-sm']) ?>
            <?= $this->Html->link(__('_CREACION_MASIVA'), ['action' => 'bulk'], ['class' => 'btn btn-accent btn-sm']) ?>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats stats-horizontal shadow mb-6 w-full">
        <div class="stat">
            <div class="stat-figure text-primary">
                <?= $this->Icon->render('calendar-days', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TOTAL_FESTIVOS_ANO') ?></div>
            <div class="stat-value text-primary"><?= number_format($stats['total']) ?></div>
            <div class="stat-desc"><?= __('_ANO') ?> <?= $stats['year'] ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-secondary">
                <?= $this->Icon->render('building-office', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TOTAL_EMPRESAS') ?></div>
            <div class="stat-value text-secondary"><?= number_format($stats['total_companies']) ?></div>
            <div class="stat-desc"><?= __('_CON_FESTIVOS') ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-accent">
                <?= $this->Icon->render('chart-bar', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_MEDIA_FESTIVOS_EMPRESA') ?></div>
            <div class="stat-value text-accent"><?= number_format($stats['avg_per_company'], 1) ?></div>
        </div>

        <div class="stat">
            <div class="stat-figure text-info">
                <?= $this->Icon->render('clock', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_PROXIMOS_FESTIVOS') ?></div>
            <div class="stat-value text-info"><?= number_format($stats['upcoming']) ?></div>
            <div class="stat-desc"><?= __('_DESDE_HOY') ?></div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card bg-base-100 shadow-lg mb-6">
        <div class="card-body">
            <h3 class="card-title mb-4">
                <?= $this->Icon->render('funnel', 'solid', ['class' => 'w-6 h-6 mr-2']) ?>
                <?= __('_FILTROS_DE_BUSQUEDA') ?>
            </h3>
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'flex flex-wrap gap-4 items-end', 'id' => 'filter-form']) ?>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_NOMBRE') ?></span></label>
                <?= $this->Form->text('search', [
                    'value' => $filters['search'] ?? '',
                    'class' => 'input input-bordered input-sm w-60',
                    'placeholder' => __('_BUSCAR_POR_NOMBRE')
                ]) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_FECHA') ?></span></label>
                <?= $this->Form->date('date', [
                    'value' => $filters['date'] ?? '',
                    'class' => 'input input-bordered input-sm'
                ]) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_EMPRESA') ?></span></label>
                <?= $this->Form->select('company_id', 
                    ['' => __('_TODAS_LAS_EMPRESAS')] + $companies, 
                    [
                        'value' => $filters['company_id'] ?? '',
                        'class' => 'select select-bordered select-sm w-60'
                    ]
                ) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_TIPO') ?></span></label>
                <?= $this->Form->select('type', [
                    '' => __('_TODOS_LOS_TIPOS'),
                    'national' => __('_NACIONAL'),
                    'regional' => __('_REGIONAL'),
                    'company' => __('_EMPRESA')
                ], [
                    'value' => $filters['type'] ?? '',
                    'class' => 'select select-bordered select-sm'
                ]) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_ESTADO') ?></span></label>
                <?= $this->Form->select('is_active', [
                    '' => __('_SOLO_ACTIVOS'),
                    '1' => __('_ACTIVOS'),
                    '0' => __('_INACTIVOS')
                ], [
                    'value' => isset($filters['is_active']) ? (string)(int)$filters['is_active'] : '',
                    'class' => 'select select-bordered select-sm'
                ]) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_ANO') ?></span></label>
                <?= $this->Form->select('year', 
                    array_combine($years, $years), 
                    [
                        'value' => $filters['year'] ?? date('Y'),
                        'class' => 'select select-bordered select-sm'
                    ]
                ) ?>
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
                            <th><?= __('_NOMBRE') ?></th>
                            <th><?= __('_FECHA') ?></th>
                            <th><?= __('_EMPRESA') ?></th>
                            <th><?= __('_TIPO') ?></th>
                            <th><?= __('_RECURRENTE') ?></th>
                            <th><?= __('_ESTADO') ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($holidays as $holiday): ?>
                        <tr>
                            <td>
                                <div class="font-bold"><?= h($holiday->name) ?></div>
                                <?php if ($holiday->is_today): ?>
                                    <span class="badge badge-info badge-sm"><?= __('_HOY') ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= $holiday->formatted_date ?></td>
                            <td><?= h($holiday->company->name ?? '') ?></td>
                            <td>
                                <?php
                                $typeClass = match($holiday->type) {
                                    'NATIONAL' => 'badge-success',
                                    'regional' => 'badge-info',
                                    'company' => 'badge-warning',
                                    default => 'badge-ghost'
                                };
                                ?>
                                <span class="badge <?= $typeClass ?>"><?= __('_' . $holiday->type) ?></span>
                            </td>
                            <td>
                                <?php if ($holiday->recurring): ?>
                                    <span class="badge badge-secondary badge-sm"><?= __('_SI') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-ghost badge-sm"><?= __('_NO') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($holiday->is_active): ?>
                                    <span class="badge badge-success"><?= __('_ACTIVO') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-error"><?= __('_INACTIVO') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <?= $this->Html->link(__('_VER'), ['action' => 'view', $holiday->id], ['class' => 'btn btn-ghost btn-xs']) ?>
                                    <?= $this->Html->link(__('_EDITAR'), ['action' => 'edit', $holiday->id], ['class' => 'btn btn-ghost btn-xs']) ?>
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