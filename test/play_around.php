<pre>
<?php

include_once '../x²sql.php';


$col = "col";
$val = "fields";
$sql = x²sql::query()
->select(array($val, new x²string('actor', $col)))
->from('actors')
->union(
    x²sql::query()
    ->select(array($val, new x²string('event', $col)))
    ->from('events'))
->union(
    x²sql::query()
    ->select(array($val, new x²string('location', $col)))
    ->from('locations')
)->command;

print $sql."\n\n";

$cfg = '{
"select" : ["fields",{"type":"x²string","value":"actor","alias":"col"}],
"from"   : "actors",
"union"  : [
		{"type":"x²sql",
		"select" : ["fields",{"type":"x²string","value":"event","alias":"col"}],
		"from"   : "events"},
		{"type":"x²sql",
		"select" : ["fields",{"type":"x²string","value":"location","alias":"col"}],
		"from"   : "locations"}
					]
}';

$sql = x²sql::query($cfg)->command;

print $sql;
?>
