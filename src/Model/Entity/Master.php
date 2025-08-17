<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * Entidad Master
 *
 * Representa un usuario master con privilegios administrativos del sistema
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $master_role_id
 * @property bool $is_active
 * @property string|null $allowed_ips
 * @property string|null $two_factor_secret
 * @property bool $two_factor_enabled
 * @property \Cake\I18n\DateTime|null $last_login
 * @property int $login_attempts
 * @property \Cake\I18n\DateTime|null $locked_until
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 *
 * @property \App\Model\Entity\MasterRole $role
 * @property \App\Model\Entity\MasterAccessLog[] $master_access_logs
 */
class Master extends Entity
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
        'email' => true,
        'password' => true,
        'master_role_id' => true,
        'is_active' => true,
        'allowed_ips' => true,
        'two_factor_secret' => true,
        'two_factor_enabled' => true,
        'last_login' => true,
        'login_attempts' => true,
        'locked_until' => true,
        'created' => true,
        'modified' => true,
        'role' => true,
        'master_access_logs' => true,
    ];

    /**
     * Campos que son excluidos de las versiones JSON de la entidad.
     *
     * @var list<string>
     */
    protected array $_hidden = [
        'password',
        'two_factor_secret',
    ];

    /**
     * Hash de contraseña
     *
     * Procesa y hashea automáticamente las contraseñas cuando se asignan
     *
     * @param string $password Contraseña a hashear
     * @return string|null Contraseña hasheada o null si está vacía
     */
    protected function _setPassword(string $password): ?string
    {
        if (strlen($password) > 0) {
            $hasher = new DefaultPasswordHasher();

            return $hasher->hash($password);
        }

        return null;
    }
}
