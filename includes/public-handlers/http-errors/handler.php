<?php


require_once $_SERVER[ 'DOCUMENT_ROOT' ] . 'wp-load.php';

$status = $_SERVER[ 'REDIRECT_STATUS' ] ?? '';

if ( ! $status ) {
	edg_abort();
}

$texts = require_once 'texts.php';

function _edg_get_text( $_key ) {
	global $status;
	global $texts;

	if ( ! array_key_exists( $status, $texts ) ) exit;

	$data = $texts[ $status ];

	return $data[ $_key ];
}

$title = _edg_get_text( 'title' );
$title = "$status $title";
$message = _edg_get_text( 'message' );

require_once 'view.php';
