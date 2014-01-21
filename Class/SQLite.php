<?php

class SQLite implements IDatabase {
	protected $kernelDir;
	protected $path;

	public function __construct($kernelDir, $path) {
		$this->kernelDir = $kernelDir;
		$this->path = $path;
	}

	public function getPath() {
		return $this->path;
	}

	//Implements IDatabase
	public function create() {
		throw new Exception("No implementation", 1);
	}

	public function exist() {
		return true; //Return already true because if database doesn't exist, the SQLite3 object create then.
	}

	public function open() {
		return new SQLite3($this->kernelDir."/".$this->path);
	}
}