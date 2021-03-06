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
	
	}


class _x²sql extends UnitTestCase {

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
		$this->stack[0] = "test___construct";
		$this->stack[1] = "test_Sql";
		$this->stack[2] = "test_query"; 
		$this->stack[3] = "test_escape"; 
		$this->stack[4] = "test_select"; 
		$this->stack[5] = "test_from"; 
		$this->stack[6] = "test_where"; 
		$this->stack[7] = "test_having"; 
		$this->stack[8] = "test_group"; 
		$this->stack[9] = "test_order"; 
		$this->stack[10] = "test_limit"; 
		$this->stack[11] = "test_offset"; 
		$this->stack[12] = "test_insert"; 
		$this->stack[13] = "test_columns"; 
		$this->stack[14] = "test_values"; 
		$this->stack[15] = "test_update"; 
		$this->stack[16] = "test_set"; 
		$this->stack[17] = "test_delete"; 
		$this->stack[18] = "test_fetch";
		$this->stack[19] = "test_fetch_type";
		$this->stack[20] = "test_alias";
		$this->stack[21] = "test_name";
		$this->stack[22] = "test_comment";
		$this->stack[23] = "test_union";
		$this->stack[24] = "test_x²key";
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

	/**
	 * test method Sql
	 */
	function test_Sql() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$k = x²sql::esc_key;
		$s = x²sql::esc_string;
		$n = x²sql::esc_num;
		$this->test->reset();
		$cfg = '{
			"select": ["id","name","value"],
			"from"  : ["a","b"]
			}';
		$expect = "select {$k}id{$k},{$k}name{$k},{$k}value{$k} from {$k}a{$k},{$k}b{$k}";
		$this->assertEqual($expect, x²sql::query($cfg)->command);
		//$this->assertEqual($expect, $this->test->Sql($cfg)->command);
		//$this->assertEqual($expect, (new x²sql($cfg))->command);

		//stdclass->type
		$cfg = '{
			"select": [{"type":"x²string","value":"const"},"id","name",{"type":"x²token","value":"value","alias":"val"}],
			"from"  : ["a",{"type":"x²sql","select":"*","from":"t2","alias":"t2"}],
			"limit" : "?"
			}';
		$expect = "select {$s}const{$s},{$k}id{$k},{$k}name{$k},:value {$k}val{$k} from {$k}a{$k},(select * from {$k}t2{$k}) {$k}t2{$k} limit ?";
		//$this->assertEqual($expect, x²sql::query($cfg)->command);
	}

	/**
	 * test method __construct
	 */
	function test___construct() {
		if (!$this->runTest(__FUNCTION__))
			return;
		;
	}

	/**
	 * test method query.
	 * the rest is handled in method __construct.
	 * returns object x²sql.
	 */
	function test_query() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->assertIsA(x²sql::query(), "x²sql");
	}

	/**
	 * test method escape
	 */
	function test_escape() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$k = x²sql::esc_key;
		$s = x²sql::esc_string;
		$n = x²sql::esc_num;
		$_ = x²sql::esc_non;
		$in = array(
			x²sql::null_string,
			null,
			false,
			2,
			3.56,
			0x44,
			"col",
			"?",
			":tok",
			0,
			"=","<=","between"
		);
		$out = array(
			x²sql::null_string,
			x²sql::null_string,
			0,
			2,
			3.56,
			0x44,
			"col",
			"?",
			":tok",
			0,
			"=","<=","between"
		);
		//expect pass
		$this->assertEqual(count($in), count($out));
		$c = count($in);
		while ($c--) {
			$this->assertEqual($out[$c], x²sql::escape($in[$c]));
		}
	}

	/**
	 * test method select
	 */
	function test_select() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->test->reset();
		$x = $this->test;
		$k = x²sql::esc_key;
		$s = x²sql::esc_string;
		$n = x²sql::esc_num;
		$c = x²sql::esc_non;
		$t = x²sql::tokenizer;
		$in = array(
			array("=","<="),"between",
			array(new x²operator("*"),"c"),
			null, "",
			true, false,
			234, 4e-1, 0x44,
			456.325, "col",
			"?", "{$t}tok",
			array("id", "name", "value"),
			new x²bool(true),
			new x²key("id"),
			new x²number(12),
			new x²func("count","RecordCount",new x²number(0)),
			x²sql::query()->select(1)->alias("t"),
			new x²string("string"),
			new x²token("tok"),
			new x²func("count", null,new x²operator("*"))
		);
		$out = array(
			"select {$k}={$k},{$k}<={$k}","select {$k}between{$k}",
			"select *,{$k}c{$k}",
			"select *", "select *",
			"select {$k}1{$k}", "select {$k}0{$k}",
			"select {$k}234{$k}", "select {$k}0.4{$k}", "select {$k}68{$k}",
			"select {$k}456.325{$k}", "select {$k}col{$k}",
			"select ?", "select {$t}tok",
			"select {$k}id{$k},{$k}name{$k},{$k}value{$k}",
			"select 1",
			"select {$k}id{$k}",
			"select 12",
			"select count(0) {$k}RecordCount{$k}",
			"select (select {$k}1{$k}) {$k}t{$k}",
			"select {$s}string{$s}",
			"select {$t}tok",
			"select count(*)"
		);
		$inst = $this->test->select($in[0]);
		$this->assertEqual($this->test, $inst);
		$this->assertEqual($this->test->command_type, "select");
		$this->assertEqual(count($in), count($out));
		$c = count($in);
		while ($c--) {
			
			$inst = $this->test->select($in[$c]);
			$this->assertEqual($out[$c],$this->test->last_word. $this->test->last_append, "%s fail-index:$c");
		}
		$this->assertEqual(3, $x->bind_count, "bindcount,%s");
	}

	/**
	 * test method from.
	 * this method handles also: insert,update,delete.
	 */
	function test_from($call = "from", $word = " from") {

		if (!$this->runTest(__FUNCTION__) && $word == " from")
			return;
		$this->test->reset();
		$k = x²sql::esc_key;
		$n = x²sql::esc_num;
		$t = x²sql::tokenizer;
		$in = array(
			true, false,
			234, 4e-1, 0x44,
			456.325,
			new x²bool(true),
			new x²number(12),
			"col",
			array("t1", "t2", "t3"),
			new x²key("table", "alias"),
			x²sql::query()->select()->from("t")->where(array(new x²key("x"), "<", 3))->alias("alias")
		);


		$out = array(
			"$word {$k}1{$k}",
			"$word {$k}0{$k}",
			"$word {$k}234{$k}",
			"$word {$k}0.4{$k}",
			"$word {$k}68{$k}",
			"$word {$k}456.325{$k}",
			"$word 1",
			"$word 12",
			"$word {$k}col{$k}",
			"$word {$k}t1{$k},{$k}t2{$k},{$k}t3{$k}",
			"$word {$k}table{$k} {$k}alias{$k}",
			"$word (select * from {$k}t{$k} where ({$k}x{$k} < {$n}3{$n})) {$k}alias{$k}"
		);

		//expect pass
		$this->assertEqual(count($in), count($out));
		$c = count($in);
		while ($c--) {
			$inst = $this->test->$call($in[$c]);
			$this->assertEqual($out[$c], $this->test->last_word.$this->test->last_append);
		}
	}

	/**
	 * test method where.
	 * this method also handles : having,group,order. 
	 */
	function test_where($call = "where", $word = " where") {
		if (!$this->runTest(__FUNCTION__) && $word == " where")
			return;
		$k = x²sql::esc_key;
		$n = x²sql::esc_num;
		$this->assertEqual(
				" ({$k}x{$k} < {$n}3{$n})", $this->test->$call(array(new x²key("x"), "<", 3))->last_append, "%s");
		$this->assertNotEqual(
				" ({$k}x{$k} in (select * from {$k}table{$k}))", $this->test->$call(array(new x²key("x"), "in", x²sql::query()->select()->from("table")))->last_append, "%s");
		$this->assertEqual(
				" (({$k}x{$k} < {$n}3{$n}) and (1 <> {$n}3{$n}))", $this->test->$call(array(array(new x²key("x"), "<", 3), "and", array(true, "<>", 3)))->last_append, "%s");
		$this->assertEqual(" (({$k}table{$k}.{$k}schema_name{$k} = schema()) and ({$k}table_name{$k} = :table))",
			$this->test->$call( 
										[ [ new x²key(["table","schema_name"]),"=",new x²func("schema")]
										,"and",
										[ new x²key("table_name"),"=",":table" ]])->last_append, "%s");
				
			}

	/**
	 * test method having
	 */
	function test_having() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->test_where("having", " having");
	}

	/**
	 * test method group
	 */
	function test_group() {
		if (!$this->runTest(__FUNCTION__))
			return;
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->test->reset();
		$k = x²sql::esc_key;
		$s = x²sql::esc_string;
		$n = x²sql::esc_num;
		$c = x²sql::esc_non;
		$t = x²sql::tokenizer;
		$in = array(
			array(new x²operator("*"),"c"),
			null, "",
			true, false,
			234, 4e-1, 0x44,
			456.325, "col",
			"?", "{$t}tok",
			array("id", "name", "value"),
			new x²bool(true),
			new x²key("id"),
			new x²number(12),
			new x²func("count","no-alias",new x²number(0)),
			//x²sql::query()->select(1)->alias("no-alias"),
			new x²string("string"),
			new x²token("tok"),
			new x²func("count",null, new x²operator("*"))
		);
		$out = array(
			" group by *,{$k}c{$k}",
			" group by ", " group by ",
			" group by {$k}1{$k}", " group by {$k}0{$k}",
			" group by {$k}234{$k}", " group by {$k}0.4{$k}", " group by {$k}68{$k}",
			" group by {$k}456.325{$k}", " group by {$k}col{$k}",
			" group by ?", " group by {$t}tok",
			" group by {$k}id{$k},{$k}name{$k},{$k}value{$k}",
			" group by 1",
			" group by {$k}id{$k}",
			" group by 12",
			" group by count(0)",
			//" group by (select {$k}1{$k})",
			" group by {$s}string{$s}",
			" group by {$t}tok",
			" group by count(*)"
		);
		$inst = $this->test->group($in[0]);
		$this->assertEqual($this->test, $inst);
		$this->assertEqual(count($in), count($out));
		$c = count($in);
		while ($c--) {
			$this->test->group($in[$c]);
			$this->assertEqual($out[$c],$this->test->last_word. $this->test->last_append, "%s fail-index:$c");
		}

	}

	/**
	 * test method order
	 */
	function test_order() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$k = x²sql::esc_key;
		$this->test->reset();
		$this->assertEqual(" {$k}col{$k}", $this->test->order(new x²order("col"))->last_append);
		$this->assertEqual(" {$k}col{$k} AsC", $this->test->order(new x²order("col","AsC"))->last_append);
		$this->assertEqual(" {$k}col{$k} asc,{$k}col2{$k},{$k}col3{$k} desC", $this->test->order(array(new x²order("col","asc"),"col2",new x²order("col3","desC")))->last_append);
	}

	/**
	 * test method limit
	 */
	function test_limit($call = "limit", $word = " limit") {
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->test->reset();
		$this->assertEqual("14", $this->test->$call("14")->last_append);
		$this->assertEqual("14", $this->test->$call(14)->last_append);
		$this->assertEqual("13", $this->test->$call(13.5)->last_append);
		$this->assertEqual(" 13.5", $this->test->$call(new x²number(13.5))->last_append);
		$inst = null;
		try {
			$inst = $this->test->$call("2m3");
		} catch (Exception $inst) {
			
		}
		$this->assertIsA($inst, "Exception", "%s :2m3");
	}

	/**
	 * test method offset
	 */
	function test_offset() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->test_limit("offset", " offset");
	}

	/**
	 * test method insert
	 */
	function test_insert() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->test_from("insert", "insert into");
	}

	/**
	 * test method columns
	 */
	function test_columns() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->test->reset();
		$k = x²sql::esc_key;
		$in = array(
			"col",
			"?", ":tok",
			array("id", "name", "value"),
			new x²place("", "no-alias-must-be-set"),
			new x²key("id", "no-alias-must-be-set"),
			new x²string("id", "no-alias-must-be-set")
		);
		$out = array(
			" ({$k}col{$k})",
			" (?)", " (:tok)",
			" ({$k}id{$k},{$k}name{$k},{$k}value{$k})",
			" (?)",
			" ({$k}id{$k})",
			" ({$k}id{$k})"
		);
		//expect pass
		$this->assertEqual(count($in), count($out));
		$c = count($in);
		while ($c--) {
			$inst = $this->test->columns($in[$c]);
			$this->assertEqual($out[$c], $this->test->last_append, "index:$c,%s");
		}

	}

	/**
	 * test method values
	 */
	function test_values() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->test->reset();
		$k = x²sql::esc_key;
		$s = x²sql::esc_string;
		$n = x²sql::esc_num;
		$in = array(
			"col",
			"?", ":tok",
			array("id", "name", "value"),
			new x²place("", "no-alias-must-be-set"),
			new x²key("id", "no-alias-must-be-set"),
			new x²string("id", "no-alias-must-be-set"),
			null,
			true, false,
			234, 4e-1, 0x44,
			456.325,
			new x²bool(true),
			new x²number(12),
			new x²func("convert", null,array("\u034", "utf8"), "alias")
		);
		$out = array(
			" values({$s}col{$s})",
			" values(?)", " values(:tok)",
			" values({$s}id{$s},{$s}name{$s},{$s}value{$s})",
			" values(?)",
			" values({$k}id{$k})",
			" values({$s}id{$s})",
			" values(" . x²sql::null_string . ")",
			" values(1)", " values(0)",
			" values(234)", " values(0.4)", " values(68)",
			" values(456.325)",
			" values(1)",
			" values(12)",
			" values(convert('\u034','utf8'))"
		);
		//expect pass
		$this->assertEqual(count($in), count($out));
		$c = count($in);
		while ($c--) {
			$inst = $this->test->values($in[$c]);
			$this->assertEqual($out[$c], $this->test->last_word.$this->test->last_append, "index:$c,%s");
		}
	}

	/**
	 * test method update
	 */
	function test_update() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->test_from("update", "update");
	}

	/**
	 * test method set
	 */
	function test_set() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->test->reset();
		$x = $this->test;
		$k = x²sql::esc_key;
		$s = x²sql::esc_string;
		$n = x²sql::esc_num;
		$d = x²sql::delimiter;
		$set = new stdClass;
		$set->rt = "?";
		$set->foo = "bar";
		$set->peri = 1234;
		$set->subq = x²sql::query()->select(new x²func("now"));
		$expect = "set {$k}rt{$k}=?{$d}{$k}foo{$k}={$s}bar{$s}{$d}{$k}peri{$k}={$n}1234{$n}{$d}{$k}subq{$k}=select now()";
		$this->test->set($set);
		$this->assertEqual($this->test->last_word.$this->test->last_append, $expect);
	$this->assertTrue($this->test->prepare, $expect);
	}

	/**
	 * test method delete
	 */
	function test_delete() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->test_from("delete", "delete from");
	}

	/**
	 * test method fetch
	 */
	function test_fetch() {
		if (!$this->runTest(__FUNCTION__))
			return;
		;
	}

	/**
	 * test method fetch_type
	 */
	function test_fetch_type() {
		if (!$this->runTest(__FUNCTION__))
			return;
		;
	}

	/**
	 * test method alias
	 */
	function test_alias() {
		if (!$this->runTest(__FUNCTION__))
			return;
		;
	}

	/**
	 * test method name
	 */
	function test_name() {
		if (!$this->runTest(__FUNCTION__))
			return;
		;
	}

	/**
	 * test method comment
	 */
	function test_comment() {
		if (!$this->runTest(__FUNCTION__))
			return;
		;
	}

	/**
	 * test method union
	 */
	function test_union() {
		if (!$this->runTest(__FUNCTION__))
			return;
		$this->test->reset();
		$k = x²sql::esc_key;
		$this->test->select()->from("books")->union(
				x²sql::query()->select()->from("movies"));
		$this->assertEqual($this->test->command, "select * from {$k}books{$k} union select * from {$k}movies{$k}");
	}
	
	function test_x²key(){

		$key = new x²key(new column);
		$this->assertEqual("columnName",$key->value);
	}
	function test_x²string(){

		$key = new x²string(new column);
		$this->assertEqual("columnName",$key->value);
	}
}
		class column {
		public function __toString() {
			return "columnName";
			}
		}
?>
