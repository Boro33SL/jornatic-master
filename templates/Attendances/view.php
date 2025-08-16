<?php
/**
 * @var \App\View\AppView $this
 * @var \JornaticCore\Model\Entity\Attendance $attendance
 * @var array $relatedAttendances
 * @var array $dayStats
 */

$this->Html->css('leaflet/leaflet', ['block' => true]);
$this->Html->script('leaflet/leaflet', ['block' => true]);
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="breadcrumbs text-sm">
                <ul>
                    <li><?= $this->Html->link(__('_GESTION_ASISTENCIAS'), ['action' => 'index']) ?></li>
                    <li><?= h($attendance->user->name . ' ' . $attendance->user->first_surname) ?></li>
                    <li><?= h($attendance->timestamp->format('d/m/Y H:i')) ?></li>
                </ul>
            </div>
            <h1 class="text-3xl font-bold text-base-content"><?= __('_DETALLE_ASISTENCIA') ?></h1>
        </div>
        <div class="flex gap-2">
            <?= $this->Html->link(
                $this->Icon->render('pencil-square', 'solid', ['class' => 'w-4 h-4 mr-1']) . __('_EDITAR'),
                ['action' => 'edit', $attendance->id],
                ['class' => 'btn btn-primary btn-sm', 'escape' => false]
            ) ?>
            <?= $this->Html->link(__('_VOLVER'), ['action' => 'index'], ['class' => 'btn btn-outline btn-sm']) ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Información Principal de la Asistencia -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center gap-3 mb-4">
                    <?= $this->Icon->render('clock', 'solid', ['class' => 'w-6 h-6 text-primary']) ?>
                    <h2 class="card-title"><?= __('_INFORMACION_ASISTENCIA') ?></h2>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_FECHA_HORA') ?></label>
                        <p class="text-xl font-bold"><?= h($attendance->timestamp->format('d/m/Y H:i:s')) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_TIPO') ?></label>
                        <div class="mt-1"><?= $attendance->badge ?></div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_IP_ADDRESS') ?></label>
                        <p class="text-lg font-mono bg-base-200 px-3 py-1 rounded"><?= h($attendance->ip_address) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_UUID') ?></label>
                        <p class="text-sm font-mono text-base-content/60"><?= h($attendance->uuid) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Usuario -->
        <div class="card bg-base-100 shadow-lg">
            <div class="card-body">
                <div class="flex items-center gap-3 mb-4">
                    <?= $this->Icon->render('user', 'solid', ['class' => 'w-6 h-6 text-info']) ?>
                    <h2 class="card-title"><?= __('_INFORMACION_USUARIO') ?></h2>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="avatar placeholder">
                            <div class="bg-neutral text-neutral-content rounded-full w-12">
                                <span class="font-bold">
                                    <?= strtoupper(substr($attendance->user->name, 0, 1) . substr($attendance->user->first_surname ?? '', 0, 1)) ?>
                                </span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">
                                <?= h($attendance->user->name . ' ' . ($attendance->user->first_surname ?? '')) ?>
                            </h3>
                            <p class="text-sm opacity-70"><?= h($attendance->user->email) ?></p>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_EMPRESA') ?></label>
                        <p class="text-lg"><?= h($attendance->user->company->name ?? __('_NO_ASIGNADA')) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_DEPARTAMENTO') ?></label>
                        <p class="text-lg"><?= h($attendance->user->department->name ?? __('_NO_ASIGNADO')) ?></p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-base-content/60"><?= __('_ROL') ?></label>
                        <p class="text-lg"><?= h($attendance->user->role->name ?? __('_NO_ASIGNADO')) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de Geolocalización -->
    <?php if ($attendance->latitude && $attendance->longitude): ?>
    <div class="card bg-base-100 shadow-lg mt-6">
        <div class="card-body">
            <div class="flex items-center gap-3 mb-4">
                <?= $this->Icon->render('map-pin', 'solid', ['class' => 'w-6 h-6 text-success']) ?>
                <h2 class="card-title"><?= __('_INFORMACION_UBICACION') ?></h2>
            </div>
            
            <!-- Mapa de Ubicación -->
            <div class="mb-6">
                <div id="attendance-map" class="w-full h-80 rounded-lg border border-base-200"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="text-sm font-medium text-base-content/60"><?= __('_LATITUD') ?></label>
                    <p class="text-lg font-mono"><?= h($attendance->latitude) ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-base-content/60"><?= __('_LONGITUD') ?></label>
                    <p class="text-lg font-mono"><?= h($attendance->longitude) ?></p>
                </div>
                <?php if ($attendance->location_precision): ?>
                <div>
                    <label class="text-sm font-medium text-base-content/60"><?= __('_PRECISION') ?></label>
                    <p class="text-lg"><?= h($attendance->location_precision) ?>m</p>
                </div>
                <?php endif; ?>
                <?php if ($attendance->location_source): ?>
                <div>
                    <label class="text-sm font-medium text-base-content/60"><?= __('_ORIGEN') ?></label>
                    <p class="text-lg"><?= h($attendance->location_source) ?></p>
                </div>
                <?php endif; ?>
            </div>
            <?php if ($attendance->location_type): ?>
            <div class="mt-4">
                <label class="text-sm font-medium text-base-content/60"><?= __('_TIPO_UBICACION') ?></label>
                <span class="badge badge-outline ml-2"><?= h($attendance->location_type) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript para inicializar el mapa -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Coordenadas de la asistencia
        const lat = <?= json_encode((float)$attendance->latitude) ?>;
        const lng = <?= json_encode((float)$attendance->longitude) ?>;
        const precision = <?= json_encode($attendance->location_precision ? (float)$attendance->location_precision : null) ?>;
        const timestamp = <?= json_encode($attendance->timestamp->format('d/m/Y H:i:s')) ?>;
        const userInfo = <?= json_encode($attendance->user->name . ' ' . ($attendance->user->first_surname ?? '')) ?>;
        const attendanceType = <?= json_encode($attendance->type) ?>;
        
        // Inicializar el mapa
        const map = L.map('attendance-map').setView([lat, lng], 16);
        
        // Añadir tiles de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Determinar el color del marcador según el tipo de asistencia
        let markerColor = 'blue';
        switch(attendanceType) {
            case 'in':
                markerColor = 'green';
                break;
            case 'out':
                markerColor = 'red';
                break;
            case 'break_start':
                markerColor = 'orange';
                break;
            case 'break_end':
                markerColor = 'yellow';
                break;
        }
        
        // Crear contenido del popup
        let popupContent = `
            <div class="text-center">
                <h3 class="font-bold text-lg mb-2">${userInfo}</h3>
                <p class="text-sm mb-1"><strong>Tipo:</strong> ${attendanceType}</p>
                <p class="text-sm mb-1"><strong>Fecha:</strong> ${timestamp}</p>
                <p class="text-sm mb-1"><strong>Coordenadas:</strong> ${lat.toFixed(6)}, ${lng.toFixed(6)}</p>
                ${precision ? `<p class="text-sm"><strong>Precisión:</strong> ${precision}m</p>` : ''}
            </div>
        `;
        
        // Añadir marcador principal
        const marker = L.marker([lat, lng])
            .addTo(map)
            .bindPopup(popupContent)
            .openPopup();
        
        // Si hay información de precisión, añadir círculo de precisión
        if (precision && precision > 0) {
            L.circle([lat, lng], {
                color: markerColor,
                fillColor: markerColor,
                fillOpacity: 0.1,
                radius: precision
            }).addTo(map);
        }
        
        // Añadir control de escala
        L.control.scale().addTo(map);
    });
    </script>
    <?php endif; ?>

    <!-- Estadísticas del Día -->
    <?php if (!empty($dayStats)): ?>
    <div class="card bg-base-100 shadow-lg mt-6">
        <div class="card-body">
            <div class="flex items-center gap-3 mb-4">
                <?= $this->Icon->render('chart-bar', 'solid', ['class' => 'w-6 h-6 text-warning']) ?>
                <h2 class="card-title"><?= __('_RESUMEN_DIA') ?></h2>
                <span class="text-sm text-base-content/60">
                    <?= h($attendance->timestamp->format('d/m/Y')) ?>
                </span>
            </div>
            <div class="stats stats-horizontal w-full">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <?= $this->Icon->render('login', 'solid', ['class' => 'w-8 h-8']) ?>
                    </div>
                    <div class="stat-title"><?= __('_ENTRADAS') ?></div>
                    <div class="stat-value text-primary"><?= h($dayStats['check_ins'] ?? 0) ?></div>
                </div>
                
                <div class="stat">
                    <div class="stat-figure text-error">
                        <?= $this->Icon->render('logout', 'solid', ['class' => 'w-8 h-8']) ?>
                    </div>
                    <div class="stat-title"><?= __('_SALIDAS') ?></div>
                    <div class="stat-value text-error"><?= h($dayStats['check_outs'] ?? 0) ?></div>
                </div>
                
                <div class="stat">
                    <div class="stat-figure text-info">
                        <?= $this->Icon->render('pause', 'solid', ['class' => 'w-8 h-8']) ?>
                    </div>
                    <div class="stat-title"><?= __('_DESCANSOS') ?></div>
                    <div class="stat-value text-info"><?= h($dayStats['breaks'] ?? 0) ?></div>
                </div>
                
                <?php if (!empty($dayStats['total_hours'])): ?>
                <div class="stat">
                    <div class="stat-figure text-success">
                        <?= $this->Icon->render('clock', 'solid', ['class' => 'w-8 h-8']) ?>
                    </div>
                    <div class="stat-title"><?= __('_HORAS_TOTALES') ?></div>
                    <div class="stat-value text-success"><?= h($dayStats['total_hours']) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Asistencias Relacionadas del Mismo Día -->
    <?php if (!empty($relatedAttendances)): ?>
    <div class="card bg-base-100 shadow-lg mt-6">
        <div class="card-body">
            <div class="flex items-center gap-3 mb-4">
                <?= $this->Icon->render('list-bullet', 'solid', ['class' => 'w-6 h-6 text-accent']) ?>
                <h2 class="card-title"><?= __('_OTRAS_ASISTENCIAS_MISMO_DIA') ?></h2>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th><?= __('_HORA') ?></th>
                            <th><?= __('_TIPO') ?></th>
                            <th><?= __('_UBICACION') ?></th>
                            <th><?= __('_IP') ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($relatedAttendances as $relatedAttendance): ?>
                        <tr>
                            <td class="font-mono"><?= h($relatedAttendance->timestamp->format('H:i:s')) ?></td>
                            <td><?= $relatedAttendance->badge ?></td>
                            <td>
                                <?php if ($relatedAttendance->latitude && $relatedAttendance->longitude): ?>
                                    <div class="flex items-center gap-1 text-success">
                                        <?= $this->Icon->render('map-pin', 'solid', ['class' => 'w-4 h-4']) ?>
                                        <span class="text-xs">GPS</span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-base-content/40">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-sm font-mono"><?= h($relatedAttendance->ip_address) ?></td>
                            <td class="text-center">
                                <?= $this->Html->link(
                                    $this->Icon->render('eye', 'solid', ['class' => 'w-4 h-4']),
                                    ['action' => 'view', $relatedAttendance->id],
                                    [
                                        'class' => 'btn btn-ghost btn-xs',
                                        'escape' => false,
                                        'title' => __('_VER_DETALLES')
                                    ]
                                ) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Acciones -->
    <div class="flex gap-4 mt-6">
        <?= $this->Html->link(
            $this->Icon->render('user', 'solid', ['class' => 'w-4 h-4 mr-2']) . __('_VER_USUARIO'),
            ['controller' => 'Users', 'action' => 'view', $attendance->user_id],
            ['class' => 'btn btn-info', 'escape' => false]
        ) ?>
        <?= $this->Html->link(
            $this->Icon->render('clock', 'solid', ['class' => 'w-4 h-4 mr-2']) . __('_VER_ASISTENCIAS_USUARIO'),
            ['controller' => 'Users', 'action' => 'attendances', $attendance->user_id],
            ['class' => 'btn btn-secondary', 'escape' => false]
        ) ?>
    </div>
</div>