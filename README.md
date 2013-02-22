					
x²sql
======
###Code Or Configuration To Sql

-----

String Is generally considered being poisonous to Code and my Coding Experience is confirming that. And ever again I stumble upon the fact that SQL is not very popular from a Coders Point Of View.

So the solution solving that is ***x²sql***, which is heavily inspired by the way sql-coding is done in  [the codeigniter framework](http://codeigniter.org). 

>###features

+ json2sql
+ php2sql
+ xml2sql
+ placeholder recognition
+ subqueries
+ select, insert, update & delete queries
+ x2-helper classes

 <code>&lt;?php
  print x2sql::query()-&gt;select()-&gt;from("table")-&gt;limit(100)-&gt;offset("?")-&gt;command;

  // select * from table limit 100 offset ?;

  $cfg = json_decode('{
    "select" : "*",
    "from"   : "table",
    "limit"  : "offset",
    "offset" : "?"
    }');

  print x2sql::query($cfg)-&gt;command;

  //select * from table limit 100 offset ?;

  print_r(x2sql::query($cfg));

  $xml = "<&lt;query&gt;&lt;select&gt;*&lt;/select&gt;&lt;from&gt;table&lt;/from&gt;&lt;limit&gt;100&lt;/limit&gt;&lt;offset&gt;?&lt;/offset&gt;&lt;query&gt;";

  print x2sql::query($cfg)-&gt;command;

  //select * from table limit 100 offset ?;

?></code>
More complex structures can be easily realized.

Website : [x2sql.get-resource.net](x2sql.get-resource.net).

It was important to have a script with no other dependencies than the programming-language itself and so be able to easily port it later on.

>####todo

 + write testcases
 + the xml as configuration-convenience.
 + xsd-schema to consolidate the method.
					