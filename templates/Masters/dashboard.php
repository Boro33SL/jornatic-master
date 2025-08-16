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
        <div class="stat-value text-primary">-</div>
        <div class="stat-desc">Total registradas</div>
    </div>

    <div class="stat stat-master bg-base-100 shadow-lg">
        <div class="stat-figure text-accent">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
        </div>
        <div class="stat-title">Suscripciones Activas</div>
        <div class="stat-value text-accent">-</div>
        <div class="stat-desc">Clientes pagando</div>
    </div>

    <div class="stat stat-master bg-base-100 shadow-lg">
        <div class="stat-figure text-success">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
        </div>
        <div class="stat-title">Ingresos Mensuales</div>
        <div class="stat-value text-success">€-</div>
        <div class="stat-desc">Mes actual</div>
    </div>

    <div class="stat stat-master bg-base-100 shadow-lg">
        <div class="stat-figure text-info">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="stat-title">Estado del Sistema</div>
        <div class="stat-value text-success">✓</div>
        <div class="stat-desc text-success">Operativo</div>
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
            <a href="<?= $this->Url->build(['controller' => 'Masters', 'action' => 'add']) ?>" class="card bg-gradient-to-br from-success/5 to-success/10 hover:from-success/10 hover:to-success/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-success">Crear Usuario Master</h3>
                    <p class="text-sm text-base-content/70 mt-1">Añadir nuevo administrador</p>
                </div>
            </a>

            <a href="#" class="card bg-gradient-to-br from-primary/5 to-primary/10 hover:from-primary/10 hover:to-primary/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-primary mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-primary">Gestionar Empresas</h3>
                    <p class="text-sm text-base-content/70 mt-1">Ver y editar configuraciones</p>
                </div>
            </a>

            <a href="#" class="card bg-gradient-to-br from-accent/5 to-accent/10 hover:from-accent/10 hover:to-accent/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-accent mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-accent">Suscripciones</h3>
                    <p class="text-sm text-base-content/70 mt-1">Monitorizar facturación</p>
                </div>
            </a>

            <a href="<?= $this->Url->build(['controller' => 'MasterAccessLogs', 'action' => 'index']) ?>" class="card bg-gradient-to-br from-info/5 to-info/10 hover:from-info/10 hover:to-info/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-info mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-info">Logs de Auditoría</h3>
                    <p class="text-sm text-base-content/70 mt-1">Registro de actividades</p>
                </div>
            </a>

            <a href="#" class="card bg-gradient-to-br from-success/5 to-success/10 hover:from-success/10 hover:to-success/20 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                <div class="card-body text-center">
                    <div class="text-success mb-2">
                        <svg class="w-8 h-8 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 109.75 9.75A9.75 9.75 0 0012 2.25z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-success">Herramientas Soporte</h3>
                    <p class="text-sm text-base-content/70 mt-1">Utilidades de soporte</p>
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
                        data: [0, 0, 0, 0, 0, 0],
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
                    labels: ['Starter', 'Professional', 'Business'],
                    datasets: [{
                        data: [0, 0, 0],
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