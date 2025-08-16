<?php
/**
 * Master Dashboard Template - DaisyUI Design
 */
$this->assign('title', 'Dashboard');
?>

<!-- Dashboard Header -->
<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-primary"><?= __('_DASHBOARD_PRINCIPAL') ?></h1>
            <p class="text-base-content/70 mt-1"><?= __('_PANEL_CONTROL_ECOSISTEMA_JORNATIC') ?></p>
        </div>
        <div class="mt-4 sm:mt-0">
            <div class="text-sm text-base-content/60">
                <?= __('_BIENVENIDO') ?>, <span class="font-medium text-primary"><?= h($master->name) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- KPI Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat stat-master bg-base-100 shadow-lg">
        <div class="stat-figure text-primary">
            <?= $this->Icon->render('building-office', 'solid', ['class' => 'w-8 h-8']) ?>
        </div>
        <div class="stat-title"><?= __('_EMPRESAS') ?></div>
        <div class="stat-value text-primary"><?= number_format($totalCompanies ?? 0) ?></div>
        <div class="stat-desc"><?= __('_TOTAL_REGISTRADAS') ?></div>
    </div>

    <div class="stat stat-master bg-base-100 shadow-lg">
        <div class="stat-figure text-accent">
            <?= $this->Icon->render('shield-check', 'solid', ['class' => 'w-8 h-8']) ?>
        </div>
        <div class="stat-title"><?= __('_SUSCRIPCIONES_ACTIVAS') ?></div>
        <div class="stat-value text-accent"><?= number_format($activeSubscriptions ?? 0) ?></div>
        <div class="stat-desc"><?= __('_CLIENTES_PAGANDO') ?></div>
    </div>

    <div class="stat stat-master bg-base-100 shadow-lg">
        <div class="stat-figure text-success">
            <?= $this->Icon->render('currency-euro', 'solid', ['class' => 'w-8 h-8']) ?>
        </div>
        <div class="stat-title"><?= __('_INGRESOS_MENSUALES') ?></div>
        <div class="stat-value text-success">€<?= number_format($monthlyRevenueAmount ?? 0, 2, ',', '.') ?></div>
        <div class="stat-desc"><?= __('_MES_ACTUAL') ?></div>
    </div>

    <div class="stat stat-master bg-base-100 shadow-lg">
        <div class="stat-figure text-info">
            <?= $this->Icon->render('check-circle', 'solid', ['class' => 'w-8 h-8']) ?>
        </div>
        <div class="stat-title"><?= __('_LOGS_HOY') ?></div>
        <div class="stat-value text-info"><?= number_format($todayLogsCount ?? 0) ?></div>
        <div class="stat-desc">
            <span class="text-success"><?= $todaySuccessCount ?? 0 ?> <?= __('_EXITOSOS') ?></span> / 
            <span class="text-error"><?= $todayFailedCount ?? 0 ?> <?= __('_FALLIDOS') ?></span>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Revenue Chart -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-primary">
                <?= $this->Icon->render('chart-bar', 'solid', ['class' => 'w-5 h-5 mr-2']) ?>
                <?= __('_EVOLUCION_INGRESOS') ?>
            </h2>
            <div class="w-full h-64" x-data="revenueChart()">
                <canvas x-ref="revenueCanvas" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Subscriptions Chart -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-accent">
                <?= $this->Icon->render('chart-pie', 'solid', ['class' => 'w-5 h-5 mr-2']) ?>
                <?= __('_DISTRIBUCION_POR_PLANES') ?>
            </h2>
            <div class="w-full h-64" x-data="plansChart()">
                <canvas x-ref="plansCanvas" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card bg-base-100 shadow-xl">
    <div class="card-body">
        <h2 class="card-title text-primary mb-6">
            <?= $this->Icon->render('bolt', 'solid', ['class' => 'w-6 h-6 mr-2']) ?>
            <?= __('_ACCIONES_RAPIDAS') ?>
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Fila 1: Gestión Principal -->
            <a href="<?= $this->Url->build(['controller' => 'Companies', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-primary/5 to-primary/10 hover:from-primary/10 hover:to-primary/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <?= $this->Icon->render('building-office', 'solid', ['class' => 'w-8 h-8 mx-auto']) ?>
                    </div>
                    <h3 class="font-bold text-primary"><?= __('_EMPRESAS') ?></h3>
                    <p class="text-sm text-base-content/70 mt-1"><?= __('_GESTIONAR_CLIENTES_EMPRESARIALES') ?></p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Subscriptions', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-accent/5 to-accent/10 hover:from-accent/10 hover:to-accent/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-accent mb-2">
                        <?= $this->Icon->render('shield-check', 'solid', ['class' => 'w-8 h-8 mx-auto']) ?>
                    </div>
                    <h3 class="font-bold text-accent"><?= __('_SUSCRIPCIONES') ?></h3>
                    <p class="text-sm text-base-content/70 mt-1"><?= __('_FACTURACION_Y_PAGOS') ?></p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Plans', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-secondary/5 to-secondary/10 hover:from-secondary/10 hover:to-secondary/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-secondary mb-2">
                        <?= $this->Icon->render('squares-2x2', 'solid', ['class' => 'w-8 h-8 mx-auto']) ?>
                    </div>
                    <h3 class="font-bold text-secondary"><?= __('_PLANES') ?></h3>
                    <p class="text-sm text-base-content/70 mt-1"><?= __('_CONFIGURAR_OFERTAS') ?></p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-info/5 to-info/10 hover:from-info/10 hover:to-info/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <?= $this->Icon->render('user-group', 'solid', ['class' => 'w-8 h-8 mx-auto']) ?>
                    </div>
                    <h3 class="font-bold text-info"><?= __('_USUARIOS') ?></h3>
                    <p class="text-sm text-base-content/70 mt-1"><?= __('_EMPLEADOS_DEL_SISTEMA') ?></p>
                </div>
            </a>

            <!-- Fila 2: Gestión Operativa -->
            <a href="<?= $this->Url->build(['controller' => 'Departments', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-warning/5 to-warning/10 hover:from-warning/10 hover:to-warning/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <?= $this->Icon->render('user-group', 'solid', ['class' => 'w-8 h-8 mx-auto']) ?>
                    </div>
                    <h3 class="font-bold text-warning"><?= __('_DEPARTAMENTOS') ?></h3>
                    <p class="text-sm text-base-content/70 mt-1"><?= __('_ORGANIZACION_EMPRESARIAL') ?></p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Contracts', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-success/5 to-success/10 hover:from-success/10 hover:to-success/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <?= $this->Icon->render('document-text', 'solid', ['class' => 'w-8 h-8 mx-auto']) ?>
                    </div>
                    <h3 class="font-bold text-success"><?= __('_CONTRATOS') ?></h3>
                    <p class="text-sm text-base-content/70 mt-1"><?= __('_RELACIONES_LABORALES') ?></p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Attendances', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-error/5 to-error/10 hover:from-error/10 hover:to-error/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-error mb-2">
                        <?= $this->Icon->render('clock', 'solid', ['class' => 'w-8 h-8 mx-auto']) ?>
                    </div>
                    <h3 class="font-bold text-error"><?= __('_ASISTENCIAS') ?></h3>
                    <p class="text-sm text-base-content/70 mt-1"><?= __('_CONTROL_DE_FICHAJES') ?></p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Features', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-neutral/5 to-neutral/10 hover:from-neutral/10 hover:to-neutral/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-neutral mb-2">
                        <?= $this->Icon->render('puzzle-piece', 'solid', ['class' => 'w-8 h-8 mx-auto']) ?>
                    </div>
                    <h3 class="font-bold text-neutral"><?= __('_CARACTERISTICAS') ?></h3>
                    <p class="text-sm text-base-content/70 mt-1"><?= __('_FUNCIONALIDADES_SISTEMA') ?></p>
                </div>
            </a>

            <!-- Fila 3: Administración -->
            <a href="<?= $this->Url->build(['controller' => 'Holidays', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-purple-500/5 to-purple-500/10 hover:from-purple-500/10 hover:to-purple-500/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-purple-500 mb-2">
                        <?= $this->Icon->render('calendar-days', 'solid', ['class' => 'w-8 h-8 mx-auto']) ?>
                    </div>
                    <h3 class="font-bold text-purple-500"><?= __('_FESTIVOS') ?></h3>
                    <p class="text-sm text-base-content/70 mt-1"><?= __('_CALENDARIO_EMPRESARIAL') ?></p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Masters', 'action' => 'add']) ?>" class="card bg-gradient-to-br from-success/5 to-success/10 hover:from-success/10 hover:to-success/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <?= $this->Icon->render('user-plus', 'solid', ['class' => 'w-8 h-8 mx-auto']) ?>
                    </div>
                    <h3 class="font-bold text-success"><?= __('_USUARIO_MASTER') ?></h3>
                    <p class="text-sm text-base-content/70 mt-1"><?= __('_ANADIR_ADMINISTRADOR') ?></p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'MasterAccessLogs', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-info/5 to-info/10 hover:from-info/10 hover:to-info/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <?= $this->Icon->render('document-text', 'solid', ['class' => 'w-8 h-8 mx-auto']) ?>
                    </div>
                    <h3 class="font-bold text-info"><?= __('_LOGS_DE_AUDITORIA') ?></h3>
                    <p class="text-sm text-base-content/70 mt-1"><?= __('_REGISTRO_ACTIVIDADES') ?></p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Attendances', 'action' => 'reports']) ?>" class="card bg-gradient-to-br from-orange-500/5 to-orange-500/10 hover:from-orange-500/10 hover:to-orange-500/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-orange-500 mb-2">
                        <?= $this->Icon->render('chart-bar-square', 'solid', ['class' => 'w-8 h-8 mx-auto']) ?>
                    </div>
                    <h3 class="font-bold text-orange-500"><?= __('_REPORTES') ?></h3>
                    <p class="text-sm text-base-content/70 mt-1"><?= __('_INFORMES_Y_ESTADISTICAS') ?></p>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Alpine.js Chart Components -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('revenueChart', () => ({
        init() {
            const ctx = this.$refs.revenueCanvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Ingresos (€)',
                        data: <?= json_encode($revenueData ?? [0, 0, 0, 0, 0, 0]) ?>,
                        borderColor: '#1e3a8a',
                        backgroundColor: 'rgba(30, 58, 138, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        }
                    }
                }
            });
        }
    }));

    Alpine.data('plansChart', () => ({
        init() {
            const ctx = this.$refs.plansCanvas.getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_column($planDistribution ?? [], 'name') ?: ['Starter', 'Professional', 'Business']) ?>,
                    datasets: [{
                        data: <?= json_encode(array_column($planDistribution ?? [], 'count') ?: [0, 0, 0]) ?>,
                        backgroundColor: [
                            '#0369a1',
                            '#1e3a8a', 
                            '#0f172a'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }
    }));
});
</script>