<?php

/**
 * Test a class without a primary key extending php-tick
 *
 * PHP Version 5.1.2
 *
 * @category Test
 * @package  Test
 * @author   Johannes Skov Frandsen <localgod@heaven.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
use Tick\TickManager;
/**
 * Test a class without a primary key extending php-tick
 *
 * @category Test
 * @package Test
 * @subpackage Test
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
class UserMailsTest extends PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        TickManager::addDefaultConnectionConfig('sqlite', ':memory:', null, null, '127.0.0.1', null, array(PDO::ATTR_PERSISTENT => true));
        TickManager::setModelPath(dirname(__FILE__) . '/_testdata/');
        $storage = TickManager::getStorage();
        $storage->getConnection()->exec(file_get_contents(dirname(__FILE__) . '/_testdata/schema.sql'));
        $storage->getConnection()->exec(file_get_contents(dirname(__FILE__) . '/_testdata/fixture.sql'));
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
     * 
     * @return void
     */
    public function createNew()
    {
        $userMails = new UserMails();
        $userMails->setUserId(1);
        $userMails->setMailId(1);
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
        $userMails = new UserMails();
        $userMails->setUserId(5);
        $userMails->setMailId(5);
        $userMails->save();
        
        $this->assertEquals(5, $userMails->get()
            ->whereEquals('userId', 5)
            ->whereEquals('mailId', 5)
            ->current()
            ->getUserId());
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
        $userMails = new UserMails();
        $userMails = $userMails->get()
            ->whereEquals('userId', 1)
            ->whereEquals('mailId', 1)
            ->current();
        $userMails->remove(array(
            'userId' => 1,
            'mailId' => 1
        ));
        $this->assertEquals(0, $userMails->get()
            ->whereEquals('userId', 1)
            ->whereEquals('mailId', 1)
            ->count());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function update()
    {
        $userMails = new UserMails();
        $userMails = $userMails->get()
            ->whereEquals('userId', 1)
            ->whereEquals('mailId', 1)
            ->current();
        $userMails->setUserId(6);
        $userMails->setMailId(6);
        $userMails->update(array(
            'userId' => 1,
            'mailId' => 1
        ));
        $userMails = new UserMails();
        $this->assertEquals(1, $userMails->get()
            ->whereEquals('userId', 6)
            ->whereEquals('mailId', 6)
            ->count());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function getWithOneCriteria()
    {
        $userMails = new UserMails();
        $this->assertEquals(2, $userMails->get()
            ->whereEquals('userId', 1)
            ->count());
    }

    /**
     * Test
     *
     * @test
     * 
     * @return void
     */
    public function getWithBiggerThanCriterias()
    {
        $userMails = new UserMails();
        $this->assertEquals(0, $userMails->get()
            ->where('userId', '>', 1)
            ->count());
    }
}
require_once dirname(__FILE__) . '/_testdata/UserMails.php';