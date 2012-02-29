<?php

namespace li3_topsy\tests\mocks;

class MockTopsySocket extends \lithium\net\Socket {

	protected $_data = null;

	public function open(array $options = array()) {
		parent::open($options);
		return true;
	}

	public function close() {
		return true;
	}

	public function eof() {
		return true;
	}

	public function read() {
		return join("\r\n", array(
			'HTTP/1.1 200 OK',
			'Header: Value',
			'Connection: close',
			'Content-Type: text/html;charset=UTF-8',
			'',
			$this->_data
		));
	}

	public function write($data) {
		$url = $data->to('url');
		return $this->_data = $this->_response($url);
	}

	public function timeout($time) {
		return true;
	}

	public function encoding($charset) {
		return true;
	}

	private function _response($url) {
		if (strpos($url, '/authorinfo')) {
			$json = '/responses/authorinfo.json';
		}

		return file_get_contents(__DIR__ . $json);
	}
}

?>