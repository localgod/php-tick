<?php

/**
 * Test a class extending php-tick
 *
 * PHP version >=8.0
 *
 * @author   Johannes Skov Frandsen <jsf@greenoak.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
use Localgod\Tick\Manager;

/**
 * Test a class extending php-tick
 *
 * @author Johannes Skov Frandsen <jsf@greenoak.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
class ManagerTest extends PHPUnit\Framework\TestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        Manager::setModelPath(dirname(__FILE__) . '/_testdata/');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Manager::removeAllConnections();
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function removeAllConnectionsOnlyDefault()
    {
        Manager::addDefaultConnectionConfig('sqlite', ':memory:');
        Manager::getStorage();
        $this->assertNotNull($GLOBALS[TickManagerExtension::getUniqueName("default")]);
        Manager::removeAllConnections();
        $this->assertFalse(key_exists("TickConnection:default", $GLOBALS));
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function removeAllConnectionsOnlyNamed()
    {
        Manager::addConnectionConfig('my_connection', 'sqlite', ':memory:');
        Manager::getStorage("my_connection");
        $this->assertNotNull($GLOBALS["TickConnection:my_connection"]);
        $this->assertFalse(key_exists("TickConnection:default", $GLOBALS));
        Manager::removeAllConnections();
        $this->assertFalse(key_exists("TickConnection:my_connection", $GLOBALS));
        $this->assertFalse(key_exists("TickConnection:default", $GLOBALS));
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function removeAllConnectionsBoth()
    {
        Manager::addConnectionConfig('my_connection', 'sqlite', ':memory:');
        Manager::getStorage("my_connection");
        Manager::addDefaultConnectionConfig('sqlite', ':memory:');
        Manager::getStorage();
        $this->assertNotNull($GLOBALS["TickConnection:my_connection"]);
        $this->assertNotNull($GLOBALS["TickConnection:default"]);
        Manager::removeAllConnections();
        $this->assertFalse(key_exists("TickConnection:my_connection", $GLOBALS));
        $this->assertFalse(key_exists("TickConnection:default", $GLOBALS));
    }

    /**
     * Test
     *
     * @tests
     *
     * @return void
     */
    public function defaultIsAddedToGlobalAfterStorageIsRequested()
    {
        Manager::addDefaultConnectionConfig('sqlite', ':memory:');
        Manager::getStorage();
        $this->assertNotNull($GLOBALS["TickConnection:default"]);
        $this->assertTrue($GLOBALS["TickConnection:default"] instanceof Localgod\Tick\Storage\SqlStorage);
    }

    /**
     * Test
     *
     * @tests
     * @expectedException InvalidArgumentException
     *
     * @return void
     */
    public function throwsExceptionOnWrongName()
    {
        Manager::getStorage("qwe");
    }

    /**
     * Test
     *
     * @tests
     * @expectedException InvalidArgumentException
     *
     * @return void
     */
    public function setNonExistingModelPathFails()
    {
        Manager::setModelPath("/this-should-not/exist/");
    }

    /**
     * Test
     *
     * @tests
     *
     * @return void
     */
    public function setAndGetExistingModelPathFails()
    {
        Manager::setModelPath("./");
        $this->assertEquals("./", Manager::getModelPath("./"));
    }

    /**
     * Test
     *
     * @tests
     *
     * @return void
     */
    public function defaultIsNotAddedToGlobalBeforeStorageIsRequested()
    {
        Manager::addDefaultConnectionConfig('sqlite', ':memory:');
        $this->assertFalse(key_exists("TickConnection:default", $GLOBALS));
    }
}
require_once dirname(__FILE__) . '/_testdata/TickManagerExtension.php';
