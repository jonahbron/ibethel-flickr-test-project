<?php

class Btp_Flickr_Request_Http {

	private $api_key;
	private $tags;
	private $per_page;

	public function __construct($api_key, $tags, $per_page) {
		$this->api_key = $api_key;
		$this->tags = $tags;
		$this->per_page = $per_page;
	}

	public function search() {
		$request = $this->buildRequest(FLICKR_METHOD_SEARCH);
		$raw_response = file_get_contents($request);
		return $raw_response;
	}

	private function buildRequest($method) {
		$query = http_build_query(array(
			'api_key' => FLICKR_API_KEY,
			'method' => $method,
			'tags' => FLICKR_SEARCH_TAGS,
			'format' => 'json',
			'nojsoncallback' => 1,
			'per_page' => $this->per_page,
			'extras' => 'url_q',
		));
		return FLICKR_API_URL . '?' . $query;
	}
}

