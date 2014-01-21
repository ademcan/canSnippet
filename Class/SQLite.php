<?php

class SQLite implements IDatabase {
	protected $kernelDir;
	protected $path;
	protected $db;
	protected $lastRequest;

	public function __construct($kernelDir, $path) {
		$this->kernelDir = $kernelDir;
		$this->path = $path;
		$this->db = null;
		$this->lastRequest = null;
	}

	public function getPath() {
		return $this->path;
	}

	//Implements IDatabase
	public function create() {
		$this->open();
	}

	public function escape($value) {
		return SQLite3::escapeString($value);
	}

	public function execute($sql) {
		$this->open();

		$this->lastRequest = $this->db->query($sql);

		return $this;
	}

	public function exist() {
		if (file_exists($this->path)) {
		    return true;
		}
		return false;
	}

	public function fetchArray() {
		return $this->lastRequest->fetchArray();
	}

	public function open() {
		if ($this->db == null) {
			$this->db = new SQLite3($this->kernelDir."/".$this->path);
		}
	}
}