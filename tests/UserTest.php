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
class UserTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		TickManager::addDefaultConnectionConfig('sqlite', ':memory:');
		TickManager::setModelPath(dirname(__FILE__).'/_testdata/');
		$storage = TickManager::getStorage();
		$storage->getConnection()->exec(file_get_contents(dirname(__FILE__).'/_testdata/schema.sql'));
		$storage->getConnection()->exec(file_get_contents(dirname(__FILE__).'/_testdata/fixture.sql'));
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
	public function createNew()
	{
		$user = new User();
		$this->assertEquals('Jane', $user->getFirstname());
		$this->assertEquals('Doe Doe', $user->getLastname());
		$this->assertEquals('', $user->getCreated());
		$this->assertEquals('', $user->getOwner());
	}

	/**
	 * Test
	 *
	 * @test
	 * @expectedException RangeException
	 * @return void
	 */
	public function setIdWidthToBigANumber()
	{
		$user = new User();
		$user->setId(123456789101);
	}

	/**
	 * Test
	 *
	 * @test
	 * @expectedException RangeException
	 * @return void
	 */
	public function setWithToLongString()
	{
		$user = new User();
		$longString = str_pad('', 256, 'D');
		$user->setFirstname($longString);
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function storeNew()
	{
		$now = new DateTime();
		$user = new User();
		$user->setCreated($now);
		$user->save();
		$user = User::getById(4);
		$this->assertEquals($now, $user->getCreated());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function update()
	{
		$now = new DateTime();
		$user = User::getById(1);
		$user->setCreated($now);
		$user->save();
		$user = User::getById(1);
		$this->assertEquals($now, $user->getCreated());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function remove()
	{
		$user = User::getById(1);
		$user->remove();
		$user = User::getById(1);
		$this->assertEquals(null, $user);
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function getWithOneCriteria()
	{
		$user = new User();
		$this->assertEquals($user->get()->whereEquals('id', 1)->current()->getFirstname(), 'John');
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function getWithTwoCriterias()
	{
		$user = new User();
		$this->assertEquals($user->get()->where('firstname', '=', 'John')->where('id', '=', 1)->current()->getFirstname(), 'John');
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function getAll()
	{
		$user = new User();
		$res = $user->get();
		$this->assertEquals($user->get()->current()->getFirstname(), 'John');
		$this->assertEquals($user->get()->count(), 3);
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function getAllWithLimit()
	{
		$user = new User();
		$this->assertEquals($user->get()->limit(1)->current()->getFirstname(), 'John');
		$this->assertEquals($user->get()->limit(2)->count(), 2);
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function getAllWithLimitAndOffset()
	{
		$user = new User();
		$this->assertEquals($user->get()->limit(2)->offset(1)->current()->getFirstname(), 'Jonny');
		$this->assertEquals($user->get()->limit(2)->offset(1)->count(), 2);
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function getAllOrderByDESC()
	{
		$user = new User();
		$this->assertEquals($user->get()->orderBy(array('firstname'), false)->current()->getFirstname(), 'Jonny');
		$this->assertEquals($user->get()->orderBy(array('firstname'), false)->count(), 3);
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function getAllOrderByASC()
	{
		$user = new User();
		$this->assertEquals($user->get()->orderBy(array('firstname'), true)->current()->getFirstname(), 'Jacob');
		$this->assertEquals($user->get()->orderBy(array('firstname'), true)->count(), 3);
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function getAllOrderByTwoProperties()
	{
		$user = new User();
		$this->assertEquals($user->get()->orderBy(array('firstname', 'lastname'), true)->current()->getFirstname(), 'Jacob');
		$this->assertEquals($user->get()->orderBy(array('firstname', 'lastname'), true)->count(), 3);
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function getAllOrderByNoDirection()
	{
		$user = new User();
		$this->assertEquals($user->get()->orderBy(array('firstname', 'lastname'))->current()->getFirstname(), 'Jacob');
		$this->assertEquals($user->get()->orderBy(array('firstname', 'lastname'))->count(), 3);
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function getWithBiggerThanCriterias()
	{
		$user = new User();
		$this->assertEquals($user->get()->where('id', '>', 2)->current()->getFirstname(), 'Jacob');

	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function getFloatValue()
	{
		$user = new User();
		$this->assertEquals($user->get()->whereEquals('id', 1)->current()->getLatitude(), 43.8801);
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function setFloatValue()
	{
		$user = new User();
		$user = $user->get()->whereEquals('id', 1)->current();
		$user->setLatitude(43.8802);
		$user->save();
		$user = new User();
		$user = $user->get()->whereEquals('id', 1)->current();
		$this->assertEquals($user->getLatitude(), 43.8802);
	}


	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function setNegativeFloatValue()
	{
		$user = new User();
		$user = $user->get()->whereEquals('id', 1)->current();
		$user->setLongitude(-75.6840);
		$user->save();
		$user = new User();
		$user = $user->get()->whereEquals('id', 1)->current();
		$this->assertEquals($user->getLongitude(), -75.6840);
	}

	/**
	 * Utility method to debug tests.
	 *
	 * @return void
	 */
	private function dump() {
		$u = new User();

		print("\n|User ID   |First name|Last name |Created   |Owner     |Latitude  |Longitude |");
		print("\n------------------------------------------------------------------------------");
		foreach ($u->get() as $m) {
			printf("\n|%10s|%10s|%10s|%10s|%10s|%10f|%10f|", $m->getId(), $m->getFirstname(), $m->getLastname(), $m->getCreated()->format('Y'), $m->getOwner(), $m->getLatitude(), $m->getLongitude());
		}
		print("\n");
	}
}