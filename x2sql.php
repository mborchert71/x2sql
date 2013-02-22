<?php

/**
 * x²sql
 *
 * An open source SQL-generator-class.
 * Inspired by the database-driver in Code-igniter http://ellislab.com/codeigniter.
 *
 * @package		x²sql
 * @author		mborchert
 * @copyright           Copyright (c) 2013.
 * @license		http://creativecommons.org/licenses/by/3.0/deed.de
 * @link		http://get-resource.net/app/php/x2sql
 * @since		Version 1.0
 * @todo: union  join  intersect
 * @todo: cleanup: implode, values + combine
 *                 issue: stringvalues in regex_operator get not escaped. (set caller option)
 *                 alias nicht im where, having, group und order (set caller option)
 * @todo: support special settings : distinct, etc.
 * @todo: xml2sql
 */
// ------------------------------------------------------------------------

/**
 * x2sql a Structured Query Language (SQL) Command Creator
 * 
 * This class enables you to create a SQL-Command from multiple input types.
 * as there are, PHP-Codeand Selrialized, json and xml.
 *
 */
class x2sql {
    /**
     * Indicate a string in the ConfigurationSet to be replaced.
     * Default replacement off requested tokens is stringEscaped.
     */

    const TOKENIZER = ":";
    /**
     * The Standard Placeholder in Queries
     */
    const PLACEHOLDER = "?";
    /**
     * Most likely to become a Configparam in the near future
     */
    const ESC_STRING = "'";
    /**
     * Most likely to become a Configparam in the near future
     */
    const ESC_KEY = " ";
    /**
     * Most likely to become a Configparam in the near future
     */
    const ESC_NON = " ";
    /**
     * Most likely to become a Configparam in the near future
     */
    const ESC_NUM = " ";
    /**
     * Most likely to become a Configparam in the near future
     */
    const NULL_STRING = "null";
    /**
     * Operators Regular Expression, to savely not! string-escape these
     */
    const REGEX_OPERATORS = "/^(:=|\|\||OR|XOR|&&|AND|NOT|BETWEEN|CASE|WHEN|THEN|ELSE|=|<=>|>=|<=|<|<>|>|!=|IS|LIKE|REGEXP|IN|\||&|<<|>>|-|\+|\*|\/|DIV|%|MOD|^|~|!|BINARY|COLLATE)$/i";

    /**
     * Give the Query a name
     * 
     * @var str  name
     */
    public $name;

    /**
     * Obligated in use of Subqueries
     * 
     * @var str  alias
     */
    public $alias;

    /**
     * Adding information to the Query
     *
     * @var str comment
     */
    public $comment;

    /**
     * The textoutput of the Query
     * 
     * @var str  command
     */
    public $command;

    /**
     * Supported are : select, update, delete , insert.
     *
     * @var str command_type 
     */
    public $command_type;

    /**
     * Indicating that the prepare routines have to take action.
     *
     * @var bool Flag
     */
    public $prepare = false;

    /**
     * holding both: the placeholders and the tokenizers.
     *
     * @var stdclass
     */
    public $bind;

    /**
     * like argv and argc, the count of prepare actions.
     *
     * @var int
     */
    public $bind_count = 0;

    /**
     * the PDO Queryobject fetch-Functionname.
     * Can easily be wrapped to non PDO connection.
     * 
     * @var str function name
     */
    public $fetch = "fetchAll";

    /**
     * the PDO const FETCH_*  type.
     * NUM, ASSOC, OBJECT, BOTH (NUM,ASSOC)
     * @var int 
     */
    public $fetch_type = PDO::FETCH_NUM;

    /**
     * the initial Feed for the Constructor
     *
     * @var stdClass
     */
    public $init_data;

