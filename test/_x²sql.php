<?php 
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
$option->call = array(
	4,13
		);
 
include_once '../x²sql.php';
class _x²sql extends UnitTestCase {

	public $test;
    public $stack=array();
	public $call=array();
	public $complete;
    /**
     * 
     */
    function __construct(x²sql $x=null) {
        parent::__construct();
		$this->test = $x = new x²sql;
		$this->complete = true;
		$this->stack[0]="test_Sql";
		$this->stack[1]="test___construct";
		$this->stack[2]="test_query";
		$this->stack[3]="test_escape";
		$this->stack[4]="test_select";
		$this->stack[5]="test_from";
		$this->stack[6]="test_where";
		$this->stack[7]="test_having";
		$this->stack[8]="test_group";
		$this->stack[9]="test_order";
		$this->stack[10]="test_limit";
		$this->stack[11]="test_offset";
		$this->stack[12]="test_insert";
		$this->stack[13]="test_columns";
		$this->stack[14]="test_values";
		$this->stack[15]="test_update";
		$this->stack[16]="test_set";
		$this->stack[17]="test_delete";
		$this->stack[18]="test_fetch";
		$this->stack[19]="test_fetch_type";
		$this->stack[20]="test_alias";
		$this->stack[21]="test_name";
		$this->stack[22]="test_comment";
    }
    /**
     * SimpleTest builtin
     */
    function setUp() {
	   global $option;
       if(isset($option->complete)) $this->complete = $option->complete;
	   if(isset($option->call) && is_array($option->call)){
		foreach($option->call as $call){
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
	function runTest($func){
		if(!in_array($func, $this->call) && !$this->complete) return false;
		return true;
	}
	/**
	 * push test to call-stack
	 * @param type $test
	 */
	function setTest($test){
		$test = (is_numeric($test)&& $test<count($this->stack)) 
			? $this->stack[$test] 
			: (in_array($test,$this->stack) ? $test : "");
		if(!in_array($test,$this->call)) array_push($this->call,$test);
	}

	/**
	* test method Sql
	*/
	function test_Sql() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method __construct
	*/
	function test___construct() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method query
	*/
	function test_query() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method escape
	*/
	function test_escape() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method select
	*/
	function test_select() {
		if(!$this->runTest(__FUNCTION__))return;
		
		$k = x²sql::esc_key;
		$s = x²sql::esc_string;
		$n = x²sql::esc_num;
		$c = x²sql::esc_non;
		$t = x²sql::tokenizer;
		$in = array(
			null, "",
			true, false,
			234, 4e-1, 0x44,
			456.325, "col",
			"?", "{$t}tok",
			array("id", "name", "value"),
			new x²bool(true),
			new x²key("id"),
			new x²number(12),
			x²sql::query()->select(1)->alias("t"),
			new x²string("string"),
			new x²token("tok"),
			new x²func("count", 0)
		);
		$out = array(
			"select * ", "select * ",
			"select 1", "select 0",
			"select 234", "select 0.4", "select 68",
			"select 456.325", "select {$k}col{$k}",
			"select ?", "select {$t}tok",
			"select {$k}id{$k},{$k}name{$k},{$k}value{$k} ",
			"select 1 ",
			"select {$k}id{$k} ",
			"select 12 ",
			"select (select 1){$k}t{$k}",
			"select {$s}string{$s} ",
			"select {$t}tok ",
			"select count( 0 )"
		);
		$inst = $this->test->select($in[0]);
		$this->assertEqual($this->test, $inst);
		$this->assertEqual($this->test->command_type, "select");
		$this->assertEqual(count($in), count($out));
		$c = count($in);
		while ($c--) {
			$inst = $this->test->select($in[$c]);
			$this->assertEqual($out[$c], $this->test->last_append);
		}
	}
	/**
	* test method from
	*/
	function test_from() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method where
	*/
	function test_where() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method having
	*/
	function test_having() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method group
	*/
	function test_group() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method order
	*/
	function test_order() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method limit
	*/
	function test_limit() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method offset
	*/
	function test_offset() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method insert
	*/
	function test_insert() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method columns
	*/
	function test_columns() {
		if(!$this->runTest(__FUNCTION__))return;

		$k = x²sql::esc_key;
		$in = array(
			"col",
			"?", ":tok",
			array("id", "name", "value"),
			new x²place("","no-alias-must-be-set"),
			new x²key("id","no-alias-must-be-set"),
			new x²string("id","no-alias-must-be-set")
		);
		$out = array(
			"({$k}col{$k})",
			"(?)", "(:tok)",
			"({$k}id{$k},{$k}name{$k},{$k}value{$k})",
			"(?)",
			"({$k}id{$k})",
			"({$k}id{$k})"
		);
		//expect pass
		$this->assertEqual(count($in), count($out));
		$c = count($in);
		while ($c--) {
			$inst = $this->test->columns($in[$c]);
			$this->assertEqual($out[$c], $this->test->last_append);
		}
		//expect fail
		$in = array(
			null, "",
			true, false,
			234, 4e-1, 0x44,
			456.325,
			new x²bool(true),
			new x²number(12),
			new stdClass()
		);
		$c = count($in);
		while ($c--) {
			$e = null;
			try{
				$inst = $this->test->columns($in[$c]);
			}
			catch(Exception $e){
			}
			$this->assertIsA($e, "Exception","input:$c");
		}		
	}
	/**
	* test method values
	*/
	function test_values() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method update
	*/
	function test_update() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method set
	*/
	function test_set() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method delete
	*/
	function test_delete() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method fetch
	*/
	function test_fetch() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method fetch_type
	*/
	function test_fetch_type() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method alias
	*/
	function test_alias() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method name
	*/
	function test_name() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}
	/**
	* test method comment
	*/
	function test_comment() {
		if(!$this->runTest(__FUNCTION__))return;
		;
	}}
//eof