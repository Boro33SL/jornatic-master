<?php
/**
 * @var \App\View\AppView $this
 * @var \JornaticCore\Model\Entity\Holiday $holiday
 * @var array $relatedHolidays
 * @var array $conflictingUsers
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><?= $this->Html->link(__('_GESTION_FESTIVOS'), ['action' => 'index']) ?></li>
                    <li><?= h($holiday->name) ?></li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold text-base-content"><?= h($holiday->name) ?></h1>
            <p class="text-base-content/60 mt-1"><?= $holiday->date->format('d/m/Y') ?> 
                - <?= h($holiday->company->name ?? __('_NACIONAL')) ?></p>
        </div>

        <div class="flex gap-2">
            <?= $this->Html->link(
                $this->Icon->render('pencil-square', 'solid', ['class' => 'w-5 h-5 mr-2 text-white']) . __('_EDITAR'),
                ['action' => 'edit', $holiday->id],
                [
                    'class' => 'btn btn-primary btn-sm',
                    'escape' => false
                ]
            ) ?>

            <?= $this->Html->link(
                $this->Icon->render('arrow-left', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_VOLVER_AL_LISTADO'),
                ['action' => 'index'],
                [
                    'class' => 'btn btn-outline btn-sm',
                    'escape' => false
                ]
            ) ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2">
            <!-- Datos del Festivo -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h2 class="card-title text-xl"><?= __('_INFORMACION_PRINCIPAL') ?></h2>
                    
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <?php
                            $statusClass = $holiday->is_active ? 'badge-success' : 'badge-error';
                            $statusText = $holiday->is_active ? __('_ACTIVO') : __('_INACTIVO');
                            ?>
                            <div class="badge <?= $statusClass ?> badge-lg"><?= $statusText ?></div>
                            
                            <?php if ($holiday->is_today): ?>
                                <div class="badge badge-info badge-lg ml-2"><?= __('_HOY') ?></div>
                            <?php endif; ?>

                            <?php if ($holiday->is_recurring): ?>
                                <div class="badge badge-secondary badge-lg ml-2"><?= __('_RECURRENTE') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="text-right">
                            <div class="text-3xl font-bold text-primary"><?= $holiday->date->format('d') ?></div>
                            <div class="text-sm text-base-content/60"><?= ucfirst($holiday->date->format('M Y')) ?></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_NOMBRE') ?></label>
                                <p class="text-lg font-semibold"><?= h($holiday->name) ?></p>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA') ?></label>
                                <p class="text-lg font-semibold"><?= $holiday->date->format('d/m/Y') ?></p>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_TIPO') ?></label>
                                <p class="text-lg">
                                    <?php
                                    $typeClass = match($holiday->type) {
                                        'national' => 'badge-success',
                                        'regional' => 'badge-info', 
                                        'company' => 'badge-warning',
                                        default => 'badge-ghost'
                                    };
                                    $typeText = match($holiday->type) {
                                        'national' => __('_NACIONAL'),
                                        'regional' => __('_REGIONAL'),
                                        'company' => __('_EMPRESA'),
                                        default => ucfirst($holiday->type)
                                    };
                                    ?>
                                    <span class="badge <?= $typeClass ?>"><?= $typeText ?></span>
                                </p>
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_EMPRESA') ?></label>
                                <p class="text-lg font-semibold"><?= h($holiday->company->name ?? __('_NACIONAL')) ?></p>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_RECURRENTE') ?></label>
                                <p class="text-lg"><?= $holiday->is_recurring ? __('_SI') : __('_NO') ?></p>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_CREACION') ?></label>
                                <p class="text-lg"><?= $holiday->created->format('d/m/Y H:i') ?></p>
                            </div>
                        </div>
                    </div>

                    <?php if ($holiday->description): ?>
                    <div class="mt-6">
                        <label class="text-sm font-medium text-base-content/60"><?= __('_DESCRIPCION') ?></label>
                        <p class="mt-2 text-base-content/80"><?= h($holiday->description) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Empleados Afectados -->
            <?php if (!empty($conflictingUsers)): ?>
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl text-warning">
                        <?= $this->Icon->render('exclamation-triangle', 'solid', ['class' => 'w-6 h-6 mr-2']) ?>
                        <?= __('_EMPLEADOS_CON_ASISTENCIAS') ?>
                    </h2>
                    <p class="text-base-content/60 mb-4"><?= __('_EMPLEADOS_REGISTRARON_ASISTENCIAS_EN_FESTIVO') ?></p>
                    
                    <div class="overflow-x-auto">
                        <table class="table table-zebra table-sm">
                            <thead>
                                <tr>
                                    <th><?= __('_EMPLEADO') ?></th>
                                    <th><?= __('_HORA') ?></th>
                                    <th><?= __('_TIPO') ?></th>
                                    <th><?= __('_UBICACION') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($conflictingUsers as $attendance): ?>
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-8">
                                                    <span class="text-xs"><?= strtoupper(substr($attendance->user->name ?? '', 0, 2)) ?></span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-bold text-sm"><?= h($attendance->user->name ?? '') ?> <?= h($attendance->user->lastname ?? '') ?></div>
                                                <div class="text-xs opacity-50"><?= h($attendance->user->email ?? '') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= $attendance->timestamp->format('H:i') ?></td>
                                    <td>
                                        <?php
                                        $typeClass = match($attendance->type) {
                                            'in' => 'badge-success',
                                            'out' => 'badge-error',
                                            'break_start' => 'badge-warning',
                                            'break_end' => 'badge-info',
                                            default => 'badge-ghost'
                                        };
                                        $typeText = match($attendance->type) {
                                            'in' => __('_ENTRADA'),
                                            'out' => __('_SALIDA'),
                                            'break_start' => __('_INICIO_DESCANSO'),
                                            'break_end' => __('_FIN_DESCANSO'),
                                            default => ucfirst($attendance->type)
                                        };
                                        ?>
                                        <span class="badge <?= $typeClass ?> badge-xs"><?= $typeText ?></span>
                                    </td>
                                    <td>
                                        <?php if ($attendance->latitude && $attendance->longitude): ?>
                                            <?= $this->Icon->render('map-pin', 'solid', ['class' => 'w-4 h-4 text-success']) ?>
                                            <span class="text-xs">GPS</span>
                                        <?php elseif ($attendance->ip_address): ?>
                                            <?= $this->Icon->render('wifi', 'solid', ['class' => 'w-4 h-4 text-info']) ?>
                                            <span class="text-xs">IP</span>
                                        <?php else: ?>
                                            <span class="text-xs text-base-content/50"><?= __('_NO_DISPONIBLE') ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Festivos Relacionados -->
            <?php if (!empty($relatedHolidays)): ?>
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl">
                        <?= $this->Icon->render('calendar-days', 'solid', ['class' => 'w-6 h-6 mr-2']) ?>
                        <?= __('_OTROS_FESTIVOS_MISMO_ANO') ?>
                    </h2>
                    <p class="text-base-content/60 mb-4"><?= __('_FESTIVOS_RELACIONADOS_ANO') ?> <?= $holiday->date->format('Y') ?></p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($relatedHolidays as $relatedHoliday): ?>
                        <div class="card card-compact bg-base-200 shadow">
                            <div class="card-body">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-sm"><?= h($relatedHoliday->name) ?></h3>
                                        <p class="text-xs text-base-content/60"><?= $relatedHoliday->date->format('d/m/Y') ?></p>
                                        <p class="text-xs">
                                            <?php
                                            $typeClass = match($relatedHoliday->type) {
                                                'national' => 'badge-success',
                                                'regional' => 'badge-info', 
                                                'company' => 'badge-warning',
                                                default => 'badge-ghost'
                                            };
                                            $typeText = match($relatedHoliday->type) {
                                                'national' => __('_NACIONAL'),
                                                'regional' => __('_REGIONAL'),
                                                'company' => __('_EMPRESA'),
                                                default => ucfirst($relatedHoliday->type)
                                            };
                                            ?>
                                            <span class="badge <?= $typeClass ?> badge-xs"><?= $typeText ?></span>
                                        </p>
                                    </div>
                                    <div class="flex gap-1">
                                        <?= $this->Html->link(
                                            $this->Icon->render('eye', 'solid', ['class' => 'w-4 h-4']),
                                            ['action' => 'view', $relatedHoliday->id],
                                            [
                                                'class' => 'btn btn-ghost btn-xs',
                                                'escape' => false,
                                                'title' => __('_VER_DETALLES')
                                            ]
                                        ) ?>
                                        <?= $this->Html->link(
                                            $this->Icon->render('pencil-square', 'solid', ['class' => 'w-4 h-4']),
                                            ['action' => 'edit', $relatedHoliday->id],
                                            [
                                                'class' => 'btn btn-ghost btn-xs',
                                                'escape' => false,
                                                'title' => __('_EDITAR')
                                            ]
                                        ) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (count($relatedHolidays) >= 10): ?>
                    <div class="text-center mt-4">
                        <?= $this->Html->link(
                            __('_VER_TODOS_LOS_FESTIVOS'),
                            ['action' => 'index', '?' => ['year' => $holiday->date->format('Y')]],
                            ['class' => 'btn btn-ghost btn-sm']
                        ) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Panel Lateral -->
        <div class="lg:col-span-1">
            <!-- Estadísticas -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h3 class="card-title"><?= __('_ESTADISTICAS') ?></h3>
                    <div class="stats stats-vertical shadow">
                        <div class="stat">
                            <div class="stat-figure text-primary">
                                <?= $this->Icon->render('calendar', 'solid', ['class' => 'w-8 h-8']) ?>
                            </div>
                            <div class="stat-title"><?= __('_FESTIVOS_MISMO_ANO') ?></div>
                            <div class="stat-value text-primary"><?= count($relatedHolidays) + 1 ?></div>
                        </div>
                        
                        <?php if (!empty($conflictingUsers)): ?>
                        <div class="stat">
                            <div class="stat-figure text-warning">
                                <?= $this->Icon->render('exclamation-triangle', 'solid', ['class' => 'w-8 h-8']) ?>
                            </div>
                            <div class="stat-title"><?= __('_ASISTENCIAS_REGISTRADAS') ?></div>
                            <div class="stat-value text-warning"><?= count($conflictingUsers) ?></div>
                        </div>
                        <?php endif; ?>

                        <div class="stat">
                            <div class="stat-figure text-info">
                                <?= $this->Icon->render('clock', 'solid', ['class' => 'w-8 h-8']) ?>
                            </div>
                            <div class="stat-title"><?= __('_DIAS_HASTA_FESTIVO') ?></div>
                            <div class="stat-value text-info"><?= $holiday->date->diffInDays(\Cake\I18n\Date::now(), false) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h3 class="card-title"><?= __('_ACCIONES') ?></h3>
                    <div class="space-y-2">
                        <?php if ($holiday->is_active): ?>
                            <?= $this->Form->postLink(
                                $this->Icon->render('x-circle', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_DESACTIVAR'),
                                ['action' => 'deactivate', $holiday->id],
                                [
                                    'class' => 'btn btn-error btn-sm w-full justify-start',
                                    'escape' => false,
                                    'confirm' => __('_CONFIRMAR_DESACTIVAR_FESTIVO')
                                ]
                            ) ?>
                        <?php else: ?>
                            <?= $this->Form->postLink(
                                $this->Icon->render('check-circle', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_ACTIVAR'),
                                ['action' => 'activate', $holiday->id],
                                [
                                    'class' => 'btn btn-success btn-sm w-full justify-start',
                                    'escape' => false,
                                    'confirm' => __('_CONFIRMAR_ACTIVAR_FESTIVO')
                                ]
                            ) ?>
                        <?php endif; ?>

                        <?= $this->Html->link(
                            $this->Icon->render('calendar', 'solid', ['class' => 'w-5 h-5 mr-2 text-white']) . __('_VER_CALENDARIO'),
                            ['action' => 'calendar', '?' => ['year' => $holiday->date->format('Y')]],
                            [
                                'class' => 'btn btn-secondary btn-sm w-full justify-start',
                                'escape' => false
                            ]
                        ) ?>

                        <?= $this->Html->link(
                            $this->Icon->render('document-duplicate', 'solid', ['class' => 'w-5 h-5 mr-2 text-white']) . __('_DUPLICAR_FESTIVO'),
                            ['action' => 'add', '?' => ['duplicate' => $holiday->id]],
                            [
                                'class' => 'btn btn-accent btn-sm w-full justify-start',
                                'escape' => false
                            ]
                        ) ?>
                    </div>
                </div>
            </div>


            <!-- Información del Sistema -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h3 class="card-title"><?= __('_INFO_SISTEMA') ?></h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_ID') ?></label>
                            <p class="font-mono text-sm"><?= $holiday->id ?></p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_UUID') ?></label>
                            <p class="font-mono text-xs break-all"><?= $holiday->uuid ?></p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_ULTIMA_MODIFICACION') ?></label>
                            <p class="text-sm"><?= $holiday->modified 
                                ? $holiday->modified->format('d/m/Y H:i') 
                                : $holiday->created->format('d/m/Y H:i') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>