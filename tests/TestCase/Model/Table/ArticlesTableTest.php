<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ArticlesTable;
use Cake\ORM\TableLocator;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ArticlesTable Test Case
 */
class ArticlesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ArticlesTable
     */
    public $Articles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.articles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableLocator::exists('Articles') ? [] : ['className' => 'App\Model\Table\ArticlesTable'];
        $this->Articles = TableLocator::get('Articles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Articles);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
