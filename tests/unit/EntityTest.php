<?php
/**
 * Test Entity
 *
 * PHP version >=8.0
 *
 * @author   Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
use Localgod\Tick\Manager;

/**
 * Test Entity
 *
 * @author   Brian Demant <brian.demant@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/localgod/php-tick php-tick
 */
class EntityTest extends PHPUnit\Framework\TestCase
{
    /**
     * Expected content
     * @var array
     */
    public $expected;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        Manager::setModelPath(dirname(__FILE__) . '/_testdata/');
        $this->expected = array(
            "properties" => array(
                "id" => array("field" => "user_id", "type" => "integer", 'default' => null,"size" => 11,
                    "unique" => true),
                "name" => array("field" => "full_name", "type" => "string", "size" => 255,
                    "null" => true, "default" => "Jane",),
                "username" => array("field" => "email", "type" => "string", "size" => 128,
                    "default" => "Doe Doe"),
                "created" => array("field" => "created", "type" => "DateTime", 'default' => null),
                "updated" => array("field" => "updated", "type" => "DateTime", 'default' => null, "null" => true)
            ),
            "fields" => array(
                "user_id" => array('property' => 'id',"field" => "user_id", "type" => "integer", "size" => 11,
                    "unique" => true, 'default' => null),
                "full_name" => array('property' => 'name',"field" => "full_name", "type" => "string", "size" => 255,
                    "null" => true, "default" => "Jane"),
                "email" => array('property' => 'username',"field" => "email", "type" => "string", "size" => 128,
                    "default" => "Doe Doe"),
                "created" => array('property' => 'created',"field" => "created", "type" => "DateTime", 'default' => null),
                "updated" => array('property' => 'updated',"field" => "updated", "type" => "DateTime", "null" => true, 'default' => null)
            )
        );
    }

    /**
     * Test
     *
     * @test
     * @return void
     */
    public function readCollectionNameFromDocumentation()
    {
        $re = new EntityExtension();
        $this->assertEquals("entity_extension_collection", $re->getCollectionName());
    }

    /**
     * Test
     *
     * @test
     * @return void
     */
    public function readConnectionNameFromDocumentation()
    {
        $re = new EntityExtension();
        $this->assertEquals("entity_extension_connection", $re->getConnectionName());
    }

    /**
     * Test
     *
     * @test
     * @return void
     */
    public function listProperties()
    {
        $re = new EntityExtension();
        $this->assertEquals(array_keys($this->expected["properties"]), $re->listPropertyNames());
    }

    /**
     * Test
     *
     * @test
     * @return void
     */
    public function propertyExists()
    {
        $re = new EntityExtension();
        foreach ($this->expected["properties"] as $name => $prop) {
            $this->assertTrue($re->propertyExists($name));
        }
        unset($name, $prop);
    }

    /**
     * Test
     *
     * @test
     * @return void
     */
    public function propertyAlias()
    {
        $re = new EntityExtension();
        foreach ($this->expected["properties"] as $name => $prop) {
            $this->assertEquals(
                $prop["field"],
                $re->propertyAlias($name),
                "field: $name expected  " . $prop["field"] . " but got " . $re->propertyAlias($name)
            );
        }
    }

    /**
     * Test
     *
     * @test
     * @return void
     */
    public function defaultValue()
    {
        $re = new EntityExtension();
        foreach ($this->expected["properties"] as $name => $prop) {
            @$this->assertEquals(
                $prop["default"],
                $re->defaultValue($name),
                "default: $name expected  " . $prop["default"] . " but got " . $re->defaultValue($name)
            );
        }
    }


    /**
     * Test
     *
     * @test
     * @return void
     */
    public function notNull()
    {
        $re = new EntityExtension();
        foreach ($this->expected["properties"] as $name => $prop) {
            @$this->assertEquals(
                !!$prop["null"],
                !!$re->notNull($name),
                "null: $name expected  " . ($prop["null"] != true) . " but got " . $re->notNull($name)
            );
        }
        unset($name, $prop);
    }

    /**
     * Test
     *
     * @test
     * @return void
     */
    public function fieldAlias()
    {
        $re = new EntityExtension();
        foreach ($this->expected["properties"] as $name => $prop) {
            if (key_exists("field", $prop)) {
                $field = $prop["field"];
            } else {
                $field = $name;
            }

            @$this->assertEquals(
                $name,
                $re->fieldAlias($field),
                "fieldAlias: $name ($field) expected  " . $name . " but got " . $re->fieldAlias($field)
            );
        }
        unset($name, $prop);
    }

    /**
     * Test
     *
     * @test
     * @return void
     */
    public function mustBeUnique()
    {
        $re = new EntityExtension();
        foreach ($this->expected["properties"] as $name => $prop) {
            @$this->assertEquals(
                !!$prop["unique"],
                !!$re->mustBeUnique($name),
                "unique: $name expected  " . ($prop["unique"] != true) . " but got " . $re->mustBeUnique($name)
            );
        }
        unset($name, $prop);
    }

    /**
     * Test
     *
     * @test
     * @return void
     */
    public function isValidLength()
    {
        $re = new EntityExtension();
        foreach ($this->expected["properties"] as $name => $prop) {
            @$this->assertTrue($re->_isValidLength($name, 10), "10 should be valid value for $name");
            @$this->assertTrue($re->_isValidLength($name, 300), "300 should be an invalid value for $name");
        }
        unset($name, $prop);
    }

    /**
     * Test
     *
     * @test
     * @return void
     */
    public function propertyType()
    {
        $re = new EntityExtension();
        foreach ($this->expected["properties"] as $name => $prop) {
            @$this->assertEquals(
                $prop["type"],
                $re->propertyType($name),
                "type: $name expected  " . $prop["type"] . " but got " . $re->propertyType($name)
            );
        }
    }

    /**
     * Test
     *
     * @test
     * @return void
     */
    public function parseDocumentComment()
    {
        $re = new EntityExtension();
        $re->parseDcoumentComment();
        $this->assertEquals($this->expected, $re->_getMetadata());
    }
}
require_once dirname(__FILE__) . '/_testdata/EntityExtension.php';
