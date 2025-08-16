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
<div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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

<!-- Salud del Negocio Section -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-base-content mb-4 flex items-center">
        <?= $this->Icon->render('heart', 'solid', ['class' => 'w-6 h-6 mr-3 text-error']) ?>
        <?= __('_SALUD_DEL_NEGOCIO') ?>
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Conversion Rate -->
        <div class="stat bg-base-100 shadow-lg border-l-4 border-success">
            <div class="stat-figure text-success">
                <?= $this->Icon->render('arrow-trending-up', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TASA_CONVERSION') ?></div>
            <div class="stat-value text-success"><?= $conversionRate ?>%</div>
            <div class="stat-desc"><?= __('_TRIAL_A_PAGADAS') ?></div>
        </div>

        <!-- Churn Rate -->
        <div class="stat bg-base-100 shadow-lg border-l-4 border-warning">
            <div class="stat-figure text-warning">
                <?= $this->Icon->render('arrow-trending-down', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TASA_CHURN') ?></div>
            <div class="stat-value text-warning"><?= $churnRate ?>%</div>
            <div class="stat-desc"><?= __('_CANCELACIONES_MES') ?></div>
        </div>

        <!-- ARR Growth -->
        <div class="stat bg-base-100 shadow-lg border-l-4 <?= $arrGrowth >= 0 ? 'border-success' : 'border-error' ?>">
            <div class="stat-figure <?= $arrGrowth >= 0 ? 'text-success' : 'text-error' ?>">
                <?= $this->Icon->render($arrGrowth >= 0 ? 'chart-bar-square' : 'chart-bar', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_CRECIMIENTO_ARR') ?></div>
            <div class="stat-value <?= $arrGrowth >= 0 ? 'text-success' : 'text-error' ?>">
                <?= $arrGrowth >= 0 ? '+' : '' ?><?= $arrGrowth ?>%
            </div>
            <div class="stat-desc"><?= __('_VS_ANO_ANTERIOR') ?></div>
        </div>

        <!-- Average CLV -->
        <div class="stat bg-base-100 shadow-lg border-l-4 border-info">
            <div class="stat-figure text-info">
                <?= $this->Icon->render('user-circle', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_CLV_PROMEDIO') ?></div>
            <div class="stat-value text-info">€<?= number_format($averageClv, 0, ',', '.') ?></div>
            <div class="stat-desc"><?= __('_VALOR_VIDA_CLIENTE') ?></div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-2 gap-6 mb-8">
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

<!-- Advanced Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- MoM Growth Chart -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-success">
                <?= $this->Icon->render('arrow-trending-up', 'solid', ['class' => 'w-5 h-5 mr-2']) ?>
                <?= __('_CRECIMIENTO_MOM') ?>
            </h2>
            <div class="w-full h-64" x-data="momGrowthChart()">
                <canvas x-ref="momGrowthCanvas" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Geographic Distribution Chart -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-info">
                <?= $this->Icon->render('map-pin', 'solid', ['class' => 'w-5 h-5 mr-2']) ?>
                <?= __('_DISTRIBUCION_GEOGRAFICA') ?>
            </h2>
            <div class="w-full h-64" x-data="geographicChart()">
                <canvas x-ref="geographicCanvas" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Actividad Tiempo Real Section -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-base-content mb-4 flex items-center">
        <?= $this->Icon->render('bolt', 'solid', ['class' => 'w-6 h-6 mr-3 text-warning']) ?>
        <?= __('_ACTIVIDAD_TIEMPO_REAL') ?>
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Empresas Activas Hoy -->
        <div class="stat bg-base-100 shadow-lg border-l-4 border-primary">
            <div class="stat-figure text-primary">
                <?= $this->Icon->render('building-office-2', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_EMPRESAS_ACTIVAS_HOY') ?></div>
            <div class="stat-value text-primary"><?= number_format($activeCompaniesToday) ?></div>
            <div class="stat-desc"><?= __('_CON_FICHAJES_HOY') ?></div>
        </div>

        <!-- Empleados Únicos Hoy -->
        <div class="stat bg-base-100 shadow-lg border-l-4 border-accent">
            <div class="stat-figure text-accent">
                <?= $this->Icon->render('users', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_EMPLEADOS_UNICOS_HOY') ?></div>
            <div class="stat-value text-accent"><?= number_format($uniqueEmployeesToday) ?></div>
            <div class="stat-desc"><?= __('_HAN_FICHADO_HOY') ?></div>
        </div>

        <!-- Fichajes por Hora -->
        <div class="stat bg-base-100 shadow-lg border-l-4 border-secondary">
            <div class="stat-figure text-secondary">
                <?= $this->Icon->render('clock', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_FICHAJES_POR_HORA') ?></div>
            <div class="stat-value text-secondary"><?= $avgAttendancesPerHour ?></div>
            <div class="stat-desc"><?= __('_PROMEDIO_HOY') ?></div>
        </div>

        <!-- Empresas Inactivas -->
        <div class="stat bg-base-100 shadow-lg border-l-4 border-error">
            <div class="stat-figure text-error">
                <?= $this->Icon->render('exclamation-triangle', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_EMPRESAS_INACTIVAS') ?></div>
            <div class="stat-value text-error"><?= number_format($inactiveCompanies) ?></div>
            <div class="stat-desc"><?= __('_MAS_30_DIAS_SIN_ACTIVIDAD') ?></div>
        </div>
    </div>
</div>

<!-- Alertas y Atención Section -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-base-content mb-4 flex items-center">
        <?= $this->Icon->render('bell', 'solid', ['class' => 'w-6 h-6 mr-3 text-error']) ?>
        <?= __('_ALERTAS_Y_ATENCION') ?>
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Trials Expirando -->
        <div class="stat bg-base-100 shadow-lg border-l-4 border-warning">
            <div class="stat-figure text-warning">
                <?= $this->Icon->render('calendar-days', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_TRIALS_EXPIRANDO') ?></div>
            <div class="stat-value text-warning"><?= number_format($trialsExpiring) ?></div>
            <div class="stat-desc"><?= __('_PROXIMOS_7_DIAS') ?></div>
        </div>

        <!-- Pagos Fallidos -->
        <div class="stat bg-base-100 shadow-lg border-l-4 border-error">
            <div class="stat-figure text-error">
                <?= $this->Icon->render('credit-card', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_PAGOS_FALLIDOS') ?></div>
            <div class="stat-value text-error"><?= number_format($failedPayments) ?></div>
            <div class="stat-desc"><?= __('_REQUIEREN_ATENCION') ?></div>
        </div>

        <!-- Empresas Sin Actividad -->
        <div class="stat bg-base-100 shadow-lg border-l-4 border-neutral">
            <div class="stat-figure text-neutral">
                <?= $this->Icon->render('building-office', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_SIN_ACTIVIDAD') ?></div>
            <div class="stat-value text-neutral"><?= number_format($inactiveCompanies) ?></div>
            <div class="stat-desc"><?= __('_MAS_30_DIAS') ?></div>
        </div>

        <!-- System Health -->
        <div class="stat bg-base-100 shadow-lg border-l-4 <?= $systemHealthPercentage >= 95 ? 'border-success' : ($systemHealthPercentage >= 85 ? 'border-warning' : 'border-error') ?>">
            <div class="stat-figure <?= $systemHealthPercentage >= 95 ? 'text-success' : ($systemHealthPercentage >= 85 ? 'text-warning' : 'text-error') ?>">
                <?= $this->Icon->render('server', 'solid', ['class' => 'w-8 h-8']) ?>
            </div>
            <div class="stat-title"><?= __('_SYSTEM_HEALTH') ?></div>
            <div class="stat-value <?= $systemHealthPercentage >= 95 ? 'text-success' : ($systemHealthPercentage >= 85 ? 'text-warning' : 'text-error') ?>">
                <?= $systemHealthPercentage ?>%
            </div>
            <div class="stat-desc"><?= __('_UPTIME_7_DIAS') ?></div>
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

    Alpine.data('momGrowthChart', () => ({
        init() {
            const ctx = this.$refs.momGrowthCanvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mes -5', 'Mes -4', 'Mes -3', 'Mes -2', 'Mes -1', 'Actual'],
                    datasets: [{
                        label: 'Crecimiento MoM (%)',
                        data: <?= json_encode($momGrowthData ?? [0, 0, 0, 0, 0, 0]) ?>,
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#16a34a',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6
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
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
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

    Alpine.data('geographicChart', () => ({
        init() {
            const ctx = this.$refs.geographicCanvas.getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($geographicDistribution ?? [], 'province') ?: ['Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Bilbao']) ?>,
                    datasets: [{
                        label: 'Empresas',
                        data: <?= json_encode(array_column($geographicDistribution ?? [], 'count') ?: [0, 0, 0, 0, 0]) ?>,
                        backgroundColor: [
                            '#0ea5e9',
                            '#0369a1',
                            '#0284c7',
                            '#0891b2',
                            '#0e7490'
                        ],
                        borderWidth: 0
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
                                display: false
                            }
                        }
                    }
                }
            });
        }
    }));
});
</script>