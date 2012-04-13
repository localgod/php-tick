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
 * @author	 Johannes Skov Frandsen <jsf.greenoak@gmail.com>
 * @author	 Brian Demant <brian.demant@gmail.com>
 * @license	 http://www.opensource.org/licenses/mit-license.php MIT
 * @link	 https://github.com/localgod/php-tick php-tick
 */
class RecordTest extends PHPUnit_Framework_TestCase {
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
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function readCollectionNameFromDocumentation() {
		$re = new RecordExtension();
		$this->assertEquals("record_extension_collection", $re->getCollectionName());
	}

	/**
	 * Test
	 *
	 * @test
	 * @return void
	 */
	public function readConnectionNameFromDocumentation() {
		$re = new RecordExtension();
		$this->assertEquals("record_extension_connection", $re->getConnectionName());
	}
}