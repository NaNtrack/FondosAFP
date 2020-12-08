<?php
namespace App\Test\TestCase\Shell;

use App\Shell\ImportarShell;
use Cake\TestSuite\TestCase;

/**
 * App\Shell\ImportarShell Test Case
 */
class ImportarShellTest extends TestCase
{

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Test subject
     *
     * @var \App\Shell\ImportarShell
     */
    public $Importar;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $this->Importar = new ImportarShell($this->io);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Importar);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain()
    {
        $count = $this->Importar->main();
        $this->assertTrue($count > 0);
    }
}
