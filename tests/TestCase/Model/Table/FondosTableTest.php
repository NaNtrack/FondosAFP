<?php
namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableLocator;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FondosTable Test Case
 */
class FondosTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\FondosTable
     */
    public $Fondos;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.fondos',
        'app.cuotas',
        'app.afps',
        'app.changes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableLocator::exists('Fondos') ? [] : ['className' => 'App\Model\Table\FondosTable'];
        $this->Fondos = TableLocator::get('Fondos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Fondos);

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
            'name' => 'A',
            'description' => 'Fondo A',
            'api_name' => 'a',
            'country' => 'CL',
            'status' => 1
        ];
        $entity = $this->Fondos->newEntity($data);
        $this->assertEmpty($entity->errors());
    }
}
