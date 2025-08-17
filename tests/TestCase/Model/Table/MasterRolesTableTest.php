<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterRolesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterRolesTable Test Case
 */
class MasterRolesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterRolesTable
     */
    protected $MasterRoles;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.MasterRoles',
        'app.Masters',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('MasterRoles') ? [] : ['className' => MasterRolesTable::class];
        $this->MasterRoles = $this->getTableLocator()->get('MasterRoles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->MasterRoles);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\MasterRolesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
