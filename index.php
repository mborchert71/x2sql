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
        <a href="x²sql-latest.zip">download zip</a>
        <a href="x²sql-latest.targz">download targz</a>
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
