<?php
/**
 * Call the Flickr API for data
 *
 * This class will fetch data from the Flickr API.  If it has cached the data
 * already within a certain time period, it will load from the cache instead.
 *
 * @author Jonah Dahlquist
 * @copyright Copyright 2012 Jonah Dahlquist
 */
class Btp_Flickr_Request_Http {

	private $api_key;
	private $tags;
	private $per_page;
	private $page;

	private $cache_dir;
	private $cache_timeout;

	public function __construct($api_key, $tags, $per_page, $page) {
		$this->api_key = $api_key;
		$this->tags = $tags;
		$this->per_page = $per_page;
		$this->page = $page;

		$this->cache_dir = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/cache/';
		$this->cache_timeout = 1800;
	}

	public function search() {
		$request = $this->buildRequest(FLICKR_METHOD_SEARCH);
		$cache_file = $this->getCacheFile($request);
		if ($this->cacheIsValid($request)) {
			$source = $cache_file;
		} else {
			$source = $request;
		}
		$raw_response = file_get_contents($source);
		if ($source != $cache_file)
			$this->cache($cache_file, $raw_response);
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
			'page' => $this->page,
			'extras' => 'url_q',
		));
		return FLICKR_API_URL . '?' . $query;
	}

	private function getCacheFile($request) {
		return $this->cache_dir . urlencode(strrchr($request, '/')) . '.cache.txt';
	}

	private function cacheIsValid($request) {
		$cache_file = $this->getCacheFile($request);
		return file_exists($cache_file) && filemtime($cache_file) > (time() - $this->cache_timeout);
	}

	private function cache($file, $data) {
		file_put_contents($file, $data);
	}
}

