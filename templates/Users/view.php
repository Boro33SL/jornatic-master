<?php
/**
 * @var \App\View\AppView $this
 * @var \JornaticCore\Model\Entity\User $user
 */
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><?= $this->Html->link(__('_USUARIOS'), ['action' => 'index']) ?></li>
                    <li><?= h($user->name . ' ' . $user->lastname) ?></li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold text-base-content"><?= h($user->name . ' ' . $user->lastname) ?></h1>
        </div>
        <div class="flex gap-2">
            <?= $this->Html->link(__('_EDITAR'), ['action' => 'edit', $user->id], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= $this->Html->link(__('_VOLVER'), ['action' => 'index'], ['class' => 'btn btn-outline btn-sm']) ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title"><?= __('_INFORMACION_PERSONAL') ?></h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_NOMBRE_COMPLETO') ?></label>
                        <p class="text-lg font-semibold"><?= h($user->name . ' ' . $user->lastname) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_EMAIL') ?></label>
                        <p class="text-lg"><?= h($user->email) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_DNI_NIE') ?></label>
                        <p class="text-lg font-mono"><?= h($user->dni_nie ?? __('_NO_ESPECIFICADO')) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_TELEFONO') ?></label>
                        <p class="text-lg"><?= h($user->phone ?? __('_NO_ESPECIFICADO')) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_NACIMIENTO') ?></label>
                        <p class="text-lg"><?= $user->birth_date ? $user->birth_date->format('d/m/Y') : __('_NO_ESPECIFICADO') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title"><?= __('_INFORMACION_LABORAL') ?></h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_EMPRESA') ?></label>
                        <p class="text-lg font-semibold"><?= h($user->company->name ?? __('_NO_ASIGNADA')) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_DEPARTAMENTO') ?></label>
                        <p class="text-lg"><?= h($user->department->name ?? __('_NO_ASIGNADO')) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_ROL') ?></label>
                        <p class="text-lg"><?= h($user->role->name ?? __('_NO_ASIGNADO')) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_ESTADO') ?></label>
                        <p class="text-lg">
                            <?php if ($user->is_active): ?>
                                <span class="badge badge-success badge-lg"><?= __('_ACTIVO') ?></span>
                            <?php else: ?>
                                <span class="badge badge-error badge-lg"><?= __('_INACTIVO') ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_INGRESO') ?></label>
                        <p class="text-lg"><?= $user->created->format('d/m/Y') ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Contrato Activo -->
    <?php if ($activeContract): ?>
    <div class="card bg-base-100 shadow-lg mt-6">
        <div class="card-body">
            <div class="flex items-center gap-3 mb-4">
                <?= $this->Icon->render('document-text', 'solid', ['class' => 'w-6 h-6 text-primary']) ?>
                <h2 class="card-title"><?= __('_CONTRATO_ACTIVO') ?></h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_INICIO') ?></label>
                    <p class="text-lg font-semibold"><?= h($activeContract->start_date ? $activeContract->start_date->format('d/m/Y') : '') ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_FIN') ?></label>
                    <p class="text-lg"><?= h($activeContract->end_date ? $activeContract->end_date->format('d/m/Y') : __('_INDEFINIDO')) ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-base-content/60"><?= __('_SALARIO') ?></label>
                    <p class="text-lg font-semibold text-success"><?= h($activeContract->salary ? number_format($activeContract->salary, 2) . '€' : '') ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-base-content/60"><?= __('_CATEGORIA_PROFESIONAL') ?></label>
                    <p class="text-lg"><?= h($activeContract->professional_category ?? '') ?></p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="text-sm font-medium text-base-content/60"><?= __('_TIPO') ?></label>
                    <p class="text-lg"><?= h($activeContract->contract_type ?? '') ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-base-content/60"><?= __('_JORNADA') ?></label>
                    <p class="text-lg"><?= h($activeContract->working_hours ? $activeContract->working_hours . 'h' : '') ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-warning mt-6">
        <div class="flex items-center gap-2">
            <?= $this->Icon->render('exclamation-triangle', 'solid', ['class' => 'w-5 h-5']) ?>
            <span><?= __('_SIN_CONTRATO_ACTIVO') ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Últimas Asistencias -->
    <div class="card bg-base-100 shadow-lg mt-6">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <?= $this->Icon->render('clock', 'solid', ['class' => 'w-6 h-6 text-info']) ?>
                    <h2 class="card-title"><?= __('_ULTIMAS_ASISTENCIAS') ?></h2>
                </div>
                <?= $this->Html->link(
                    $this->Icon->render('arrow-right', 'solid', ['class' => 'w-4 h-4 mr-1']) . __('_VER_TODAS'),
                    ['action' => 'attendances', $user->id],
                    ['class' => 'btn btn-outline btn-sm', 'escape' => false]
                ) ?>
            </div>
            
            <?php if (!empty($user->attendances)): ?>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th><?= __('_FECHA_HORA') ?></th>
                                <th><?= __('_TIPO') ?></th>
                                <th><?= __('_UBICACION') ?></th>
                                <th><?= __('_IP') ?></th>
                                <th class="text-center"><?= __('_ACCIONES') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($user->attendances, 0, 5) as $attendance): ?>
                            <tr>
                                <td>
                                    <div class="font-medium"><?= h($attendance->timestamp->format('d/m/Y')) ?></div>
                                    <div class="text-sm opacity-60"><?= h($attendance->timestamp->format('H:i:s')) ?></div>
                                </td>
                                <td>
                                    <?= $attendance->badge ?>
                                </td>
                                <td>
                                    <?php if ($attendance->latitude && $attendance->longitude): ?>
                                        <div class="flex items-center gap-1 text-success">
                                            <?= $this->Icon->render('map-pin', 'solid', ['class' => 'w-4 h-4']) ?>
                                            <span class="text-xs">GPS</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-base-content/40">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-sm font-mono"><?= h($attendance->ip_address) ?></span>
                                </td>
                                <td class="text-center">
                                    <?= $this->Html->link(
                                        $this->Icon->render('eye', 'solid', ['class' => 'w-4 h-4']),
                                        ['controller' => 'Attendances', 'action' => 'view', $attendance->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_VER_DETALLES')
                                        ]
                                    ) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (count($user->attendances) > 5): ?>
                    <div class="text-center mt-4">
                        <span class="text-sm text-base-content/60">
                            ... y <?= count($user->attendances) - 5 ?> asistencias más
                        </span>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-8">
                    <div class="flex flex-col items-center gap-2">
                        <?= $this->Icon->render('information-circle', 'solid', ['class' => 'w-12 h-12 text-info/50']) ?>
                        <p class="text-lg text-base-content/60"><?= __('_SIN_ASISTENCIAS_REGISTRADAS') ?></p>
                        <p class="text-sm text-base-content/40"><?= __('_USUARIO_NO_HA_FICHADO') ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex gap-4 mt-6">
        <?= $this->Html->link(
            $this->Icon->render('clock', 'solid', ['class' => 'w-4 h-4 mr-2']) . __('_VER_ASISTENCIAS'),
            ['action' => 'attendances', $user->id],
            ['class' => 'btn btn-secondary', 'escape' => false]
        ) ?>
        <?= $this->Html->link(
            $this->Icon->render('document-text', 'solid', ['class' => 'w-4 h-4 mr-2']) . __('_VER_CONTRATOS'),
            ['controller' => 'Contracts', 'action' => 'index', '?' => ['user_id' => $user->id]],
            ['class' => 'btn btn-accent', 'escape' => false]
        ) ?>
    </div>
</div>