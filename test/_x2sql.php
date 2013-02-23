<?php 
/**
 * _x2sql
 *
  * The test drive for an open source SQL-generator-class
 * Using the http://simpletest.org/ test-engine.
 * 
 * @package		x2sql
 * @author		m.borchert
 * @copyright           Copyright (c) 2013
 * @license		http://creativecommons.org/licenses/by/3.0/deed.de
 * @link		http://get-resource.net/app/php/x2sql
 * @since		Version 1.0
 */
 class _x2sql extends UnitTestCase {

/**
  simpletest-engine's asserts
  assertTrue($x)                      Fail unless $x evaluates true
  assertFalse($x)                     Fail unless $x evaluates false
  assertNull($x)                      Fail unless $x is not set
  assertNotNull($x)                   Fail unless $x is set to something
  assertIsA($x, $t)                   Fail unless $x is the class or type $t
  assertNotA($x, $t)                  Fail unless $x is not the class or type $t
  assertEqual($x, $y)                 Fail unless $x == $y is true
  assertNotEqual($x, $y)              Fail unless $x == $y is false
  assertWithinMargin($x, $y, $margin) Fail unless $x and $y are separated less than $margin
  assertOutsideMargin($x, $y, $margin)Fail unless $x and $y are sufficiently different
  assertIdentical($x, $y)             Fail unless $x === $y for variables, $x == $y for objects of the same type
  assertNotIdentical($x, $y)          Fail unless $x === $y is false, or two objects are unequal or different types
  assertReference($x, $y)             Fail unless $x and $y are the same variable
  assertCopy($x, $y)                  Fail unless $x and $y are the different in any way
  assertSame($x, $y)                  Fail unless $x and $y are the same objects
  assertClone($x, $y)                 Fail unless $x and $y are identical, but separate objects
  assertPattern($p, $x)               Fail unless the regex $p matches $x
  assertNoPattern($p, $x)             Fail if the regex $p matches $x
  expectError($e)                     Triggers a fail if this error does not happen before the end of the test
  expectException($e)                 Triggers a fail if this exception is not thrown before the end of the test
 * 
 */

    /**
     * 
     */
    function __construct() {
        parent::__construct();
    }
    /**
     * SimpleTest builtin
     */
    function setUp() {
       ; 
    }
    /**
     * SimpletTest builtin
     */
    function tearDown() {
       ; 
    }

	/**
	* test method Sql
	*/
	function test_Sql() {
		;
	}
	/**
	* test method __construct
	*/
	function test___construct() {
		;
	}
	/**
	* test method query
	*/
	function test_query() {
		;
	}
	/**
	* test method escape
	*/
	function test_escape() {
		;
	}
	/**
	* test method select
	*/
	function test_select() {
		;
	}
	/**
	* test method from
	*/
	function test_from() {
		;
	}
	/**
	* test method where
	*/
	function test_where() {
		;
	}
	/**
	* test method having
	*/
	function test_having() {
		;
	}
	/**
	* test method group
	*/
	function test_group() {
		;
	}
	/**
	* test method order
	*/
	function test_order() {
		;
	}
	/**
	* test method limit
	*/
	function test_limit() {
		;
	}
	/**
	* test method offset
	*/
	function test_offset() {
		;
	}
	/**
	* test method insert
	*/
	function test_insert() {
		;
	}
	/**
	* test method columns
	*/
	function test_columns() {
		;
	}
	/**
	* test method values
	*/
	function test_values() {
		;
	}
	/**
	* test method update
	*/
	function test_update() {
		;
	}
	/**
	* test method set
	*/
	function test_set() {
		;
	}
	/**
	* test method delete
	*/
	function test_delete() {
		;
	}
	/**
	* test method fetch
	*/
	function test_fetch() {
		;
	}
	/**
	* test method fetch_type
	*/
	function test_fetch_type() {
		;
	}
	/**
	* test method alias
	*/
	function test_alias() {
		;
	}
	/**
	* test method name
	*/
	function test_name() {
		;
	}
	/**
	* test method comment
	*/
	function test_comment() {
		;
	}}
