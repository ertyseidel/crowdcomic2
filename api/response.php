<?php

class Response {

	const SUCCESS = 'success';
	const ERROR = 'error';

	public $status;
	public $data;

	public function __construct($status = self::SUCCESS, $data = '') {
		$this->status = $status;
		$this->data = $data;
	}

	public function __toString() {
		return json_encode([
			'status' => $this->status,
			'data' => $this->data
		]);
	}

}
