<?php
/**
 * Master Access Logs Index Template - DaisyUI Design
 */
$this->assign('title', __('_LOGS_DE_AUDITORIA'));
?>

<!-- Header con estadísticas -->
<div class="mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-primary"><?= __('_LOGS_DE_AUDITORIA') ?></h1>
            <p class="text-base-content/70 mt-1"><?= __('_REGISTRO_ACTIVIDADES_MASTERS') ?></p>
        </div>
        
        <!-- Botón de exportar -->
        <div class="mt-4 lg:mt-0">
            <?= $this->Html->link(
                '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>' . __('_EXPORTAR_CSV'),
                ['action' => 'export'] + $this->request->getQueryParams(),
                [
                    'class' => 'btn btn-accent btn-sm',
                    'escape' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- KPIs del día -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat stat-master bg-base-100 shadow-lg">
            <div class="stat-figure text-primary">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div class="stat-title"><?= __('_TOTAL_HOY') ?></div>
            <div class="stat-value text-primary"><?= number_format($todayStats['total']) ?></div>
            <div class="stat-desc"><?= __('_ACCIONES_REALIZADAS') ?></div>
        </div>

        <div class="stat stat-master bg-base-100 shadow-lg">
            <div class="stat-figure text-success">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="stat-title"><?= __('_EXITOSAS') ?></div>
            <div class="stat-value text-success"><?= number_format($todayStats['successful']) ?></div>
            <div class="stat-desc"><?= __('_OPERACIONES_CORRECTAS') ?></div>
        </div>

        <div class="stat stat-master bg-base-100 shadow-lg">
            <div class="stat-figure text-error">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="stat-title"><?= __('_FALLIDAS') ?></div>
            <div class="stat-value text-error"><?= number_format($todayStats['failed']) ?></div>
            <div class="stat-desc"><?= __('_OPERACIONES_INCORRECTAS') ?></div>
        </div>

        <div class="stat stat-master bg-base-100 shadow-lg">
            <div class="stat-figure text-info">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div class="stat-title"><?= __('_IPS_UNICAS') ?></div>
            <div class="stat-value text-info"><?= number_format($todayStats['unique_ips']) ?></div>
            <div class="stat-desc"><?= __('_DIRECCIONES_DIFERENTES') ?></div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card bg-base-100 shadow-xl mb-6">
    <div class="card-body">
        <h2 class="card-title text-accent mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
            </svg>
            <?= __('_FILTROS_DE_BUSQUEDA') ?>
        </h2>

        <?= $this->Form->create(null, [
            'type' => 'get',
            'class' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4'
        ]) ?>

            <!-- Filtro por Master -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium"><?= __('_USUARIO_MASTER') ?></span>
                </label>
                <?= $this->Form->control('master_id', [
                    'type' => 'select',
                    'options' => ['' => __('_TODOS_LOS_USUARIOS')] + $masters,
                    'label' => false,
                    'class' => 'select select-bordered w-full focus:select-primary',
                    'value' => $filters['master_id'] ?? ''
                ]) ?>
            </div>

            <!-- Filtro por Acción -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium"><?= __('_ACCION') ?></span>
                </label>
                <?php 
                $actionOptions = ['' => __('_TODAS_LAS_ACCIONES')];
                foreach ($actions as $action) {
                    $actionOptions[$action->action] = $action->action;
                }
                ?>
                <?= $this->Form->control('action', [
                    'type' => 'select',
                    'options' => $actionOptions,
                    'label' => false,
                    'class' => 'select select-bordered w-full focus:select-primary',
                    'value' => $filters['action'] ?? ''
                ]) ?>
            </div>

            <!-- Filtro por Éxito -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium"><?= __('_RESULTADO') ?></span>
                </label>
                <?= $this->Form->control('success', [
                    'type' => 'select',
                    'options' => [
                        '' => __('_TODOS_LOS_RESULTADOS'),
                        '1' => __('_EXITOSO'),
                        '0' => __('_FALLIDO')
                    ],
                    'label' => false,
                    'class' => 'select select-bordered w-full focus:select-primary',
                    'value' => $filters['success'] ?? ''
                ]) ?>
            </div>

            <!-- Filtro por IP -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium"><?= __('_DIRECCION_IP') ?></span>
                </label>
                <?= $this->Form->control('ip_address', [
                    'label' => false,
                    'class' => 'input input-bordered w-full focus:input-primary',
                    'placeholder' => '192.168.1.100',
                    'value' => $filters['ip_address'] ?? ''
                ]) ?>
            </div>

            <!-- Filtro por Fecha Desde -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium"><?= __('_FECHA_DESDE') ?></span>
                </label>
                <?= $this->Form->control('date_from', [
                    'type' => 'date',
                    'label' => false,
                    'class' => 'input input-bordered w-full focus:input-primary',
                    'value' => $filters['date_from'] ?? ''
                ]) ?>
            </div>

            <!-- Filtro por Fecha Hasta -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium"><?= __('_FECHA_HASTA') ?></span>
                </label>
                <?= $this->Form->control('date_to', [
                    'type' => 'date',
                    'label' => false,
                    'class' => 'input input-bordered w-full focus:input-primary',
                    'value' => $filters['date_to'] ?? ''
                ]) ?>
            </div>

            <!-- Botones -->
            <div class="flex flex-col justify-end">
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-1">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <?= __('_FILTRAR') ?>
                    </button>
                    <?= $this->Html->link(
                        __('_LIMPIAR'),
                        ['action' => 'index'],
                        ['class' => 'btn btn-ghost btn-sm flex-1']
                    ) ?>
                </div>
            </div>

        <?= $this->Form->end() ?>
    </div>
</div>

<!-- Tabla de logs -->
<div class="card bg-base-100 shadow-xl">
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead class="bg-base-200">
                    <tr>
                        <th><?= __('_FECHA') ?></th>
                        <th><?= __('_USUARIO') ?></th>
                        <th><?= __('_ACCION') ?></th>
                        <th><?= __('_RECURSO') ?></th>
                        <th><?= __('_IP') ?></th>
                        <th><?= __('_RESULTADO') ?></th>
                        <th><?= __('_ACCIONES') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($masterAccessLogs->count() > 0): ?>
                        <?php foreach ($masterAccessLogs as $log): ?>
                            <tr class="hover">
                                <td>
                                    <div class="flex flex-col">
                                        <span class="font-medium"><?= $log->created->format('d/m/Y') ?></span>
                                        <span class="text-sm text-base-content/60"><?= $log->created->format('H:i:s') ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar placeholder">
                                            <div class="bg-primary text-primary-content rounded-full w-8 h-8">
                                                <span class="text-xs"><?= strtoupper(substr($log->master->name ?? 'U', 0, 1)) ?></span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold"><?= h($log->master->name ?? __('_USUARIO_ANONIMO')) ?></div>
                                            <div class="text-sm text-base-content/60"><?= h($log->master->email ?? '') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="badge badge-outline"><?= h($log->action) ?></div>
                                </td>
                                <td>
                                    <?php if ($log->resource): ?>
                                        <div class="flex flex-col">
                                            <span class="font-medium"><?= h($log->resource) ?></span>
                                            <?php if ($log->resource_id): ?>
                                                <span class="text-sm text-base-content/60">ID: <?= h($log->resource_id) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-base-content/40">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <code class="text-sm"><?= h($log->ip_address) ?></code>
                                </td>
                                <td>
                                    <?php if ($log->success): ?>
                                        <div class="badge badge-success gap-2">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <?= __('_EXITOSO') ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="badge badge-error gap-2">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <?= __('_FALLIDO') ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $this->Html->link(
                                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>',
                                        ['action' => 'view', $log->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'title' => __('_VER_DETALLES'),
                                            'escape' => false
                                        ]
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-8">
                                <div class="flex flex-col items-center justify-center text-base-content/60">
                                    <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium"><?= __('_NO_HAY_LOGS_DISPONIBLES') ?></p>
                                    <p class="text-sm"><?= __('_NO_SE_ENCONTRARON_REGISTROS_CON_LOS_FILTROS_APLICADOS') ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Paginación -->
<?php if ($masterAccessLogs->count() > 0): ?>
    <div class="flex justify-center mt-6">
        <div class="join">
            <?= $this->Paginator->first('<<', ['class' => 'join-item btn btn-sm']) ?>
            <?= $this->Paginator->prev('<', ['class' => 'join-item btn btn-sm']) ?>
            <?= $this->Paginator->numbers(['class' => 'join-item btn btn-sm']) ?>
            <?= $this->Paginator->next('>', ['class' => 'join-item btn btn-sm']) ?>
            <?= $this->Paginator->last('>>', ['class' => 'join-item btn btn-sm']) ?>
        </div>
    </div>

    <div class="text-center text-sm text-base-content/60 mt-4">
        <?= $this->Paginator->counter('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de {{count}} total') ?>
    </div>
<?php endif; ?>