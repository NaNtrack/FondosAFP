<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\ApiController Test Case
 */
class ApiControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.changes',
        'app.users',
        'app.afps',
        'app.cuotas',
        'app.fondos',
        'app.preferences'
    ];

    /**
     * Test fondos method
     *
     * @return void
     */
    public function testFondosNoType()
    {
        $this->get('/api/fondos?afp=1&fondo=1');
        $this->assertResponseEquals('{"response":{"status":500,"message":"No data"}}');
    }
    
    /**
     * Test fondos method
     *
     * @return void
     */
    public function testFondos()
    {
        $this->get('/api/fondos?afp=1&fondo=1,2&type=valor');
        $this->assertResponseContains('Valor cuota de la AFP Habitat');
    }
}
