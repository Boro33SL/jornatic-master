<?php
/**
 * @var \App\View\AppView $this
 * @var array $userSummary
 * @var array $periodStats
 * @var array $filters
 * @var array $companies
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-base-content"><?= __('_REPORTES_ASISTENCIAS') ?></h1>
            <p class="text-base-content/60 mt-1"><?= __('_ANALISIS_ASISTENCIAS_PERIODO') ?></p>
        </div>
        
        <div class="flex gap-2">
            <?= $this->Html->link(
                $this->Icon->render('arrow-down-tray', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_EXPORTAR_CSV'),
                ['action' => 'export'] + $filters,
                [
                    'class' => 'btn btn-outline btn-sm',
                    'escape' => false
                ]
            ) ?>
            
            <?= $this->Html->link(
                $this->Icon->render('arrow-left', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_VOLVER_AL_LISTADO'),
                ['action' => 'index'],
                [
                    'class' => 'btn btn-ghost btn-sm',
                    'escape' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Filtros de Período -->
    <div class="card bg-base-100 shadow-lg mb-6">
        <div class="card-body">
            <h3 class="card-title mb-4"><?= __('_FILTROS_PERIODO') ?></h3>
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'flex flex-wrap gap-4 items-end']) ?>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_FECHA_DESDE') ?></span></label>
                <?= $this->Form->date('date_from', [
                    'value' => $filters['date_from'] ?? date('Y-m-01'),
                    'class' => 'input input-bordered input-sm'
                ]) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_FECHA_HASTA') ?></span></label>
                <?= $this->Form->date('date_to', [
                    'value' => $filters['date_to'] ?? date('Y-m-t'),
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
            
            <div class="flex gap-2">
                <?= $this->Form->button(__('_GENERAR_REPORTE'), ['class' => 'btn btn-primary btn-sm']) ?>
                <?= $this->Html->link(__('_LIMPIAR'), ['action' => 'reports'], ['class' => 'btn btn-ghost btn-sm']) ?>
            </div>
            
            <?= $this->Form->end() ?>
        </div>
    </div>

    <!-- Estadísticas del Período -->
    <div class="stats stats-horizontal shadow mb-6 w-full">
        <div class="stat">
            <div class="stat-figure text-primary">
                <?= $this->Icon->render('clipboard-document-list', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TOTAL_ASISTENCIAS_PERIODO') ?></div>
            <div class="stat-value text-primary"><?= number_format($periodStats['total']) ?></div>
            <div class="stat-desc"><?= __('_PERIODO') ?>: <?= date('d/m/Y', strtotime($periodStats['period_from'])) ?> - <?= date('d/m/Y', strtotime($periodStats['period_to'])) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-success">
                <?= $this->Icon->render('arrow-right-circle', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_ENTRADAS_PERIODO') ?></div>
            <div class="stat-value text-success"><?= number_format($periodStats['ins']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-error">
                <?= $this->Icon->render('arrow-left-circle', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_SALIDAS_PERIODO') ?></div>
            <div class="stat-value text-error"><?= number_format($periodStats['outs']) ?></div>
        </div>

        <div class="stat">
            <div class="stat-figure text-info">
                <?= $this->Icon->render('users', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_EMPLEADOS_ACTIVOS') ?></div>
            <div class="stat-value text-info"><?= count($userSummary) ?></div>
        </div>
    </div>

    <!-- Resumen por Empleado -->
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="card-body pb-0">
                <h2 class="card-title">
                    <?= $this->Icon->render('user-group', 'solid', ['class' => 'w-6 h-6 mr-2']) ?>
                    <?= __('_RESUMEN_POR_EMPLEADO') ?>
                </h2>
                <p class="text-base-content/60"><?= __('_DETALLE_ASISTENCIAS_CADA_EMPLEADO') ?></p>
            </div>
            
            <?php if (!empty($userSummary)): ?>
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th><?= __('_EMPLEADO') ?></th>
                            <th><?= __('_EMPRESA') ?></th>
                            <th class="text-center"><?= __('_TOTAL_ASISTENCIAS') ?></th>
                            <th class="text-center"><?= __('_ENTRADAS') ?></th>
                            <th class="text-center"><?= __('_SALIDAS') ?></th>
                            <th class="text-center"><?= __('_DIAS_UNICOS') ?></th>
                            <th class="text-center"><?= __('_PROMEDIO_DIA') ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userSummary as $summary): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-neutral text-neutral-content rounded-full w-10">
                                            <span class="text-sm"><?= strtoupper(substr($summary['user']->name ?? '', 0, 2)) ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold"><?= h($summary['user']->name ?? '') ?> <?= h($summary['user']->lastname ?? '') ?></div>
                                        <div class="text-sm opacity-50"><?= h($summary['user']->email ?? '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= h($summary['user']->company->name ?? '') ?></td>
                            <td class="text-center">
                                <div class="badge badge-primary"><?= number_format($summary['total_attendances']) ?></div>
                            </td>
                            <td class="text-center">
                                <div class="badge badge-success"><?= number_format($summary['ins']) ?></div>
                            </td>
                            <td class="text-center">
                                <div class="badge badge-error"><?= number_format($summary['outs']) ?></div>
                            </td>
                            <td class="text-center">
                                <div class="badge badge-info"><?= number_format($summary['unique_days']) ?></div>
                            </td>
                            <td class="text-center">
                                <?php 
                                $avgPerDay = $summary['unique_days'] > 0 ? round($summary['total_attendances'] / $summary['unique_days'], 1) : 0;
                                ?>
                                <div class="badge badge-accent"><?= $avgPerDay ?></div>
                            </td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <?= $this->Html->link(
                                        $this->Icon->render('eye', 'solid', ['class' => 'w-4 h-4']),
                                        ['action' => 'index', '?' => [
                                            'user_id' => $summary['user']->id,
                                            'date_from' => $filters['date_from'] ?? date('Y-m-01'),
                                            'date_to' => $filters['date_to'] ?? date('Y-m-t')
                                        ]],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_VER_ASISTENCIAS_USUARIO')
                                        ]
                                    ) ?>
                                    
                                    <?= $this->Html->link(
                                        $this->Icon->render('user', 'solid', ['class' => 'w-4 h-4']),
                                        ['controller' => 'Users', 'action' => 'view', $summary['user']->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_VER_EMPLEADO')
                                        ]
                                    ) ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Resumen de la tabla -->
            <div class="card-body pt-0">
                <div class="text-center text-sm text-base-content/60">
                    <?= __('_MOSTRANDO_RESULTADOS_EMPLEADOS', [count($userSummary)]) ?>
                </div>
            </div>
            
            <?php else: ?>
            <div class="card-body">
                <div class="text-center py-8">
                    <?= $this->Icon->render('document-text', 'outline', ['class' => 'w-16 h-16 mx-auto text-base-content/30 mb-4']) ?>
                    <h3 class="text-lg font-medium text-base-content/60 mb-2"><?= __('_SIN_DATOS_PERIODO') ?></h3>
                    <p class="text-base-content/50"><?= __('_NO_HAY_ASISTENCIAS_PERIODO_SELECCIONADO') ?></p>
                    <div class="mt-4">
                        <?= $this->Html->link(
                            __('_AJUSTAR_FILTROS'),
                            '#',
                            ['class' => 'btn btn-primary btn-sm', 'onclick' => 'document.querySelector(\'[name="date_from"]\').focus()']
                        ) ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Panel de Análisis -->
    <?php if (!empty($userSummary)): ?>
    <div class="mt-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Performers -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-success">
                        <?= $this->Icon->render('trophy', 'solid', ['class' => 'w-6 h-6 mr-2']) ?>
                        <?= __('_EMPLEADOS_MAS_ACTIVOS') ?>
                    </h3>
                    
                    <?php 
                    $topPerformers = array_slice(
                        array_filter($userSummary, function($user) { return $user['total_attendances'] > 0; }),
                        0, 5
                    );
                    usort($topPerformers, function($a, $b) {
                        return $b['total_attendances'] <=> $a['total_attendances'];
                    });
                    ?>
                    
                    <div class="space-y-3">
                        <?php foreach ($topPerformers as $index => $performer): ?>
                        <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="badge badge-<?= $index === 0 ? 'warning' : ($index === 1 ? 'neutral' : 'outline') ?> badge-lg">
                                    #<?= $index + 1 ?>
                                </div>
                                <div>
                                    <div class="font-semibold text-sm"><?= h($performer['user']->name) ?> <?= h($performer['user']->lastname) ?></div>
                                    <div class="text-xs text-base-content/60"><?= h($performer['user']->company->name ?? '') ?></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-primary"><?= $performer['total_attendances'] ?></div>
                                <div class="text-xs text-base-content/60"><?= __('_ASISTENCIAS') ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Adicionales -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title text-info">
                        <?= $this->Icon->render('chart-bar', 'solid', ['class' => 'w-6 h-6 mr-2']) ?>
                        <?= __('_ESTADISTICAS_ADICIONALES') ?>
                    </h3>
                    
                    <?php
                    $totalAttendances = array_sum(array_column($userSummary, 'total_attendances'));
                    $totalDays = array_sum(array_column($userSummary, 'unique_days'));
                    $avgAttendancesPerUser = count($userSummary) > 0 ? round($totalAttendances / count($userSummary), 1) : 0;
                    $avgDaysPerUser = count($userSummary) > 0 ? round($totalDays / count($userSummary), 1) : 0;
                    ?>
                    
                    <div class="stats stats-vertical shadow">
                        <div class="stat">
                            <div class="stat-title"><?= __('_PROMEDIO_ASISTENCIAS_EMPLEADO') ?></div>
                            <div class="stat-value text-primary text-2xl"><?= $avgAttendancesPerUser ?></div>
                        </div>
                        
                        <div class="stat">
                            <div class="stat-title"><?= __('_PROMEDIO_DIAS_TRABAJADOS') ?></div>
                            <div class="stat-value text-secondary text-2xl"><?= $avgDaysPerUser ?></div>
                        </div>
                        
                        <div class="stat">
                            <div class="stat-title"><?= __('_TOTAL_DIAS_TRABAJADOS') ?></div>
                            <div class="stat-value text-accent text-2xl"><?= $totalDays ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>