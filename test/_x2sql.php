<?php  namespace testunits;
/**
 * _x²sql
 *
 * The test drive for an open source SQL-generator-class
 * Using the http://simpletest.org/ test-engine.
 * 
 * @package		x²sql
 * @author		mborchert
 * @copyright           Copyright (c) 2013.
 * @license		http://creativecommons.org/licenses/by/3.0/deed.de
 * @link		http://get-resource.net/app/php/x2sql
 * @since		Version 1.0
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
include_once 'init.php';
require_once('../x2sql.php');
/**
 * x2sql a Structured Query Language (SQL) Command Creator
 * 
 * This class enables you to create a SQL-Command from multiple input types.
 * as there are, PHP-Codeand Selrialized, json and xml.
 * These Testcases help in the validating the desired functionality.
 *
 */
class _x2sql extends UnitTestCase {
 
    /**
     * 
     */
    function __construct() {
        parent::__construct('x2sql test');
    }
    /**
     * SimpleTest builtin
     */
    function setUp() {
        
    }
    /**
     * SimpletTest builtin
     */
    function tearDown() {
        
    }
    /**
     * initial testcase, test engine is up and running
     */
    function testBasicSetup() {
        $this->assertEqual($this, $this);
    }

}

/* * 2013-02-21 tutorial typo : SimpleTestOptions::ignore
  SimpleTest::ignore('_x4sql');
  class _x4sql extends UnitTestCase {
  }
 */
?>
<pre><?php
include 'x2sql.php';

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
                ->where(array(new x2func("avg","?"), "<", 3))
                ->group("col1")
                ->alias("supi")
            ))
            ->where(array(array(7,"between",3,"and",10),"and",array(new x2func("count", ":tremolo"), " <= ", "null")));

    print_r($cmd);
    
    print x2sql::query()
        ->select()
        ->from("table")
        ->where(array(
            x2sql::query()->select("id")->from("table")->where(array(new x2keyword("name"),"=","herbert")),
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
?>