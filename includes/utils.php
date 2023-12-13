<?php


function edg_abort( $status = 302 ) {
	wp_safe_redirect( home_url(), $status );
	exit;
}

function edg_admin_abort( $status = 302 ) {
	wp_safe_redirect( admin_url(), $status );
	exit;
}

function edg_is_role( $role ) {
	if ( ! is_user_logged_in() ) {
		return false;
	}

	$user = wp_get_current_user();
	$roles = $user->roles;

	if ( is_string( $role ) ) {
		return in_array( $role, $roles );
	}

	if ( is_array( $role ) ) {
		return array_intersect( $role, $roles );
	}
}

function edg_is_admin_role() {
	return edg_is_role( 'administrator' );
}

function edg_is_public_role() {
	return edg_is_role( EDG_PUBLIC_USER_ROLES );
}

function edg_debug( $data ) {
	if ( ! edg_is_admin_role() ) return;

	echo '<pre>';
	print_r( $data );
	echo '</pre>';
	exit;
}

function edg_is_wp_login_page() {
	global $pagenow;

	return $pagenow === 'wp-login.php';
}

function edg_credit_url( $referrer ) {
	$url = 'https://edgarbollow.com';
	$url = add_query_arg( [
		'utm_source' => 'built-site',
		'utm_medium' => rawurlencode( $referrer ),
		'utm_campaign' => $_SERVER[ 'HTTP_HOST' ],
	], $url );

	return $url;
}

function edg_cookie_status() {
	if ( ! function_exists( 'cn_cookies_accepted' ) ) return;

	$cookie = $_COOKIE[ 'cookie_notice_accepted' ] ?? '';
	$accepted = cn_cookies_accepted();

	if ( ! $cookie ) {
		$status = 'not-set';
	}

	if ( $accepted ) {
		$status = 'accepted';
	}

	if ( $cookie && ! $accepted ) {
		$status = 'declined';
	}

	return $status;
}

function edg_cookies_not_set() {
	return edg_cookie_status() === 'not-set';
}

function edg_cookies_accepted() {
	return edg_cookie_status() === 'accepted';
}

function edg_cookies_declined() {
	return edg_cookie_status() === 'declined';
}

function edg_404_back_url() {
	$url = home_url();
	$url = add_query_arg( [
		'utm_source' => 'same-origin',
		'utm_medium' => '404',
		'utm_campaign' => rawurlencode( $_SERVER[ 'REQUEST_URI' ] ),
	], $url );

	return $url;
}
