<?php 
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
//include_once 'init.php';
require_once('../x2sql.php');

$q = new x2sql;
$test_class_indikator_prefix="_";//tcip ... ha ha
$class = "x2sql";
$describe=" * The test drive for an open source SQL-generator-class
 * Using the http://simpletest.org/ test-engine.";
$author="m.borchert";
$license = "http://creativecommons.org/licenses/by/3.0/deed.de";
$link = "http://get-resource.net/app/php/x2sql";
$since =  "Version 1.0";
$head = "<?php 
/**
 * $test_class_indikator_prefix{$class}
 *
 $describe
 * 
 * @package		$class
 * @author		$author
 * @copyright           Copyright (c) ".date("Y")."
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

foreach(class_get_methods($q) as $i => $name){
    $body.="\n\t/**\n\t* test method $name\n\t*/\n\tfunction test_$name() {\n\t\t;\n\t}";
}

file_put_contents($test_class_indikator_prefix.$class.".php",$head.$body.$feet);
exit;
