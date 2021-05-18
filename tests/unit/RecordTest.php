<?php
/**
 * Test Record
 *
 * PHP version >=8.0
 *
 * @author   Johannes Skov Frandsen <jsf@greenoak.dk>
 * @author   Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
use Localgod\Tick\Manager;
use Localgod\Tick\Result;

/**
 * Test Record
 *
 * @author Johannes Skov Frandsen <jsf@greenoak.dk>
 * @author Brian Demant <brian.demant@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link https://github.com/localgod/php-tick php-tick
 */
class RecordTest extends PHPUnit\Framework\TestCase
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
     * Test
     *
     * @test
     *
     * @return void
     */
    public function readCollectionNameFromDocumentation()
    {
        $re = new RecordExtension();
        $this->assertEquals("record_extension_collection", $re->getCollectionName());
    }

    /**
     * Test
     *
     * @test
     *
     * @return void
     */
    public function readConnectionNameFromDocumentation()
    {
        $re = new RecordExtension();
        $this->assertEquals("record_extension_connection", $re->getConnectionName());
    }
}
require_once dirname(__FILE__) . '/_testdata/RecordExtension.php';
