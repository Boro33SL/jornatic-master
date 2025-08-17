<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Master Entity
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
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
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
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var list<string>
     */
    protected array $_hidden = [
        'password',
        'two_factor_secret',
    ];

    /**
     * Password hashing
     *
     * @param string $password Password to hash
     * @return string|null
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
