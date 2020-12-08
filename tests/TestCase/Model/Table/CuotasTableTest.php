<?php
namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableLocator;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CuotasTable Test Case
 */
class CuotasTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CuotasTable
     */
    public $Cuotas;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.cuotas',
        'app.afps',
        'app.changes',
        'app.fondos'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableLocator::exists('Cuotas') ? [] : ['className' => 'App\Model\Table\CuotasTable'];
        $this->Cuotas = TableLocator::get('Cuotas', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Cuotas);

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
            'fecha' => '2016-01-01',
            'valor' => 3655.6,
            'patrimonio' => 3223323,
            'variacion_val' => 1234,
            'varacion_por' => 3
        ];
        $entity = $this->Cuotas->newEntity($data);
        $this->assertEmpty($entity->errors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $entity = new \Cake\ORM\Entity([
            'fondo_id' => 1,
            'afp_id' => 1,
            'fecha' => '2016-01-01',
            'valor' => 12000
        ]);
        $this->Cuotas->save($entity);
        $result = $entity->errors();
        $this->assertEquals([], $result);
    }

    public function testGetCuotasPorcentaje()
    {
        $result = $this->Cuotas->getCuotas(1, [1], '2011-01-01', date('Y-m-d'), 'porcentaje');
        $this->assertEquals(0, count($result));
    }

    public function testGetCuotasValor()
    {
        $result = $this->Cuotas->getCuotas(1, [1], '2011-01-01', date('Y-m-d'), 'valor');
        $this->assertEquals(1, count($result));
    }

    public function testGetCuotasPatrimonio()
    {
        $result = $this->Cuotas->getCuotas(1, [1], '2011-01-01', date('Y-m-d'), 'patrimonio');
        $this->assertEquals(1, count($result));
    }
}
