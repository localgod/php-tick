<?php
/**
 * Test the Result class
 *
 * PHP Version 5.1.2
 *
 * @category Test
 * @package  Test
 * @author	Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	  http://code.google.com/p/php-tick/ php-tick
 * @since	 2011-11-16
 */
/**
 * Test the Result class
 *
 * @category	Test
 * @package	 Test
 * @subpackage Test
 * @author	  Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @license	 http://www.opensource.org/licenses/mit-license.php MIT
 * @link		 http://code.google.com/p/php-tick/ php-tick
 */
class ResultTest extends PHPUnit_Framework_TestCase {
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp() {
		TickManager::addDefaultConnectionConfig('sqlite', ':memory:');
		TickManager::setModelPath(dirname(__FILE__) . '/../_testdata/');
		$storage = TickManager::getStorage();
		$storage->getConnection()->exec(file_get_contents(dirname(__FILE__) . '/../_testdata/schema.sql'));
		$storage->getConnection()->exec(file_get_contents(dirname(__FILE__) . '/../_testdata/fixture.sql'));
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
	public function resultObjectWithoutClauses() {
		$result = new Result("User");
		$this->assertEquals(3, $result->count());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithLimitClause() {
		$result = new Result("User");
		$result->limit(1);
		$this->assertEquals(1, $result->count());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithOffsetClause() {
		//This test requires that phpunit has convertWarningsToExceptions
		try {
			$result = new Result("User");
			$result->offset(1);
			$this->assertEquals(2, $result->count());
		} catch (Exception $e) {
			$this->assertEquals('Limit was not specifically set, so Tick defaulted to 10000', $e->getMessage());
		}
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithOffsetAndLimitClause() {
		$result = new Result("User");
		$result->limit(3)->offset(1);
		$this->assertEquals(2, $result->count());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithOrderByClauseWithOnePropertyAndNoDirection() {
		$result = new Result("User");
		$result->orderBy(array('firstname'));
		$this->assertEquals('Jacob', $result->current()->getFirstname());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithOrderByClauseWithOnePropertyAndASCDirection() {
		$result = new Result("User");
		$result->orderBy(array('firstname'), true);
		$this->assertEquals('Jacob', $result->current()->getFirstname());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithOrderByClauseWithOnePropertyAndDESCDirection() {
		$result = new Result("User");
		$result->orderBy(array('firstname'), false);
		$this->assertEquals('Jonny', $result->current()->getFirstname());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithOrderByClauseWithTwoProperties() {
		$result = new Result("User");
		$result->orderBy(array('firstname', 'lastname'));
		$this->assertEquals('Jacob', $result->current()->getFirstname());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithOrderByClauseWithOnePropertyAndLimit() {
		$result = new Result("User");
		$result->orderBy(array('firstname'))->limit(1);
		$this->assertEquals('Jacob', $result->current()->getFirstname());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithOrderByClauseWithOnePropertyAndLimitAndOffset() {
		$result = new Result("User");
		$result->orderBy(array('firstname'))->limit(1)->offset(1);
		$this->assertEquals('John', $result->current()->getFirstname());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithWhereClause() {
		$result = new Result("User");
		$result->where('firstname', '=', 'John');
		$this->assertEquals('John', $result->current()->getFirstname());
	}
	
	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithWhereEqualsClause() {
		$result = new Result("User");
		$result->whereEquals('firstname', 'John');
		$this->assertEquals('John', $result->current()->getFirstname());
	}
	
	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithWhereBetweenClause() {
		$result = new Result("User");
		$result->whereBetween('id', 1, 3);
		$this->assertEquals('Jonny', $result->current()->getFirstname());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function iterateOverResult() {
		$result = new Result("User");
		$count = 0;
		foreach ($result as $obj) {
			$this->assertInstanceOf("User", $obj);
			$count++;
		}
		$this->assertEquals(3, $count);
	}
	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function resultObjectWithOutAnyHit() {
		$result = new Result("User");
		$result->whereEquals('firstname', 'Kurt');
		$this->assertEquals(null, $result->current());
	}
}