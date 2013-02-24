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
 * @link		http://get-resource.net/app/php/x²sql
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
 * x²sql a Structured Query Language (SQL) Command Creator
 * 
 * This class enables you to create a SQL-Command from multiple input types.
 * as there are, PHP-Codeand Selrialized, json and xml.
 *
 */
class x²sql {
	/**
	 * Indicate a string in the ConfigurationSet to be replaced.
	 * Default replacement off requested tokens is stringEscaped.
	 */

	const tokenizer = ":";
	/**
	 * The Standard Placeholder in Queries
	 */
	const placerholder = "?";
	/**
	 *
	 */
	const esc_string = "'";
	/**
	 *
	 */
	const esc_key = "`";
	/**
	 *
	 */
	const esc_non = "";
	/**
	 *
	 */
	const esc_num = "";
	/**
	 *
	 */
	const null_string = "null";
	/**
	 * Operators Regular Expression, to savely not! string-escape these
	 */
	const regex_operators = "/^(:=|\|\||OR|XOR|&&|AND|NOT|BETWEEN|CASE|WHEN|THEN|ELSE|=|<=>|>=|<=|<|<>|>|!=|IS|LIKE|REGEXP|IN|\||&|<<|>>|-|\+|\*|\/|DIV|%|MOD|^|~|!|BINARY|COLLATE)$/i";
	/**
	 * 
	 */
	const x²null = "x²null";
	/**
	 * 
	 */
	const x²bool = "x²bool";
	/**
	 * 
	 */
	const x²number = "x²number";
	/**
	 * 
	 */
	const x²string = "x²string";
	/**
	 * 
	 */
	const x²func = "x²func";
	/**
	 * 
	 */
	const x²token = "x²token";
	/**
	 * 
	 */
	const x²place = "x²place";
	/**
	 * 
	 */
	const x²key = "x²key";
	/**
	 * 
	 */
	const char_list_delimiter = ",";
	/**
	 * 
	 */
	const char_bracket_open = "(";
	/**
	 * 
	 */
	const char_bracket_close = ")";

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
	 * the last chunk appended to command 
	 *
	 * @var string
	 */
	public $last_append;

