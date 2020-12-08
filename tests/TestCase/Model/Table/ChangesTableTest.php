<?php
namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableLocator;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ChangesTable Test Case
 */
class ChangesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ChangesTable
     */
    public $Changes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.changes',
        'app.users',
        'app.preferences',
        'app.afps',
        'app.cuotas',
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
        $config = TableLocator::exists('Changes') ? [] : ['className' => 'App\Model\Table\ChangesTable'];
        $this->Changes = TableLocator::get('Changes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Changes);

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
            'from_value' => 3445.5,
            'to_value' => 3655.6,
            'profits_loss' => 322
        ];
        $entity = $this->Changes->newEntity($data);
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
            'user_id' => 1,
            'afp_id' => 1,
            'from_fondo_id' => 1,
            'to_fondo_id' => 2
        ]);
        $this->Changes->save($entity);
        $result = $entity->errors();
        $this->assertEquals([], $result);
    }

    /**
     * Test validateDate method
     *
     * @return void
     */
    public function testValidateDate()
    {
        $invalid = $this->Changes->validateDate('2015-12-23');
        $this->assertFalse($invalid);

        $valid = $this->Changes->validateDate('23/12/2015');
        $this->assertTrue($valid);
    }

    /**
     * @expectedException \Exception
     */
    public function testCargarMovimientosInvalidAFP()
    {
        $this->Changes->cargarMovimientos(1, '/etc/hosts', 'INVALID');
    }

    /**
     * @expectedException \Exception
     */
    public function testCargarMovimientosInvalidFile()
    {
        $this->Changes->cargarMovimientos(1, 'invalid');
    }

    public function testCargarMovimientos() {
        $filename = dirname(__DIR__) .'/../../docs/CertificadoAfpHabitat.pdf';
        $result = $this->Changes->cargarMovimientos(1, $filename);
        $expected = [
            'date' => '21 de agosto de 2016',
            'fullname' => 'JULIO EDUARDO ARAYA CERDA',
            'rut' => '15.055.391-1'
        ];
        $this->assertEquals($expected, $result['data']);
        $this->assertEquals(100, count($result['movimientos']));
    }

    public function testGuardarCambios()
    {
        $filename = dirname(__DIR__) .'/../../docs/CertificadoAfpHabitat.pdf';
        $movimientos = $this->Changes->cargarMovimientos(1, $filename);
        $result = $this->Changes->guardarCambios(1, 'HABITAT', $movimientos['movimientos']);
        $this->assertEquals(6, count($result));
    }

    public function testFindCuotaDt()
    {
        $result = $this->Changes->findCuotaDt(1, 1, 1.5, '2016-08-30');
        $this->assertEquals('2016-08-29', $result);
    }

    public function testFindCuotaValue()
    {
        $result = $this->Changes->findCuotaValue(1, 1, '2016-08-29');
        $this->assertEquals(1.5, $result);
    }

    public function testCalculateProfitOrLossSameFondo()
    {
        $result = $this->Changes->calculateProfitOrLoss([
            'from_fondo_id' => 1,
            'to_fondo_id' => 1
        ]);
        $this->assertEquals(0, $result);
    }

    public function testCalculateProfitOrLoss()
    {
        $result = $this->Changes->calculateProfitOrLoss([
            'user_id' => 1,
            'afp_id' => 1,
            'from_fondo_id' => 2,
            'to_fondo_id' => 1,
            'cuota_dt' => '2016-08-30',
            'from_value' => 10
        ]);
        $this->assertEquals(8.2, $result);
    }

    public function testGetLatestChange()
    {
        $result = $this->Changes->getLatestChange(1, 1);
        $this->assertEquals(1332.0, $result[0]['monto']);
    }
}
