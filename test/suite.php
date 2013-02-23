<?php
/**
 * _x²sql
 *
 * The test drive for an open source SQL-generator-class
 * Using the http://simpletest.org/ test-engine.
 * 
 * 
 */
include_once 'init.php';
/**
 * x2sql a Structured Query Language (SQL) Command Creator
 * 
 * This class enables you to create a SQL-Command from multiple input types.
 * as there are, PHP-Codeand Selrialized, json and xml.
 * These Testcases help in the validating the desired functionality.
 *
 */
class AllTests extends TestSuite {
    /**
     * set in init.php
     * @global type $dir_simpletest
     */
    function __construct() {
        parent::__construct("x²sql");
        global $dir_simpletest;
        (new HtmlReporter("utf8"))->paintHeader("x²sql");
        $this->addFile("_x2sql.php");
    }

}

?>
