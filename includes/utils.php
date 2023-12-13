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

function edg_404_back_url() {
	$url = home_url();
	$url = add_query_arg( [
		'utm_source' => 'same-origin',
		'utm_medium' => '404',
		'utm_campaign' => rawurlencode( $_SERVER[ 'REQUEST_URI' ] ),
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

function edg_cookie_notice_text() {
	$info = __( 'We use cookies to optimize our content for you and to give you the best possible experience. Please allow us to use them so that we can provide you with exactly what you are looking for.', 'edg-bricks-child' );

	if ( edg_cookies_not_set() && ! is_privacy_policy() ) {
		$privacy_policy_url = get_privacy_policy_url();
		$info .= ' <a href="' . $privacy_policy_url . '#cookie-status" target="_blank">' . __( 'Learn more', 'edg-bricks-child' ) . '</a>';
	}

	if ( edg_cookies_not_set() && is_privacy_policy() ) {
		$info .= ' <a href="#cookie-status">' . __( 'Learn more', 'edg-bricks-child' ) . '</a>';
	}

	if ( edg_cookies_accepted() ) {
		$info .= ' ' . __( 'Tracking is currently enabled on this website.', 'edg-bricks-child' ) . ' ' . __( 'Do you want to decline our cookies?', 'edg-bricks-child' );
	}

	if ( edg_cookies_declined() ) {
		$info .= ' ' . __( 'Tracking is currently disabled on this website.', 'edg-bricks-child' ) . ' ' . __( 'Do you want to accept our cookies?', 'edg-bricks-child' );
	}

	return $info;
}

function edg_manage_cookies() {
	$info = '';

	if ( edg_cookies_not_set() ) {
		$info = __( 'Tracking is currently disabled on this website.', 'edg-bricks-child' );
	}

	if ( edg_cookies_accepted() ) {
		$info = __( 'Tracking is currently enabled on this website.', 'edg-bricks-child' ) . '<button class="edg-manage-cookies cn-revoke-cookie">' . __( 'Disable now', 'edg-bricks-child' ) . '</button>';
	}

	if ( edg_cookies_declined() ) {
		$info = __( 'Tracking is currently disabled on this website.', 'edg-bricks-child' ) . '<button class="edg-manage-cookies cn-revoke-cookie">' . __( 'Enable now', 'edg-bricks-child' ) . '</button>';
	}

	return $info;
}
