<?php
namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableLocator;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AfpsTable Test Case
 */
class AfpsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\AfpsTable
     */
    public $Afps;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.afps',
        'app.changes',
        'app.cuotas'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableLocator::exists('Afps') ? [] : ['className' => 'App\Model\Table\AfpsTable'];
        $this->Afps = TableLocator::get('Afps', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Afps);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $data = [
            'name' => 'Name',
            'api_name' => 'name',
            'country' => 'CL',
            'status' => 1
        ];
        $entity = $this->Afps->newEntity($data);
        $this->assertEmpty($entity->errors());
    }
}
