<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\FacebookController Test Case
 */
class FacebookControllerTest extends IntegrationTestCase
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
     * Test login method when the user is already logged in
     *
     * @return void
     */
    public function testLoginAlreadyLoggedIn()
    {
        $this->session(['Auth.User' => [
            'id' => 1,
        ]]);
        $this->get('/facebook_login');
        $this->assertRedirect(['controller' => 'Dashboard', 'action' => 'index']);
    }

    /**
     * Test login method
     *
     * @return void
     */
    public function testLogin()
    {
        $this->session(['Auth.User' => null]);
        $this->get('/facebook_login');
        $this->assertRedirect();
    }

    /**
     * Test callback method when the user is already logged in
     *
     * @return void
     */
    public function testCallbackAlreadyLoggedIn()
    {
        $this->session(['Auth.User' => [
            'id' => 1,
        ]]);
        $this->get('/facebook/callback');
        $this->assertRedirect(['controller' => 'Dashboard', 'action' => 'index']);
    }

    /**
     * Test callback method
     *
     * @return void
     */
    public function testCallback()
    {
        $this->session(['Auth.User' => null]);
        $this->get('/facebook/callback?code=123');
        $this->assertRedirect(['controller' => 'Pages', 'action' => 'display', 'home']);
    }
}
