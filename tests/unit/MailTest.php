<?php

/**
 * Test a class without 2 keys as primary key extending php-tick
 *
 * PHP version >=8.0
 *
 * @author   Johannes Skov Frandsen <jsf@greenoak.dk>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
use Localgod\Tick\Manager;

/**
 * Test a class without 2 keys as primary key extending php-tick
 *
 * @author Johannes Skov Frandsen <jsf@greenoak.dk>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
class MailTest extends PHPUnit\Framework\TestCase
{

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        Manager::addDefaultConnectionConfig('sqlite', ':memory:', null, null, '127.0.0.1', null, array(PDO::ATTR_PERSISTENT => true));
        Manager::setModelPath(dirname(__FILE__) . '/_testdata/');
        $storage = Manager::getStorage();
        $storage->getConnection()->exec(file_get_contents(dirname(__FILE__) . '/_testdata/schema.sql'));
        $storage->getConnection()->exec(file_get_contents(dirname(__FILE__) . '/_testdata/fixture.sql'));
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
    public function storeNew()
    {
        $mail = new Mail();
        $mail->setUserId(5);
        $mail->setMailId(5);
        $mail->setMail('Benny');
        $mail->save();
        $mail = $mail->get()
            ->whereEquals('userId', 5)
            ->whereEquals('mailId', 5)
            ->current();
        $this->assertEquals(5, $mail->getUserId());
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
        $mail = new Mail();
        $mail = $mail->get()
            ->whereEquals('userId', 1)
            ->whereEquals('mailId', 1)
            ->current();
        $mail->remove(array(
            'userId' => 1,
            'mailId' => 1
        ));
        $mail = new Mail();
        $this->assertEquals(0, $mail->get()
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
        $mail = new Mail();
        $mail = $mail->get()
            ->whereEquals('userId', 1)
            ->whereEquals('mailId', 1)
            ->current();
        $mail->setMail('Conny');
        $mail->update(array(
            'userId' => 1,
            'mailId' => 1
        ));
        $mail = new Mail();
        $this->assertEquals(1, $mail->get()
            ->whereEquals('userId', 1)
            ->whereEquals('mailId', 1)
            ->whereEquals('mail', 'Conny')
            ->count());
    }

    /**
     * Utility method to debug tests.
     *
     * @return void
     */
    private function dump()
    {
        $mail = new Mail();
        print("\n|User ID   |Mail ID   |Mail      |");
        print("\n----------------------------------");
        foreach ($mail->get() as $m) {
            printf("\n|%10s|%10s|%10s|", $m->getUserId(), $m->getMailId(), $m->getMail());
        }
        print("\n");
    }
}
require_once dirname(__FILE__) . '/_testdata/Mail.php';
