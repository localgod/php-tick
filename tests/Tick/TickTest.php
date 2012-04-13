<?php
/**
 * Test Record
 *
 * PHP Version 5.1.2
 *
 * @category Test
 * @package  Test
 * @author	 Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 * @since	 2011-04-17
 */
/**
 * Test Record
 *
 * @category Test
 * @package	 Test
 * @author   Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license	 http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 */
class TickTest extends PHPUnit_Framework_TestCase {
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp() {

		TickManager::setModelPath(dirname(__FILE__).'/../_testdata/');
		TickManager::addDefaultConnectionConfig('sqlite', ':memory:');
		$dbPath = dirname(__FILE__).'/../_testdata/test.sqlite';
		touch($dbPath);
		TickManager::addConnectionConfig('tick_connection', 'sqlite', $dbPath);

		$storage = TickManager::getStorage();
		$storage->getConnection()->exec(file_get_contents(dirname(__FILE__).'/../_testdata/schema.sql'));
		$storage = TickManager::getStorage("tick_connection");
		$storage->getConnection()->exec(file_get_contents(dirname(__FILE__).'/../_testdata/schema.sql'));
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown() {
		$dbPath = dirname(__FILE__).'/../_testdata/test.sqlite';
		unlink($dbPath);
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function writeToOneConnectioAndMakeSureItISNotPresentInOtherConnection() {
		$obj1 = new TickExtension();
		$obj1->setLatitude(12.9);
		$obj1->setLongitude(12.8);
		$obj1->save();
		$obj1 = new TickExtension();

		$this->assertEquals(1, $obj1->get()->count());
		$obj2 = new TickExtension2();
		$this->assertEquals(0, $obj2->get()->count());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function switchCollectionRuntime() {
		$obj1 = new TickExtension();
		$obj1->setLatitude(12.9);
		$obj1->setLongitude(12.8);
		$obj1->save();
		$obj1 = new TickExtension();

		$this->assertEquals(1, $obj1->get()->count());

		$obj1 = new TickExtension();
		$obj1->setCollectionName('tick_collection2');
		$res1 = $obj1->get(array());
		$this->assertEquals(0, $obj1->get()->count());
		$obj1->resetCollectionName();
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function switchConnectionRuntime() {
		$obj1 = new TickExtension();
		$obj1->setLatitude(12.9);
		$obj1->setLongitude(12.8);
		$obj1->save();
		$obj1 = new TickExtension();
		$res = $obj1->get(array());

		$this->assertEquals(1, sizeof($res));

		$obj2 = new TickExtension2();
		$this->assertEquals("tick_connection", $obj2->getConnectionName());
		$obj2->setConnectionName('default');
		$this->assertEquals("default", $obj2->getConnectionName());
		$res2 = $obj2->get(array());
		$this->assertEquals(1, sizeof($res2));
		$obj2->resetCollectionName();
	}
}