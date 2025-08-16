<?php
/**
 * Master Add Template - DaisyUI Design
 */
$this->assign('title', 'Crear Usuario Master');
?>

<!-- Header -->
<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-primary">Crear Usuario Master</h1>
            <p class="text-base-content/70 mt-1">Añadir nuevo administrador del sistema</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <?= $this->Html->link(
                'Volver al Dashboard',
                ['action' => 'dashboard'],
                ['class' => 'btn btn-ghost btn-sm']
            ) ?>
        </div>
    </div>
</div>

<!-- Formulario -->
<div class="card bg-base-100 shadow-xl max-w-2xl mx-auto">
    <div class="card-body">
        <h2 class="card-title text-primary mb-6">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Información del Usuario Master
        </h2>

        <?= $this->Form->create($master, [
            'class' => 'space-y-6',
            'x-data' => '{ isSubmitting: false }',
            'x-on:submit' => 'isSubmitting = true'
        ]) ?>

            <!-- Grid de campos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Nombre -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Nombre completo</span>
                        <span class="label-text-alt text-error">*</span>
                    </label>
                    <?= $this->Form->control('name', [
                        'label' => false,
                        'class' => 'input input-bordered w-full focus:input-primary',
                        'placeholder' => 'Ej: Juan Pérez García',
                        'required' => true,
                        'maxlength' => 255
                    ]) ?>
                    <?php if ($master->hasErrors('name')): ?>
                        <label class="label">
                            <span class="label-text-alt text-error">
                                <?= h($master->getError('name')[0]) ?>
                            </span>
                        </label>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Email</span>
                        <span class="label-text-alt text-error">*</span>
                    </label>
                    <?= $this->Form->control('email', [
                        'label' => false,
                        'class' => 'input input-bordered w-full focus:input-primary',
                        'placeholder' => 'admin@jornatic.com',
                        'required' => true,
                        'type' => 'email',
                        'autocomplete' => 'email'
                    ]) ?>
                    <?php if ($master->hasErrors('email')): ?>
                        <label class="label">
                            <span class="label-text-alt text-error">
                                <?= h($master->getError('email')[0]) ?>
                            </span>
                        </label>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Contraseña</span>
                        <span class="label-text-alt text-error">*</span>
                    </label>
                    <?= $this->Form->control('password', [
                        'label' => false,
                        'class' => 'input input-bordered w-full focus:input-primary',
                        'placeholder' => '••••••••',
                        'required' => true,
                        'type' => 'password',
                        'autocomplete' => 'new-password'
                    ]) ?>
                    <label class="label">
                        <span class="label-text-alt text-base-content/60">
                            Mínimo 8 caracteres, se recomienda incluir números y símbolos
                        </span>
                    </label>
                    <?php if ($master->hasErrors('password')): ?>
                        <label class="label">
                            <span class="label-text-alt text-error">
                                <?= h($master->getError('password')[0]) ?>
                            </span>
                        </label>
                    <?php endif; ?>
                </div>

                <!-- Phone (opcional) -->
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-medium">Teléfono</span>
                        <span class="label-text-alt text-base-content/60">opcional</span>
                    </label>
                    <?= $this->Form->control('phone', [
                        'label' => false,
                        'class' => 'input input-bordered w-full focus:input-primary',
                        'placeholder' => '+34 600 000 000',
                        'type' => 'tel'
                    ]) ?>
                    <?php if ($master->hasErrors('phone')): ?>
                        <label class="label">
                            <span class="label-text-alt text-error">
                                <?= h($master->getError('phone')[0]) ?>
                            </span>
                        </label>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Estado activo -->
            <div class="form-control">
                <label class="label cursor-pointer justify-start">
                    <?= $this->Form->control('is_active', [
                        'type' => 'checkbox',
                        'label' => false,
                        'class' => 'checkbox checkbox-primary mr-3',
                        'checked' => true
                    ]) ?>
                    <span class="label-text">
                        <span class="font-medium">Usuario activo</span>
                        <span class="block text-sm text-base-content/60">El usuario podrá acceder al sistema inmediatamente</span>
                    </span>
                </label>
            </div>

            <!-- Observaciones (opcional) -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Observaciones</span>
                    <span class="label-text-alt text-base-content/60">opcional</span>
                </label>
                <?= $this->Form->control('notes', [
                    'label' => false,
                    'class' => 'textarea textarea-bordered w-full focus:textarea-primary',
                    'placeholder' => 'Notas adicionales sobre este usuario master...',
                    'rows' => 3,
                    'maxlength' => 500
                ]) ?>
                <label class="label">
                    <span class="label-text-alt text-base-content/60">
                        Máximo 500 caracteres
                    </span>
                </label>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-base-300">
                <button 
                    type="submit" 
                    class="btn btn-primary btn-master flex-1"
                    :class="{ 'loading': isSubmitting }"
                    :disabled="isSubmitting"
                >
                    <svg x-show="!isSubmitting" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span x-show="!isSubmitting">Crear Usuario Master</span>
                    <span x-show="isSubmitting" class="loading loading-spinner loading-sm"></span>
                </button>
                
                <?= $this->Html->link(
                    'Cancelar',
                    ['action' => 'dashboard'],
                    ['class' => 'btn btn-ghost flex-1']
                ) ?>
            </div>

        <?= $this->Form->end() ?>

        <!-- Información adicional -->
        <div class="alert alert-info mt-6">
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="font-bold">Información importante:</h3>
                <div class="text-sm mt-1">
                    • Los usuarios master tienen acceso completo al sistema<br>
                    • Se recomienda crear contraseñas seguras<br>
                    • El email debe ser único en el sistema<br>
                    • Se enviará un email de notificación al crear el usuario
                </div>
            </div>
        </div>
    </div>
</div>