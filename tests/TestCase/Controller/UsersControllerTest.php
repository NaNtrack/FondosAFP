<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\UsersController Test Case
 */
class UsersControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.preferences',
        'app.users',
        'app.changes'
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/users');
        $this->assertResponseError();
    }

    public function testPostLoginAlreadyLogin()
    {
        $this->session(['Auth.User.id' => 1]);
        $this->get('/');
        $this->assertRedirect(['controller' => 'Dashboard', 'action' => 'index']);
        $this->get('/logout');
    }

    public function testPostSavePreferencesExists()
    {
        $this->session(['Auth.User.id' => 1]);
        $this->post('/users/save-preferences',[
            'afp' => 1,
            'fondo' => 1
        ]);
    }

    public function testPostSavePreferences()
    {
        $this->session(['Auth.User.id' => 2]);
        $this->post('/users/save-preferences',[
            'afp' => 1,
            'fondo' => 1
        ]);
    }

    public function testGetSavePreferences()
    {
        $this->session(['Auth.User.id' => 1]);
        $this->get('/users/save-preferences');
        $this->assertResponseError();
    }

}
