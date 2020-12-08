<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\DashboardController Test Case
 */
class DashboardControllerTest extends IntegrationTestCase
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
            'picture' => 'todo',
            'first_name' => 'todo',
            'last_name' => 'todo',
            'email' => 'todo',
            'gender' => 'm',
            'preference' => [
                'afp_id' => 1,
                'fondo_id' => 1
            ]
        ]]);
        $this->get('/dashboard');
        $this->assertResponseContains('Perfil');
    }
}
