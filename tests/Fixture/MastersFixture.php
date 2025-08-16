<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MastersFixture
 */
class MastersFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'email' => 'Lorem ipsum dolor sit amet',
                'password' => 'Lorem ipsum dolor sit amet',
                'role' => 'Lorem ipsum dolor sit amet',
                'is_active' => 1,
                'allowed_ips' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'two_factor_secret' => 'Lorem ipsum dolor sit amet',
                'two_factor_enabled' => 1,
                'last_login' => '2025-08-16 15:32:57',
                'login_attempts' => 1,
                'locked_until' => '2025-08-16 15:32:57',
                'created' => '2025-08-16 15:32:57',
                'modified' => '2025-08-16 15:32:57',
            ],
        ];
        parent::init();
    }
}
