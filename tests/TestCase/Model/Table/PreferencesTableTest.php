<?php
namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableLocator;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PreferencesTable Test Case
 */
class PreferencesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PreferencesTable
     */
    public $Preferences;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.preferences',
        'app.users',
        'app.changes',
        'app.afps',
        'app.cuotas',
        'app.fondos',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableLocator::exists('Preferences') ? [] : ['className' => 'App\Model\Table\PreferencesTable'];
        $this->Preferences = TableLocator::get('Preferences', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Preferences);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $data = [];
        $entity = $this->Preferences->newEntity($data);
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
            'fondos_id' => 1
        ]);
        $this->Preferences->save($entity);
        $result = $entity->errors();
        $this->assertEquals([], $result);
    }
}
