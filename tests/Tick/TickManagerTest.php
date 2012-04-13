<?php

/**
 * Test a class extending php-tick
 *
 * PHP Version 5.1.2
 *
 * @category Test
 * @package  Test
 * @author	 Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 * @since	 2011-04-09
 */
/**
 * Test a class extending php-tick
 *
 * @category Test
 * @package	 Test
 * @author	 Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license	 http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 */
class TickManagerTest extends PHPUnit_Framework_TestCase {
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp() {
		TickManager::setModelPath(dirname(__FILE__) . '/../_testdata/');
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown() {
		TickManager::removeAllConnections();
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function removeAllConnectionsOnlyDefault() {
		TickManager::addDefaultConnectionConfig('sqlite', ':memory:');
		TickManager::getStorage();
		$this->assertNotNull($GLOBALS[TickManagerExtension::getUniqueName("default")]);
		TickManager::removeAllConnections();
		$this->assertFalse(key_exists("TickConnection:default", $GLOBALS));
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function removeAllConnectionsOnlyNamed() {
		TickManager::addConnectionConfig('my_connection', 'sqlite', ':memory:');
		TickManager::getStorage("my_connection");
		$this->assertNotNull($GLOBALS["TickConnection:my_connection"]);
		$this->assertFalse(key_exists("TickConnection:default", $GLOBALS));
		TickManager::removeAllConnections();
		$this->assertFalse(key_exists("TickConnection:my_connection", $GLOBALS));
		$this->assertFalse(key_exists("TickConnection:default", $GLOBALS));
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function removeAllConnectionsBoth() {
		TickManager::addConnectionConfig('my_connection', 'sqlite', ':memory:');
		TickManager::getStorage("my_connection");
		TickManager::addDefaultConnectionConfig('sqlite', ':memory:');
		TickManager::getStorage();
		$this->assertNotNull($GLOBALS["TickConnection:my_connection"]);
		$this->assertNotNull($GLOBALS["TickConnection:default"]);
		TickManager::removeAllConnections();
		$this->assertFalse(key_exists("TickConnection:my_connection", $GLOBALS));
		$this->assertFalse(key_exists("TickConnection:default", $GLOBALS));
	}

	/**
	 * Test
	 *
	 * @tests
	 * @return void
	 */
	public function defaultIsAddedToGlobalAfterStorageIsRequested() {
		TickManager::addDefaultConnectionConfig('sqlite', ':memory:');
		TickManager::getStorage();
		$this->assertNotNull($GLOBALS["TickConnection:default"]);
		$this->assertTrue($GLOBALS["TickConnection:default"] instanceof SqlStorage);
	}

	/**
	 * Test
	 *
	 * @tests
	 * @expectedException InvalidArgumentException
	 * @return void
	 */
	public function throwsExceptionOnWrongName() {
		TickManager::getStorage("qwe");
	}

	/**
	 * Test
	 *
	 * @tests
	 * @expectedException InvalidArgumentException
	 * @return void
	 */
	public function setNonExistingModelPathFails() {
		TickManager::setModelPath("/this-should-not/exist/");
	}

	/**
	 * Test
	 *
	 * @tests
	 * @return void
	 */
	public function setAndGetExistingModelPathFails() {
		TickManager::setModelPath("./");
		$this->assertEquals("./", TickManager::getModelPath("./"));
	}

	/**
	 * Test
	 *
	 * @tests
	 * @return void
	 */
	public function defaultIsNotAddedToGlobalBeforeStorageIsRequested() {
		TickManager::addDefaultConnectionConfig('sqlite', ':memory:');
		$this->assertFalse(key_exists("TickConnection:default", $GLOBALS));
	}

}