<?php
/**
 * @var \App\View\AppView $this
 * @var \JornaticCore\Model\Entity\Company $company
 * @var array $companyStats
 */
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><?= $this->Html->link(__('_EMPRESAS'), ['action' => 'index']) ?></li>
                    <li><?= h($company->name) ?></li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold text-base-content"><?= h($company->name) ?></h1>
            <p class="text-base-content/60 mt-1"><?= h($company->industry ?? __('_SIN_INDUSTRIA')) ?></p>
        </div>

        <div class="flex gap-2">
            <?= $this->Html->link(
                $this->Icon->render('pencil-square', 'solid', ['class' => 'w-5 h-5 mr-2 text-white']) . __('_EDITAR'),
                ['action' => 'edit', $company->id],
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

    <!-- Stats Cards -->
    <div class="stats stats-horizontal shadow w-full mb-6">
        <div class="stat">
            <div class="stat-figure text-primary">
                <?= $this->Icon->render('users', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TOTAL_EMPLEADOS') ?></div>
            <div class="stat-value text-primary"><?= number_format($companyStats['total_users']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-success">
                <?= $this->Icon->render('check-circle', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_EMPLEADOS_ACTIVOS') ?></div>
            <div class="stat-value text-success"><?= number_format($companyStats['active_users']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-info">
                <?= $this->Icon->render('building-office', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_DEPARTAMENTOS') ?></div>
            <div class="stat-value text-info"><?= number_format($companyStats['departments_count']) ?></div>
        </div>
        
        <div class="stat">
            <div class="stat-figure text-warning">
                <?= $this->Icon->render('clock', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_FICHAJES_HOY') ?></div>
            <div class="stat-value text-warning"><?= number_format($companyStats['attendances_today']) ?></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2">
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h2 class="card-title text-xl"><?= __('_INFORMACION_EMPRESA') ?></h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_NOMBRE_EMPRESA') ?></label>
                                <p class="text-lg font-semibold"><?= h($company->name) ?></p>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_EMAIL') ?></label>
                                <p class="text-lg"><?= h($company->email) ?></p>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_TELEFONO') ?></label>
                                <p class="text-lg"><?= h($company->phone ?? __('_NO_ESPECIFICADO')) ?></p>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_CIF') ?></label>
                                <p class="text-lg font-mono"><?= h($company->nif) ?></p>
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_INDUSTRIA') ?></label>
                                <p class="text-lg"><?= h($company->industry ?? __('_NO_ESPECIFICADO')) ?></p>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_SITIO_WEB') ?></label>
                                <p class="text-lg">
                                    <?php if ($company->website): ?>
                                        <a href="<?= h($company->website) ?>" target="_blank" class="link link-primary">
                                            <?= h($company->website) ?>
                                        </a>
                                    <?php else: ?>
                                        <?= __('_NO_ESPECIFICADO') ?>
                                    <?php endif; ?>
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_ESTADO') ?></label>
                                <div>
                                    <?php if ($company->is_active): ?>
                                        <span class="badge badge-success badge-lg"><?= __('_ACTIVA') ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-error badge-lg"><?= __('_INACTIVA') ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_CREACION') ?></label>
                                <p class="text-lg"><?= $company->created->format('d/m/Y H:i') ?></p>
                            </div>
                        </div>
                    </div>

                    <?php if ($company->description): ?>
                        <div class="mt-6">
                            <label class="text-sm font-medium text-base-content/60"><?= __('_DESCRIPCION') ?></label>
                            <p class="text-lg mt-2"><?= h($company->description) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información Financiera de Stripe -->
            <?php if (!empty($stripeCustomerData)): ?>
                <div class="card bg-base-100 shadow-lg mb-6">
                    <div class="card-body">
                        <h2 class="card-title text-xl">
                            <?= $this->Icon->render('credit-card', 'solid', ['class' => 'w-5 h-5 mr-2']) ?>
                            <?= __('_INFORMACION_FINANCIERA') ?>
                            <div class="badge badge-sm badge-success"><?= __('_TIEMPO_REAL') ?></div>
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Información del Customer -->
                            <?php if (!empty($stripeCustomerData['customer'])): ?>
                                <?php $customer = $stripeCustomerData['customer']; ?>
                                
                                <div>
                                    <div class="mb-4">
                                        <label class="text-sm font-medium text-base-content/60"><?= __('_EMAIL_FACTURACION') ?></label>
                                        <p class="text-lg"><?= h($customer->email ?? __('_NO_ESPECIFICADO')) ?></p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="text-sm font-medium text-base-content/60"><?= __('_SALDO_CUSTOMER') ?></label>
                                        <p class="text-lg font-semibold <?= $customer->balance > 0 ? 'text-success' : ($customer->balance < 0 ? 'text-error' : '') ?>">
                                            <?= number_format($customer->balance / 100, 2) ?> <?= strtoupper($customer->currency ?? 'EUR') ?>
                                        </p>
                                    </div>

                                    <?php if (!empty($customer->default_source)): ?>
                                        <div class="mb-4">
                                            <label class="text-sm font-medium text-base-content/60"><?= __('_METODO_PAGO_DEFECTO') ?></label>
                                            <p class="text-lg"><?= h($customer->default_source) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div>
                                <!-- Métodos de Pago -->
                                <?php if (!empty($stripeCustomerData['payment_methods'])): ?>
                                    <div class="mb-4">
                                        <label class="text-sm font-medium text-base-content/60"><?= __('_METODOS_PAGO') ?></label>
                                        <div class="space-y-2 mt-2">
                                            <?php foreach (array_slice($stripeCustomerData['payment_methods'], 0, 2) as $paymentMethod): ?>
                                                <div class="flex items-center gap-2 p-2 bg-base-200 rounded">
                                                    <?= $this->Icon->render('credit-card', 'solid', ['class' => 'w-4 h-4 text-primary']) ?>
                                                    <span class="text-sm">
                                                        **** **** **** <?= h($paymentMethod->card->last4 ?? '') ?>
                                                    </span>
                                                    <span class="text-xs text-base-content/60">
                                                        <?= h(strtoupper($paymentMethod->card->brand ?? '')) ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Facturas Recientes -->
                                <?php if (!empty($stripeCustomerData['recent_invoices'])): ?>
                                    <div class="mb-4">
                                        <label class="text-sm font-medium text-base-content/60"><?= __('_FACTURAS_RECIENTES') ?></label>
                                        <div class="space-y-2 mt-2">
                                            <?php foreach (array_slice($stripeCustomerData['recent_invoices'], 0, 2) as $invoice): ?>
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
                </div>
            <?php elseif (!empty($company->active_subscription) && !empty($company->active_subscription->stripe_customer_id)): ?>
                <!-- Mostrar mensaje cuando hay ID de customer pero no se pudieron obtener los datos -->
                <div class="card bg-base-100 shadow-lg mb-6">
                    <div class="card-body">
                        <h2 class="card-title text-xl">
                            <?= $this->Icon->render('credit-card', 'solid', ['class' => 'w-5 h-5 mr-2']) ?>
                            <?= __('_INFORMACION_FINANCIERA') ?>
                            <div class="badge badge-sm badge-warning"><?= __('_ERROR_STRIPE') ?></div>
                        </h2>
                        
                        <div class="text-center py-4">
                            <p class="text-base-content/60"><?= __('_STRIPE_NO_DISPONIBLE') ?></p>
                            <p class="text-xs text-base-content/40 mt-2">Customer ID: <?= h($company->active_subscription->stripe_customer_id) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Empleados Recientes -->
            <?php if (!empty($company->users)): ?>
                <div class="card bg-base-100 shadow-lg">
                    <div class="card-body p-0">
                        <h3 class="card-title px-6 pt-6"><?= __('_EMPLEADOS_RECIENTES') ?></h3>
                        <div class="overflow-x-auto">
                            <table class="table table-zebra">
                                <thead>
                                <tr>
                                    <th><?= __('_EMPLEADO') ?></th>
                                    <th><?= __('_EMAIL') ?></th>
                                    <th><?= __('_DEPARTAMENTO') ?></th>
                                    <th><?= __('_ESTADO') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach (array_slice($company->users, 0, 5) as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-3">
                                                <div class="avatar placeholder">
                                                    <div class="bg-neutral text-neutral-content rounded-full w-8">
                                                        <span class="text-xs"><?= strtoupper(substr($user->name, 0, 1) .
                                                                substr($user->lastname, 0, 1)) ?></span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-bold"><?= h($user->name . ' ' .
                                                            $user->lastname) ?></div>
                                                    <div class="text-sm opacity-50"><?= h($user->dni_nie ?? '') ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= h($user->email) ?></td>
                                        <td><?= h($user->department->name ?? __('_SIN_DEPARTAMENTO')) ?></td>
                                        <td>
                                            <?php if ($user->is_active): ?>
                                                <span class="badge badge-success"><?= __('_ACTIVO') ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-error"><?= __('_INACTIVO') ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Panel Lateral -->
        <div class="lg:col-span-1">
            <!-- Acciones Rápidas -->
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h3 class="card-title"><?= __('_ACCIONES_RAPIDAS') ?></h3>
                    <div class="space-y-2">
                        <?= $this->Html->link(
                            $this->Icon->render('users', 'solid', ['class' => 'w-4 h-4 text-white']) . __('_VER_EMPLEADOS'),
                            ['controller' => 'Users', 'action' => 'index', '?' => ['company_id' => $company->id]],
                            [
                                'class' => 'btn btn-primary btn-sm w-full justify-start',
                                'escape' => false
                            ]
                        ) ?>

                        <?= $this->Html->link(
                            $this->Icon->render('building-office', 'solid', ['class' => 'w-4 h-4 text-white']) . __('_VER_DEPARTAMENTOS'),
                            ['controller' => 'Departments', 'action' => 'index', '?' => ['company_id' => $company->id]],
                            [
                                'class' => 'btn btn-secondary btn-sm w-full justify-start',
                                'escape' => false
                            ]
                        ) ?>

                        <?= $this->Html->link(
                            $this->Icon->render('clock', 'solid', ['class' => 'w-4 h-4 text-white']) . __('_VER_ASISTENCIAS'),
                            ['controller' => 'Attendances', 'action' => 'index', '?' => ['company_id' => $company->id]],
                            [
                                'class' => 'btn btn-accent btn-sm w-full justify-start',
                                'escape' => false
                            ]
                        ) ?>

                        <?= $this->Html->link(
                            $this->Icon->render('globe-asia-australia', 'solid', ['class' => 'w-4 h-4 text-white']) . __('_VER_FESTIVOS'),
                            ['controller' => 'Holidays', 'action' => 'index', '?' => ['company_id' => $company->id]],
                            [
                                'class' => 'btn btn-info btn-sm w-full justify-start',
                                'escape' => false
                            ]
                        ) ?>
                    </div>
                </div>
            </div>

            <!-- Suscripción -->
            <?php if (!empty($company->active_subscription)): ?>
                <?php $subscription = $company->active_subscription; ?>
                <div class="card bg-base-100 shadow-lg mb-6">
                    <div class="card-body">
                        <h3 class="card-title"><?= __('_SUSCRIPCION_ACTUAL') ?></h3>
                        <div class="text-center">
                            <h4 class="text-2xl font-bold text-primary"><?= h($subscription->plan->name ?? '') ?></h4>
                            <p class="text-base-content/60 mb-4"><?= h($subscription->period ?? '') ?></p>

                            <div class="badge badge-lg <?= $subscription->status === 'active'
                                ? 'badge-success'
                                : 'badge-warning' ?>">
                                <?= ucfirst($subscription->status) ?>
                            </div>

                            <div class="mt-4">
                                <p class="text-sm text-base-content/60"><?= __('_INICIO') ?></p>
                                <p class="font-semibold"><?= $subscription->starts
                                        ? $subscription->starts->format('d/m/Y')
                                        : __('_NO_DISPONIBLE') ?></p>
                            </div>

                            <?php if ($subscription->ends): ?>
                                <div class="mt-2">
                                    <p class="text-sm text-base-content/60"><?= __('_VENCIMIENTO') ?></p>
                                    <p class="font-semibold"><?= $subscription->ends->format('d/m/Y') ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="mt-4 text-center">
                                <?= $this->Html->link(
                                    $this->Icon->render('eye', 'solid', ['class' => 'w-4 h-4 mr-2 text-white']) . __('_VER_DETALLES'),
                                    ['controller' => 'Subscriptions', 'action' => 'view', $subscription->id],
                                    ['class' => 'btn btn-primary btn-sm', 'escape' => false]
                                ) ?>
                            </div>
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
                            <label class="text-sm font-medium text-base-content/60"><?= __('_ID_EMPRESA') ?></label>
                            <p class="font-mono text-sm"><?= $company->id ?></p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_UUID') ?></label>
                            <p class="font-mono text-xs break-all"><?= h($company->uuid ?? '') ?></p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-base-content/60"><?= __('_ULTIMA_MODIFICACION') ?></label>
                            <p class="text-sm"><?= $company->modified->format('d/m/Y H:i') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>