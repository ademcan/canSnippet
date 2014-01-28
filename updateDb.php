
<?php
$dbname = 'snippets.sqlite';
$mytable = "snippets";
$base = new SQLite3($dbname);
$query_name = "ALTER TABLE $mytable ADD COLUMN 'highlight' longtext, 'lines' integer";
$base->query($query_name);
?>