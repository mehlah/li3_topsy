<?php

namespace li3_topsy\tests\cases\extensions\adapter\data\source\http;

use lithium\data\Connections;

class TopsyTest extends \lithium\test\Unit {

	public function setUp() {
		Connections::add('test-topsy', array(
			'type' => 'http',
			'adapter' => 'Topsy',
			'socket' => 'li3_topsy\tests\mocks\MockTopsySocket'
		));
	}

	public function testBasicGet() {
		$topsy = Connections::get('test-topsy');
		$headers = array('Content-Type' => 'application/json');
		$results = json_decode(
			$topsy->connection->get('authorinfo.json?url=http://twitter.com/mehlah', array(), compact('headers'))
		);

		$this->assertEqual('Mehdi Lahmam B.', $results->response->name);
	}
}

?>