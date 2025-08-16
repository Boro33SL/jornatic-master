<?php
/**
 * @var \App\View\AppView $this
 * @var \JornaticCore\Model\Entity\Contract $contract
 * @var array $contractStats
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><?= $this->Html->link(__('_CONTRATOS'), ['action' => 'index']) ?></li>
                    <li><?= __('_CONTRATO') ?> #<?= $contract->id ?></li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold text-base-content"><?= __('_DETALLE_CONTRATO') ?></h1>
            <p class="text-base-content/60 mt-1"><?= h($contract->user->full_name ?? '') ?> 
                - <?= h($contract->user->company->name ?? '') ?></p>
        </div>

        <div class="flex gap-2">
            <?= $this->Html->link(
                $this->Icon->render('pencil-square', 'solid', ['class' => 'w-5 h-5 mr-2 text-white']) . __('_EDITAR'),
                ['action' => 'edit', $contract->id],
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
            <!-- Estado del Contrato -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h2 class="card-title text-xl"><?= __('_ESTADO_CONTRATO') ?></h2>
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <?php
                            $statusClass = $contract->is_active ? 'badge-success' : 'badge-error';
                            $statusText = $contract->is_active ? __('_ACTIVO') : __('_INACTIVO');
                            ?>
                            <div class="badge <?= $statusClass ?> badge-lg"><?= $statusText ?></div>

                            <?php if ($contract->end_date && $contract->is_active): ?>
                                <?php $daysToExpire = $contract->end_date->diffInDays(\Cake\I18n\Date::now(), false); ?>
                                <?php if ($daysToExpire < 30 && $daysToExpire > 0): ?>
                                    <div class="text-warning text-sm mt-2"><?= __('_VENCE_EN_{0}_DIAS', [$daysToExpire]) ?></div>
                                <?php elseif ($daysToExpire <= 0): ?>
                                    <div class="text-error text-sm mt-2"><?= __('_CONTRATO_VENCIDO') ?></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="text-right">
                            <?php if ($contract->salary): ?>
                                <div class="text-3xl font-bold text-primary">€<?= number_format($contract->salary, 2) ?></div>
                                <div class="text-sm text-base-content/60"><?= __('_SALARIO_MENSUAL') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_INICIO') ?></label>
                                <p class="text-lg font-semibold"><?= $contract->start_date 
                                    ? $contract->start_date->format('d/m/Y') 
                                    : __('_NO_ESPECIFICADO') ?></p>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_FIN') ?></label>
                                <p class="text-lg font-semibold"><?= $contract->end_date 
                                    ? $contract->end_date->format('d/m/Y') 
                                    : __('_INDEFINIDO') ?></p>
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_CATEGORIA_PROFESIONAL') ?></label>
                                <p class="text-lg font-semibold"><?= h($contract->professional_category->name ?? __('_NO_ASIGNADA')) ?></p>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_CREACION') ?></label>
                                <p class="text-lg"><?= $contract->created_at->format('d/m/Y H:i') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Empleado -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl"><?= __('_INFORMACION_EMPLEADO') ?></h2>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="avatar placeholder">
                            <div class="bg-neutral text-neutral-content rounded-full w-16">
                                <span class="text-xl"><?= strtoupper(substr($contract->user->full_name ?? '', 0, 2)) ?></span>
                            </div>
                        </div>

                        <div class="flex-1">
                            <h3 class="text-xl font-bold"><?= h($contract->user->full_name ?? '') ?></h3>
                            <p class="text-base-content/60"><?= h($contract->user->email ?? '') ?></p>
                        </div>

                        <div>
                            <?= $this->Html->link(
                                __('_VER_EMPLEADO'),
                                ['controller' => 'Users', 'action' => 'view', $contract->user_id],
                                ['class' => 'btn btn-outline btn-sm']
                            ) ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_DNI_NIE') ?></label>
                            <p class="font-mono"><?= h($contract->user->document ?? '') ?></p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_DEPARTAMENTO') ?></label>
                            <p><?= h($contract->user->department->name ?? __('_SIN_DEPARTAMENTO')) ?></p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_ROL') ?></label>
                            <p><?= h($contract->user->role->name ?? __('_SIN_ROL')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="lg:col-span-1">
            <!-- Estadísticas del Contrato -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h3 class="card-title"><?= __('_ESTADISTICAS_CONTRATO') ?></h3>
                    <div class="stats stats-vertical shadow">
                        <div class="stat">
                            <div class="stat-figure text-primary">
                                <?= $this->Icon->render('clock', 'solid', ['class' => 'w-8 h-8']) ?>
                            </div>
                            <div class="stat-title"><?= __('_ASISTENCIAS') ?></div>
                            <div class="stat-value text-primary"><?= number_format($contractStats['attendances_count'] ?? 0) ?></div>
                        </div>
                        
                        <div class="stat">
                            <div class="stat-figure text-secondary">
                                <?= $this->Icon->render('calendar-days', 'solid', ['class' => 'w-8 h-8']) ?>
                            </div>
                            <div class="stat-title"><?= __('_AUSENCIAS') ?></div>
                            <div class="stat-value text-secondary"><?= number_format($contractStats['absences_count'] ?? 0) ?></div>
                        </div>

                        <?php if (isset($contractStats['duration']) && $contractStats['duration']): ?>
                        <div class="stat">
                            <div class="stat-figure text-accent">
                                <?= $this->Icon->render('calendar', 'solid', ['class' => 'w-8 h-8']) ?>
                            </div>
                            <div class="stat-title"><?= __('_DURACION') ?></div>
                            <div class="stat-value text-accent"><?= number_format($contractStats['duration']) ?></div>
                            <div class="stat-desc"><?= __('_DIAS') ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h3 class="card-title"><?= __('_ACCIONES') ?></h3>
                    <div class="space-y-2">
                        <?php if ($contract->is_active): ?>
                            <?= $this->Form->postLink(
                                $this->Icon->render('x-circle', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_FINALIZAR_CONTRATO'),
                                ['action' => 'deactivate', $contract->id],
                                [
                                    'class' => 'btn btn-error btn-sm w-full justify-start',
                                    'escape' => false,
                                    'confirm' => __('_CONFIRMAR_FINALIZAR_CONTRATO')
                                ]
                            ) ?>
                        <?php else: ?>
                            <?= $this->Form->postLink(
                                $this->Icon->render('check-circle', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_ACTIVAR_CONTRATO'),
                                ['action' => 'activate', $contract->id],
                                [
                                    'class' => 'btn btn-success btn-sm w-full justify-start',
                                    'escape' => false,
                                    'confirm' => __('_CONFIRMAR_ACTIVAR_CONTRATO')
                                ]
                            ) ?>
                        <?php endif; ?>

                        <?= $this->Html->link(
                            $this->Icon->render('clock', 'solid', ['class' => 'w-5 h-5 mr-2 text-white']) . __('_VER_ASISTENCIAS'),
                            [
                                'controller' => 'Attendances', 'action' => 'index',
                                '?' => ['user_id' => $contract->user_id]
                            ],
                            [
                                'class' => 'btn btn-secondary btn-sm w-full justify-start',
                                'escape' => false
                            ]
                        ) ?>

                        <?= $this->Html->link(
                            $this->Icon->render('calendar-days', 'solid', ['class' => 'w-5 h-5 mr-2 text-white']) . __('_VER_AUSENCIAS'),
                            [
                                'controller' => 'Absences', 'action' => 'index',
                                '?' => ['user_id' => $contract->user_id]
                            ],
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
                            <label class="text-sm font-medium text-base-content/60"><?= __('_ID_CONTRATO') ?></label>
                            <p class="font-mono text-sm"><?= $contract->id ?></p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_EMPRESA') ?></label>
                            <p class="text-sm"><?= h($contract->user->company->name ?? '') ?></p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_ULTIMA_MODIFICACION') ?></label>
                            <p class="text-sm"><?= $contract->updated_at 
                                ? $contract->updated_at->format('d/m/Y H:i') 
                                : $contract->created_at->format('d/m/Y H:i') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>