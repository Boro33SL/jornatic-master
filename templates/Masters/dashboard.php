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
            <h1 class="text-3xl font-bold text-primary">Dashboard Principal</h1>
            <p class="text-base-content/70 mt-1">Panel de control del ecosistema Jornatic</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <div class="text-sm text-base-content/60">
                Bienvenido, <span class="font-medium text-primary"><?= h($master->name) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- KPI Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="stat stat-master bg-base-100 shadow-lg">
        <div class="stat-figure text-primary">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
        </div>
        <div class="stat-title">Empresas</div>
        <div class="stat-value text-primary"><?= number_format($totalCompanies ?? 0) ?></div>
        <div class="stat-desc">Total registradas</div>
    </div>

    <div class="stat stat-master bg-base-100 shadow-lg">
        <div class="stat-figure text-accent">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
        </div>
        <div class="stat-title">Suscripciones Activas</div>
        <div class="stat-value text-accent"><?= number_format($activeSubscriptions ?? 0) ?></div>
        <div class="stat-desc">Clientes pagando</div>
    </div>

    <div class="stat stat-master bg-base-100 shadow-lg">
        <div class="stat-figure text-success">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
        </div>
        <div class="stat-title">Ingresos Mensuales</div>
        <div class="stat-value text-success">€<?= number_format($monthlyRevenueAmount ?? 0, 2, ',', '.') ?></div>
        <div class="stat-desc">Mes actual</div>
    </div>

    <div class="stat stat-master bg-base-100 shadow-lg">
        <div class="stat-figure text-info">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="stat-title">Logs Hoy</div>
        <div class="stat-value text-info"><?= number_format($todayLogsCount ?? 0) ?></div>
        <div class="stat-desc">
            <span class="text-success"><?= $todaySuccessCount ?? 0 ?> exitosos</span> / 
            <span class="text-error"><?= $todayFailedCount ?? 0 ?> fallidos</span>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Revenue Chart -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
                Evolución de Ingresos
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
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Distribución por Planes
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
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Acciones Rápidas
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Fila 1: Gestión Principal -->
            <a href="<?= $this->Url->build(['controller' => 'Companies', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-primary/5 to-primary/10 hover:from-primary/10 hover:to-primary/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-primary">Empresas</h3>
                    <p class="text-sm text-base-content/70 mt-1">Gestionar clientes empresariales</p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Subscriptions', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-accent/5 to-accent/10 hover:from-accent/10 hover:to-accent/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-accent mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-accent">Suscripciones</h3>
                    <p class="text-sm text-base-content/70 mt-1">Facturación y pagos</p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Plans', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-secondary/5 to-secondary/10 hover:from-secondary/10 hover:to-secondary/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-secondary mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-secondary">Planes</h3>
                    <p class="text-sm text-base-content/70 mt-1">Configurar ofertas</p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-info/5 to-info/10 hover:from-info/10 hover:to-info/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-info">Usuarios</h3>
                    <p class="text-sm text-base-content/70 mt-1">Empleados del sistema</p>
                </div>
            </a>

            <!-- Fila 2: Gestión Operativa -->
            <a href="<?= $this->Url->build(['controller' => 'Departments', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-warning/5 to-warning/10 hover:from-warning/10 hover:to-warning/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-warning mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-warning">Departamentos</h3>
                    <p class="text-sm text-base-content/70 mt-1">Organización empresarial</p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Contracts', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-success/5 to-success/10 hover:from-success/10 hover:to-success/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-success">Contratos</h3>
                    <p class="text-sm text-base-content/70 mt-1">Relaciones laborales</p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Attendances', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-error/5 to-error/10 hover:from-error/10 hover:to-error/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-error mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-error">Asistencias</h3>
                    <p class="text-sm text-base-content/70 mt-1">Control de fichajes</p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Features', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-neutral/5 to-neutral/10 hover:from-neutral/10 hover:to-neutral/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-neutral mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a1 1 0 01-1-1V9a1 1 0 011-1h1a2 2 0 100-4H4a1 1 0 01-1-1V4a1 1 0 011-1h3a1 1 0 001-1v-1a2 2 0 114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-neutral">Características</h3>
                    <p class="text-sm text-base-content/70 mt-1">Funcionalidades sistema</p>
                </div>
            </a>

            <!-- Fila 3: Administración -->
            <a href="<?= $this->Url->build(['controller' => 'Holidays', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-purple-500/5 to-purple-500/10 hover:from-purple-500/10 hover:to-purple-500/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-purple-500 mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-purple-500">Festivos</h3>
                    <p class="text-sm text-base-content/70 mt-1">Calendario empresarial</p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Masters', 'action' => 'add']) ?>" class="card bg-gradient-to-br from-success/5 to-success/10 hover:from-success/10 hover:to-success/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-success">Usuario Master</h3>
                    <p class="text-sm text-base-content/70 mt-1">Añadir administrador</p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'MasterAccessLogs', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-info/5 to-info/10 hover:from-info/10 hover:to-info/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-info">Logs Auditoría</h3>
                    <p class="text-sm text-base-content/70 mt-1">Registro actividades</p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'Attendances', 'action' => 'reports']) ?>" class="card bg-gradient-to-br from-orange-500/5 to-orange-500/10 hover:from-orange-500/10 hover:to-orange-500/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-orange-500 mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-orange-500">Reportes</h3>
                    <p class="text-sm text-base-content/70 mt-1">Informes y estadísticas</p>
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