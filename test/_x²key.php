<?php
/**
 * todo : 
 * x²func without name should raise excpetion
 */
/**
 * _x²sql
 *
 * The test drive for an open source SQL-generator-class
 * Using the http://simpletest.org/ test-engine.
 * 
 * @package		x²sql
 * @author		m.borchert
 * @copyright           Copyright (c) 2013
 * @license		http://creativecommons.org/licenses/by/3.0/deed.de
 * @link		http://get-resource.net/app/php/x²sql
 * @since		Version 1.0
 */
$option = new stdClass;
$option->complete = false;
$option->call = array();
if (@$_REQUEST["call"]) {
	$option->call = explode(",", $_REQUEST["call"]);
}else{
	$option->complete = true;
}
include_once '../php²sql';

if(!defined("k")){
	
	define ("k", x²sql::esc_key);
	define ("s", x²sql::esc_string);
	define ("n", x²sql::esc_num);
	define ("d", x²sql::delimiter);
	define ("t", x²sql::tokenizer);
	}

class _x²key extends UnitTestCase {

	public $test;
	public $stack = array();
	public $call = array();
	public $complete;


	/**
	 * 
	 */
	function __construct(x²sql $x = null) {
		parent::__construct();
		$this->test = $x = new x²sql;
		$this->complete = true;
		$this->stack[1] = "";
	}

	/**
	 * SimpleTest builtin
	 */
	function setUp() {
		global $option;
		if (isset($option->complete))
			$this->complete = $option->complete;
		if (isset($option->call) && is_array($option->call)) {
			foreach ($option->call as $call) {
				$this->setTest($call);
			}
		}
	}

	/**
	 * SimpletTest builtin
	 */
	function tearDown() {
		;
	}

	/**
	 * filter calling test
	 * @param type $func
	 * @return boolean
	 */
	function runTest($func) {
		if (!in_array($func, $this->call) && !$this->complete)
			return false;
		return true;
	}

	/**
	 * push test to call-stack
	 * @param type $test
	 */
	function setTest($test) {
		$test = (is_numeric($test) && $test < count($this->stack)) ? $this->stack[$test] : (in_array($test, $this->stack) ? $test : "");
		if (!in_array($test, $this->call))
			array_push($this->call, $test);
	}

	function test_name_display(){
		$in=[
			"?",
			true,
			false,
			234,
			4e-1,
			0x44,
			456.325,
			new x²bool(true),
			new x²number(12),
			"col",
			":tok",
			array("t1", "t2", "t3"),
			array(":tok", "t2", "t3"),
			array(":tok", "?")
			];
		$out=[
			"?",
			k."1".k,
			"",
			k."234".k,
			k."0.4".k,
			k."68".k,
			k."456.325".k,
			k."1".k,
			k."12".k,
			k."col".k,
			t."tok",
			k."t1".k.".".k."t2".k.".".k."t3".k,
			t."tok".".".k."t2".k.".".k."t3".k,
			t."tok".".?"
			];

		foreach($in as $key =>$input){
			$this->assertEqual($out[$key],(new x²key($in[$key]))->display);
		}
	}

	function test_name_display_with_alias(){
		$in=[
			["?","?"],
			[true,true],
			[false,false],
			[234,234],
			[4e-1,4e-1],
			[0x44,0x44],
			[456.325,456.325],
			[new x²bool(true),new x²bool(true)],
			[new x²number(12),new x²number(12)],
			["col","col"],
			[t."tok",":tok"],
			[array("t1", "t2", "t3"), "alias"],
			[array(t."tok", "t2", "t3"),"alias"],
			[array(t."tok", "?"),"alias"]
			];
		$out=[
			"?"." "."?",
			k."1".k." ".k."1".k,
			"",
			k."234".k." ".k."234".k,
			k."0.4".k." ".k."0.4".k,
			k."68".k." ".k."68".k,
			k."456.325".k." ".k."456.325".k,
			k."1".k." "."1",
			k."12".k." "."12",
			k."col".k." ".k."col".k,
			t."tok"." ".t."tok",
			k."t1".k.".".k."t2".k.".".k."t3".k." ".k."alias".k,
			t."tok".".".k."t2".k.".".k."t3".k." ".k."alias".k,
			t."tok".".?"." ".k."alias".k
			];

		foreach($in as $key =>$input){
			$this->assertEqual($out[$key],(new x²key($in[$key][0],$in[$key][1]))->display);
		}
	}
	
}

?>
