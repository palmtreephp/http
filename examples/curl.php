<?php

use Palmtree\Http\Curl;

require dirname( __DIR__ ) . '/vendor/autoload.php';

$curl = new Curl( 'http://www.google.co.uk' );

var_dump( $curl->getHeaders() );
var_dump( $curl->getHttpStatus() );
