<?php

$test_class_indikator_prefix = "_"; //tcip ... ha ha
$class = "x2sql";
$describe = " * The test drive for an open source SQL-generator-class
 * Using the http://simpletest.org/ test-engine.";
$author = "m.borchert";
$license = "http://creativecommons.org/licenses/by/3.0/deed.de";
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
 * @copyright           Copyright (c) " . date("Y") . "
 * @license		$license
 * @link		$link
 * @since		$since
 */
 class $test_class_indikator_prefix{$class} extends UnitTestCase {

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
    }\n";
$body = "";
$feet = "}\n//eof";

foreach (get_class_methods($q) as $i => $name) {
	$body.="\n\t/**\n\t* test method $name\n\t*/\n\tfunction test_$name() {\n\t\t;\n\t}";
}

file_put_contents($test_class_indikator_prefix . $class . ".php", $head . $body . $feet);

?>	