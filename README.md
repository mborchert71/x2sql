					
x²sql
======
###Code Or Configuration To Sql

-----

String Is generally considered being poisonous to Code and my Coding Experience is confirming that.
And ever again I stumble upon the fact that SQL is not very popular from a Coders Point Of View.

So the solution solving that is ***x²sql***, which is heavily inspired by the way sql-coding is done in  
[the codeigniter framework](http://codeigniter.org). 

>###features

+ json2sql
+ php2sql
+ xml2sql
+ placeholder recognition
+ subqueries
+ select, insert, update & delete queries
+ x2-helper classes
+ so far >160 tests to validate functionality

 `print x2sql::query()->select()->from("table")->limit(100)->offset("?")->command;`

  >prints: select * from table limit 100 offset ?;

 `$cfg = json_decode('{"select" : "*","from"   : "table","limit"  : "offset","offset" : "?"}');`

 `print x2sql::query($cfg)->command;`

 >prints: select * from table limit 100 offset ?;

More complex structures can be easily realized.
For more Examples and the full documentation visit:

>##[x2sql.get-resource.net](http://x2sql.get-resource.net).

It was important to have a script with no other dependencies than the programming-language 
itself and so be able to easily port it later on.

x²sql make use of the [SimpleTestEngine](http://simpletest.org).


feel free to fork and improve.
this is my little todo-list;

 + 'join' and 'union'.
 + specials like   'distinct' and 'all', etc.
 + xml and xsd-schema.
					