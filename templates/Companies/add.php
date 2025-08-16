<?php
/**
 * @var \App\View\AppView $this
 * @var \JornaticCore\Model\Entity\Company $company
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><?= $this->Html->link(__('_EMPRESAS'), ['action' => 'index']) ?></li>
                    <li><?= __('_NUEVA_EMPRESA') ?></li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold text-base-content"><?= __('_NUEVA_EMPRESA') ?></h1>
            <p class="text-base-content/60 mt-1"><?= __('_CREAR_NUEVA_EMPRESA_EN_SISTEMA') ?></p>
        </div>
        
        <div class="flex gap-2">
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
            <?= $this->Form->create($company, ['class' => 'space-y-6']) ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Información Básica -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content border-b border-base-300 pb-2">
                        <?= __('_INFORMACION_BASICA') ?>
                    </h3>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_NOMBRE_EMPRESA') ?> <span class="text-error">*</span></span>
                        </label>
                        <?= $this->Form->control('name', [
                            'label' => false,
                            'placeholder' => __('_NOMBRE_COMPLETO_EMPRESA'),
                            'class' => 'input input-bordered w-full',
                            'required' => true
                        ]) ?>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_EMAIL_EMPRESA') ?> <span class="text-error">*</span></span>
                        </label>
                        <?= $this->Form->control('email', [
                            'label' => false,
                            'type' => 'email',
                            'placeholder' => 'contacto@empresa.com',
                            'class' => 'input input-bordered w-full',
                            'required' => true
                        ]) ?>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_TELEFONO') ?></span>
                        </label>
                        <?= $this->Form->control('phone', [
                            'label' => false,
                            'placeholder' => '+34 000 000 000',
                            'class' => 'input input-bordered w-full'
                        ]) ?>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_CIF') ?> <span class="text-error">*</span></span>
                        </label>
                        <?= $this->Form->control('nif', [
                            'label' => false,
                            'placeholder' => 'B12345678',
                            'class' => 'input input-bordered w-full',
                            'required' => true
                        ]) ?>
                        <label class="label">
                            <span class="label-text-alt text-base-content/60"><?= __('_CIF_EMPRESA_FORMATO_VALIDO') ?></span>
                        </label>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content border-b border-base-300 pb-2">
                        <?= __('_INFORMACION_ADICIONAL') ?>
                    </h3>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_INDUSTRIA') ?></span>
                        </label>
                        <?= $this->Form->select('industry', [
                            '' => __('_SELECCIONAR_INDUSTRIA'),
                            'technology' => __('_TECNOLOGIA'),
                            'manufacturing' => __('_MANUFACTURA'),
                            'retail' => __('_COMERCIO_MINORISTA'),
                            'healthcare' => __('_SALUD'),
                            'finance' => __('_FINANZAS'),
                            'education' => __('_EDUCACION'),
                            'construction' => __('_CONSTRUCCION'),
                            'hospitality' => __('_HOSTELERIA'),
                            'transportation' => __('_TRANSPORTE'),
                            'consulting' => __('_CONSULTORIA'),
                            'other' => __('_OTRO')
                        ], [
                            'class' => 'select select-bordered w-full'
                        ]) ?>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_SITIO_WEB') ?></span>
                        </label>
                        <?= $this->Form->control('website', [
                            'label' => false,
                            'type' => 'url',
                            'placeholder' => 'https://www.empresa.com',
                            'class' => 'input input-bordered w-full'
                        ]) ?>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_NUMERO_EMPLEADOS') ?></span>
                        </label>
                        <?= $this->Form->select('employee_count', [
                            '' => __('_SELECCIONAR_RANGO'),
                            '1-10' => '1-10 ' . __('_EMPLEADOS'),
                            '11-50' => '11-50 ' . __('_EMPLEADOS'),
                            '51-100' => '51-100 ' . __('_EMPLEADOS'),
                            '101-250' => '101-250 ' . __('_EMPLEADOS'),
                            '251-500' => '251-500 ' . __('_EMPLEADOS'),
                            '500+' => '500+ ' . __('_EMPLEADOS')
                        ], [
                            'class' => 'select select-bordered w-full'
                        ]) ?>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_ESTADO') ?></span>
                        </label>
                        <div class="form-control">
                            <label class="label cursor-pointer justify-start">
                                <?= $this->Form->checkbox('is_active', [
                                    'value' => 1,
                                    'checked' => true,
                                    'class' => 'checkbox checkbox-primary mr-3'
                                ]) ?>
                                <span class="label-text"><?= __('_EMPRESA_ACTIVA') ?></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-medium"><?= __('_DESCRIPCION') ?></span>
                </label>
                <?= $this->Form->control('description', [
                    'label' => false,
                    'type' => 'textarea',
                    'rows' => 4,
                    'placeholder' => __('_DESCRIPCION_OPCIONAL_EMPRESA'),
                    'class' => 'textarea textarea-bordered w-full'
                ]) ?>
            </div>

            <!-- Dirección -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content border-b border-base-300 pb-2">
                        <?= __('_DIRECCION') ?>
                    </h3>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_DIRECCION') ?></span>
                        </label>
                        <?= $this->Form->control('address', [
                            'label' => false,
                            'placeholder' => __('_CALLE_NUMERO'),
                            'class' => 'input input-bordered w-full'
                        ]) ?>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium"><?= __('_CIUDAD') ?></span>
                            </label>
                            <?= $this->Form->control('city', [
                                'label' => false,
                                'placeholder' => __('_CIUDAD'),
                                'class' => 'input input-bordered w-full'
                            ]) ?>
                        </div>
                        
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-medium"><?= __('_CODIGO_POSTAL') ?></span>
                            </label>
                            <?= $this->Form->control('postal_code', [
                                'label' => false,
                                'placeholder' => '28001',
                                'class' => 'input input-bordered w-full'
                            ]) ?>
                        </div>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_PROVINCIA') ?></span>
                        </label>
                        <?= $this->Form->control('state', [
                            'label' => false,
                            'placeholder' => __('_PROVINCIA_COMUNIDAD'),
                            'class' => 'input input-bordered w-full'
                        ]) ?>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-medium"><?= __('_PAIS') ?></span>
                        </label>
                        <?= $this->Form->control('country', [
                            'label' => false,
                            'value' => 'España',
                            'class' => 'input input-bordered w-full'
                        ]) ?>
                    </div>
                </div>

                <!-- Panel de Ayuda -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-base-content border-b border-base-300 pb-2">
                        <?= __('_AYUDA') ?>
                    </h3>
                    
                    <div class="alert alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold"><?= __('_INFORMACION_IMPORTANTE') ?></h3>
                            <div class="text-xs mt-2 space-y-1">
                                <p>• <?= __('_CIF_DEBE_SER_VALIDO_UNICO') ?></p>
                                <p>• <?= __('_EMAIL_SERA_USADO_NOTIFICACIONES') ?></p>
                                <p>• <?= __('_EMPRESA_ACTIVA_POR_DEFECTO') ?></p>
                                <p>• <?= __('_PODRA_MODIFICAR_DATOS_DESPUES') ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 15.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div>
                            <h3 class="font-bold"><?= __('_CAMPOS_OBLIGATORIOS') ?></h3>
                            <div class="text-xs mt-2">
                                <p><?= __('_NOMBRE_EMAIL_CIF_SON_REQUERIDOS') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end gap-4 pt-6 border-t border-base-300">
                <?= $this->Html->link(
                    __('_CANCELAR'),
                    ['action' => 'index'],
                    ['class' => 'btn btn-ghost']
                ) ?>
                
                <?= $this->Form->button(__('_CREAR_EMPRESA'), [
                    'class' => 'btn btn-primary',
                    'type' => 'submit'
                ]) ?>
            </div>
            
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<script>
// Validación básica del CIF
document.addEventListener('DOMContentLoaded', function() {
    const cifInput = document.querySelector('input[name="cif"]');
    if (cifInput) {
        cifInput.addEventListener('input', function(e) {
            const value = e.target.value.toUpperCase();
            e.target.value = value;
            
            // Validación básica del formato
            const cifPattern = /^[ABCDEFGHJNPQRSUVW]\d{8}$/;
            const parentDiv = e.target.closest('.form-control');
            const existingError = parentDiv.querySelector('.text-error');
            
            if (existingError) {
                existingError.remove();
            }
            
            if (value && !cifPattern.test(value)) {
                const errorMsg = document.createElement('span');
                errorMsg.className = 'text-error text-xs mt-1';
                errorMsg.textContent = '<?= __('_FORMATO_CIF_INVALIDO') ?>';
                parentDiv.appendChild(errorMsg);
            }
        });
    }
});
</script>