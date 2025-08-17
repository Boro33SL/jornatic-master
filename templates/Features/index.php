<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\JornaticCore\Model\Entity\Feature> $features
 */
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-base-content"><?= __('_GESTION_CARACTERISTICAS') ?></h1>
        <div class="flex gap-2">
            <?= $this->Html->link(__('_NUEVA_CARACTERISTICA'), ['action' => 'add'], ['class' => 'btn btn-primary btn-sm']) ?>
            <?= $this->Html->link(__('_VER_USO'), ['action' => 'usage'], ['class' => 'btn btn-secondary btn-sm']) ?>
        </div>
    </div>

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th><?= __('_NOMBRE') ?></th>
                            <th><?= __('_CODIGO') ?></th>
                            <th><?= __('_TIPO_DATO') ?></th>
                            <th><?= __('_POSICION') ?></th>
                            <th><?= __('_PLANES') ?></th>
                            <th class="text-center"><?= __('_ACCIONES') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($features as $feature): ?>
                        <tr>
                            <td>
                                <div class="font-bold"><?= h($feature->name) ?></div>
                                <?php if ($feature->icon): ?>
                                    <div class="text-sm opacity-50"><?= h($feature->icon) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><code class="text-sm"><?= h($feature->code) ?></code></td>
                            <td>
                                <?php
                                $typeClass = match($feature->data_type) {
                                    'boolean' => 'badge-success',
                                    'integer' => 'badge-info',
                                    'string' => 'badge-warning',
                                    default => 'badge-ghost'
                                };
                                ?>
                                <span class="badge <?= $typeClass ?>"><?= h($feature->data_type) ?></span>
                            </td>
                            <td><?= number_format($feature->order ?? 0) ?></td>
                            <td><span class="badge badge-primary"><?= count($feature->plans ?? []) ?></span></td>
                            <td>
                                <div class="flex justify-center gap-1">
                                    <?= $this->Html->link(__('_VER'), ['action' => 'view', $feature->id], ['class' => 'btn btn-ghost btn-xs']) ?>
                                    <?= $this->Html->link(__('_EDITAR'), ['action' => 'edit', $feature->id], ['class' => 'btn btn-ghost btn-xs']) ?>
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