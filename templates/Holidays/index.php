<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\JornaticCore\Model\Entity\Holiday> $holidays
 */
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-base-content"><?= __('_GESTION_FESTIVOS') ?></h1>
        <div class="flex gap-2">
            <?= $this->Html->link(__('_NUEVO_FESTIVO'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= $this->Html->link(__('_CALENDARIO'), ['action' => 'calendar'], ['class' => 'btn btn-secondary btn-sm']) ?>
            <?= $this->Html->link(__('_CREACION_MASIVA'), ['action' => 'bulk'], ['class' => 'btn btn-accent btn-sm']) ?>
        </div>
    </div>

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th><?= __('_NOMBRE') ?></th>
                            <th><?= __('_FECHA') ?></th>
                            <th><?= __('_EMPRESA') ?></th>
                            <th><?= __('_TIPO') ?></th>
                            <th><?= __('_RECURRENTE') ?></th>
                            <th><?= __('_ESTADO') ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($holidays as $holiday): ?>
                        <tr>
                            <td>
                                <div class="font-bold"><?= h($holiday->name) ?></div>
                                <?php if ($holiday->is_today): ?>
                                    <span class="badge badge-info badge-sm"><?= __('_HOY') ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= $holiday->formatted_date ?></td>
                            <td><?= h($holiday->company->name ?? '') ?></td>
                            <td>
                                <?php
                                $typeClass = match($holiday->type) {
                                    'national' => 'badge-success',
                                    'regional' => 'badge-info',
                                    'company' => 'badge-warning',
                                    default => 'badge-ghost'
                                };
                                ?>
                                <span class="badge <?= $typeClass ?>"><?= $holiday->type_text ?></span>
                            </td>
                            <td>
                                <?php if ($holiday->recurring): ?>
                                    <span class="badge badge-secondary badge-sm"><?= __('_SI') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-ghost badge-sm"><?= __('_NO') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($holiday->is_active): ?>
                                    <span class="badge badge-success"><?= __('_ACTIVO') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-error"><?= __('_INACTIVO') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <?= $this->Html->link(__('_VER'), ['action' => 'view', $holiday->id], ['class' => 'btn btn-ghost btn-xs']) ?>
                                    <?= $this->Html->link(__('_EDITAR'), ['action' => 'edit', $holiday->id], ['class' => 'btn btn-ghost btn-xs']) ?>
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