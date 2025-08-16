<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\JornaticCore\Model\Entity\Contract> $contracts
 */
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-base-content"><?= __('_GESTION_CONTRATOS') ?></h1>
        <?= $this->Html->link(__('_NUEVO_CONTRATO'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm']) ?>
    </div>

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th><?= __('_EMPLEADO') ?></th>
                            <th><?= __('_EMPRESA') ?></th>
                            <th><?= __('_CATEGORIA') ?></th>
                            <th><?= __('_INICIO') ?></th>
                            <th><?= __('_FIN') ?></th>
                            <th><?= __('_ESTADO') ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contracts as $contract): ?>
                        <tr>
                            <td>
                                <div class="font-bold"><?= h($contract->user->name ?? '') ?> <?= h($contract->user->lastname ?? '') ?></div>
                                <div class="text-sm opacity-50"><?= h($contract->user->email ?? '') ?></div>
                            </td>
                            <td><?= h($contract->user->company->name ?? '') ?></td>
                            <td><?= h($contract->professional_category->name ?? '') ?></td>
                            <td><?= $contract->start_date ? $contract->start_date->format('d/m/Y') : '' ?></td>
                            <td><?= $contract->end_date ? $contract->end_date->format('d/m/Y') : __('_INDEFINIDO') ?></td>
                            <td>
                                <?php if ($contract->is_active): ?>
                                    <span class="badge badge-success"><?= __('_ACTIVO') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-error"><?= __('_INACTIVO') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <?= $this->Html->link(__('_VER'), ['action' => 'view', $contract->id], ['class' => 'btn btn-ghost btn-xs']) ?>
                                    <?= $this->Html->link(__('_EDITAR'), ['action' => 'edit', $contract->id], ['class' => 'btn btn-ghost btn-xs']) ?>
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