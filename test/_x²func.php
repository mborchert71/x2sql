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
	define ("d", x²sql::char_list_delimiter);
	define ("t", x²sql::tokenizer);
	}

class _x²func extends UnitTestCase {

	public $test;
	public $stack = array();
	public $call = array();
	public $complete;


	/**
	 *
	 */
	function __construct(x²sql $x = null) {
		parent::__construct();

		$this->complete = true;
		$this->stack[1] = "test_x²key";
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
/*
	function test_func_name_text(){
		
		$this->test = $x = new x²sql;
		
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
			];
		$out=[
			"?()",
			"1()",
			"()",
			"234()",
			"0.4()",
			"68()",
			"456.325()",
			"1()",
			"12()",
			"col()",
			t."tok()",
			];

		foreach($in as $key =>$input){
			$this->test->reset();
			$this->assertEqual($out[$key],$this->test->complode(new x²func($in[$key])));
		}
	}

	function test_func_name_text_argus(){
		
		$this->test = $x = new x²sql;
		
		$in=[
			["count","*"],
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
			[":tok",":tok"],
			["do",array("t1", "t2", "t3"), "alias"],
			["do",array(":tok", "t2", "t3"),"alias"],
			["do",array(":tok", "?","t3"),"alias"]
			];
		$out=[
			"count(".s."*".s.")",
			"?(?)",
			"1(1)",
			"(0)",
			"234(234)",
			"0.4(0.4)",
			"68(68)",
			"456.325(456.325)",
			"1(1)",
			"12(12)",
			"col(".s."col".s.")",
			t."tok(".t."tok)",
			"do(".s."t1".s.",".s."t2".s.",".s."t3".s.")",
			"do(".t."tok,".s."t2".s.",".s."t3".s.")",
			"do(".t."tok,?,".s."t3".s.")"
			];

		foreach($in as $key =>$input){
			$this->test->reset();
			$this->assertEqual($out[$key],$this->test->complode(new x²func($in[$key][0],$in[$key][1])));
		}
	}

	function test_func_name_text_argus_alias(){
		
		$this->test = $x = new x²sql;
		
		$in=[
			["count","count","count"],
			["?","?","?"],
			[true,true,true],
			[false,false,false],
			[234,234,234],
			[4e-1,4e-1,4e-1],
			[0x44,0x44,0x44],
			[456.325,456.325,456.325],
			[new x²bool(true),new x²bool(true),new x²bool(true)],
			[new x²number(12),new x²number(12),new x²number(12)],
			["col","col","col"],
			[":tok",":tok",":tok"],
			["do",array("t1", "t2", "t3"), "alias"],
			["do",array(":tok", "t2", "t3"),"alias"],
			["do",array(":tok", "?","t3"),"alias"]
			];
		$out=[
			"count(".s."count".s.")"." ".k."count".k,
			"?(?) ?",
			"1(1)"." ".k."1".k,
			"(0)",
			"234(234)"." ".k."234".k,
			"0.4(0.4)"." ".k."0.4".k,
			"68(68)"." ".k."68".k,
			"456.325(456.325)"." ".k."456.325".k,
			"1(1)"." ".k."1".k,
			"12(12)"." ".k."12".k,
			"col(".s."col".s.")"." ".k."col".k,
			t."tok(".t."tok)"." ".t."tok",
			"do(".s."t1".s.",".s."t2".s.",".s."t3".s.")"." ".k."alias".k,
			"do(".t."tok,".s."t2".s.",".s."t3".s.")"." ".k."alias".k,
			"do(".t."tok,?,".s."t3".s.")"." ".k."alias".k,
			];

		foreach($in as $key =>$input){
			$this->test->reset();
			$this->assertEqual($out[$key],$this->test->complode(new x²func($in[$key][0],$in[$key][1],$in[$key][2])));
		}
	}
*/
	
	function test_function_value_display(){
			
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
			];
		$out=[
			"?()",
			"1()",
			"()",
			"234()",
			"0.4()",
			"68()",
			"456.325()",
			"1()",
			"12()",
			"col()",
			t."tok()",
			];

		foreach($in as $key =>$input){
			$this->assertEqual($out[$key],(new x²func($in[$key]))->display);
		}
	}

	
	function test_function_value_display_argus(){
				
		$in=[
			["count", new x²operator("*")],
			["count","*"],
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
			[":tok",":tok"],
			["do",array("t1", "t2", "t3")],
			["do",array(":tok", "t2", "t3")],
			["do",array(":tok", "?","t3")]
			];
		$out=[
			"count(*)",
			"count(".s."*".s.")",
			"?(?)",
			"1(1)",
			"(0)",
			"234(234)",
			"0.4(0.4)",
			"68(68)",
			"456.325(456.325)",
			"1(1)",
			"12(12)",
			"col(".s."col".s.")",
			t."tok(".t."tok)",
			"do(".s."t1".s.",".s."t2".s.",".s."t3".s.")",
			"do(".t."tok,".s."t2".s.",".s."t3".s.")",
			"do(".t."tok,?,".s."t3".s.")"
			];

		foreach($in as $key =>$input){
			$this->assertEqual($out[$key],(new x²func($in[$key][0],null,$in[$key][1]))->display);
		}
	}

	function test_function_value_display_argus_alias(){
		
		$in=[
			["count","count","count"],
			["?","?","?"],
			[true,true,true],
			[false,false,false],
			[234,234,234],
			[4e-1,4e-1,4e-1],
			[0x44,0x44,0x44],
			[456.325,456.325,456.325],
			[new x²bool(true),new x²bool(true),new x²bool(true)],
			[new x²number(12),new x²number(12),new x²number(12)],
			["col","col","col"],
			[":tok",":tok",":tok"],
			["do",array("t1", "t2", "t3"), "alias"],
			["do",array(":tok", "t2", "t3"),"alias"],
			["do",array(":tok", "?","t3"),"alias"]
			];
		$out=[
			"count(".s."count".s.")"." ".k."count".k,
			"?(?) ?",
			"1(1)"." ".k."1".k,
			"(0)",
			"234(234)"." ".k."234".k,
			"0.4(0.4)"." ".k."0.4".k,
			"68(68)"." ".k."68".k,
			"456.325(456.325)"." ".k."456.325".k,
			"1(1)"." ".k."1".k,
			"12(12)"." ".k."12".k,
			"col(".s."col".s.")"." ".k."col".k,
			t."tok(".t."tok)"." ".t."tok",
			"do(".s."t1".s.",".s."t2".s.",".s."t3".s.")"." ".k."alias".k,
			"do(".t."tok,".s."t2".s.",".s."t3".s.")"." ".k."alias".k,
			"do(".t."tok,?,".s."t3".s.")"." ".k."alias".k
			];

		foreach($in as $key =>$input){
			$this->assertEqual($out[$key],(new x²func($in[$key][0],$in[$key][2],$in[$key][1]))->display);
		}
	}

		}

?>
