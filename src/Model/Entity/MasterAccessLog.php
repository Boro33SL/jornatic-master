<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Entidad MasterAccessLog
 *
 * Representa un registro de acceso y actividad de usuarios master
 *
 * @property int $id
 * @property int $master_id
 * @property string $ip_address
 * @property string|null $user_agent
 * @property string $action
 * @property string|null $resource
 * @property int|null $resource_id
 * @property string|null $details
 * @property bool $success
 * @property \Cake\I18n\DateTime $created
 *
 * @property \App\Model\Entity\Master $master
 */
class MasterAccessLog extends Entity
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
        'master_id' => true,
        'ip_address' => true,
        'user_agent' => true,
        'action' => true,
        'resource' => true,
        'resource_id' => true,
        'details' => true,
        'success' => true,
        'created' => true,
        'master' => true,
    ];
}
