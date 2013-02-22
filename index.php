<img src="x2sql.png"/>
<p>Code Or Configuration To Structured Query Language</p>
<ul>
    <li><a href="doc">documentation</a></li>
    <li><a href="test">test</a></li>
    <li>
        <a href="x2sql-latest.zip">download zip</a>
        <a href="x2sql-latest.targz">download targz</a>
    </li>
</ul>

<div class="prettyprint"><pre><code>
&lt;?php

print x2sql::query()->select()->from("table")->limit(100)->offset("?")->command;

// select * from table limit 100 offset ?;

//the same with a config string:

$cfg = json_decode('{
    "select" : "*",
    "from"   : "table",
    "limit"  : "offset",
    "offset" : "?"
    }');

print x2sql::query($cfg)->command;

//select * from table limit 100 offset ?;

print_r(x2sql::query($cfg));

?&gt;
    </code></pre></div><pre>
<?php include 'x2sql.php';

print x2sql::query()->select()->from("table")->limit(100)->offset("?")->command;
print "<br/>";

$cfg = json_decode('{
    "select" : "*",
    "from"   : "table",
    "limit"  : 100,
    "offset" : "?"
    }');

print x2sql::query($cfg)->command;

print_r(x2sql::query($cfg));
