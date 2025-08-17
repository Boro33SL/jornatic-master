<?php
/**
 * @var \App\View\AppView $this
 * @var \JornaticCore\Model\Entity\Plan $plan
 * @var array $allFeatures
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><?= $this->Html->link(__('_PLANES'), ['action' => 'index']) ?></li>
                    <li><?= $this->Html->link(h($plan->name), ['action' => 'view', $plan->id]) ?></li>
                    <li><?= __('_EDITAR') ?></li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold text-base-content"><?= __('_EDITAR_PLAN') ?>: <?= h($plan->name) ?></h1>
        </div>
        <div class="flex gap-2">
            <?= $this->Html->link(
                $this->Icon->render('eye', 'solid', ['class' => 'w-5 h-5 mr-2']) . __('_VER_PLAN'),
                ['action' => 'view', $plan->id],
                [
                    'class' => 'btn btn-outline btn-sm',
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

    <?= $this->Form->create($plan, ['class' => 'space-y-6']) ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Información Básica del Plan -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title"><?= __('_INFORMACION_BASICA') ?></h2>
                <div class="space-y-4">
                    <div class="form-control">
                        <?= $this->Form->label('name', __('_NOMBRE'), ['class' => 'label']) ?>
                        <?= $this->Form->control('name', [
                            'class' => 'input input-bordered w-full',
                            'label' => false,
                            'required' => true
                        ]) ?>
                    </div>

                    <div class="form-control">
                        <?= $this->Form->label('description', __('_DESCRIPCION'), ['class' => 'label']) ?>
                        <?= $this->Form->control('description', [
                            'type' => 'textarea',
                            'class' => 'textarea textarea-bordered w-full',
                            'label' => false,
                            'rows' => 3
                        ]) ?>
                    </div>

                    <div class="form-control">
                        <?= $this->Form->label('max_users', __('_LIMITE_USUARIOS'), ['class' => 'label']) ?>
                        <?= $this->Form->control('max_users', [
                            'type' => 'number',
                            'class' => 'input input-bordered w-full',
                            'label' => false,
                            'min' => 1
                        ]) ?>
                    </div>

                    <div class="form-control">
                        <?= $this->Form->label('max_departments', __('_LIMITE_DEPARTAMENTOS'), ['class' => 'label']) ?>
                        <?= $this->Form->control('max_departments', [
                            'type' => 'number',
                            'class' => 'input input-bordered w-full',
                            'label' => false,
                            'min' => 1
                        ]) ?>
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer">
                            <?= $this->Form->checkbox('is_trial', [
                                'class' => 'checkbox checkbox-primary',
                                'label' => false
                            ]) ?>
                            <span class="label-text"><?= __('_ES_PLAN_TRIAL') ?></span>
                        </label>
                    </div>

                    <?php if ($plan->is_trial): ?>
                    <div class="form-control">
                        <?= $this->Form->label('trial_days', __('_DIAS_TRIAL'), ['class' => 'label']) ?>
                        <?= $this->Form->control('trial_days', [
                            'type' => 'number',
                            'class' => 'input input-bordered w-full',
                            'label' => false,
                            'min' => 1
                        ]) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Precios -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <h2 class="card-title"><?= __('_PRECIOS') ?></h2>
                <?php if (!empty($plan->prices)): ?>
                    <div class="space-y-4">
                        <?php foreach ($plan->prices as $index => $price): ?>
                        <div class="p-4 bg-base-200 rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold">
                                    <?= $price->period === 'annual' ? __('_PRECIO_ANUAL') : __('_PRECIO_MENSUAL') ?>
                                </span>
                                <span class="text-2xl font-bold text-primary">€<?= number_format($price->amount, 2) ?></span>
                            </div>
                            <p class="text-sm text-base-content/60">
                                <?= __('_CONFIGURAR_PRECIOS_EN_SECCION_DEDICADA') ?>
                            </p>
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

    <!-- Características del Plan -->
    <?php if (!empty($allFeatures)): ?>
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <h2 class="card-title"><?= __('_CARACTERISTICAS_DEL_PLAN') ?></h2>
            <p class="text-base-content/60 mb-4"><?= __('_SELECCIONA_Y_CONFIGURA_CARACTERISTICAS') ?></p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($allFeatures as $feature): ?>
                    <?php 
                    $currentValue = null;
                    $isSelected = false;
                    
                    // Buscar si esta feature está asociada al plan
                    foreach ($plan->features as $planFeature) {
                        if ($planFeature->id == $feature->id) {
                            $isSelected = true;
                            $currentValue = $plan->getFeatureValue($feature->code);
                            break;
                        }
                    }
                    ?>
                    
                    <div class="card bg-base-200 border border-base-300">
                        <div class="card-body p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-base"><?= __($feature->translation_string) ?></h3>
                                    <p class="text-sm text-base-content/60 mt-1">
                                        <?= __('_TIPO') ?>: 
                                        <span class="badge badge-outline badge-sm">
                                            <?= ucfirst($feature->data_type) ?>
                                        </span>
                                    </p>
                                </div>
                                
                                <!-- Checkbox para seleccionar la feature -->
                                <div class="form-control">
                                    <?= $this->Form->checkbox("features.{$feature->id}.selected", [
                                        'class' => 'checkbox checkbox-primary',
                                        'label' => false,
                                        'checked' => $isSelected,
                                        'data-feature-id' => $feature->id,
                                        'data-feature-type' => $feature->data_type
                                    ]) ?>
                                </div>
                            </div>
                            
                            <!-- Campo de valor según el tipo -->
                            <div class="mt-3 feature-value-section" data-feature-id="<?= $feature->id ?>" style="<?= $isSelected ? '' : 'display: none;' ?>">
                                <?php if ($feature->data_type === 'boolean'): ?>
                                    <div class="form-control">
                                        <label class="label cursor-pointer">
                                            <?= $this->Form->checkbox("features.{$feature->id}.value", [
                                                'class' => 'checkbox checkbox-success',
                                                'label' => false,
                                                'checked' => (bool)$currentValue
                                            ]) ?>
                                            <span class="label-text"><?= __('_ACTIVAR_CARACTERISTICA') ?></span>
                                        </label>
                                    </div>
                                <?php elseif ($feature->data_type === 'integer'): ?>
                                    <div class="form-control">
                                        <?= $this->Form->label("features.{$feature->id}.value", __('_VALOR_NUMERICO'), ['class' => 'label text-sm']) ?>
                                        <?= $this->Form->control("features.{$feature->id}.value", [
                                            'type' => 'number',
                                            'class' => 'input input-bordered input-sm w-full',
                                            'label' => false,
                                            'value' => $currentValue ?: 0,
                                            'min' => 0
                                        ]) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="form-control">
                                        <?= $this->Form->label("features.{$feature->id}.value", __('_VALOR_TEXTO'), ['class' => 'label text-sm']) ?>
                                        <?= $this->Form->control("features.{$feature->id}.value", [
                                            'type' => 'text',
                                            'class' => 'input input-bordered input-sm w-full',
                                            'label' => false,
                                            'value' => $currentValue ?: ''
                                        ]) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Campo oculto para el ID de la feature -->
                                <?= $this->Form->hidden("features.{$feature->id}.feature_id", ['value' => $feature->id]) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Botones de Acción -->
    <div class="flex justify-end gap-4 pt-6 border-t border-base-300">
        <?= $this->Html->link(
            __('_CANCELAR'),
            ['action' => 'view', $plan->id],
            ['class' => 'btn btn-outline']
        ) ?>
        <?= $this->Form->button(__('_GUARDAR_CAMBIOS'), [
            'type' => 'submit',
            'class' => 'btn btn-primary'
        ]) ?>
    </div>

    <?= $this->Form->end() ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar el mostrar/ocultar secciones de valor según si está seleccionada la feature
    const featureCheckboxes = document.querySelectorAll('input[data-feature-id]');
    
    featureCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const featureId = this.dataset.featureId;
            const valueSection = document.querySelector('.feature-value-section[data-feature-id="' + featureId + '"]');
            
            if (valueSection) {
                if (this.checked) {
                    valueSection.style.display = 'block';
                } else {
                    valueSection.style.display = 'none';
                    
                    // Limpiar valores cuando se deselecciona
                    const inputs = valueSection.querySelectorAll('input, textarea, select');
                    inputs.forEach(function(input) {
                        if (input.type === 'checkbox') {
                            input.checked = false;
                        } else {
                            input.value = '';
                        }
                    });
                }
            }
        });
    });
});
</script>