<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\JornaticCore\Model\Entity\Subscription> $subscriptions
 * @var array $filters
 * @var array $stats
 * @var array $companies
 * @var array $plans
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-base-content"><?= __('_GESTION_SUSCRIPCIONES') ?></h1>
            <p class="text-base-content/60 mt-1"><?= __('_ADMINISTRAR_SUSCRIPCIONES_EMPRESAS') ?></p>
        </div>
        
        <div class="flex gap-2">
            <?= $this->Html->link(
                '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>' . __('_EXPORTAR_CSV'),
                ['action' => 'export'] + $filters,
                [
                    'class' => 'btn btn-outline btn-sm',
                    'escape' => false
                ]
            ) ?>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card bg-base-200">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="stat-value text-2xl text-primary"><?= number_format($stats['total']) ?></div>
                    <div class="stat-title ml-3"><?= __('_TOTAL_SUSCRIPCIONES') ?></div>
                </div>
            </div>
        </div>
        
        <div class="card bg-base-200">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="stat-value text-2xl text-success"><?= number_format($stats['active']) ?></div>
                    <div class="stat-title ml-3"><?= __('_ACTIVAS') ?></div>
                </div>
            </div>
        </div>
        
        <div class="card bg-base-200">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="stat-value text-2xl text-info"><?= number_format($stats['trial']) ?></div>
                    <div class="stat-title ml-3"><?= __('_EN_PRUEBA') ?></div>
                </div>
            </div>
        </div>
        
        <div class="card bg-base-200">
            <div class="card-body p-4">
                <div class="flex items-center">
                    <div class="stat-value text-2xl text-accent">€<?= number_format($stats['monthly_revenue'], 2) ?></div>
                    <div class="stat-title ml-3"><?= __('_INGRESOS_MENSUALES') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card bg-base-100 shadow-lg mb-6">
        <div class="card-body">
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'flex flex-wrap gap-4 items-end']) ?>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_EMPRESA') ?></span></label>
                <?= $this->Form->select('company_id', ['' => __('_TODAS_LAS_EMPRESAS')] + $companies, [
                    'value' => $filters['company_id'] ?? '',
                    'class' => 'select select-bordered select-sm w-60'
                ]) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_PLAN') ?></span></label>
                <?= $this->Form->select('plan_id', ['' => __('_TODOS_LOS_PLANES')] + $plans, [
                    'value' => $filters['plan_id'] ?? '',
                    'class' => 'select select-bordered select-sm w-48'
                ]) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_ESTADO') ?></span></label>
                <?= $this->Form->select('status', [
                    '' => __('_TODOS'),
                    'active' => __('_ACTIVA'),
                    'trial' => __('_PRUEBA'),
                    'canceled' => __('_CANCELADA'),
                    'expired' => __('_EXPIRADA')
                ], [
                    'value' => $filters['status'] ?? '',
                    'class' => 'select select-bordered select-sm'
                ]) ?>
            </div>
            
            <div class="form-control">
                <label class="label"><span class="label-text"><?= __('_PERIODO') ?></span></label>
                <?= $this->Form->select('period', [
                    '' => __('_TODOS'),
                    'monthly' => __('_MENSUAL'),
                    'annual' => __('_ANUAL')
                ], [
                    'value' => $filters['period'] ?? '',
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

    <!-- Tabla de Suscripciones -->
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th><?= $this->Paginator->sort('company_id', __('_EMPRESA')) ?></th>
                            <th><?= $this->Paginator->sort('plan_id', __('_PLAN')) ?></th>
                            <th><?= $this->Paginator->sort('period', __('_PERIODO')) ?></th>
                            <th><?= __('_PRECIO') ?></th>
                            <th><?= $this->Paginator->sort('status', __('_ESTADO')) ?></th>
                            <th><?= $this->Paginator->sort('start_date', __('_INICIO') ?></th>
                            <th><?= $this->Paginator->sort('end_date', __('_VENCIMIENTO')) ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscriptions as $subscription): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-neutral text-neutral-content rounded-full w-8">
                                            <span class="text-xs"><?= strtoupper(substr($subscription->company->name ?? '', 0, 2)) ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold"><?= h($subscription->company->name ?? '') ?></div>
                                        <div class="text-sm opacity-50"><?= h($subscription->company->email ?? '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-bold"><?= h($subscription->plan->name ?? '') ?></div>
                                <div class="text-sm opacity-50"><?= h($subscription->plan->description ?? '') ?></div>
                            </td>
                            <td>
                                <?php
                                $periodClass = $subscription->period === 'annual' ? 'badge-success' : 'badge-primary';
                                $periodText = $subscription->period === 'annual' ? __('_ANUAL') : __('_MENSUAL');
                                ?>
                                <span class="badge <?= $periodClass ?>"><?= $periodText ?></span>
                            </td>
                            <td>
                                <?php if (!empty($subscription->plan->prices)): ?>
                                    <?php foreach ($subscription->plan->prices as $price): ?>
                                        <?php if ($price->period === $subscription->period): ?>
                                            <span class="font-bold text-lg">€<?= number_format($price->amount, 2) ?></span>
                                            <div class="text-sm opacity-50"><?= $subscription->period === 'annual' ? __('_POR_ANO') : __('_POR_MES') ?></div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = match($subscription->status) {
                                    'active' => 'badge-success',
                                    'trial' => 'badge-info',
                                    'canceled' => 'badge-warning',
                                    'expired' => 'badge-error',
                                    default => 'badge-ghost'
                                };
                                $statusText = match($subscription->status) {
                                    'active' => __('_ACTIVA'),
                                    'trial' => __('_PRUEBA'),
                                    'canceled' => __('_CANCELADA'),
                                    'expired' => __('_EXPIRADA'),
                                    default => ucfirst($subscription->status)
                                };
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
                            <td><?= $subscription->start_date ? $subscription->start_date->format('d/m/Y') : '' ?></td>
                            <td>
                                <?php if ($subscription->end_date): ?>
                                    <?= $subscription->end_date->format('d/m/Y') ?>
                                    <?php
                                    $daysToExpire = $subscription->end_date->diffInDays(\Cake\I18n\Date::now(), false);
                                    if ($daysToExpire < 30 && $daysToExpire > 0): ?>
                                        <div class="text-xs text-warning"><?= __('_VENCE_EN_{0}_DIAS', [$daysToExpire]) ?></div>
                                    <?php elseif ($daysToExpire <= 0): ?>
                                        <div class="text-xs text-error"><?= __('_VENCIDA') ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-sm opacity-50"><?= __('_SIN_VENCIMIENTO') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <?= $this->Html->link(
                                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>',
                                        ['action' => 'view', $subscription->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_VER_DETALLES')
                                        ]
                                    ) ?>
                                    
                                    <?= $this->Html->link(
                                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>',
                                        ['action' => 'edit', $subscription->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_EDITAR')
                                        ]
                                    ) ?>
                                    
                                    <?php if ($subscription->status === 'active'): ?>
                                        <?= $this->Form->postLink(
                                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                            </svg>',
                                            ['action' => 'cancel', $subscription->id],
                                            [
                                                'class' => 'btn btn-error btn-xs',
                                                'escape' => false,
                                                'title' => __('_CANCELAR'),
                                                'confirm' => __('_CONFIRMAR_CANCELAR_SUSCRIPCION')
                                            ]
                                        ) ?>
                                    <?php elseif ($subscription->status === 'canceled'): ?>
                                        <?= $this->Form->postLink(
                                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>',
                                            ['action' => 'reactivate', $subscription->id],
                                            [
                                                'class' => 'btn btn-success btn-xs',
                                                'escape' => false,
                                                'title' => __('_REACTIVAR'),
                                                'confirm' => __('_CONFIRMAR_REACTIVAR_SUSCRIPCION')
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