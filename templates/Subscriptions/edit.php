<?php
/**
 * @var \App\View\AppView $this
 * @var \JornaticCore\Model\Entity\Subscription $subscription
 * @var array $companies
 * @var array $plans
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><?= $this->Html->link(__('_SUSCRIPCIONES'), ['action' => 'index']) ?></li>
                    <li><?= $this->Html->link(__('_SUSCRIPCION') . ' #' . $subscription->id, ['action' => 'view', $subscription->id]) ?></li>
                    <li><?= __('_EDITAR') ?></li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold text-base-content"><?= __('_EDITAR_SUSCRIPCION') ?></h1>
            <p class="text-base-content/60 mt-1"><?= h($subscription->company->name ?? '') ?> - <?= h($subscription->plan->name ?? '') ?></p>
        </div>
        
        <div class="flex gap-2">
            <?= $this->Html->link(
                '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>' . __('_VER_SUSCRIPCION'),
                ['action' => 'view', $subscription->id],
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

    <!-- Formulario -->
    <div class="card bg-base-100 shadow-lg">
        <div class="card-body">
            <?= $this->Form->create($subscription, ['class' => 'space-y-6']) ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Información Básica -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content border-b border-base-300 pb-2">
                        <?= __('_INFORMACION_SUSCRIPCION') ?>
                    </h3>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_EMPRESA') ?> <span class="text-error">*</span></span>
                        </label>
                        <?= $this->Form->select('company_id', $companies, [
                            'class' => 'select select-bordered w-full',
                            'required' => true,
                            'empty' => __('_SELECCIONAR_EMPRESA')
                        ]) ?>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_PLAN') ?> <span class="text-error">*</span></span>
                        </label>
                        <?= $this->Form->select('plan_id', $plans, [
                            'class' => 'select select-bordered w-full',
                            'required' => true,
                            'empty' => __('_SELECCIONAR_PLAN')
                        ]) ?>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_PERIODO') ?> <span class="text-error">*</span></span>
                        </label>
                        <?= $this->Form->select('period', [
                            'monthly' => __('_MENSUAL'),
                            'annual' => __('_ANUAL')
                        ], [
                            'class' => 'select select-bordered w-full',
                            'required' => true
                        ]) ?>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_ESTADO') ?> <span class="text-error">*</span></span>
                        </label>
                        <?= $this->Form->select('status', [
                            'active' => __('_ACTIVA'),
                            'trial' => __('_PRUEBA'),
                            'canceled' => __('_CANCELADA'),
                            'expired' => __('_EXPIRADA')
                        ], [
                            'class' => 'select select-bordered w-full',
                            'required' => true
                        ]) ?>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content border-b border-base-300 pb-2">
                        <?= __('_FECHAS') ?>
                    </h3>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_FECHA_INICIO') ?> <span class="text-error">*</span></span>
                        </label>
                        <?= $this->Form->control('start_date', [
                            'label' => false,
                            'type' => 'date',
                            'class' => 'input input-bordered w-full',
                            'required' => true
                        ]) ?>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_FECHA_VENCIMIENTO') ?></span>
                            <span class="label-text-alt"><?= __('_OPCIONAL') ?></span>
                        </label>
                        <?= $this->Form->control('end_date', [
                            'label' => false,
                            'type' => 'date',
                            'class' => 'input input-bordered w-full'
                        ]) ?>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60"><?= __('_DEJAR_VACIO_PARA_SUSCRIPCION_INDEFINIDA') ?></span>
                        </label>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_FECHA_CANCELACION') ?></span>
                            <span class="label-text-alt"><?= __('_SOLO_LECTURA') ?></span>
                        </label>
                        <?= $this->Form->control('canceled_at', [
                            'label' => false,
                            'type' => 'datetime-local',
                            'class' => 'input input-bordered w-full',
                            'readonly' => true
                        ]) ?>
                    </div>
                </div>
            </div>

            <!-- Información de Stripe -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content border-b border-base-300 pb-2">
                        <?= __('_INFORMACION_STRIPE') ?>
                    </h3>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_ID_SUSCRIPCION_STRIPE') ?></span>
                        </label>
                        <?= $this->Form->control('stripe_subscription_id', [
                            'label' => false,
                            'placeholder' => 'sub_1234567890',
                            'class' => 'input input-bordered w-full font-mono text-sm'
                        ]) ?>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60"><?= __('_ID_GENERADO_POR_STRIPE') ?></span>
                        </label>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_ID_CUSTOMER_STRIPE') ?></span>
                        </label>
                        <?= $this->Form->control('stripe_customer_id', [
                            'label' => false,
                            'placeholder' => 'cus_1234567890',
                            'class' => 'input input-bordered w-full font-mono text-sm'
                        ]) ?>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60"><?= __('_ID_CUSTOMER_EN_STRIPE') ?></span>
                        </label>
                    </div>
                </div>

                <!-- Panel de Ayuda -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content border-b border-base-300 pb-2">
                        <?= __('_INFORMACION_IMPORTANTE') ?>
                    </h3>
                    
                    <div class="alert alert-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 15.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div>
                            <h3 class="font-bold"><?= __('_CAMBIOS_CRITICOS') ?></h3>
                            <div class="text-xs mt-2 space-y-1">
                                <p>• <?= __('_CAMBIAR_PLAN_AFECTA_CAPACIDAD') ?></p>
                                <p>• <?= __('_CAMBIAR_ESTADO_AFECTA_ACCESO') ?></p>
                                <p>• <?= __('_VERIFICAR_DATOS_STRIPE') ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold"><?= __('_DATOS_ACTUALES') ?></h3>
                            <div class="text-xs mt-2 space-y-1">
                                <p><strong><?= __('_ID') ?>:</strong> <?= $subscription->id ?></p>
                                <p><strong><?= __('_CREADA') ?>:</strong> <?= $subscription->created->format('d/m/Y H:i') ?></p>
                                <p><strong><?= __('_MODIFICADA') ?>:</strong> <?= $subscription->modified->format('d/m/Y H:i') ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($subscription->status === 'active'): ?>
                    <div class="alert alert-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-bold"><?= __('_SUSCRIPCION_ACTIVA') ?></h3>
                            <div class="text-xs mt-2">
                                <p><?= __('_LA_EMPRESA_TIENE_ACCESO_COMPLETO') ?></p>
                            </div>
                        </div>
                    </div>
                    <?php elseif ($subscription->status === 'canceled'): ?>
                    <div class="alert alert-error">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-bold"><?= __('_SUSCRIPCION_CANCELADA') ?></h3>
                            <div class="text-xs mt-2">
                                <p><?= __('_LA_EMPRESA_NO_TIENE_ACCESO') ?></p>
                                <?php if ($subscription->canceled_at): ?>
                                <p><?= __('_CANCELADA_EL') ?>: <?= $subscription->canceled_at->format('d/m/Y H:i') ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notas -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium"><?= __('_NOTAS_INTERNAS') ?></span>
                </label>
                <?= $this->Form->control('notes', [
                    'label' => false,
                    'type' => 'textarea',
                    'rows' => 3,
                    'placeholder' => __('_NOTAS_CAMBIOS_OBSERVACIONES'),
                    'class' => 'textarea textarea-bordered w-full'
                ]) ?>
                <label class="label">
                    <span class="label-text-alt text-base-content/60"><?= __('_NOTAS_SOLO_VISIBLES_ADMINISTRADORES') ?></span>
                </label>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end gap-4 pt-6 border-t border-base-300">
                <?= $this->Html->link(
                    __('_CANCELAR'),
                    ['action' => 'view', $subscription->id],
                    ['class' => 'btn btn-ghost']
                ) ?>
                
                <?= $this->Form->button(__('_GUARDAR_CAMBIOS'), [
                    'class' => 'btn btn-primary',
                    'type' => 'submit'
                ]) ?>
            </div>
            
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
// Validación y comportamiento del formulario
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.querySelector('select[name="status"]');
    const endDateInput = document.querySelector('input[name="end_date"]');
    const periodSelect = document.querySelector('select[name="period"]');
    
    // Función para mostrar/ocultar fecha de vencimiento según el estado
    function toggleEndDate() {
        if (statusSelect.value === 'canceled' || statusSelect.value === 'expired') {
            endDateInput.required = true;
            endDateInput.closest('.form-control').querySelector('.label-text-alt').textContent = '<?= __('_REQUERIDO_PARA_ESTADOS_INACTIVOS') ?>';
        } else {
            endDateInput.required = false;
            endDateInput.closest('.form-control').querySelector('.label-text-alt').textContent = '<?= __('_DEJAR_VACIO_PARA_SUSCRIPCION_INDEFINIDA') ?>';
        }
    }
    
    // Aplicar validación inicial
    if (statusSelect) {
        toggleEndDate();
        statusSelect.addEventListener('change', toggleEndDate);
    }
    
    // Validación de fechas
    const startDateInput = document.querySelector('input[name="start_date"]');
    if (startDateInput && endDateInput) {
        function validateDates() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            
            if (startDate && endDate && endDate <= startDate) {
                endDateInput.setCustomValidity('<?= __('_FECHA_VENCIMIENTO_DEBE_SER_POSTERIOR') ?>');
            } else {
                endDateInput.setCustomValidity('');
            }
        }
        
        startDateInput.addEventListener('change', validateDates);
        endDateInput.addEventListener('change', validateDates);
    }
});
</script>