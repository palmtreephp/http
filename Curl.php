<?php

namespace Palmtree\Http;

class Curl {
	protected $handle;
	protected $url;
	protected $curlOpts = [];

	protected $httpStatus;
	protected $headers;
	protected $contents;

	public static $curlOptDefaults = [
		CURLOPT_SSL_VERIFYHOST => false,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_HEADER         => false,
		CURLOPT_AUTOREFERER    => true,
		CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.10 (KHTML, like Gecko) Chrome/8.0.552.28 Safari/534.10',
	];

	const HTTP_NOT_FOUND = 404;
	const HTTP_OK_MIN = 200;
	const HTTP_OK_MAX = 299;

	public function __construct( $args = [] ) {
		$curlOpts = self::$curlOptDefaults;
		if ( is_array( $args ) ) {
			if ( isset( $args['curlOpts'] ) ) {
				foreach ( $args['curlOpts'] as $key => $value ) {
					$curlOpts[ $key ] = $value;
				}
			}
		} else if ( is_string( $args ) ) {
			$this->setUrl( $args );
		}

		$this->handle = curl_init( $this->getUrl() );

		$this->curlOpts = $curlOpts;

		curl_setopt_array( $this->handle, $this->curlOpts );
	}

	public function getHeaders() {
		if ( $this->headers === null ) {
			curl_setopt( $this->handle, CURLOPT_HEADER, true );
			curl_setopt( $this->handle, CURLOPT_NOBODY, true );
			curl_setopt( $this->handle, CURLOPT_RETURNTRANSFER, true );

			$this->headers = curl_exec( $this->handle );
		}

		return $this->headers;
	}

	public function getContents() {
		if ( $this->contents === null ) {
			// @todo: Parse headers into an array so we don't need to make to requests to get content and headers.
			curl_setopt( $this->handle, CURLOPT_RETURNTRANSFER, true );

			$contents = curl_exec( $this->handle );

			if ( $this->isOk() ) {
				$this->contents = $contents;
			}
		}

		return $this->contents;
	}

	public function getHttpStatus() {
		if ( $this->httpStatus === null ) {
			$status = curl_getinfo( $this->handle, CURLINFO_HTTP_CODE );

			if ( $status !== false ) {
				$this->httpStatus = (int) $status;
			}
		}

		return $this->httpStatus;
	}

	public function isOk() {
		$status = $this->getHttpStatus();

		return ( $status >= self::HTTP_OK_MIN && $status <= self::HTTP_OK_MAX );
	}

	public function is404() {
		return ( $this->getHttpStatus() === self::HTTP_NOT_FOUND );
	}

	public static function curlGetContents( $url ) {
		$instance = new self( $url );

		$contents = $instance->getContents();
		$instance = null;

		return $contents;
	}

	/**
	 * @param mixed $url
	 *
	 * @return Curl
	 */
	public function setUrl( $url ) {
		$this->url = $url;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getUrl() {
		return $this->url;
	}
}
