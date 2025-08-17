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
            <?= $this->Html->link(__('_NUEVA_CARACTERISTICA'), ['action' => 'add'],
                ['class' => 'btn btn-primary btn-sm']) ?>
        </div>
    </div>

    <!-- Instrucciones -->
    <div class="alert alert-info mb-4">
        <div class="flex items-center">
            <?= $this->Icon->render('information-circle', 'solid', ['class' => 'w-5 h-5 mr-2']) ?>
            <span><?= __('_ARRASTRA_PARA_REORDENAR_CARACTERISTICAS') ?></span>
        </div>
    </div>

    <div class="card bg-base-100 shadow-lg">
        <div class="card-body p-4">
            <!-- Lista Drag & Drop -->
            <div id="features-sortable" class="space-y-3">
                <?php foreach ($features as $feature): ?>
                    <div class="feature-item bg-base-200 border border-base-300 rounded-lg p-4 cursor-move hover:bg-base-300 transition-colors"
                         data-feature-id="<?= $feature->id ?>">
                        <div class="flex items-center justify-between">
                            <!-- Icono de arrastre -->
                            <div class="flex items-center gap-3">
                                <div class="text-base-content/60">
                                    <?= $this->Icon->render('bars-3', 'solid', ['class' => 'w-5 h-5 drag-handle']) ?>
                                </div>

                                <!-- Información principal -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div>
                                            <h3 class="font-bold text-lg"><?= __($feature->translation_string) ?></h3>
                                            <div class="flex items-center gap-2 mt-1">
                                                <code class="text-sm bg-base-100 px-2 py-1 rounded"><?= h($feature->name)
                                                    ?></code>
                                                <code class="text-sm bg-base-100 px-2 py-1 rounded"><?= h($feature->code) ?></code>
                                                <?php
                                                $typeClass = match ($feature->data_type) {
                                                    'boolean' => 'badge-success',
                                                    'integer' => 'badge-info',
                                                    'string' => 'badge-warning',
                                                    default => 'badge-ghost'
                                                };
                                                ?>
                                                <span class="badge <?= $typeClass ?> badge-sm">
                                                <?= h($feature->data_type) ?>
                                            </span>
                                                <span class="badge  badge-sm">
                                                Posición: <?= number_format($feature->order ?? 0) ?>
                                            </span>
                                                <span class="badge  badge-sm">
                                                Panes: <?= count($feature->plans ?? []) ?>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="flex gap-2">
                                <?= $this->Html->link(
                                    $this->Icon->render('eye', 'solid', ['class' => 'w-4 h-4']),
                                    ['action' => 'view', $feature->id],
                                    [
                                        'class' => 'btn btn-ghost btn-sm',
                                        'escape' => false,
                                        'title' => __('_VER')
                                    ]
                                ) ?>
                                <?= $this->Html->link(
                                    $this->Icon->render('pencil-square', 'solid', ['class' => 'w-4 h-4']),
                                    ['action' => 'edit', $feature->id],
                                    [
                                        'class' => 'btn btn-ghost btn-sm',
                                        'escape' => false,
                                        'title' => __('_EDITAR')
                                    ]
                                ) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Loading indicator -->
    <div id="saving-indicator" class="toast toast-top toast-center" style="display: none;">
        <div class="alert alert-info">
            <span class="loading loading-spinner loading-sm"></span>
            <?= __('_GUARDANDO_ORDEN') ?>...
        </div>
    </div>
</div>

<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sortableList = document.getElementById('features-sortable');
        const savingIndicator = document.getElementById('saving-indicator');

        if (sortableList) {
            const sortable = Sortable.create(sortableList, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'opacity-50',
                chosenClass: 'chosen-item',
                onEnd: function (evt) {
                    // Obtener el nuevo orden
                    const featureIds = Array.from(sortableList.children).map(item =>
                        item.getAttribute('data-feature-id')
                    );

                    // Mostrar indicador de guardado
                    savingIndicator.style.display = 'block';

                    // Enviar via AJAX
                    fetch('<?= $this->Url->build(['action' => 'reorder']) ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-Token': '<?= $this->request->getAttribute('csrfToken') ?>'
                        },
                        body: JSON.stringify({
                            features: featureIds
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            savingIndicator.style.display = 'none';

                            if (data.success) {
                                // Actualizar los números de posición en la interfaz
                                updatePositionNumbers();

                                // Mostrar mensaje de éxito brevemente
                                showToast('success', '<?= __('_ORDEN_ACTUALIZADO_CORRECTAMENTE') ?>');
                            } else {
                                showToast('error', data.message || '<?= __('_ERROR_AL_ACTUALIZAR_ORDEN') ?>');
                                // Revertir el cambio en caso de error
                                //  location.reload();
                            }
                        })
                        .catch(error => {
                            savingIndicator.style.display = 'none';
                            console.error('Error:', error);
                            showToast('error', '<?= __('_ERROR_AL_ACTUALIZAR_ORDEN') ?>');
                            // location.reload();
                        });
                }
            });
        }

        function updatePositionNumbers() {
            const items = sortableList.children;
            Array.from(items).forEach((item, index) => {
                const positionElement = item.querySelector('.text-center .font-semibold');
                if (positionElement) {
                    positionElement.textContent = (index + 1).toLocaleString();
                }
            });
        }

        function showToast(type, message) {
            const toast = document.createElement('div');
            toast.className = `toast toast-top toast-center`;
            toast.innerHTML = `
            <div class="alert alert-${type === 'success' ? 'success' : 'error'}">
                <span>${message}</span>
            </div>
        `;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
    });
</script>

<style>
    .chosen-item {
        box-shadow: 0 0 0 2px hsl(var(--p));
        border-radius: 0.5rem;
    }
</style>
