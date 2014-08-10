<?php

class DBO{

	private $db;

	public $query_count;

	private $print_debug = false;

	public function __construct($db_host, $db_name, $db_username, $db_password) {
		$this->db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_username, $db_password);
		$this->query_count = 0;
	}

	public function getHandle() {
		return $this->db;
	}

	public function getObjects($query, $args = []) {
		$sth = $this->db->prepare($query);
		$this->query_count ++;
		$executed = $sth->execute($args);

		$err = $sth->errorInfo();
		if($err[0] != '00000' && $this->print_debug) print_r($err);

		return $sth->fetchAll(PDO::FETCH_OBJ);
	}

	public function getObject($query, $args = []) {
		$sth = $this->db->prepare($query);
		$this->query_count ++;
		$executed = $sth->execute($args);

		$err = $sth->errorInfo();
		if($err[0] != '00000' && $this->print_debug) print_r($err);

		return $sth->fetch(PDO::FETCH_OBJ);
	}

	public function getColumn($query, $args = []) {
		$sth = $this->db->prepare($query);
		$executed = $sth->execute($args);
		$this->query_count ++;

		$err = $sth->errorInfo();
		if($err[0] != '00000' && $this->print_debug) print_r($err);

		return $sth->fetchColumn();
	}

	public function query($query, $args) {
		$sth = $this->db->prepare($query);
		$this->query_count ++;
		$executed = $sth->execute($args);

		$err = $sth->errorInfo();
		if($err[0] != '00000' && $this->print_debug) print_r($err);

		return $executed;
	}
}
