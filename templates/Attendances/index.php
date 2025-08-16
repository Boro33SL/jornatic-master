<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\JornaticCore\Model\Entity\Attendance> $attendances
 */
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-base-content"><?= __('_GESTION_ASISTENCIAS') ?></h1>
        <?= $this->Html->link(__('_EXPORTAR_CSV'), ['action' => 'export'], ['class' => 'btn btn-outline btn-sm']) ?>
    </div>

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th><?= __('_EMPLEADO') ?></th>
                            <th><?= __('_EMPRESA') ?></th>
                            <th><?= __('_FECHA_HORA') ?></th>
                            <th><?= __('_TIPO') ?></th>
                            <th><?= __('_UBICACION') ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendances as $attendance): ?>
                        <tr>
                            <td>
                                <div class="font-bold"><?= h($attendance->user->name ?? '') ?> <?= h($attendance->user->lastname ?? '') ?></div>
                                <div class="text-sm opacity-50"><?= h($attendance->user->dni_nie ?? '') ?></div>
                            </td>
                            <td><?= h($attendance->user->company->name ?? '') ?></td>
                            <td><?= $attendance->datetime ? $attendance->datetime->format('d/m/Y H:i') : '' ?></td>
                            <td>
                                <?php
                                $typeClass = match($attendance->type) {
                                    'check_in' => 'badge-success',
                                    'check_out' => 'badge-error',
                                    'break_start' => 'badge-warning',
                                    'break_end' => 'badge-info',
                                    default => 'badge-ghost'
                                };
                                $typeText = match($attendance->type) {
                                    'check_in' => __('_ENTRADA'),
                                    'check_out' => __('_SALIDA'),
                                    'break_start' => __('_PAUSA_INICIO'),
                                    'break_end' => __('_PAUSA_FIN'),
                                    default => ucfirst($attendance->type)
                                };
                                ?>
                                <span class="badge <?= $typeClass ?>"><?= $typeText ?></span>
                            </td>
                            <td>
                                <?php if ($attendance->latitude && $attendance->longitude): ?>
                                    <div class="text-xs"><?= __('_GPS') ?>: <?= number_format($attendance->latitude, 4) ?>, <?= number_format($attendance->longitude, 4) ?></div>
                                <?php endif; ?>
                                <?php if ($attendance->ip_address): ?>
                                    <div class="text-xs"><?= __('_IP') ?>: <?= h($attendance->ip_address) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <?= $this->Html->link(__('_VER'), ['action' => 'view', $attendance->id], ['class' => 'btn btn-ghost btn-xs']) ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>