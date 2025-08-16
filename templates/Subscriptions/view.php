<?php
/**
 * @var \App\View\AppView $this
 * @var \JornaticCore\Model\Entity\Subscription $subscription
 * @var array $subscriptionStats
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><?= $this->Html->link(__('_SUSCRIPCIONES'), ['action' => 'index']) ?></li>
                    <li><?= __('_SUSCRIPCION') ?> #<?= $subscription->id ?></li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold text-base-content"><?= __('_DETALLE_SUSCRIPCION') ?></h1>
            <p class="text-base-content/60 mt-1"><?= h($subscription->company->name ?? '') ?> - <?= h($subscription->plan->name ?? '') ?></p>
        </div>
        
        <div class="flex gap-2">
            <?= $this->Html->link(
                '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>' . __('_EDITAR'),
                ['action' => 'edit', $subscription->id],
                [
                    'class' => 'btn btn-primary btn-sm',
                    'escape' => false
                ]
            ) ?>
            
            <?= $this->Html->link(
                '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>' . __('_VOLVER_AL_LISTADO'),
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
            <!-- Estado de Suscripción -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h2 class="card-title text-xl"><?= __('_ESTADO_SUSCRIPCION') ?></h2>
                    <div class="flex items-center justify-between mb-6">
                        <div>
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
                            <div class="badge <?= $statusClass ?> badge-lg"><?= $statusText ?></div>
                            
                            <?php if ($subscription->end_date): ?>
                                <?php $daysToExpire = $subscription->end_date->diffInDays(\Cake\I18n\Date::now(), false); ?>
                                <?php if ($daysToExpire < 30 && $daysToExpire > 0): ?>
                                    <div class="text-warning text-sm mt-2"><?= __('_VENCE_EN_{0}_DIAS', [$daysToExpire]) ?></div>
                                <?php elseif ($daysToExpire <= 0): ?>
                                    <div class="text-error text-sm mt-2"><?= __('_SUSCRIPCION_VENCIDA') ?></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-right">
                            <?php if (!empty($subscription->plan->prices)): ?>
                                <?php foreach ($subscription->plan->prices as $price): ?>
                                    <?php if ($price->period === $subscription->period): ?>
                                        <div class="text-3xl font-bold text-primary">€<?= number_format($price->amount, 2) ?></div>
                                        <div class="text-sm text-base-content/60"><?= $subscription->period === 'annual' ? __('_POR_ANO') : __('_POR_MES') ?></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_INICIO') ?></label>
                                <p class="text-lg font-semibold"><?= $subscription->start_date ? $subscription->start_date->format('d/m/Y') : __('_NO_ESPECIFICADO') ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_VENCIMIENTO') ?></label>
                                <p class="text-lg font-semibold"><?= $subscription->end_date ? $subscription->end_date->format('d/m/Y') : __('_SIN_VENCIMIENTO') ?></p>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_PERIODO') ?></label>
                                <p class="text-lg font-semibold"><?= $subscription->period === 'annual' ? __('_ANUAL') : __('_MENSUAL') ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_CREACION') ?></label>
                                <p class="text-lg"><?= $subscription->created->format('d/m/Y H:i') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del Plan -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h2 class="card-title text-xl"><?= __('_PLAN_CONTRATADO') ?></h2>
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-primary"><?= h($subscription->plan->name ?? '') ?></h3>
                            <p class="text-base-content/60 mb-4"><?= h($subscription->plan->description ?? '') ?></p>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-medium text-base-content/60"><?= __('_LIMITE_USUARIOS') ?></label>
                                    <p class="text-lg font-semibold"><?= number_format($subscription->plan->max_users ?? 0) ?></p>
                                </div>
                                
                                <div>
                                    <label class="text-sm font-medium text-base-content/60"><?= __('_CARACTERISTICAS') ?></label>
                                    <p class="text-lg font-semibold"><?= count($subscription->plan->features ?? []) ?> <?= __('_DISPONIBLES') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Características del Plan -->
                    <?php if (!empty($subscription->plan->features)): ?>
                    <div class="mt-6">
                        <h4 class="text-lg font-semibold mb-3"><?= __('_CARACTERISTICAS_INCLUIDAS') ?></h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <?php foreach ($subscription->plan->features as $feature): ?>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span><?= h($feature->name) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información de la Empresa -->
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-xl"><?= __('_EMPRESA_SUSCRITA') ?></h2>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="avatar placeholder">
                            <div class="bg-neutral text-neutral-content rounded-full w-16">
                                <span class="text-xl"><?= strtoupper(substr($subscription->company->name ?? '', 0, 2)) ?></span>
                            </div>
                        </div>
                        
                        <div class="flex-1">
                            <h3 class="text-xl font-bold"><?= h($subscription->company->name ?? '') ?></h3>
                            <p class="text-base-content/60"><?= h($subscription->company->email ?? '') ?></p>
                        </div>
                        
                        <div>
                            <?= $this->Html->link(
                                __('_VER_EMPRESA'),
                                ['controller' => 'Companies', 'action' => 'view', $subscription->company_id],
                                ['class' => 'btn btn-outline btn-sm']
                            ) ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_CIF') ?></label>
                            <p class="font-mono"><?= h($subscription->company->cif ?? '') ?></p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_TELEFONO') ?></label>
                            <p><?= h($subscription->company->phone ?? __('_NO_ESPECIFICADO')) ?></p>
                        </div>
                        
                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_ESTADO_EMPRESA') ?></label>
                            <div>
                                <?php if ($subscription->company->is_active ?? false): ?>
                                    <span class="badge badge-success"><?= __('_ACTIVA') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-error"><?= __('_INACTIVA') ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="lg:col-span-1">
            <!-- Estadísticas de Uso -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h3 class="card-title"><?= __('_ESTADISTICAS_USO') ?></h3>
                    <div class="space-y-4">
                        <div class="stat bg-base-200 rounded-box p-4">
                            <div class="stat-title"><?= __('_USUARIOS_ACTIVOS') ?></div>
                            <div class="stat-value text-primary"><?= number_format($subscriptionStats['users_count'] ?? 0) ?></div>
                            <div class="stat-desc"><?= __('_DE') ?> <?= number_format($subscription->plan->max_users ?? 0) ?> <?= __('_PERMITIDOS') ?></div>
                        </div>
                        
                        <div class="stat bg-base-200 rounded-box p-4">
                            <div class="stat-title"><?= __('_FICHAJES_ESTE_MES') ?></div>
                            <div class="stat-value text-secondary"><?= number_format($subscriptionStats['attendances_this_month'] ?? 0) ?></div>
                        </div>
                        
                        <div class="stat bg-base-200 rounded-box p-4">
                            <div class="stat-title"><?= __('_DEPARTAMENTOS') ?></div>
                            <div class="stat-value text-accent"><?= number_format($subscriptionStats['departments_count'] ?? 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h3 class="card-title"><?= __('_ACCIONES') ?></h3>
                    <div class="space-y-2">
                        <?php if ($subscription->status === 'active'): ?>
                            <?= $this->Form->postLink(
                                '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                </svg>' . __('_CANCELAR_SUSCRIPCION'),
                                ['action' => 'cancel', $subscription->id],
                                [
                                    'class' => 'btn btn-error btn-sm w-full justify-start',
                                    'escape' => false,
                                    'confirm' => __('_CONFIRMAR_CANCELAR_SUSCRIPCION')
                                ]
                            ) ?>
                        <?php elseif ($subscription->status === 'canceled'): ?>
                            <?= $this->Form->postLink(
                                '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>' . __('_REACTIVAR_SUSCRIPCION'),
                                ['action' => 'reactivate', $subscription->id],
                                [
                                    'class' => 'btn btn-success btn-sm w-full justify-start',
                                    'escape' => false,
                                    'confirm' => __('_CONFIRMAR_REACTIVAR_SUSCRIPCION')
                                ]
                            ) ?>
                        <?php endif; ?>
                        
                        <?= $this->Html->link(
                            '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>' . __('_VER_USUARIOS'),
                            ['controller' => 'Users', 'action' => 'index', '?' => ['company_id' => $subscription->company_id]],
                            [
                                'class' => 'btn btn-primary btn-sm w-full justify-start',
                                'escape' => false
                            ]
                        ) ?>
                        
                        <?= $this->Html->link(
                            '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>' . __('_VER_ASISTENCIAS'),
                            ['controller' => 'Attendances', 'action' => 'index', '?' => ['company_id' => $subscription->company_id]],
                            [
                                'class' => 'btn btn-secondary btn-sm w-full justify-start',
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
                            <label class="text-sm font-medium text-base-content/60"><?= __('_ID_SUSCRIPCION') ?></label>
                            <p class="font-mono text-sm"><?= $subscription->id ?></p>
                        </div>
                        
                        <?php if ($subscription->stripe_subscription_id): ?>
                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_ID_STRIPE') ?></label>
                            <p class="font-mono text-xs break-all"><?= h($subscription->stripe_subscription_id) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_ULTIMA_MODIFICACION') ?></label>
                            <p class="text-sm"><?= $subscription->modified->format('d/m/Y H:i') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>