    /**
     * This function gives us the convenience, to throw in a Configuration Object and getting Out the SqlStatement.
     *
     * @param $cfg
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function Sql(&$cfg) {
        $this->init_data = $cfg;
        if (isset($cfg->select))
            return $this->name(@$cfg->name)
                            ->comment(@$cfg->comment)
                            ->select($cfg->select)
                            ->from($cfg->from)
                            ->where(@$cfg->where)
                            ->having(@$cfg->having)
                            ->group(@$cfg->group)
                            ->order(@$cfg->order)
                            ->limit(@$cfg->limit)
                            ->offset(@$cfg->offset)
                            ->fetch(@$cfg->fetch)
                            ->fetch_type(@$cfg->fetch_type)
                            ->alias(@$cfg->alias);
        if (isset($cfg->insert))
            return $this->name(@$cfg->name)
                            ->comment(@$cfg->comment)
                            ->insert($cfg->insert)
                            ->columns($cfg->columns)
                            ->values($cfg->values);
        if (isset($cfg->update))
            return $this->name(@$cfg->name)
                            ->comment(@$cfg->comment)
                            ->update($cfg->update)
                            ->set($cfg->set)
                            ->where($cfg->where);
        if (isset($cfg->delete))
            return $this->name(@$cfg->name)
                            ->comment(@$cfg->comment)
                            ->delete($cfg->delete)
                            ->where($cfg->where);
    }

    /**
     *
     * @param $cfg
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function __construct($cfg = null) {
        $this->bind = new stdClass;
        if ($cfg) {
            return $this->Sql($cfg);
        }
    }

    /**
     * Static access to a new x2sql instance.
     * Helpful for PHP-Version prior to 5.4.
     * <code>
     * x2sql::query()->select()->from(
     *      "table1",
     *      x2sql::query()->select()->from("table2")->alias("$table2")
     *      );
     * </code>
     * @param $cfg
     *
     * @return x2sql $this
     *
     * @access public
     */
    static function query($cfg = null) {
        return new x2sql($cfg);
    }

    /**
     * 
     * Only utilized by update.values()
     * Shall merge with combine() or implode() as one unique input-iterator.
     * to elaborate!
     * @depreacted
     * 
     * @param $set
     * @param $options
     *
     * @return str
     *
     * @access private
     */
    private function implode_values($set, $options = null) {
        foreach ($set as $key => $val) {
            $set[$key] = $this->escape($val, self::ESC_STRING);
        }
        return implode($set, ",") . " ";
    }

    /**
     * 
     * Besides combine() and implode() a input iterator.
     * Don't know what and if there will be only one iterator.
     *
     * @param $set
     * @param $options
     *
     * @return str
     *
     * @access private
     */
    private function implode($set, $options = null) {
        if (!is_array($set)) {
            return $this->implode(array($set), $options);
        }
        foreach ($set as $key => $val) {
            if (is_a($val, "x2bool")) {
                $set[$key] = $val->value . " "
                        . $this->escape($val->alias, self::ESC_KEY);
            } else if (is_a($val, "x2number")) {
                $set[$key] = $val->value . " "
                        . $this->escape($val->alias, self::ESC_KEY);
            } else if (is_a($val, "x2keyword")) {
                $set[$key] = $this->escape($val->value, self::ESC_KEY) . " "
                        . $this->escape($val->alias, self::ESC_KEY);
            } else if (is_a($val, "x2string")) {
                $set[$key] = $this->escape($val->value, self::ESC_STRING) . " "
                        . $this->escape($val->alias, self::ESC_KEY);
            } else if (is_a($val, "x2sql")) {
                if (!$val->alias)
                    throw new Exception(__CLASS__
                            . "->implode: nested selects need alias");
                $set[$key] = "(" . $val->command . ")"
                        . $this->escape($val->alias, self::ESC_KEY);
                if ($val->prepare) {
                    $this->prepare = true;
                    foreach ($val->bind as $key => $bind) {
                        $this->bind_count+=1;
                        $this->bind->{$this->bind_count + $val->bind_count} = $bind;
                    }
                }
            } else if (is_a($val, "x2function")) {
                $str = array();
                foreach ($val->argus as $arg) {
                    $str[] = $this->combine($arg);
                }
                $set[$key] = $val->name . "(" .
                        implode(",", $str) . ")" .
                        $this->escape($val->alias, self::ESC_KEY);
            } else if (is_object($val)) { // oh zurück zum prepare required etc.
                switch (@$val->type) {
                    case "null" : $set[$key] = $this->implode(
                                array(new x2null($val->value, @$val->alias)));
                        break;
                    case "bool" : $set[$key] = $this->implode(
                                array(new x2bool($val->value, @$val->alias)));
                        break;
                    case "number" : $set[$key] = $this->implode(
                                array(new x2number($val->value, @$val->alias)));
                        break;
                    case "string" : $set[$key] = $this->implode(
                                array(new x2string($val->value, @$val->alias)));
                        break;
                    case "key" : $set[$key] = $this->implode(
                                array(new x2keyword($val->value, @$val->alias)));
                        break;
                    case "query": $set[$key] = $this->implode(
                                array(new x2sql($val))
                        );
                        break;
                    default : print_r($val);
                        throw
                        new Exception(__CLASS__ . "->implode unkown type");

                        break;
                }
            } else if (is_numeric($val))
                $set[$key] = $val;
            if (is_string($val)) {
                //guessing constant string use by char begin end
                $set[$key] = $this->escape($val, self::ESC_KEY);
            }
        }
        return implode($set, ",") . " ";
    }

