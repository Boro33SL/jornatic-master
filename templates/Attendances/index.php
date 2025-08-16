<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\JornaticCore\Model\Entity\Attendance> $attendances
 * @var array $filters
 * @var array $stats
 * @var array $companies
 * @var array $users
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-base-content"><?= __('_GESTION_ASISTENCIAS') ?></h1>
            <p class="text-base-content/60 mt-1"><?= __('_CONTROL_ASISTENCIAS_EMPLEADOS') ?></p>
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
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats stats-horizontal shadow mb-6 w-full">
        <div class="stat">
            <div class="stat-figure text-primary">
                <?= $this->Icon->render('clipboard-document-list', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TOTAL_ASISTENCIAS') ?></div>
            <div class="stat-value text-primary"><?= number_format($stats['total']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-secondary">
                <?= $this->Icon->render('building-office', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TOTAL_EMPRESAS') ?></div>
            <div class="stat-value text-secondary"><?= number_format($stats['total_companies']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-accent">
                <?= $this->Icon->render('chart-bar', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_MEDIA_POR_EMPRESA') ?></div>
            <div class="stat-value text-accent"><?= number_format($stats['avg_per_company'], 1) ?></div>
        </div>

        <div class="stat">
            <div class="stat-figure text-info">
                <?= $this->Icon->render('calendar', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_HOY') ?></div>
            <div class="stat-value text-info"><?= number_format($stats['today_total']) ?></div>
            <div class="stat-desc"><?= $stats['today_ins'] ?> <?= __('_ENTRADAS') ?> / <?= $stats['today_outs'] ?> <?= __('_SALIDAS') ?></div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card bg-base-100 shadow-lg mb-6">
        <div class="card-body">
            <h3 class="card-title mb-4"><?= __('_FILTROS_DE_BUSQUEDA') ?></h3>
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'flex flex-wrap gap-4 items-end', 'id' => 'filter-form']) ?>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_EMPRESA') ?></span></label>
                <?= $this->Form->select('company_id', 
                    ['' => __('_TODAS_LAS_EMPRESAS')] + $companies, 
                    [
                        'value' => $filters['company_id'] ?? '',
                        'class' => 'select select-bordered select-sm w-60',
                        'id' => 'company-select'
                    ]
                ) ?>
            </div>
            
            <?php if (!empty($users)): ?>
            <div class="form-control" id="user-filter">
                <label class="label"><span class="label-text"><?= __('_EMPLEADO') ?></span></label>
                <?= $this->Form->select('user_id', 
                    ['' => __('_TODOS_LOS_USUARIOS')] + $users, 
                    [
                        'value' => $filters['user_id'] ?? '',
                        'class' => 'select select-bordered select-sm w-60'
                    ]
                ) ?>
            </div>
            <?php endif; ?>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_TIPO') ?></span></label>
                <?= $this->Form->select('type', [
                    '' => __('_TODOS'),
                    'in' => __('_ENTRADA'),
                    'out' => __('_SALIDA'),
                    'break_in' => __('_INICIO_DESCANSO'),
                    'break_out' => __('_FIN_DESCANSO')
                ], [
                    'value' => $filters['type'] ?? '',
                    'class' => 'select select-bordered select-sm'
                ]) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_FECHA_DESDE') ?></span></label>
                <?= $this->Form->date('date_from', [
                    'value' => $filters['date_from'] ?? '',
                    'class' => 'input input-bordered input-sm'
                ]) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_FECHA_HASTA') ?></span></label>
                <?= $this->Form->date('date_to', [
                    'value' => $filters['date_to'] ?? '',
                    'class' => 'input input-bordered input-sm'
                ]) ?>
            </div>
            
            <div class="flex gap-2">
                <?= $this->Form->button(__('_FILTRAR'), ['class' => 'btn btn-primary btn-sm']) ?>
                <?= $this->Html->link(__('_LIMPIAR'), ['action' => 'index'], ['class' => 'btn btn-ghost btn-sm']) ?>
            </div>
            
            <?= $this->Form->end() ?>
        </div>
    </div>

    <!-- Tabla de Asistencias -->
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('user_id', __('_EMPLEADO')) ?></th>
                            <th><?= __('_EMPRESA') ?></th>
                            <th><?= $this->Paginator->sort('timestamp', __('_FECHA_HORA')) ?></th>
                            <th><?= $this->Paginator->sort('type', __('_TIPO')) ?></th>
                            <th><?= __('_UBICACION') ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendances as $attendance): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-neutral text-neutral-content rounded-full w-8">
                                            <span class="text-xs"><?= strtoupper(substr($attendance->user->name ?? '', 0, 2)) ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold"><?= h($attendance->user->name ?? '') ?> <?= h($attendance->user->lastname ?? '') ?></div>
                                        <div class="text-sm opacity-50"><?= h($attendance->user->document ?? '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= h($attendance->user->company->name ?? '') ?></td>
                            <td><?= $attendance->timestamp ? $attendance->timestamp->format('d/m/Y H:i') : '' ?></td>
                            <td>
                                <?php
                                $typeClass = match($attendance->type) {
                                    'in' => 'badge-success',
                                    'out' => 'badge-error',
                                    'break_in' => 'badge-warning',
                                    'break_out' => 'badge-info',
                                    default => 'badge-ghost'
                                };
                                $typeText = match($attendance->type) {
                                    'in' => __('_ENTRADA'),
                                    'out' => __('_SALIDA'),
                                    'break_in' => __('_INICIO_DESCANSO'),
                                    'break_out' => __('_FIN_DESCANSO'),
                                    default => ucfirst($attendance->type)
                                };
                                ?>
                                <span class="badge <?= $typeClass ?>"><?= $typeText ?></span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <?php if ($attendance->latitude && $attendance->longitude): ?>
                                        <?= $this->Icon->render('map-pin', 'solid', ['class' => 'w-4 h-4 text-success']) ?>
                                        <span class="text-xs">GPS</span>
                                    <?php elseif ($attendance->ip_address): ?>
                                        <?= $this->Icon->render('wifi', 'solid', ['class' => 'w-4 h-4 text-info']) ?>
                                        <span class="text-xs">IP</span>
                                    <?php else: ?>
                                        <span class="text-xs text-base-content/50"><?= __('_NO_DISPONIBLE') ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <?= $this->Html->link(
                                        $this->Icon->render('eye', 'solid', ['class' => 'w-4 h-4']),
                                        ['action' => 'view', $attendance->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_VER_DETALLES')
                                        ]
                                    ) ?>
                                    
                                    <?= $this->Html->link(
                                        $this->Icon->render('pencil-square', 'solid', ['class' => 'w-4 h-4']),
                                        ['action' => 'edit', $attendance->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_EDITAR')
                                        ]
                                    ) ?>
                                    
                                    <?= $this->Form->postLink(
                                        $this->Icon->render('trash', 'solid', ['class' => 'w-4 h-4']),
                                        ['action' => 'delete', $attendance->id],
                                        [
                                            'class' => 'btn btn-error btn-xs',
                                            'escape' => false,
                                            'title' => __('_ELIMINAR'),
                                            'confirm' => __('_CONFIRMAR_ELIMINAR_ASISTENCIA')
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companySelect = document.getElementById('company-select');
    const filterForm = document.getElementById('filter-form');
    
    if (companySelect) {
        companySelect.addEventListener('change', function() {
            // Cuando se selecciona una empresa, enviar el formulario para recargar con usuarios
            if (this.value) {
                filterForm.submit();
            }
        });
    }
});
</script>