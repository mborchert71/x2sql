<html>
	<head>
	<meta charset="utf8"/>
	</head>
<body>
<img src="x²sql.png"/>
<p>Code Or Configuration To Structured Query Language</p>
<ul>
    <li><a href="doc">documentation</a></li>
    <li><a href="test">test</a></li>
    <li>
        <a href="https://github.com/mborchert71/x2sql/archive/master.zip">download zip</a>
    </li>
</ul>
<pre>
<?php include 'x²sql.php';

print x²sql::query()->select()->from("table")->limit(100)->offset("?")->command;
print "<br/>";

$cfg = json_decode('{
    "select" : "*",
    "from"   : "table",
    "limit"  : 100,
    "offset" : "?"
    }');

print x²sql::query($cfg)->command;

print_r(x²sql::query($cfg));
