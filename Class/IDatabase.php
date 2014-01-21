<?php

interface IDatabase {
	public function create();
	public function exist();
	public function open();
}