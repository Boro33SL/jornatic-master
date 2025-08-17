<?php
/**
 * @var \App\View\AppView $this
 * @var \JornaticCore\Model\Entity\Subscription $subscription
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
            <p class="text-base-content/60 mt-1"><?= h($subscription->company->name ?? '') ?>
                - <?= h($subscription->plan->name ?? '') ?></p>
        </div>

        <div class="flex gap-2">
            <?= $this->Html->link(
                $this->Icon->render('pencil-square', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_EDITAR'),
                ['action' => 'edit', $subscription->id],
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
            <!-- Estado de Suscripción -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h2 class="card-title text-xl"><?= __('_ESTADO_SUSCRIPCION') ?></h2>
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <?php
                            $statusClass = match ($subscription->status) {
                                'active' => 'badge-success',
                                'trial' => 'badge-info',
                                'canceled' => 'badge-warning',
                                'expired' => 'badge-error',
                                default => 'badge-ghost'
                            };
                            $statusText = match ($subscription->status) {
                                'active' => __('_ACTIVA'),
                                'trial' => __('_PRUEBA'),
                                'canceled' => __('_CANCELADA'),
                                'expired' => __('_EXPIRADA'),
                                default => ucfirst($subscription->status)
                            };
                            ?>
                            <div class="badge <?= $statusClass ?> badge-lg"><?= $statusText ?></div>

                            <?php if ($subscription->end_date): ?>
                                <?php $daysToExpire =
                                    $subscription->end_date->diffInDays(\Cake\I18n\Date::now(), false); ?>
                                <?php if ($daysToExpire < 30 && $daysToExpire > 0): ?>
                                    <div class="text-warning text-sm mt-2"><?= __('_VENCE_EN_{0}_DIAS',
                                            [$daysToExpire]) ?></div>
                                <?php elseif ($daysToExpire <= 0): ?>
                                    <div class="text-error text-sm mt-2"><?= __('_SUSCRIPCION_VENCIDA') ?></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="text-right">
                            <?php if (!empty($subscription->plan->prices)): ?>
                                <?php foreach ($subscription->plan->prices as $price): ?>
                                    <?php if ($price->period === $subscription->period): ?>
                                        <div class="text-3xl font-bold text-primary">€<?= number_format($price->amount,
                                                2) ?></div>
                                        <div class="text-sm text-base-content/60"><?= $subscription->period === 'annual'
                                                ? __('_POR_ANO')
                                                : __('_POR_MES') ?></div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_INICIO') ?></label>
                                <p class="text-lg font-semibold"><?= $subscription->start_date
                                        ? $subscription->start_date->format('d/m/Y')
                                        : __('_NO_ESPECIFICADO') ?></p>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_VENCIMIENTO') ?></label>
                                <p class="text-lg font-semibold"><?= $subscription->end_date
                                        ? $subscription->end_date->format('d/m/Y')
                                        : __('_SIN_VENCIMIENTO') ?></p>
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_PERIODO') ?></label>
                                <p class="text-lg font-semibold"><?= $subscription->period === 'annual'
                                        ? __('_ANUAL')
                                        : __('_MENSUAL') ?></p>
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
                                    <p class="text-lg font-semibold"><?= number_format($subscription->plan->max_users ??
                                            0) ?></p>
                                </div>

                                <div>
                                    <label class="text-sm font-medium text-base-content/60"><?= __('_CARACTERISTICAS') ?></label>
                                    <p class="text-lg font-semibold"><?= count($subscription->plan->features ??
                                            []) ?> <?= __('_DISPONIBLES') ?></p>
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
                                        <?= $this->Icon->render('check', 'solid', ['class' => 'w-5 h-5 text-success']) ?>
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
                                <span class="text-xl"><?= strtoupper(substr($subscription->company->name ?? '', 0,
                                        2)) ?></span>
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
                            <p class="font-mono"><?= h($subscription->company->nif ?? '') ?></p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_TELEFONO') ?></label>
                            <p><?= h($subscription->company->phone ?? __('_NO_ESPECIFICADO')) ?></p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_ESTADO_EMPRESA') ?></label>
                            <div>
                                <?php if ($subscription->company->status === 'active'): ?>
                                    <span class="badge badge-success"><?= __('_' . strtoupper
                                        ($subscription->company->status)) ?></span>
                                <?php else: ?>
                                    <span class="badge badge-error"><?= $subscription->company->status ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="lg:col-span-1">

            <!-- Acciones -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h3 class="card-title"><?= __('_ACCIONES') ?></h3>
                    <div class="space-y-2">
                        <?php if ($subscription->status === 'active'): ?>
                            <?= $this->Form->postLink(
                                $this->Icon->render('x-circle', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_CANCELAR_SUSCRIPCION'),
                                ['action' => 'cancel', $subscription->id],
                                [
                                    'class' => 'btn btn-error btn-sm w-full justify-start',
                                    'escape' => false,
                                    'confirm' => __('_CONFIRMAR_CANCELAR_SUSCRIPCION')
                                ]
                            ) ?>
                        <?php elseif ($subscription->status === 'canceled'): ?>
                            <?= $this->Form->postLink(
                                $this->Icon->render('check-circle', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_REACTIVAR_SUSCRIPCION'),
                                ['action' => 'reactivate', $subscription->id],
                                [
                                    'class' => 'btn btn-success btn-sm w-full justify-start',
                                    'escape' => false,
                                    'confirm' => __('_CONFIRMAR_REACTIVAR_SUSCRIPCION')
                                ]
                            ) ?>
                        <?php endif; ?>

                        <?= $this->Html->link(
                            $this->Icon->render('users', 'solid', ['class' => 'w-5 h-5 mr-2 text-white']) . __('_VER_USUARIOS'),
                            [
                                'controller' => 'Users', 'action' => 'index',
                                '?' => ['company_id' => $subscription->company_id]
                            ],
                            [
                                'class' => 'btn btn-primary btn-sm w-full justify-start',
                                'escape' => false
                            ]
                        ) ?>

                        <?= $this->Html->link(
                            $this->Icon->render('clock', 'solid', ['class' => 'w-5 h-5 mr-2 text-white']) . __('_VER_ASISTENCIAS'),
                            [
                                'controller' => 'Attendances', 'action' => 'index',
                                '?' => ['company_id' => $subscription->company_id]
                            ],
                            [
                                'class' => 'btn btn-secondary btn-sm w-full justify-start',
                                'escape' => false
                            ]
                        ) ?>
                    </div>
                </div>
            </div>

            <!-- Información de Stripe -->
            <?php if ($subscription->stripe_subscription_id && !empty($stripeData)): ?>
                <div class="card bg-base-100 shadow-lg mb-6">
                    <div class="card-body">
                        <h3 class="card-title">
                            <?= $this->Icon->render('credit-card', 'solid', ['class' => 'w-5 h-5 mr-2']) ?>
                            <?= __('_INFORMACION_STRIPE') ?>
                            <div class="badge badge-sm badge-success"><?= __('_TIEMPO_REAL') ?></div>
                        </h3>
                        
                        <div class="space-y-3">
                            <!-- Estado en Stripe -->
                            <div>
                                <label class="text-sm font-medium text-base-content/60"><?= __('_ESTADO_STRIPE') ?></label>
                                <?php
                                $stripeStatus = $stripeData['subscription']->status ?? 'unknown';
                                $statusTranslation = match ($stripeStatus) {
                                    'active' => __('_ACTIVA_STRIPE'),
                                    'trialing' => __('_TRIAL_STRIPE'),
                                    'canceled' => __('_CANCELADA_STRIPE'),
                                    'incomplete' => __('_INCOMPLETA_STRIPE'),
                                    'past_due' => __('_PENDIENTE'),
                                    default => ucfirst($stripeStatus)
                                };
                                $statusClass = match ($stripeStatus) {
                                    'active' => 'badge-success',
                                    'trialing' => 'badge-info',
                                    'canceled' => 'badge-error',
                                    'incomplete' => 'badge-warning',
                                    'past_due' => 'badge-warning',
                                    default => 'badge-ghost'
                                };
                                ?>
                                <div class="badge <?= $statusClass ?> badge-sm"><?= $statusTranslation ?></div>
                            </div>

                            <!-- Próximo cobro -->
                            <?php if (!empty($stripeData['subscription']->current_period_end)): ?>
                                <div>
                                    <label class="text-sm font-medium text-base-content/60"><?= __('_PROXIMO_COBRO') ?></label>
                                    <p class="font-semibold text-sm"><?= date('d/m/Y', $stripeData['subscription']->current_period_end) ?></p>
                                </div>
                            <?php endif; ?>
                            <!-- Cancelación programada -->
                            <?php if (!empty($stripeData['subscription']->cancel_at_period_end)): ?>
                                <div>
                                    <label class="text-sm font-medium text-base-content/60"><?= __('_CANCELACION_PROGRAMADA') ?></label>
                                    <p class="text-sm"><?= $stripeData['subscription']->cancel_at_period_end ? __('_SI') : __('_NO') ?></p>
                                </div>
                            <?php endif; ?>

                            <!-- Información del Customer -->
                            <?php if (!empty($stripeData['customer'])): ?>
                                <div>
                                    <label class="text-sm font-medium text-base-content/60"><?= __('_SALDO_CUSTOMER') ?></label>
                                    <p class="text-sm"><?= number_format($stripeData['customer']->balance / 100, 2) ?> <?= strtoupper($stripeData['customer']->currency ?? 'EUR') ?></p>
                                </div>
                            <?php endif; ?>

                            <!-- Facturas recientes -->
                            <?php if (!empty($stripeData['recent_invoices'])): ?>
                                <div>
                                    <label class="text-sm font-medium text-base-content/60"><?= __('_FACTURAS_RECIENTES') ?></label>
                                    <div class="space-y-2 mt-2">
                                        <?php foreach (array_slice($stripeData['recent_invoices'], 0, 3) as $invoice): ?>
                                            <div class="flex justify-between items-center p-2 bg-base-200 rounded">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium">
                                                        <?= number_format($invoice->amount_paid / 100, 2) ?> <?= strtoupper($invoice->currency) ?>
                                                    </p>
                                                    <p class="text-xs text-base-content/60">
                                                        <?= date('d/m/Y', $invoice->created) ?>
                                                    </p>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <div class="badge badge-xs <?= $invoice->status === 'paid' ? 'badge-success' : 'badge-warning' ?>">
                                                        <?= $invoice->status === 'paid' ? __('_PAGADA') : __('_PENDIENTE') ?>
                                                    </div>
                                                    <?php if (!empty($invoice->hosted_invoice_url)): ?>
                                                        <a href="<?= h($invoice->hosted_invoice_url) ?>" 
                                                           target="_blank" 
                                                           class="btn btn-xs btn-outline">
                                                            <?= $this->Icon->render('arrow-top-right-on-square', 'solid', ['class' => 'w-3 h-3']) ?>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php elseif ($subscription->stripe_subscription_id): ?>
                <!-- Mostrar mensaje cuando hay ID de Stripe pero no se pudieron obtener los datos -->
                <div class="card bg-base-100 shadow-lg mb-6">
                    <div class="card-body">
                        <h3 class="card-title">
                            <?= $this->Icon->render('credit-card', 'solid', ['class' => 'w-5 h-5 mr-2']) ?>
                            <?= __('_INFORMACION_STRIPE') ?>
                            <div class="badge badge-sm badge-warning"><?= __('_ERROR_STRIPE') ?></div>
                        </h3>
                        
                        <div class="text-center py-4">
                            <p class="text-base-content/60"><?= __('_STRIPE_NO_DISPONIBLE') ?></p>
                            <p class="text-xs text-base-content/40 mt-2">ID: <?= h($subscription->stripe_subscription_id) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

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