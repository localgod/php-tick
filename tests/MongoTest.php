<?php
/**
 * Test a class extending php-tick
 *
 * PHP Version 5.1.2
 *
 * @category Test
 * @package  Test
 * @author   Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://code.google.com/p/php-tick/ php-tick
 * @since    2011-04-09
 */
/**
 * Test a class extending php-tick
 *
 * @category   Test
 * @package    Test
 * @subpackage Test
 * @author     Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://code.google.com/p/php-tick/ php-tick
 */
class MUserTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Start a mongo server if we can
	 *
	 * @return void
	 */
	public function __construct() {
		
		if (extension_loaded('mongo')) {
			shell_exec('mkdir -p mongodb/db && mkdir mongodb/log');
			shell_exec('mongod  --fork --port 27088 --dbpath mongodb/db --logpath mongodb/log/mongo.log');
		}
	}

	/**
	 * Kill a mongo server if we can
	 *
	 * @return void
	 */
	public function __destruct() {
		
		if (extension_loaded('mongo')) {
			if (file_exists('mongodb')) {
				$pid = shell_exec('cat mongodb/db/mongod.lock');
				shell_exec('kill '.$pid);
				sleep(1);
				shell_exec('rm -rf mongodb');
			}
		}
		
	}
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		if (shell_exec('mongod --version | grep "db version"') == '') {
			$this->markTestSkipped('The mongod execuatable is not available.');
		}
		if (!extension_loaded('mongo')) {
			$this->markTestSkipped('The mongo extension is not available.');
		}

		TickManager::addDefaultConnectionConfig('mongodb', 'db', null, null, '127.0.0.1', 27088);
		TickManager::setModelPath(dirname(__FILE__).'/_testdata/');
		shell_exec('mongoimport --port 27088 --drop --db db --collection users --file '.dirname(__FILE__).'/_testdata/fixture.json');
		sleep(2);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
		TickManager::removeAllConnections();
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function getConnection()
	{
		$this->assertTrue(TickManager::getStorage() instanceof Storage);
		$this->assertTrue(TickManager::getStorage() instanceof MongoStorage);
	}

	/**
	 * Test
	 * 
	 * @return void
	 */
	public function storeNew()
	{
		$now = new DateTime();
		$user = new MUser();
		$user->setCreated($now);
		echo 'SAVING'."\n";
		$user->save();
		$user = User::getById(4);
		$this->assertEquals($now, $user->getCreated());
	}

	/**
	 * Test
	 *
	 * @return void
	 */
	public function update()
	{
		$now = new DateTime();
		$user = new User();
		$user = User::getById(1);
		$user->setCreated($now);
		$user->save();
		$user = User::getById(1);
		$this->assertEquals($now, $user->getCreated());
	}

	/**
	 * Test
	 *
	 * @return void
	 */
	public function remove()
	{
		$user = new User();
		$user = User::getById(1);
		$user->remove();
		$user = User::getById(1);
		$this->assertEquals(null, $user);
	}
}