<?php

require dirname(__FILE__) . '/../../../lib/Btp/Flickr/Parser.php';
require_once 'PHPUnit/Autoload.php';

class Btp_Flickr_ParserTest extends PHPUnit_Framework_TestCase {

	public function testResults() {
		$requester = new StubRequester();
		$parser = new Btp_Flickr_Parser($requester);

		$this->assertEquals($parser->results(), array(
			0 => (object) array(
				'title' => 'baz',
				'url' => 'foobar',
				'link' => 'http://flickr.com/photos/foo/bar'
			)
		));
	}
}

class StubRequester {

	public function search() {
		return '{"photos":{"photo":[{"owner":"foo","id":"bar","title":"baz","url_q":"foobar"}]}}';
	}
}
