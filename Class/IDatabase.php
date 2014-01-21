<?php

interface IDatabase {
	public function create();
	public function escape($sql);
	public function execute($sql);
	public function exist();
	public function fetchArray();
	public function open();
}