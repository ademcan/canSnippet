<?php

class Factory {
	public static function database($parameters) {
		$db = null;

		switch ($parameters["db"]["driver"]) {
			case 'pdo_sqlite':
				$db = new SQLite($parameters["dir"], $parameters["db"]["path"]);
				break;

			/*case 'pdo_mysql':
				break;

			case 'pdo_pgsql':
				break;*/
			
			default:
				throw new Exception("Driver unknown !", 1);
		}
		
		if (!$db->exist()) {
			if (false === strpos($_SERVER["REQUEST_URI"], "install.php")) {
				//redirect
				header("Location:install.php");
			}
		}

		return $db;
	}
}