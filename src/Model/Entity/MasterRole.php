<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Entidad MasterRole
 *
 * Representa un rol de usuario master con permisos específicos
 *
 * @property int $id
 * @property string $name
 *
 * @property \App\Model\Entity\Master[] $masters
 */
class MasterRole extends Entity
{
    /**
     * Campos que pueden ser asignados masivamente usando newEntity() o patchEntity().
     *
     * Nota: cuando '*' está en true, permite que todos los campos no especificados
     * sean asignados masivamente. Por seguridad, se recomienda establecer '*' a false
     * (o eliminarlo), y hacer específicamente accesibles los campos individuales según sea necesario.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'name' => true,
        'masters' => true,
    ];
}
