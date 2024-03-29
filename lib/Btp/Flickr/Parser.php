<?php
/**
 * Parse the Flickr API data
 *
 * This class will call a API data source for data, and parse it into an
 * array.  The data is expected to be in JSON format.
 *
 * @author Jonah Dahlquist
 * @copyright Copyright 2012 Jonah Dahlquist
 */
class Btp_Flickr_Parser {

	private $requester;

	public function __construct($requester) {
		$this->requester = $requester;
	}

	public function results() {
		$images = array();

		$raw_response = $this->requester->search();
		$response_data = json_decode($raw_response);
		$response_images = $response_data->photos->photo;

		foreach ($response_images as $response_image) {
			$link = sprintf(
				'http://flickr.com/photos/%s/%s',
				$response_image->owner,
				$response_image->id
			);
			$images[] = (object) array(
				'title' => $response_image->title,
				'url' => $response_image->url_q,
				'link' => $link,
			);
		}

		return $images;
	}
}