    /**
     * 
     * Besides combine() and implode() a input iterator.
     * Don't know what and if there will be only one iterator.
     *
     * @param $set
     *
     * @return str
     *
     * @access private
     */
    private function combine(&$set) {
        $str = "";
        if (is_a($set,"x2bool")|| is_a($set,"x2string") || is_a($set,"x2keyword")|| is_a($set,"x2number") || is_a($set,"x2null"))
                return $this->implode(array($set));
        if (is_a($set, "x2sql")) {
            if ($set->prepare) {
                $this->prepare = true;
                foreach ($set->bind as $key => $bind) {
                    $this->bind_count+=1;
                    $this->bind->{$this->bind_count + $set->bind_count} = $bind;
                }
            }
            return "(" . $set->command . ")";
        }
        if (is_a($set, "x2function")) {
            $str = array();
            foreach ($set->argus as $arg) {
                $str[] = $this->combine($arg);
            }
            return $set->name . "(" .
                    implode(",", $str) . ")" .
                    $this->escape($set->alias, self::ESC_KEY);
        }
        if (is_numeric($set))
            return " " . $set . " ";

        if (is_string($set)) {

            return " " . $this->escape($set, self::ESC_STRING) . " ";
        }
        if (is_array($set)) {

            $str = "(";
            foreach ($set as $key => $item) {

                $str.=$this->combine($item);
            }
            return $str . ") ";
        }

        if (is_object($set)) {

            if (@strtolower($set->bind) == "column") {
                return $this->escape($set->key, self::ESC_NON);
            }
            if (isset($set->bind)) {
                $this->prepare = true;
                $this->bind->{$set->key} = $set;
                return $this->escape(self::TOKENIZER . $set->key, self::ESC_NON);
            }
            switch (@$set->type) {
                case "null" : return $this->implode(
                                    array(new x2null($val->value, @$val->alias)));
                    break;
                case "bool" : return $this->implode(
                                    array(new x2bool($val->value, @$val->alias)));
                    break;
                case "number" : return $this->implode(
                                    array(new x2number($val->value, @$val->alias)));
                    break;
                case "string" : return $this->implode(
                                    array(new x2string($val->value, @$val->alias)));
                    break;
                case "key" : return $this->implode(
                                    array(new x2keyword($val->value, @$val->alias)));
                    break;
                case "query": return $this->implode(array(new x2sql($val)));
                    break;
                default : print_r($val);
                    throw
                    new Exception(__CLASS__ . "->implode unkown type");

                    break;
            }
        }
    }

