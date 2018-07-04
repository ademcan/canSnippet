<?php
$config = getConfig();

function getConfig()
{
	return array(
		"dbname" => "snippets.sqlite",
		"defaultPrismCSS" => "css/prism.css",
		"url" => (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"
	);
}

function prismTheme()
{
	$config = getConfig();

	if(file_exists($config["dbname"]))
		$base = new SQLite3($config['dbname']);
	else if(file_exists("../".$config["dbname"]))
		$base = new SQLite3("../".$config['dbname']);
	else
		return $config["defaultPrismCSS"];

	$query = "SELECT prismtheme FROM settings ";
    $results = $base->query($query);
	$result = $results->fetchArray();
	if($result && $result["prismtheme"]!="")
		return $result["prismtheme"];
	return $config["defaultPrismCSS"];
}

function language()
{
	$config = getConfig();
	if(file_exists($config["dbname"]))
		$base = new SQLite3($config['dbname']);
	else
		$base = new SQLite3("../".$config['dbname']);

	// $base = new SQLite3($config['dbname']);
	$query = "SELECT * FROM settings";
    $results = $base->query($query);
	$result = $results->fetchArray();
	return $result['lng'];
}
