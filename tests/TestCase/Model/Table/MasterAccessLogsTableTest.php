<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MasterAccessLogsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MasterAccessLogsTable Test Case
 */
class MasterAccessLogsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MasterAccessLogsTable
     */
    protected $MasterAccessLogs;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.MasterAccessLogs',
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
        $config = $this->getTableLocator()->exists('MasterAccessLogs') ? [] : ['className' => MasterAccessLogsTable::class];
        $this->MasterAccessLogs = $this->getTableLocator()->get('MasterAccessLogs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->MasterAccessLogs);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\MasterAccessLogsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\MasterAccessLogsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
