<?php
/**
 * @var \App\View\AppView $this
 * @var \JornaticCore\Model\Entity\Plan $plan
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><?= $this->Html->link(__('_PLANES'), ['action' => 'index']) ?></li>
                    <li><?= h($plan->name) ?></li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold text-base-content"><?= h($plan->name) ?></h1>
        </div>
        <div class="flex gap-2">
            <?= $this->Html->link(
                $this->Icon->render('pencil-square', 'solid', ['class' => 'w-5 h-5 mr-2 text-white']) . __('_EDITAR'),
                ['action' => 'edit', $plan->id],
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title"><?= __('_INFORMACION_PLAN') ?></h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_NOMBRE') ?></label>
                        <p class="text-lg font-semibold"><?= h($plan->name) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_DESCRIPCION') ?></label>
                        <p class="text-lg"><?= h($plan->description ?? __('_NO_ESPECIFICADO')) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_LIMITE_USUARIOS') ?></label>
                        <p class="text-lg font-semibold"><?= number_format($plan->max_users ?? 0) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_TIPO') ?></label>
                        <p class="text-lg">
                            <?php if ($plan->is_trial): ?>
                                <span class="badge badge-info"><?= __('_PLAN_TRIAL') ?></span>
                            <?php else: ?>
                                <span class="badge badge-success"><?= __('_PLAN_NORMAL') ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title"><?= __('_PRECIOS') ?></h2>
                <?php if (!empty($plan->prices)): ?>
                    <div class="space-y-4">
                        <?php foreach ($plan->prices as $price): ?>
                        <div class="flex justify-between items-center p-4 bg-base-200 rounded-lg">
                            <div>
                                <div class="font-semibold"><?= $price->period === 'annual' ? __('_ANUAL') : __('_MENSUAL') ?></div>
                            </div>
                            <div class="text-2xl font-bold text-primary">€<?= number_format($price->amount, 2) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-base-content/60"><?= __('_NO_HAY_PRECIOS_CONFIGURADOS') ?></p>
                <?php endif; ?>
                <div class="mt-4">
                    <?= $this->Html->link(
                        $this->Icon->render('currency-euro', 'solid', ['class' => 'w-5 h-5 mr-2 text-white']) . __('_GESTIONAR_PRECIOS'),
                        ['action' => 'prices', $plan->id],
                        [
                            'class' => 'btn btn-secondary btn-sm',
                            'escape' => false
                        ]
                    ) ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($plan->features)): ?>
    <div class="card bg-base-100 shadow-lg mt-6">
        <div class="card-body">
            <h2 class="card-title"><?= __('_CARACTERISTICAS_INCLUIDAS') ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <?php foreach ($plan->features as $feature): ?>
                <div class="flex items-center gap-2">
                    <?= $this->Icon->render('check', 'solid', ['class' => 'w-5 h-5 text-success']) ?>
                    <span><?= h($feature->name) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>