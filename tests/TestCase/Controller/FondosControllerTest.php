<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\FondosController Test Case
 */
class FondosControllerTest extends IntegrationTestCase
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
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->session(['Auth.User' => [
            'id' => 1,
            'preference' => [
                'afp_id' => 1,
                'fondo_id' => 1,
                'homepage' => 'dashboard',
                'mostrar_otras_afps' => 1,
                'event_fondos_up' => 0,
                'event_fondos_down' => 0,
                'resumen_semanal' => 0,
                'event_ranking_up' => 0,
                'event_ranking_down' => 0,
                'event_new_follow' => 0,
            ]
        ]]);
        $this->get('/fondos');
        $this->assertResponseContains('Habitat');
    }
}
