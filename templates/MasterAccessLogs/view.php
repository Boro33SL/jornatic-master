<?php
/**
 * Master Access Log View Template - DaisyUI Design
 */
$this->assign('title', __('_DETALLE_LOG_AUDITORIA'));
?>

<!-- Header -->
<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-primary"><?= __('_DETALLE_LOG_AUDITORIA') ?></h1>
            <p class="text-base-content/70 mt-1"><?= __('_INFORMACION_COMPLETA_DEL_REGISTRO') ?></p>
        </div>
        <div class="mt-4 sm:mt-0">
            <?= $this->Html->link(
                '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>' . __('_VOLVER_A_LOGS'),
                ['action' => 'index'],
                [
                    'class' => 'btn btn-ghost btn-sm',
                    'escape' => false
                ]
            ) ?>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Información Principal -->
    <div class="lg:col-span-2">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-primary mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <?= __('_INFORMACION_PRINCIPAL') ?>
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fecha y Hora -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_FECHA_Y_HORA') ?></span>
                        </label>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <div class="font-bold"><?= $masterAccessLog->created->format('d/m/Y H:i:s') ?></div>
                                <div class="text-sm text-base-content/60"><?= $masterAccessLog->created->timeAgoInWords() ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Usuario Master -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_USUARIO_MASTER') ?></span>
                        </label>
                        <?php if ($masterAccessLog->master): ?>
                            <div class="flex items-center gap-3">
                                <div class="avatar placeholder">
                                    <div class="bg-primary text-primary-content rounded-full w-10 h-10">
                                        <span class="text-sm font-bold"><?= strtoupper(substr($masterAccessLog->master->name, 0, 1)) ?></span>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold"><?= h($masterAccessLog->master->name) ?></div>
                                    <div class="text-sm text-base-content/60"><?= h($masterAccessLog->master->email) ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-base-content/60"><?= __('_USUARIO_ANONIMO') ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Acción -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_ACCION_REALIZADA') ?></span>
                        </label>
                        <div class="badge badge-primary badge-lg"><?= h($masterAccessLog->action) ?></div>
                    </div>

                    <!-- Estado -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_RESULTADO') ?></span>
                        </label>
                        <?php if ($masterAccessLog->success): ?>
                            <div class="badge badge-success badge-lg gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?= __('_OPERACION_EXITOSA') ?>
                            </div>
                        <?php else: ?>
                            <div class="badge badge-error badge-lg gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <?= __('_OPERACION_FALLIDA') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Recurso -->
                    <?php if ($masterAccessLog->resource): ?>
                        <div class="form-control md:col-span-2">
                            <label class="label">
                                <span class="label-text font-medium"><?= __('_RECURSO_AFECTADO') ?></span>
                            </label>
                            <div class="flex items-center gap-3">
                                <div class="badge badge-outline badge-lg"><?= h($masterAccessLog->resource) ?></div>
                                <?php if ($masterAccessLog->resource_id): ?>
                                    <div class="text-sm text-base-content/60">
                                        <?= __('_ID_RECURSO') ?>: <code><?= h($masterAccessLog->resource_id) ?></code>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Detalles -->
                <?php if ($masterAccessLog->details): ?>
                    <div class="divider"></div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_DETALLES_ADICIONALES') ?></span>
                        </label>
                        <div class="mockup-code">
                            <pre><code><?= h($masterAccessLog->details) ?></code></pre>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Información Técnica -->
    <div class="lg:col-span-1">
        <div class="card bg-base-100 shadow-xl mb-6">
            <div class="card-body">
                <h2 class="card-title text-accent mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                    <?= __('_INFORMACION_TECNICA') ?>
                </h2>

                <!-- Dirección IP -->
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-medium"><?= __('_DIRECCION_IP') ?></span>
                    </label>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <code class="bg-base-200 px-2 py-1 rounded"><?= h($masterAccessLog->ip_address) ?></code>
                    </div>
                </div>

                <!-- User Agent -->
                <?php if ($masterAccessLog->user_agent): ?>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_NAVEGADOR_DISPOSITIVO') ?></span>
                        </label>
                        <div class="text-sm bg-base-200 p-3 rounded break-all">
                            <?= h($masterAccessLog->user_agent) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Logs Relacionados -->
        <?php if (!empty($relatedLogs)): ?>
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title text-warning mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        <?= __('_LOGS_RELACIONADOS') ?>
                    </h2>
                    <p class="text-sm text-base-content/60 mb-4">
                        <?= __('_OTRAS_ACTIVIDADES_MISMO_USUARIO_MISMO_DIA') ?>
                    </p>

                    <div class="space-y-3">
                        <?php foreach ($relatedLogs as $relatedLog): ?>
                            <div class="border border-base-300 rounded-lg p-3 hover:bg-base-50 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="badge badge-outline badge-sm"><?= h($relatedLog->action) ?></div>
                                    <div class="text-xs text-base-content/60"><?= $relatedLog->created->format('H:i:s') ?></div>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <?php if ($relatedLog->success): ?>
                                        <div class="badge badge-success badge-xs"><?= __('_EXITOSO') ?></div>
                                    <?php else: ?>
                                        <div class="badge badge-error badge-xs"><?= __('_FALLIDO') ?></div>
                                    <?php endif; ?>
                                    
                                    <?= $this->Html->link(
                                        __('_VER'),
                                        ['action' => 'view', $relatedLog->id],
                                        ['class' => 'btn btn-ghost btn-xs']
                                    ) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>