	/**
	 * This function gives us the convenience, to throw in a Configuration Object and getting Out the SqlStatement.
	 *
	 * @param $cfg
	 *
	 * @return x²sql $this
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
	 * @return x²sql $this
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
	 * Static access to a new x²sql instance.
	 * Helpful for PHP-Version prior to 5.4.
	 * <code>
	 * x²sql::query()->select()->from(
	 *      "table1",
	 *      x²sql::query()->select()->from("table2")->alias("$table2")
	 *      );
	 * </code>
	 * @param $cfg
	 *
	 * @return x²sql $this
	 *
	 * @access public
	 */
	static function query($cfg = null) {
		return new x²sql($cfg);
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
	private function implode($set) {
		//scalar
		//object stdClass from Json by type
		//x² helpers
		if (is_string($set) && ( $set == self::placerholder || substr($set, 0, 1) == self::tokenizer)) {
			$this->prepare = true;
			if (preg_match("/\s/", $set))
				throw new Exception(__CLASS__ . "->escape prepare-token must not have space");
			$counter = $this->bind_count++;
			$this->bind->$counter = new stdClass();
			$this->bind->$counter->bind = "Param";
			$this->bind->$counter->key = $set;
			$this->bind->$counter->type = PDO::PARAM_STR;
			$this->bind->$counter->value = self::null_string;
		}
		if (is_a($set, "x²bool")) {
			$set = ($set->value ? "1" : "0") . " "
					. self::escape($set->alias, self::esc_key);
		} else if (is_a($set, "x²number")) {
			$set = $set->value . " "
					. self::escape($set->alias, self::esc_key);
		} else if (is_a($set, "x²key")) {
			$set = self::escape($set->value, self::esc_key) . " "
					. self::escape($set->alias, self::esc_key);
		} else if (is_a($set, "x²string")) {
			$set = self::escape($set->value, self::esc_string) . " "
					. self::escape($set->alias, self::esc_key);
		} else if (is_a($set, "x²token")) {
			$set = self::escape(self::tokenizer . $set->value, self::esc_non) . " "
					. self::escape($set->alias, self::esc_key);
		} else if (is_a($set, "x²sql")) {
			if (!$set->alias)
				throw new Exception(__CLASS__
				. "->implode: nested selects need alias");
			$set = "(" . $set->command . ")"
					. self::escape($set->alias, self::esc_key);
			if (@$set->prepare) {
				$this->prepare = true;
				foreach ($set->bind as $key => $bind) {
					$this->bind_count+=1;
					$this->bind->{$this->bind_count + $set->bind_count} = $bind;
				}
			}
		} else if (is_a($set, "x²func")) {
			$str = array();
			foreach ($set->argus as $arg) {
				$str[] = $this->combine($arg);
			}
			$set = $set->name . "(" .
					implode(",", $str) . ")" .
					self::escape($set->alias, self::esc_key);
		} else if (is_object($set)) {
			switch (@$set->type) {
				case "null" :case "bool" :case "number" :case "string" :case "key" :case "token" :
					$x²class = "x²" . $set->type;
					$set = $this->implode(new $x²class($set->value, @$set->alias));
					break;
				case "func": $set = $this->implode(new x²func($set->name, @$set->argus, @$set->alias));
					break;
				case "query": $set = $this->implode(new x²sql($set));
					break;
				default :
					throw
					new Exception(__CLASS__ . "->implode unkown type");
					break;
			}
		} elseif (is_bool($set)) {
			$set = $set ? "1" : "0";
		} elseif (is_numeric($set)) {
			;
		} elseif (is_string($set)) {
			$set = self::escape($set, self::esc_key);
		} elseif (is_array($set)) {
			foreach ($set as $key => $val)
				$set[$key] = $this->implode($val);
			$set = implode($set, ",") . " ";
		}
		return $set;
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
		if (is_string($set) && ( $set == self::placerholder || substr($set, 0, 1) == self::tokenizer)) {
			$this->prepare = true;
			if (preg_match("/\s/", $str))
				throw new Exception(__CLASS__ . "->escape prepare-token must not have space");
			$counter = $this->bind_count++;
			$this->bind->$counter = new stdClass();
			$this->bind->$counter->bind = "Param";
			$this->bind->$counter->key = $str;
			$this->bind->$counter->type = PDO::PARAM_STR;
			$this->bind->$counter->value = self::null_string;
			return $set;
		}
		if (is_a($set, "x²bool") || is_a($set, "x²string") || is_a($set, "x²key") || is_a($set, "x²number") || is_a($set, "x²null"))
			return $this->implode(array($set));
		if (is_a($set, "x²sql")) {
			if ($set->prepare) {
				$this->prepare = true;
				foreach ($set->bind as $key => $bind) {
					$this->bind_count+=1;
					$this->bind->{$this->bind_count + $set->bind_count} = $bind;
				}
			}
			return "(" . $set->command . ")";
		}
		if (is_a($set, "x²func")) {
			$str = array();
			foreach ($set->argus as $arg) {
				$str[] = $this->combine($arg);
			}
			return $set->name . "(" .
					implode(",", $str) . ")" .
					self::escape($set->alias, self::esc_key);
		}
		if (is_numeric($set))
			return " " . $set . " ";

		if (is_string($set)) {

			return " " . self::escape($set, self::esc_string) . " ";
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
				return self::escape($set->key, self::esc_non);
			}
			if (isset($set->bind)) {
				$this->prepare = true;
				$this->bind->{$set->key} = $set;
				return self::escape(self::tokenizer . $set->key, self::esc_non);
			}
			switch (@$set->type) {
				case "null" : return $this->implode(
									array(new x²null($val->value, @$val->alias)));
					break;
				case "bool" : return $this->implode(
									array(new x²bool($val->value, @$val->alias)));
					break;
				case "number" : return $this->implode(
									array(new x²number($val->value, @$val->alias)));
					break;
				case "string" : return $this->implode(
									array(new x²string($val->value, @$val->alias)));
					break;
				case "key" : return $this->implode(
									array(new x²keyword($val->value, @$val->alias)));
					break;
				case "query": return $this->implode(array(new x²sql($val)));
					break;
				default :
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
	static public function escape($str, $esc=self::esc_string) {
		$str = trim($str);
		$char = substr($str, 0, 1);
		if (is_bool($str))
			return $str ? $esc."1".$esc : $esc."0".$esc;
		elseif ($str === self::placerholder)
			return $str;
		elseif ($str === self::null_string || $str === null) {
			return self::null_string;
		}
		elseif (is_numeric($str)) {
			return $esc . $str . $esc;
		}
		elseif (preg_match(self::regex_operators, $str)) {
			return $str;
		}
		elseif (substr($str, 0, 1) != self::tokenizer){

			$ndl = array("/\\r/", "/\\n/", "/\\t/");
			$rep = array("\\r", "\\n", "\\t");

			if (trim($esc)) {
				$ndl[] = "/" . $esc . "/";
				$rep[] = "$esc";
			}
			return $esc . preg_replace($ndl, $rep, $str) . $esc;
		} else {
			if (preg_match("/\s/", $str))
				throw new Exception("tokenizer must have no space");
		}

		return $str;
	}

	/**
	 * 
	 * The columns to select.
	 * By default every inputfield from array gets keyword-escaped.
	 * Use the x²-HelperClasses to get different output.
	 * <code>
	 * x²sql::query()
	 *   ->select(new x²func("count","*","mycount"))
	 *   ->from("table")
	 *   ->group("mygroup");
	 * </code>
	 * @param $set mixed
	 *
	 * @return x²sql $this
	 *
	 * @access public
	 */
	public function select($set = array("*")) {
		if ($set === "" || $set === null)
			$set = array("*");
		if (!$this->command_type)
			$this->command_type = __FUNCTION__;
		$cfg = new stdClass;
		$cfg->no_brackets = true;
		$cfg->cast = "x²key";
		$this->command.= $this->last_append = __FUNCTION__ . " " . $this->complode($set,$cfg);
		return $this;
	}

	/**
	 * 
	 * Expecting one tablename string , an array of tablesnames and or x²sql-objects. 
	 * 
	 * @note subqueries must provide an alias!
	 * @param $set mixed
	 *
	 * @return x²sql $this
	 *
	 * @access public
	 */
	public function from($set) {
		$cfg = new stdClass;
		$cfg->cast = "x²key";
		$cfg->allow=array(self::x²string,self::x²key,self::x²token,__CLASS__);
		$cfg->no_brackets=true;
		$this->command.= $this->last_append = " ".__FUNCTION__ . " " . $this->complode($set,$cfg);
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
	 * Use the HelperClasses to force a specific type: x²bool,x²number,x²keyword,x²func.
	 * And supqueries are supported , of course.
	 * <code>
	 * x²sql::query()->select()->from("table")->where(array(
	 *     x²sql::query()->select("id")->from("table")->where(array("name","=","herbert")),
	 *     "=",234 
	 *     ))->command;
	 * @param $set
	 *
	 * @return x²sql $this
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
	 * @return x²sql $this
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
	 * @return x²sql $this
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
	 * @return x²sql $this
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
	 * @return x²sql $this
	 *
	 * @access public
	 */
	public function limit($set) {
		if (is_string($set) || is_numeric($set)) {
			$this->command.=" " . __FUNCTION__ . " ";
			$this->command.=self::escape($set, self::esc_num);
		}
		return $this;
	}

	/**
	 * 
	 * Sets the start position of recordset return
	 *
	 * @param $set int
	 *
	 * @return x²sql $this
	 *
	 * @access public
	 */
	public function offset($set) {
		if (is_string($set) || is_numeric($set)) {
			$this->command.=" " . __FUNCTION__ . " ";
			$this->command.=self::escape($set, self::esc_num);
		}
		return $this;
	}

	public function complode($set, $cfg=null) {

		if (is_array($set)) {
			foreach ($set as $subset)
				$_[] = $this->complode($subset, $cfg);
			return ((@$cfg->no_brackets) ? "" : x²sql::char_bracket_open)
					. implode(x²sql::char_list_delimiter, $_)
					. ((@$cfg->no_brackets) ? "" : x²sql::char_bracket_close);
		} elseif (is_object($set)) {
			$class = get_class($set);
			if (isset($cfg->allow) && !in_array($class, $cfg->allow)) {
				if ($class != "stdClass")
					throw new Exception("$class is not allowed");
			}
			switch ($class) {
				case self::x²place :
					if (@$cfg->no_alias) {
						return x²sql::escape($set->value, "");
					}
					else
						return $set->display;
					break;
				case self::x²token :
					if(2>strlen($set->value))
						throw new Exception(__CLASS__."->complode emptyString not allowed");					
					if (@$cfg->no_alias) {
						return x²sql::escape(x²sql::tokenizer .$set->value, "");
					}
					else
						return $set->display;
					break;
				case self::x²null:
				case self::x²bool :
				case self::x²number :
				case self::x²string :
				case self::x²key :
					if($class==self::x²string && !strlen($set->value)
					) throw new Exception(__CLASS__."->complode emptyString not allowed");
					if (@$cfg->escape) {
						return x²sql::escape($set->value, $cfg->escape)
								.(!@$cfg->no_alias
								? " " . x²sql::escape($set->alias, self::esc_key) : "");
					}
					elseif (@$cfg->no_alias) {
							return x²sql::escape($set->value, $set->escape);
					}else{
						return $set->display;
						}
					break;
				case self::x²func:
					$str = array();
					foreach ($set->argus as $arg) {
						$str[] = $this->complode($arg);
					}
					return $set->name
							. self::char_bracket_open
							. implode(self::char_list_delimiter, $str)
							. self::char_bracket_close .
							self::escape($set->alias, self::esc_key);
					break;
				case __CLASS__:
					if ($set->prepare) {
						$this->prepare = true;
						foreach ($set->bind as $key => $bind) {
							$this->bind_count+=1;
							$this->bind->{$this->bind_count + $set->bind_count} = $bind;
						}
					}
					return self::char_bracket_open
							. $set->command
							. self::char_bracket_close
							. (@$cfg->no_alias ?"": " " . self::escape($set->alias, self::esc_key));
				default:
					if (!isset($set->type))
						throw new Exception("$class::property:type does not exist");
					//$this->stdClass2x²class
					switch (@$set->type) {
						case self::x²null:
						case self::x²bool :
						case self::x²number :
						case self::x²place :
						case self::x²token :
						case self::x²string :
						case self::x²key :
							$class = $set->type;
							return $this->complode(new $class($set->value, @$set->alias), $cfg);
							break;
						case self::x²func :
							return $this->complode(new x²func($set->name, @$set->argus, @$set->alias), $cfg);
							break;
						case __CLASS__:
							return $this->complode(new x²sql($set), $cfg);
							break;
						default:throw
							new Exception(__CLASS__ . "->implode unkown type");
							break;
					}
					///$this->stdClass2x²class
					break;
			}
		} elseif (is_string($set)) {
			if ($set == self::placerholder) {
				return  $this->complode(new x²place($set), $cfg);
			} elseif (substr($set, 0, 1) == self::tokenizer) {
				return  $this->complode(new x²token(substr($set,1)), $cfg);
			} else {
				$class = @$cfg->cast ? $cfg->cast : "x²string";
				return $this->complode(new $class($set), $cfg);
			}
		} elseif (is_bool($set)) {
			$set = $set ? "1" : "0";
			$class = @$cfg->cast ? $cfg->cast : "x²bool";
			return  $this->complode(new $class($set), $cfg);
		} elseif (is_numeric($set)) {
			$class = @$cfg->cast ? $cfg->cast : "x²number";
			return  $this->complode(new $class($set), $cfg);
		}
		throw new Exception("you should never reach this point");
	}

	/**
	 * 
	 * for details see method 'from'.
	 *
	 * @param $set
	 *
	 * @return x²sql $this
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
	 * @return x²sql $this
	 *
	 * @access public
	 */
	public function columns($set) {
		if (!is_array($set)) {
			$set = array($set);
		}
		$cfg = new stdClass();
		$cfg->escape = self::esc_key;
		$cfg->allow = array(self::x²key, self::x²string, self::x²place, self::x²token);
		$cfg->no_alias= true;
		$this->command.= $this->last_append = $this->complode($set, $cfg);
		return $this;
	}

	/**
	 * 
	 * Default escaping is string Escape, use Placeholder or Token to have better control.
	 * At this time the x²-helpers are not supported.
	 * @param $set
	 *
	 * @return x²sql $this
	 *
	 * @access public
	 */
	public function values($set) {
		$this->command.= __FUNCTION__ . " (" . $this->implode($set) . ")";
		return $this;
	}

	/**
	 * 
	 * for details see method 'from'
	 *
	 * @param $set
	 *
	 * @return x²sql $this
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
	 * x²-HelperClasses are not supported.
	 * 
	 * @param $set
	 * @param $options
	 *
	 * @return x²sql $this
	 *
	 * @access public
	 */
	public function set($set, $options = null) {
		$this->command.=" " . __FUNCTION__ . " ";
		foreach ($set as $key => $val) {
			$this->command.= self::escape($key, self::esc_key);
			$this->command.= "=";
			$this->command.= self::escape($val, self::esc_string);
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
	 * @return x²sql $this
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
	 * @return x²sql $this
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
	 * @return x²sql $this
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
	 * @return x²sql $this
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
	 * @return x²sql $this
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
	 * @return x²sql $this
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
 * x²func maybe deep-nested, of course.
 *
 * @package x²sql
 * @since
 */
class x²func {

	public $name;
	public $alias;
	public $argus;
	public $text;

	/**
	 * 
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
 * @package x²sql
 * @since
 */
class x²base {

	public $value;
	public $alias;
	public $display;
	private $escape = x²sql::esc_string;
	/**
	 * 
	 */
	public function __construct($value, $alias = "") {
		$this->value = $value;
		$this->alias = $alias;
		$this->display = x²sql::escape($value, x²sql::esc_string)
				. ($this->alias ? " " . x²sql::escape($this->alias, x²sql::esc_key) : "");
	}

}

/**
 * 
 * Avoid string escaping and mark the statement to be prepared
 *
 * @package x²sql
 * @since
 */
class x²token extends x²base {
	private $escape = x²sql::esc_non;
	/**
	 * 
	 */
	public function __construct($value, $alias = "") {
		if (preg_match("/\s/", $value))
			throw new Exception(__CLASS__ . "->value space is not allowed");
		$value = (substr($value,0,1)==x²sql::tokenizer)? substr($value,1) : $value;
		parent::__construct($value, $alias);
		$this->display = x²sql::tokenizer . x²sql::escape($value, x²sql::esc_non)
				. ($this->alias ? " " . x²sql::escape($this->alias, x²sql::esc_key) : "");
		;
	}

}

/**
 * 
 * for completeness
 *
 * @package x²sql
 * @since
 */
class x²string extends x²base {

	/**
	 * 
	 */
	public function __construct($value, $alias = "") {
		if (!is_string($value))
			throw new Exception(__CLASS__ . "->value is not string");
		parent::__construct($value, $alias);
	}

}

/**
 * 
 * force database-keyword escaping: columns, tablenames, databasename.
 *
 * @package x²sql
 * @since
 */
class x²key extends x²base {
	private $escape = x²sql::esc_key;

	/**
	 * 
	 */
	public function __construct($value, $alias = "") {
		if (preg_match("/\s/", $value))
			throw new Exception(__CLASS__ . "->value space is not allowed");
		parent::__construct($value, $alias);
		$this->display = x²sql::escape($value, x²sql::esc_key)
				. ($this->alias ? " " . x²sql::escape($this->alias, x²sql::esc_key) : "");
		;
	}

}

/**
 * 
 * Allow 'true' and 'false' string to be recognized as booleans
 *
 * @package x²sql
 * @since
 */
class x²bool extends x²base {
	private $escape = x²sql::esc_non;

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
		$this->display = ($value ? "1" : "0")
				. ($this->alias ? " " . x²sql::escape($this->alias, x²sql::esc_key) : "");
	}

}

/**
 * 
 * skip string escaping, if it is a valid number of course.
 *
 * @package x²sql
 * @since
 */
class x²number extends x²base {
	private $escape = x²sql::esc_non;

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
		$this->display = x²sql::escape($value, x²sql::esc_non)
				. ($this->alias ? " " . x²sql::escape($this->alias, x²sql::esc_key) : "");
		;
	}

}

/**
 * 
 * Allow string 'null' to be handled as PHP/DB null.
 *
 * @package x²sql
 * @since
 */
class x²null extends x²base {
	private $escape = x²sql::esc_non;

	/**
	 * __construct
	 * Insert description here
	 *
	 * @access
	 * @static
	 * @see
	 * @since
	 */
	public function __construct($value = null, $alias = "") {
		if ($value !== null && $value !== x²sql::null_string)
			throw new Exception(__CLASS__ . "->value is no valid null");
		parent::__construct($value, $alias);
		$this->display = x²sql::null_string
				. ($this->alias ? " " . x²sql::escape($this->alias, x²sql::esc_key) : "");
	}

}

/**
 * 
 * Allow string 'null' to be handled as PHP/DB null.
 *
 * @package x²sql
 * @since
 */
class x²place extends x²base {
	private $escape = x²sql::esc_non;

	/**
	 * Insert description here
	 *
	 * @access
	 * @static
	 * @see
	 * @since
	 */
	public function __construct($value = null, $alias = "") {
		parent::__construct(x²sql::placerholder, $alias);
		$this->display = x²sql::placerholder
				. ($this->alias ? " " . x²sql::escape($this->alias, x²sql::esc_key) : "");
	}

}

?>
