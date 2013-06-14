<?php

$test_class_indikator_prefix = "_";
$pathtoclass = "../";
$class = "x2sql";
$describe = " * The test drive for an open source SQL-generator-class
 * Using the http://simpletest.org/ test-engine.";
$author = "m.borchert";
$license = "GPL GNU PuBLIC LicenSE";
$link = "http://get-resource.net/app/php/x2sql";
$since = "Version 1.0";

require_once('../x2sql.php');
$q = new x2sql;

$head = "<?php 
/**
 * $test_class_indikator_prefix{$class}
 *
 $describe
 * 
 * @package		$class
 * @author		$author
 * @copyright   Copyright (c) " . date("Y") . "
 * @license		$license
 * @link		$link
 * @since		$since
 */
 
\$option = new stdClass;
\$option->complete = true;
\$option->call = array();

include_once '$pathtoclass{$class}.php';

class $test_class_indikator_prefix{$class} extends UnitTestCase {

	public \$test;
    public \$stack=array();
	public \$call=array();
	public \$complete;
    /**
     * 
     */
    function __construct($class \$x=null) {
        parent::__construct();
		\$this->test = \$x = new $class;
		\$this->complete = true;
		<STACK>
    }
    /**
     * SimpleTest builtin
     */
    function setUp() {
	   global \$option;
       if(isset(\$option->complete)) \$this->complete = \$option->complete;
	   if(isset(\$option->call) && is_array(\$option->call)){
		foreach(\$option->call as \$call){
			\$this->setTest(\$call);
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
	 * @param type \$func
	 * @return boolean
	 */
	function runTest(\$func){
		if(!in_array(\$func, \$this->call) && !\$this->complete) return false;
		return true;
	}
	/**
	 * push test to call-stack
	 * @param type \$test
	 */
	function setTest(\$test){
		\$test = (is_numeric(\$test) && \$test<count(\$this->stack)) 
			? \$this->stack[\$test] 
			: (in_array(\$test,\$this->stack) ? \$test : \"\");
		if(!in_array(\$test,\$this->call)) array_push(\$this->call,\$test);
	}\n";
$body = "";
$feet = "}\n//eof";
$stack = array();

foreach (get_class_methods($q) as $i => $name) {
	$body.="\n\t/**\n\t* test method $name\n\t*/\n\tfunction test_$name() {\n\t\tif(!\$this->runTest(__FUNCTION__))return;\n\t\t;\n\t}";
	$stack[$i]="\t\t\$this->stack[$i]=\"test_$name\";";
}

file_put_contents($test_class_indikator_prefix . $class . ".php", str_replace("<STACK>",implode("\n",$stack),$head) . $body . $feet);

?>	