
<?php
$dbname = 'snippets.sqlite';
$mytable = "snippets";
$base = new SQLite3($dbname);
$query_name = "ALTER TABLE $mytable ADD COLUMN 'highlight' longtext";
$base->query($query_name);
$query_name = "ALTER TABLE $mytable ADD COLUMN 'lines' integer";
$base->query($query_name);
?>
