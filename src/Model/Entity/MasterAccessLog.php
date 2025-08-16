<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterAccessLog Entity
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
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
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
