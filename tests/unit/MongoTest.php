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
class MUserTest extends PHPUnit\Framework\TestCase
{

    /**
     * Start a mongo server if we can
     * 
     * @return void
     */
    public function __construct()
    {
        if (extension_loaded('mongo')) {
            if (! file_exists('mongodb/db')) {
                shell_exec('mkdir -p mongodb/db');
            }
            if (! file_exists('mongodb/log')) {
                shell_exec('mkdir mongodb/log');
            }
            shell_exec('mongod --fork --port 27088 --dbpath mongodb/db --logpath mongodb/log/mongo.log');
        }
    }

    /**
     * Kill a mongo server if we can
     *
     * @return void
     */
    public function __destruct()
    {
        if (extension_loaded('mongo')) {
            if (file_exists('mongodb')) {
                $pid = shell_exec('cat mongodb/db/mongod.lock');
                shell_exec('kill ' . $pid);
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
    protected function setUp(): void
    {
        if (extension_loaded('mongo')) {
            if (shell_exec('mongod --version | grep "db version"') == '') {
                $this->markTestSkipped('The mongod execuatable is not available.');
            }
        }
        if (! extension_loaded('mongo')) {
            $this->markTestSkipped('The mongo extension is not available.');
        }
        
        Manager::addDefaultConnectionConfig('mongodb', 'mongotest', null, null, '127.0.0.1', 27088);
        Manager::setModelPath(dirname(__FILE__) . '/_testdata/');
        shell_exec('mongoimport --port 27088 --drop --db mongotest --collection users --file ' . dirname(__FILE__) . '/_testdata/fixture.json');
        sleep(1);
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
    public function getConnection()
    {
        $this->assertTrue(Manager::getStorage() instanceof Storage);
        $this->assertTrue(Manager::getStorage() instanceof MongoStorage);
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function storeNew()
    {
        $now = new DateTime();
        $user = new MUser();
        $user->setFirstname('Hans');
        $user->setCreated($now);
        $user->save();
        $user = new MUser();
        $user = $user->get()
            ->whereEquals('firstname', 'Hans')
            ->current();
        $this->assertEquals($user->getFirstname(), 'Hans');
    }

    /**
     * Test
     *
     * Messe with time/timezone... fix it
     *
     * @return void
     */
    public function update()
    {
        $now = new DateTime();
        $user = new MUser();
        $user = $user->get()
            ->whereEquals('firstname', 'Jonny')
            ->current();
        $user->setCreated($now);
        $user->setLastname('Hansen');
        $user->save();
        $user = new MUser();
        $user = $user->get()
            ->whereEquals('firstname', 'Jonny')
            ->current();
        $this->assertEquals($now, $user->getCreated());
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function remove()
    {
        $user = new MUser();
        $user = $user->get()
            ->whereEquals('firstname', 'John')
            ->current();
        $user->remove();
        $user = new MUser();
        $user = $user->get()
            ->whereEquals('firstname', 'John')
            ->current();
        $this->assertEquals($user, null);
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function getOne()
    {
        $user = new MUser();
        $user = $user->get()
            ->whereEquals('firstname', 'John')
            ->current();
        $this->assertEquals('John', $user->getFirstname());
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function sortAscending()
    {
        $user = new MUser();
        $user = $user->get()
            ->whereEquals('lastname', 'Bang')
            ->orderBy('firstname', true)
            ->current();
        $this->assertEquals('Karl', $user->getFirstname());
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function countAll()
    {
        $user = new MUser();
        $this->assertEquals(6, $user->getAll()
            ->count());
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function limit()
    {
        $user = new MUser();
        $count = $user->get()
            ->whereEquals('lastname', 'Bang')
            ->limit(2)
            ->count();
        $this->assertEquals(2, $count);
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function offset()
    {
        $user = new MUser();
        $count = $user->get()
            ->whereEquals('lastname', 'Bang')
            ->limit(2)
            ->offset(1)
            ->count();
        $this->assertEquals(2, $count);
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function selectGreaterThan()
    {
        $user = new MUser();
        $count = $user->get()
            ->where('lastname', '>', 'Bang')
            ->count();
        $this->assertEquals(3, $count);
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function selectLesserThan()
    {
        $user = new MUser();
        $count = $user->get()
            ->where('lastname', '<', 'Doe')
            ->count();
        $this->assertEquals(3, $count);
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function selectLesserThanOrEqual()
    {
        $user = new MUser();
        $count = $user->get()
            ->where('firstname', '<=', 'Karl')
            ->count();
        $this->assertEquals(4, $count);
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function selectGreaterThanOrEqual()
    {
        $user = new MUser();
        $count = $user->get()
            ->where('firstname', '>=', 'Kenny')
            ->count();
        $this->assertEquals(2, $count);
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function selectLike()
    {
        $user = new MUser();
        $count = $user->get()
            ->where('firstname', 'like', 'Kenny')
            ->count();
        $this->assertEquals(1, $count);
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function selectLikeOpenEnd()
    {
        $user = new MUser();
        $count = $user->get()
            ->where('firstname', 'like', 'Ken%')
            ->count();
        $this->assertEquals(1, $count);
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function selectLikeOpenStart()
    {
        $user = new MUser();
        $count = $user->get()
            ->where('firstname', 'like', '%ny')
            ->count();
        $this->assertEquals(2, $count);
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function sortDecending()
    {
        $user = new MUser();
        $user = $user->get()
            ->whereEquals('lastname', 'Bang')
            ->orderBy('firstname', false)
            ->current();
        $this->assertEquals('Kurt', $user->getFirstname());
    }
}
require_once dirname(__FILE__) . '/_testdata/MUser.php';
