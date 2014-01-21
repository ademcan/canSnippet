<?php

class Factory {
	public static function database($config) {
		$db = null;

		switch ($config["dbdriver"]) {
			case 'pdo_sqlite':
				$db = new SQLite($config["db"]["path"]);
				break;

			/*case 'pdo_mysql':
				break;

			case 'pdo_pgsql':
				break;*/
			
			default:
				throw new Exception("Driver unknown !", 1);
		}

		if (!$db->exist()) {
			$db->create();
		}

		return $db->open();
	}
}