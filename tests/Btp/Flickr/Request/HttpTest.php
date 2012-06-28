<?php

require dirname(__FILE__) . '/../../../../config.php';
require dirname(__FILE__) . '/../../../../lib/Btp/Flickr/Request/Http.php';
require_once 'PHPUnit/Autoload.php';

class Btp_Flickr_Request_HttpTest extends PHPUnit_Framework_TestCase {

	public function testSearch() {
		$requester = new Btp_Flickr_Request_Http(FLICKR_API_KEY, FLICKR_SEARCH_TAGS, 1, 1);

		$results = $requester->search();
		$this->assertFalse(empty($results));
		json_decode($results);
		$this->assertEquals(json_last_error(), 0);
	}
}
