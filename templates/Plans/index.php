<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\JornaticCore\Model\Entity\Plan> $plans
 * @var array $filters
 * @var array $stats
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-base-content"><?= __('_GESTION_PLANES') ?></h1>
            <p class="text-base-content/60 mt-1"><?= __('_ADMINISTRAR_PLANES_SUSCRIPCION') ?></p>
        </div>
        <div class="flex gap-2">
            <?= $this->Html->link(
                $this->Icon->render('plus', 'solid', ['class' => 'w-5 h-5 mr-2 text-white']) . __('_NUEVO_PLAN'),
                ['action' => 'add'],
                [
                    'class' => 'btn btn-primary btn-sm',
                    'escape' => false
                ]
            ) ?>
            <?= $this->Html->link(
                $this->Icon->render('arrow-down-tray', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_EXPORTAR_CSV'),
                ['action' => 'export'],
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
                <?= $this->Icon->render('document-text', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TOTAL_PLANES') ?></div>
            <div class="stat-value text-primary"><?= number_format($stats['total']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-success">
                <?= $this->Icon->render('check-circle', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_PLANES_ASOCIADOS') ?></div>
            <div class="stat-value text-success"><?= number_format($stats['with_subscriptions']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-info">
                <?= $this->Icon->render('users', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_SUSCRIPCIONES_ACTIVAS') ?></div>
            <div class="stat-value text-info"><?= number_format($stats['total_active_subscriptions']) ?></div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th><?= __('_PLAN') ?></th>
                            <th><?= __('_PRECIO_MENSUAL') ?></th>
                            <th><?= __('_PRECIO_ANUAL') ?></th>
                            <th><?= __('_LIMITE_USUARIOS') ?></th>
                            <th><?= __('_SUSCRIPCIONES') ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($plans as $plan): ?>
                        <tr>
                            <td>
                                <div class="font-bold"><?= h($plan->name) ?></div>
                                <div class="text-sm opacity-50"><?= h($plan->description ?? '') ?></div>
                            </td>
                            <td>
                                <?php 
                                $monthlyPrice = '';
                                foreach ($plan->prices ?? [] as $price) {
                                    if ($price->period === 'monthly') {
                                        $monthlyPrice = '€' . number_format($price->amount, 2);
                                        break;
                                    }
                                }
                                echo $monthlyPrice ?: __('_NO_DISPONIBLE');
                                ?>
                            </td>
                            <td>
                                <?php 
                                $annualPrice = '';
                                foreach ($plan->prices ?? [] as $price) {
                                    if ($price->period === 'annual') {
                                        $annualPrice = '€' . number_format($price->amount, 2);
                                        break;
                                    }
                                }
                                echo $annualPrice ?: __('_NO_DISPONIBLE');
                                ?>
                            </td>
                            <td><?= number_format($plan->max_users ?? 0) ?></td>
                            <td><span class="badge badge-primary"><?= count($plan->subscriptions ?? []) ?></span></td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <?= $this->Html->link(
                                        $this->Icon->render('eye', 'solid', ['class' => 'w-4 h-4']),
                                        ['action' => 'view', $plan->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_VER_DETALLES')
                                        ]
                                    ) ?>
                                    
                                    <?= $this->Html->link(
                                        $this->Icon->render('pencil-square', 'solid', ['class' => 'w-4 h-4']),
                                        ['action' => 'edit', $plan->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_EDITAR')
                                        ]
                                    ) ?>
                                    
                                    <?= $this->Html->link(
                                        $this->Icon->render('currency-euro', 'solid', ['class' => 'w-4 h-4']),
                                        ['action' => 'prices', $plan->id],
                                        [
                                            'class' => 'btn btn-ghost btn-xs',
                                            'escape' => false,
                                            'title' => __('_PRECIOS')
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
</div>