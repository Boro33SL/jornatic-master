<?php
/**
 * Master Login Template - DaisyUI Design
 */
$this->assign('title', 'Login');
?>

<div class="card bg-base-100 shadow-xl master-card-shadow" x-data="{ isLoading: false }">
    <div class="card-body">
        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-primary mb-2">Jornatic Master</h1>
            <p class="text-base-content/70">Panel de Gestión Integral</p>
        </div>

        <!-- Formulario de Login -->
        <?= $this->Form->create(null, [
            'class' => 'space-y-4',
            'x-on:submit' => 'isLoading = true'
        ]) ?>
            
            <!-- Campo Email -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Email</span>
                </label>
                <?= $this->Form->control('email', [
                    'label' => false,
                    'class' => 'input input-bordered w-full focus:input-primary',
                    'placeholder' => 'admin@jornatic.com',
                    'required' => true,
                    'type' => 'email',
                    'autocomplete' => 'email'
                ]) ?>
            </div>

            <!-- Campo Password -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium">Contraseña</span>
                </label>
                <?= $this->Form->control('password', [
                    'label' => false,
                    'class' => 'input input-bordered w-full focus:input-primary',
                    'placeholder' => '••••••••',
                    'required' => true,
                    'type' => 'password',
                    'autocomplete' => 'current-password'
                ]) ?>
            </div>

            <!-- Botón Submit -->
            <div class="form-control mt-6">
                <button 
                    type="submit" 
                    class="btn btn-primary btn-master w-full"
                    :class="{ 'loading': isLoading }"
                    :disabled="isLoading"
                >
                    <span x-show="!isLoading">Iniciar Sesión</span>
                    <span x-show="isLoading" class="loading loading-spinner loading-sm"></span>
                </button>
            </div>

        <?= $this->Form->end() ?>

        <!-- Footer -->
        <div class="text-center mt-6 pt-4 border-t border-base-300">
            <p class="text-xs text-base-content/50">
                Sistema seguro de gestión empresarial
            </p>
        </div>
    </div>
</div>

<!-- Decorative background -->
<div class="absolute inset-0 -z-10 overflow-hidden">
    <div class="absolute top-0 left-1/4 w-72 h-72 bg-primary/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-accent/5 rounded-full blur-3xl"></div>
</div>