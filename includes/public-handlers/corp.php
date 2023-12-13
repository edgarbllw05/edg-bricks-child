<?php


require_once $_SERVER[ 'DOCUMENT_ROOT' ] . 'wp-load.php';

$request = getallheaders();
$origin = $request[ 'Origin' ] ?? '';
$file = $_GET[ 'file' ] ?? '';

if ( ! $origin || ! file_exists( $file ) ) {
	edg_abort();
}

$allowed_origins = [];

if ( ! in_array( $origin, $allowed_origins ) ) {
	readfile( $file );
	exit;
}

header( "Access-Control-Allow-Origin: $origin" );
readfile( $file );
