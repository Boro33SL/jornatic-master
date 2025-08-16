<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\JornaticCore\Model\Entity\Department> $departments
 */
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-base-content"><?= __('_GESTION_DEPARTAMENTOS') ?></h1>
        <?= $this->Html->link(__('_NUEVO_DEPARTAMENTO'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm']) ?>
    </div>

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th><?= __('_NOMBRE') ?></th>
                            <th><?= __('_EMPRESA') ?></th>
                            <th><?= __('_EMPLEADOS') ?></th>
                            <th><?= __('_ESTADO') ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departments as $department): ?>
                        <tr>
                            <td>
                                <div class="font-bold"><?= h($department->name) ?></div>
                                <div class="text-sm opacity-50"><?= h($department->description ?? '') ?></div>
                            </td>
                            <td><?= h($department->company->name ?? '') ?></td>
                            <td><span class="badge badge-primary"><?= count($department->users ?? []) ?></span></td>
                            <td>
                                <?php if ($department->is_active): ?>
                                    <span class="badge badge-success"><?= __('_ACTIVO') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-error"><?= __('_INACTIVO') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <?= $this->Html->link(__('_VER'), ['action' => 'view', $department->id], ['class' => 'btn btn-ghost btn-xs']) ?>
                                    <?= $this->Html->link(__('_EDITAR'), ['action' => 'edit', $department->id], ['class' => 'btn btn-ghost btn-xs']) ?>
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