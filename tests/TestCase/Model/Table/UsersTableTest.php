<?php
namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableLocator;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\UsersTable
     */
    public $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.users',
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
        $config = TableLocator::exists('Users') ? [] : ['className' => 'App\Model\Table\UsersTable'];
        $this->Users = TableLocator::get('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Users);

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
            'email' => 'todo@todo.com',
            'role' => 'admin'
        ];
        $entity = $this->Users->newEntity($data);
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
            'email' => 'email@domain.com'
        ]);
        $this->Users->save($entity);
        $result = $entity->errors();
        $this->assertEquals([], $result);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetLoginDatesNoUser()
    {
        $data = [
            'id' => 3
        ];
        $this->Users->setLoginDates($data);
    }

    public function testSetLogin()
    {
        $data = [
            'id' => 2,
        ];

        $model = $this->getMockForModel('Users', ['getMailer']);

        $emailer = new \Cake\Mailer\Email();
        $emailer->transport('debug');
        $model->expects($this->once())
            ->method('getMailer')
            ->will($this->returnValue($emailer));

        $model->setLoginDates($data);
    }
}