    /**
     * 
     * escape the string with the appropiate scope-chars and take care of security.
     *
     * @param $str
     * @param $esc
     * @param $pos optional, default -1 (used in where [o1,op,o2] [x,=,y]
     * pos 1 will not be escaped if operand is valid
     *
     * @return str
     *
     * @access public
     */
    public function escape($str, $esc, $pos = -1) {
        $str = trim($str);
        $char = substr($str, 0, 1);

        if ($char != self::PLACEHOLDER && substr($str, 0, 1) != self::TOKENIZER) {

            if ($str === self::NULL_STRING || $str === null) {
                return self::NULL_STRING;
            }
            if (is_numeric($str)) {
                return " " . $str . " ";
            }
            if (preg_match(self::REGEX_OPERATORS, $str)) {
                return $str;
            }

            $ndl = array("/r/", "/n/", "/t/");
            $rep = array("r", "n", "t");

            if (trim($esc)) {
                $ndl[] = "/" . $esc . "/";
                $rep[] = "$esc";
            }
            return $esc . preg_replace($ndl, $rep, $str) . $esc;
        } else {
            $this->prepare = true;
            if (preg_match("/\s/", $str))
                throw new Exception(__CLASS__ . "->escape prepare-token must not have space");
            $counter = $this->bind_count++;
            $this->bind->$counter = new stdClass();
            $this->bind->$counter->bind = "Param";
            $this->bind->$counter->key = $str;
            $this->bind->$counter->type = PDO::PARAM_STR;
            $this->bind->$counter->value = self::NULL_STRING;
        }

        return $str;
    }

