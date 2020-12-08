<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\ChangesController Test Case
 */
class ChangesControllerTest extends IntegrationTestCase
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
    public function testRedirectLogin()
    {
        $this->get('/cambios');
        $this->assertRedirect('/');
    }

    public function testIndex()
    {
        $this->session(['Auth.User' => [
            'id' => 1,
            'preference' => [
                'afp_id' => 1,
                'fondo_id' => 1
            ]
        ]]);
        $this->get('/cambios');
        $this->assertResponseContains('Cambios');
        //$this->assertResponseContains('Cargar Certificado');
        $this->assertResponseContains('Historial');
    }

    public function testPostIndex()
    {
        $this->session(['Auth.User' => [
            'id' => 1,
            'preference' => [
                'afp_id' => 1,
                'fondo_id' => 1
            ]
        ]]);
        $this->post('/cambios', [
            'upload' => [
                'tmp_name' => dirname(__DIR__) .'/../docs/CertificadoAfpHabitat.pdf'
            ]
        ]);

        $this->assertRedirect(['controller' => 'Changes', 'action' => 'index', '?' => ['t' => 'cargar']]);
    }

    public function testPostIndexInvalidFile()
    {
        $this->session(['Auth.User' => [
            'id' => 1,
            'preference' => [
                'afp_id' => 1,
                'fondo_id' => 1
            ]
        ]]);
        $this->post('/cambios', [
            'upload' => [
                'tmp_name' => ''
            ]
        ]);

        $this->assertRedirect(['controller' => 'Changes', 'action' => 'index', '?' => ['t' => 'cargar']]);
    }
}
