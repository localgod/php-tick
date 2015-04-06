<?php

/**
 * Test the Result class
 *
 * PHP version >=5.3.3
 *
 * @author	 Johannes Skov Frandsen <localgod@heaven.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 */
use Localgod\Tick\Manager;
use Localgod\Tick\Result;
/**
 * Test the Result class
 *
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
class ResultTest extends PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        Manager::addDefaultConnectionConfig('sqlite', ':memory:', null, null, '127.0.0.1', null, array(PDO::ATTR_PERSISTENT => true));
        Manager::setModelPath(dirname(__FILE__) . '/../_testdata/');
        $storage = Manager::getStorage();
        $storage->getConnection()->exec(file_get_contents(dirname(__FILE__) . '/../_testdata/schema.sql'));
        $storage->getConnection()->exec(file_get_contents(dirname(__FILE__) . '/../_testdata/fixture.sql'));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown()
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
    public function resultObjectWithoutClauses()
    {
        $result = new Result("User");
        $this->assertEquals(3, $result->count());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function resultObjectWithLimitClause()
    {
        $result = new Result("User");
        $result->limit(1);
        $this->assertEquals(1, $result->count());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function resultObjectWithOffsetClause()
    {
        // This test requires that phpunit has convertWarningsToExceptions
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
     * 
     * @return void
     */
    public function resultObjectWithOffsetAndLimitClause()
    {
        $result = new Result("User");
        $result->limit(3)->offset(1);
        $this->assertEquals(2, $result->count());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function resultObjectWithOrderByClauseWithOnePropertyAndNoDirection()
    {
        $result = new Result("User");
        $result->orderBy(array(
            'firstname'
        ));
        $this->assertEquals('Jacob', $result->current()
            ->getFirstname());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function resultObjectWithOrderByClauseWithOnePropertyAndAscDirection()
    {
        $result = new Result("User");
        $result->orderBy(array(
            'firstname'
        ), true);
        $this->assertEquals('Jacob', $result->current()
            ->getFirstname());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function resultObjectWithOrderByClauseWithOnePropertyAndDescDirection()
    {
        $result = new Result("User");
        $result->orderBy(array(
            'firstname'
        ), false);
        $this->assertEquals('Jonny', $result->current()
            ->getFirstname());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function resultObjectWithOrderByClauseWithTwoProperties()
    {
        $result = new Result("User");
        $result->orderBy(array(
            'firstname',
            'lastname'
        ));
        $this->assertEquals('Jacob', $result->current()
            ->getFirstname());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function resultObjectWithOrderByClauseWithOnePropertyAndLimit()
    {
        $result = new Result("User");
        $result->orderBy(array(
            'firstname'
        ))->limit(1);
        $this->assertEquals('Jacob', $result->current()
            ->getFirstname());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function resultObjectWithOrderByClauseWithOnePropertyAndLimitAndOffset()
    {
        $result = new Result("User");
        $result->orderBy(array(
            'firstname'
        ))
            ->limit(1)
            ->offset(1);
        $this->assertEquals('John', $result->current()
            ->getFirstname());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function resultObjectWithWhereClause()
    {
        $result = new Result("User");
        $result->where('firstname', '=', 'John');
        $this->assertEquals('John', $result->current()
            ->getFirstname());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function resultObjectWithWhereEqualsClause()
    {
        $result = new Result("User");
        $result->whereEquals('firstname', 'John');
        $this->assertEquals('John', $result->current()
            ->getFirstname());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function resultObjectWithWhereBetweenClause()
    {
        $result = new Result("User");
        $result->whereBetween('id', 1, 3);
        $this->assertEquals('Jonny', $result->current()
            ->getFirstname());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function iterateOverResult()
    {
        $result = new Result("User");
        $count = 0;
        foreach ($result as $obj) {
            $this->assertInstanceOf("User", $obj);
            $count ++;
        }
        $this->assertEquals(3, $count);
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function resultObjectWithOutAnyHit()
    {
        $result = new Result("User");
        $result->whereEquals('firstname', 'Kurt');
        $this->assertEquals(null, $result->current());
    }
}
require_once dirname(__FILE__) . '../../_testdata/User.php';