    /**
     * 
     * The columns to select.
     * By default every inputfield from array gets keyword-escaped.
     * Use the x2-HelperClasses to get different output.
     * <code>
     * x2sql::query()
     *   ->select(new x2func("count","*","mycount"))
     *   ->from("table")
     *   ->group("mygroup");
     * </code>
     * @param $set mixed
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function select($set = array("*")) {
        if (!$this->command_type)
            $this->command_type = __FUNCTION__;
        $this->command.= __FUNCTION__ . " " . $this->implode($set);
        return $this;
    }

    /**
     * 
     * Expecting one tablename string , an array of tablesnames and or x2sql-objects. 
     * 
     * @note subqueries must provide an alias!
     * @param $set mixed
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function from($set) {
        $this->command.= __FUNCTION__ . " " . $this->implode($set);
        return $this;
    }

    /**
     * 
     * The where-filter can be a deep nested array, with three to five fields.
     * Normally 3 fields are used to get a operand operator operand construct.
     * <code>
     * array("three","<>",3);
     * </code>
     * the "between" operation accepts 5 fields:
     * <code>
     * array(23,"between",8,"and",400);
     * </code>
     * The default handling is string escape if not a recognized operator.
     * Use the HelperClasses to force a specific type: x2bool,x2number,x2keyword,x2func.
     * And supqueries are supported , of course.
     * <code>
     * x2sql::query()->select()->from("table")->where(array(
     *     x2sql::query()->select("id")->from("table")->where(array("name","=","herbert")),
     *     "=",234 
     *     ))->command;
     * @param $set
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function where($set) {
        if ((is_array($set) && count($set)) || is_string($set)) {
            $this->command.=" " . __FUNCTION__ . " ";
            $this->command.= $this->combine($set);
        }
        return $this;
    }

    /**
     * 
     * for details look at method: where.
     *
     * @param $set
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function having($set) {
        if ((is_array($set) && count($set)) || is_string($set)) {
            $this->command.=" " . __FUNCTION__ . " ";
            $this->command.= $this->combine($set);
        }
        return $this;
    }

    /**
     * 
     * for details see method: select
     *
     * @param $set
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function group($set) {
        if ((is_array($set) && count($set)) || is_string($set)) {
            $this->command.=" " . __FUNCTION__ . " by ";
            $this->command.= $this->implode($set);
        }
        return $this;
    }

    /**
     * 
     * for details see method: select
     *
     * @param $set
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function order($set) {
        if ((is_array($set) && count($set)) || is_string($set)) {
            $this->command.=" " . __FUNCTION__ . " by ";
            $this->command.= $this->implode($set);
        }
        return $this;
    }

    /**
     * 
     * restricts the amount a recordsets
     *
     * @param $set int
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function limit($set) {
        if (is_string($set) || is_numeric($set)) {
            $this->command.=" " . __FUNCTION__ . " ";
            $this->command.=$this->escape($set, self::ESC_NUM);
        }
        return $this;
    }

    /**
     * 
     * Sets the start position of recordset return
     *
     * @param $set int
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function offset($set) {
        if (is_string($set) || is_numeric($set)) {
            $this->command.=" " . __FUNCTION__ . " ";
            $this->command.=$this->escape($set, self::ESC_NUM);
        }
        return $this;
    }

    /**
     * 
     * for details see method 'from'.
     *
     * @param $set
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function insert($set) {
        if (!$this->command_type)
            $this->command_type = __FUNCTION__;
        $this->command.= __FUNCTION__ . " into " . $this->implode($set);
        return $this;
    }

    /**
     * 
     * for details see method 'select'
     *
     * @param $set
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function columns($set) {
        $this->command.= "(" . $this->implode($set) . ")";
        return $this;
    }

    /**
     * 
     * Default escaping is string Escape, use Placeholder or Token to have better control.
     * At this time the x2-helpers are not supported.
     * @param $set
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function values($set) {
        $this->command.= __FUNCTION__ . " (" . $this->implode_values($set) . ")";
        return $this;
    }

    /**
     * 
     * for details see method 'from'
     *
     * @param $set
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function update($set) {
        if (!$this->command_type)
            $this->command_type = __FUNCTION__;
        $this->command.= __FUNCTION__ . " " . $this->implode($set);
        return $this;
    }

    /**
     * 
     * Assoc array or stdClass.
     * Key ist Columnname and Field is value forthe Column.
     * x2-HelperClasses are not supported.
     * 
     * @param $set
     * @param $options
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function set($set, $options = null) {
        $this->command.=" " . __FUNCTION__ . " ";
        foreach ($set as $key => $val) {
            $this->command.= $this->escape($key, self::ESC_KEY);
            $this->command.= "=";
            $this->command.= $this->escape($val, self::ESC_STRING);
        }
        $this->command.=" ";
        return $this;
    }

    /**
     * 
     * for details see method 'from'
     *
     * @param $set
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function delete($set) {
        if (!$this->command_type)
            $this->command_type = __FUNCTION__;
        $this->command.= __FUNCTION__ . " from " . $this->implode($set);
        return $this;
    }

    /**
     * 
     * The PDO fetch-method as string
     *
     * @param $fetch
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function fetch($fetch) {
        if ($fetch)
            $this->fetch = $fetch;
        return $this;
    }

    /**
     * 
     * The Pdo fetchtype eg: PDO::FETCH_NUM
     *
     * @param $type int
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function fetch_type($type) {
        if ($type)
            $this->fetch_type = $type;
        return $this;
    }

    /**
     * 
     * the table alias in subqueries
     *
     * @param $alias
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function alias($alias) {
        $this->alias = $alias;
        return $this;
    }

    /**
     * 
     * the name for the queries. To give a query-manager something to work with. 
     *
     * @param $name
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function name($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * 
     * Additional information for the Statement
     *
     * @param $name
     *
     * @return x2sql $this
     *
     * @access public
     */
    public function comment($text) {
        $this->name = $text;
        return $this;
    }

}

/**
 * 
 * Helper Class, to generate function statements.
 * x2function maybe deep-nested, of course.
 *
 * @package x2sql
 * @since
 */