//eof
?><pre><?php

	include_once '../x2sql.php';

	/**
	 * 1 Json2SQL
	 * 2 SerializedPHP  2SQL
	 * 4 Obj 2SQL
	 * 8 PHP 2SQL
	 * 16 XML 2SQL
	 */
	$print = 8;
	$queries = json_decode('{
        "insert" :{
            "insert":["node"],
            "columns":["id","text","no"],
            "values":[null,"?",13]
        },
        "select" :{ 
            "select":[{"type":"query","select":["1"],"from":"dual","alias":"const"},{"type":"string","value":"id"},"protocol||\'://\'||domain||path||name as url","state"],
            "from":["node"],
            "group":["domain||path"],
            "limit":"?",
            "offset":"?",
            "fetch_type":2
            }
        ,
        "update" :{   
            "update":["node"],
            "set":{"state":"?"},
            "where":[{"bind":"Column","key":"id"},"=","?"],
            "limit":100,
            "offset":0
            }
        }');
	$queries2 = array(
		array(
			"name" => "insert-node",
			"insert" => array("node"),
			"columns" => array("id", "text", "no"),
			"values" => array(null, "?", 13)
		),
		array(
			"name" => "select-node",
			"select" => array("id", "protocol||\'://\'||domain||path||name as url", "state"),
			"from" => array("node"),
			"group" => array("domain||path"),
			"limit" => "?",
			"offset" => "?",
			"fetch_type" => 2
		)
		,
		array(
			"name" => "update-node",
			"update" => ["node"],
			"set" => array("state" => "?"),
			"where" => array(array("bind" => "Column", "key" => "id"), "=", "?"),
			"limit" => 100,
			"offset" => 0
		)
	);
	if ($print & 1) {
		print "Json-SQL Config\n";

		$cmd = new stdClass;
		$cmd->insert = new x2sql($queries->insert);
		$cmd->select = new x2sql($queries->select);
		$cmd->update = new x2sql($queries->update);

		print_r($cmd->insert);
		print_r($cmd->select);
		print_r($cmd->update);
	}
	if ($print & 2) {
		print "Serialized Config\n";
		$ser_queries = serialize($queries);
		$uns_queries = unserialize($ser_queries);

		$cmd->select = new x2sql($queries->select);
		$cmd->update = new x2sql($queries->update);

		print_r($cmd->select);
		print_r($cmd->update);
	}
	if ($print & 4) {
		print "Serialized Object\n";

		$cmd->select = new x2sql($queries->select);
		$cmd->update = new x2sql($queries->update);

		$ser_queries = serialize($cmd);
		$cmd_wakeup = unserialize($ser_queries);

		print_r($cmd_wakeup->select);
		print_r($cmd_wakeup->update);
	}
	if ($print & 8) {

		print "PHP-SQL Code\n";

		$cmd = x2sql::query()
				->select(array(
					new x2func("count", array(1, new x2func("meas")), "count")
					,
					new x2bool("true", "really")
				))
				->from(array("this",
					x2sql::query()->select()
					->from("dingens")
					->where(array(new x2func("avg", "?"), "<", 3))
					->group("col1")
					->alias("supi")
				))
				->where(array(array(7, "between", 3, "and", 10), "and", array(new x2func("count", ":tremolo"), " <= ", "null")));

		print_r($cmd);

		print x2sql::query()
						->select()
						->from("table")
						->where(array(
							x2sql::query()->select("id")->from("table")->where(array(new x2keyword("name"), "=", "herbert")),
							"=",
							234
						))->command;
	}
	if ($print & 16) {

		/**
		 * basic class for converting an array to xml.
		 * @author Matt Wiseman (trollboy at shoggoth.net)
		 *
		 */
		class array2xml {

			public $data;
			public $dom_tree;
			public $item_tag = "o";
			public $document_tag = "queries";

			/**
			 * basic constructor
			 *
			 * @param array $array
			 */
			public function __construct($array) {
				if (!is_array($array)) {
					throw new Exception('array2xml requires an array', 1);
					unset($this);
				}
				if (!count($array)) {
					throw new Exception('array is empty', 2);
					unset($this);
				}

				$this->data = new DOMDocument('1.0');

				$this->dom_tree = $this->data->createElement($this->document_tag);
				$this->data->appendChild($this->dom_tree);
				$this->recurse_node($array, $this->dom_tree);
			}

			/**
			 * recurse a nested array and return dom back
			 *
			 * @param array $data
			 * @param dom element $obj
			 */
			private function recurse_node($data, $obj) {
				$i = 0;
				foreach ($data as $key => $value) {
					$key = is_numeric($key) ? $this->item_tag : $key;
					if (is_array($value)) {
						//recurse if neccisary
						$sub_obj[$i] = $this->data->createElement($key);
						$obj->appendChild($sub_obj[$i]);
						$this->recurse_node($value, $sub_obj[$i]);
					} elseif (is_object($value)) {
						//no object support so just say what it is
						$sub_obj[$i] = $this->data->createElement($key, 'Object: "' . $key . '" type: "' . get_class($value) . '"');
						$obj->appendChild($sub_obj[$i]);
					} else {
						//straight up data, no weirdness
						$sub_obj[$i] = $this->data->createElement($key, $value);
						$obj->appendChild($sub_obj[$i]);
					}
					$i++;
				}
			}

			/**
			 * get the finished xml
			 *
			 * @return string
			 */
			public function saveXML() {
				return $this->data->saveXML();
			}

		}

		try {
			$_ = new array2xml($queries2);
			print $_->saveXML();
		} catch (Exception $e) {
			print_r($e);
		}
	}

	$xmlstr = <<<XML
<query>
        <select>id</select>
        <select>name</select>
        <select>type</select>
        <from>table</from>
        <limit><x2func><name>round</name><value>100.3</value></x2func></limit>
        <offset>?</offset>
</query>
XML;

	$xml = new SimpleXMLElement($xmlstr);
	print "<hr>";
	print_r($xml);
	echo $xml->select;
	echo $xml->{'from'};

	x2sql::query($xml)->command;

