<?php

namespace Palmtree\Http;

class RemoteUser {
	public static $ipHeaders = [
		'HTTP_CLIENT_IP',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_X_CLUSTER_CLIENT_IP',
		'HTTP_FORWARDED_FOR',
		'HTTP_FORWARDED',
		'HTTP_X_REAL_IP',
		'REMOTE_ADDR',
	];

	protected $serverVars;

	protected $ip;
	protected $userAgent;

	public function __construct( $serverVars = [] ) {
		if ( empty( $serverVars ) ) {
			$serverVars = $_SERVER;
		}

		$this->serverVars = $serverVars;
	}

	public function getUserAgent() {
		return ( isset( $this->serverVars['HTTP_USER_AGENT'] ) ) ? $this->serverVars['HTTP_USER_AGENT'] : '';
	}

	public function getIpAddress() {
		if ( $this->ip === null ) {
			foreach ( static::$ipHeaders as $header ) {
				if ( isset( $this->serverVars[ $header ] ) ) {
					$ips = explode( ',', $this->serverVars[ $header ] );
					$ip  = trim( end( $ips ) );
					$ip  = filter_var( $ip, FILTER_VALIDATE_IP );

					if ( $ip !== false ) {
						$this->ip = $ip;

						return $this->ip;
					}
				}
			}
		}

		return $this->ip;
	}
}