class x2function {

    public $name;
    public $alias;
    public $argus;
    public $text;

    /**
     * __construct
     * Insert description here
     *
     * @param $name
     * @param $argus
     * @param $alias
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function __construct($name, $argus = null, $alias = null) {
        if (preg_match("/\s|\n|\r|\t/", $name))
            throw new Exception(__CLASS__ . "->name must not have spaces");
        $this->name = $name;
        if (preg_match("/\s|\n|\r|\t/", $alias))
            throw new Exception(__CLASS__ . "->alias must not have spaces");
        $this->alias = $alias;

        if (is_bool($argus) || is_numeric($argus) || is_string($argus)) {
            $this->argus = array($argus);
        } else if ($argus === null)
            $this->argus = array();
        else
            $this->argus = $argus;
    }

}

/**
 * 
 * Avoid string escaping and mark the statement to be prepared
 *
 * @package x2sql
 * @since
 */
class x2token {

    public $type;
    public $value;
    public $alias;

    /**
     * __construct
     * Insert description here
     *
     * @param $value
     * @param $alias
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function __construct($value, $alias = "") {
        $this->value = $value;
        $this->alias = $alias;
    }

}

/**
 * 
 * for completeness
 *
 * @package x2sql
 * @since
 */
class x2string extends x2token {

    /**
     * __construct
     * Insert description here
     *
     * @param $value
     * @param $alias
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function __construct($value, $alias = "") {
        if (!is_string($value))
            throw new Exception(__CLASS__ . "->value is not string");
        parent::__construct($value, $alias);
        $this->type = __CLASS__;
    }

}

/**
 * 
 * force database-keyword escaping: columns, tablenames, databasename.
 *
 * @package x2sql
 * @since
 */
class x2keyword extends x2token {

    /**
     * __construct
     * Insert description here
     *
     * @param $value
     * @param $alias
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function __construct($value, $alias = "") {
        if (preg_match("/\s/", $value))
            throw new Exception(__CLASS__ . "->value space is not allowed");
        parent::__construct($value, $alias);
        $this->type = __CLASS__;
    }

}

/**
 * 
 * Allow 'true' and 'false' string to be recognized as booleans
 *
 * @package x2sql
 * @since
 */
class x2bool extends x2token {

    /**
     * __construct
     * Insert description here
     *
     * @param $value
     * @param $alias
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function __construct($value, $alias = "") {
        if (!is_bool($value) && !preg_match("/true|false/i", $value)) // localized ...
            throw new Exception(__CLASS__ . "->value is no boolean");
        parent::__construct($value, $alias);
        $this->type = __CLASS__;
    }

}

/**
 * 
 * skip string escaping, if it is a valid number of course.
 *
 * @package x2sql
 * @since
 */
class x2number extends x2token {

    /**
     * __construct
     * Insert description here
     *
     * @param $value
     * @param $alias
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function __construct($value, $alias = "") {
        if (!is_numeric($value))
            throw new Exception(__CLASS__ . "->value is no number");
        parent::__construct($value, $alias);
        $this->type = __CLASS__;
    }

}

/**
 * 
 * Allow string 'null' to be handled as PHP/DB null.
 *
 * @package x2sql
 * @since
 */
class x2null extends x2token {

    /**
     * __construct
     * Insert description here
     *
     * @param $value
     * @param $alias
     *
     * @return
     *
     * @access
     * @static
     * @see
     * @since
     */
    public function __construct($value = null, $alias = "") {
        if ($value !== null && $value !== x2sql::NULL_STRING)
            throw new Exception(__CLASS__ . "->value is no valid null");
        parent::__construct($value, $alias);
        $this->type = __CLASS__;
    }

}

/**
 * class alias "x2function", "x2func"
 */
class_alias("x2function", "x2func");
/**
 * class alias "x2keyword", "x2key"
 */
class_alias("x2keyword", "x2key");
?>
