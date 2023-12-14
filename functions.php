<?php


#region Setup theme
#region Imports
require_once 'includes/utils.php';
#endregion Imports


#region Constants
define( 'EDG_CSP_HEADER_NONCE', wp_generate_password( 30, false ) );
define( 'EDG_PUBLIC_USER_ROLES', [
	'subscriber',
] );
define( 'EDG_AUTH_EXPIRATION', 8 * HOUR_IN_SECONDS );
define( 'EDG_MSG_GENERAL_ERROR', __( 'That didn\'t work.', 'edg-bricks-child' ) );
#endregion Constants


#region Text domain
add_action( 'after_setup_theme', function () {
	load_child_theme_textdomain( 'edg-bricks-child', get_stylesheet_directory() . '/languages' );
} );
#endregion Text domain
#endregion Setup theme


#region General settings
# Disable application passwords (shown at the very bottom on your profile)
add_filter( 'wp_is_application_passwords_available', '__return_false' );
# Disable auto-updates for themes and plugins
add_filter( 'themes_auto_update_enabled', '__return_false' );
add_filter( 'plugins_auto_update_enabled', '__return_false' );
# Disable admin email verification prompt (shows up after login once in a while)
add_filter( 'admin_email_check_interval', '__return_false' );
# Disable fuzzy URL redirects (don't guess the page link)
add_filter( 'do_redirect_guess_404_permalink', '__return_false' );
# Disable scaling down images that are too big (easier to work with for your own upload filters)
add_filter( 'big_image_size_threshold', '__return_false' );
# Disable text auto-formatter (special quotes, long hyphens, etc.)
add_filter( 'run_wptexturize', '__return_false' );
# Disable emoji auto-replacer (`:)`, `:(`, etc.)
add_filter( 'option_use_smilies', '__return_false' );
# Hide the generator for both the HTML but also the feed URL (`yoursite.com/feed/`) which reveals the current WP version
add_filter( 'the_generator', '__return_empty_string' );
# Remove Rank Math DOM credit notice
add_filter( 'rank_math/frontend/remove_credit_notice', '__return_true' );
# Remove Rank Math restriction for post keywords (max. 5)
add_filter( 'rank_math/focus_keyword/maxtags', function () {
	return PHP_INT_MAX;
} );
# Remove credit notice on Rank Math sitemaps
add_filter( 'rank_math/sitemap/remove_credit', '__return_true' );
# Disable Bricks web fonts
add_filter( 'bricks/assets/load_webfonts', '__return_false' );
# Disable Bricks page title for non-Bricks pages
add_filter( 'bricks/default_page_title', '__return_empty_string' );
# Hide footer texts on admin area
add_filter( 'admin_footer_text', '__return_empty_string' );
add_filter( 'update_footer', '__return_empty_string', 11 );
# Only allow logging in with username instead of username or email
remove_filter( 'authenticate', 'wp_authenticate_email_password', 20 );
# Disable WP's "fancy feature" that automatically converts `Wordpress` to `WordPress` with a capital `P` everywhere
remove_filter( 'the_title', 'capital_P_dangit', 11 );
remove_filter( 'the_content', 'capital_P_dangit', 11 );
remove_filter( 'comment_text', 'capital_P_dangit', 31 );
# Disable auto-deletion for posts once the were trashed (default: 30 days)
remove_action( 'wp_scheduled_delete', 'wp_scheduled_delete' );
#endregion General settings


#region Mails
#region SMTP config
add_action( 'phpmailer_init', function ( $mailer ) {
	$mailer->isSMTP();
	$mailer->Host = 'server';
	$mailer->Username = 'user';
	$mailer->Password = 'password';
	$mailer->From = 'email';
	$mailer->FromName = get_bloginfo( 'name' );
	$mailer->Port = 465;
	$mailer->SMTPSecure = 'ssl';
	$mailer->SMTPAuth = true;
} );
#endregion SMTP config


#region Content type
add_filter( 'wp_mail_content_type', function () {
	return 'text/html';
} );
#endregion Content type
#endregion Mails


#region Clean up DOM
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

// add_action( 'wp_enqueue_scripts', function () {
// 	wp_deregister_style( 'cookie-notice-front' );
// 	wp_deregister_script( '' );
// }, 999999 );

// add_action( 'wp_footer', function () {
// 	wp_deregister_style( '' );
// 	wp_deregister_script( '' );
// } );
#endregion Clean up DOM


#region Add favicon
add_action( 'wp_head', function () {
	echo '
		<link rel="icon" href="" media="(prefers-color-scheme: light)">
		<link rel="icon" href="" media="(prefers-color-scheme: dark)">
		<link rel="apple-touch-icon" href="" media="(prefers-color-scheme: light)">
		<link rel="apple-touch-icon" href="" media="(prefers-color-scheme: dark)">
	';
}, 2 );
#endregion Add favicon


#region Remove script version params
add_filter( 'style_loader_src', 'edg_remove_script_version_params', 999999 );
add_filter( 'script_loader_src', 'edg_remove_script_version_params', 999999 );

function edg_remove_script_version_params( $src ) {
	if ( str_starts_with( $src, home_url() ) && strpos( $src, 'ver=' ) !== false ) {
		$src = remove_query_arg( 'ver', $src );
	}

	return $src;
}
#endregion Remove script version params


#region Enqueue scripts
#region General
add_action( 'wp_enqueue_scripts', function () {
	wp_register_style( 'edg-bricks-child-main', get_stylesheet_directory_uri() . '/assets/css/min/main.css' );
	wp_enqueue_style( 'edg-bricks-child-main' );

	wp_register_script( 'edg-bricks-child-main', get_stylesheet_directory_uri() . '/assets/js/min/main.js', [], false, [
		'strategy' => 'defer',
	] );
	wp_enqueue_script( 'edg-bricks-child-main' );
	wp_localize_script( 'edg-bricks-child-main', 'edgBricksChild', [
		'translations' => [
			'1' => __( 'Cancel process', 'edg-bricks-child' ),
		],
	] );
}, 20 );
#endregion General


#region WP login
add_action( 'login_enqueue_scripts', function () {
	wp_register_style( 'edg-bricks-child-main', get_stylesheet_directory_uri() . '/assets/css/min/wp-login.css' );
	wp_enqueue_style( 'edg-bricks-child-main' );
}, 20 );
#endregion WP login


#region WP admin
add_action( 'admin_enqueue_scripts', function () {
	wp_register_style( 'edg-bricks-child-main', get_stylesheet_directory_uri() . '/assets/css/min/wp-admin.css' );
	wp_enqueue_style( 'edg-bricks-child-main' );
}, 20 );
#endregion WP admin
#endregion Enqueue scripts


#region Hide user nicename
add_action( 'user_register', function ( $user_id ) {
	wp_update_user( [
		'ID' => $user_id,
		'user_nicename' => $user_id,
	] );
}, 999999 );


#region Existing users
add_action( 'init', function () {
	$users = get_users();

	foreach ( $users as $user ) {
		$id = $user->ID;
		$nicename = $user->user_nicename;

		if ( strval( $id ) === $nicename ) continue;

		wp_update_user( [
			'ID' => $user_id,
			'user_nicename' => $user_id,
		] );
	}
} );
#endregion Existing users
#endregion Hide user nicename


#region Auth cookie expiration
add_filter( 'auth_cookie_expiration', function ( $length ) {
	$length = EDG_AUTH_EXPIRATION;

	if ( edg_is_public_role() ) {
		$length = YEAR_IN_SECONDS;
	}

	return $length;
} );
#endregion Auth cookie expiration


#region Nonce expiration
add_filter( 'nonce_life', function () {
	return EDG_AUTH_EXPIRATION;
} );
#endregion Nonce expiration


#region Prevent non-native auth attempts for non-EDG_PUBLIC_USER_ROLES users
#region Login
add_filter( 'wp_authenticate_user', function ( $user, $password ) {
	$error = new WP_Error( 'edg_error', EDG_MSG_GENERAL_ERROR );
	$user_roles = $user->roles;

	if ( edg_is_wp_login_page() || ! $user || array_intersect( EDG_PUBLIC_USER_ROLES, $user_roles ) ) {
		return $user;
	}

	if ( ! wp_check_password( $password, $user->user_pass ) ) {
		return $user;
	}

	return $error;
}, 999999, 2 );
#endregion Login


#region Reset password
add_filter( 'allow_password_reset', function ( $allow, $user_id ) {
	$error = new WP_Error( 'edg_error', EDG_MSG_GENERAL_ERROR );
	$user = get_user_by( 'ID', $user_id );
	$user_roles = $user->roles;

	if ( edg_is_wp_login_page() || array_intersect( EDG_PUBLIC_USER_ROLES, $user_roles ) ) {
		$allow = true;
		return $allow;
	};

	$allow = false;
	return $allow;
}, 999999, 2 );
#endregion Reset password
#endregion Prevent non-native auth attempts for non-EDG_PUBLIC_USER_ROLES users


#region Restrict WP REST API
add_filter( 'rest_authentication_errors', function ( $errors ) {
	if ( ! edg_is_public_role() ) {
		return $errors;
	}

	return new WP_Error( 'edg_error', EDG_MSG_GENERAL_ERROR, [
		'status' => rest_authorization_required_code(),
	] );
} );
#endregion Restrict WP REST API


#region CSP
#region Add header
add_filter( 'wp_headers', function ( $headers ) {
	$nonce = EDG_CSP_HEADER_NONCE;
	$headers[ 'Content-Security-Policy' ] = "script-src 'nonce-$nonce' 'strict-dynamic'";

	return $headers;
}, 999999 );
#endregion Add header


#region Add nonce attributes
function edg_csp_header_script_nonce_attr( $output ) {
	$nonce = EDG_CSP_HEADER_NONCE;
	$script_tags = '/<script\b[^>]*>/i';
	$output = preg_replace_callback( $script_tags, function ( $matches ) use ( $nonce ) {
		return str_replace( '<script', "<script nonce='$nonce'", $matches[ 0 ] );
	}, $output );

	return $output;
}

add_action( 'init', function () {
	ob_start( 'edg_csp_header_script_nonce_attr' );
} );
#endregion Add nonce attributes
#endregion CSP


#region WP login tweaks
#region Disable informational error messages
add_filter( 'login_errors', function () {
	return EDG_MSG_GENERAL_ERROR;
} );
#endregion Disable informational error messages


#region Logo URL
add_filter( 'login_headerurl', function () {
	return home_url();
} );
#endregion Logo URL


#region Disable form shaking on failed attempt
add_filter( 'login_footer', function () {
	remove_action( 'login_footer', 'wp_shake_js', 12 );
} );
#endregion Disable form shaking on failed attempt


#region Redirects
add_action( 'init', function () {
	if ( ! edg_is_wp_login_page() ) return;

	$action = $_GET[ 'action' ] ?? '';
	$request_method = $_SERVER[ 'REQUEST_METHOD' ];

	if ( $action === 'postpass' && $request_method !== 'POST' ) {
		edg_abort();
	}
	
	$nonce = $_GET[ '_wpnonce' ] ?? '';

	if ( $action === 'logout' && ! wp_verify_nonce( $nonce, 'log-out' ) ) {
		edg_abort();
	}
} );
#endregion Redirects
#endregion WP login tweaks


#region Search results
#region Disable
add_action( 'pre_get_posts', function ( $query ) {
	if ( ! is_search() ) {
		return $query;
	}

	$query->is_search = false;

	return $query;
} );
#endregion Disable
#endregion Search results


#region Redirects
#region Attachment pages
add_action( 'template_redirect', function () {
	if ( ! is_attachment() ) return;

	wp_safe_redirect( wp_get_attachment_url(), 301 );
	exit;
} );
#endregion Attachment pages


#region Author post count = 0
add_action( 'template_redirect', function () {
	if ( ! is_author() ) return;

	global $wp_query;

	if ( $wp_query->post_count > 0 ) return;

	edg_abort();
} );
#endregion Author post count = 0


#region Taxonomy post count = 0
add_action( 'template_redirect', function () {
	if ( ! is_category() && ! is_tag() && ! is_tax() ) return;

	$term = get_queried_object();
	$args = [
		'tax_query' => [
			[
				'taxonomy' => $term->taxonomy,
				'field' => 'term_id',
				'terms' => $term->term_id,
			],
		],
		'posts_per_page' => 1,
	];
	$query = new WP_Query( $args );

	if ( $query->have_posts() ) return;

	edg_abort();
} );
#endregion Taxonomy post count = 0
#endregion Redirects


#region WP admin
#region Remove WP info notices
add_filter( 'admin_head', function () {
	if ( edg_is_admin_role() ) return;

	remove_action( 'admin_notices', 'update_nag', 3 );
	remove_action( 'admin_notices', 'maintenance_nag', 10 );
} );
#endregion Remove WP info notices


#region Remove help tab
add_filter( 'admin_head', function () {
	$screen = get_current_screen();
	$screen->remove_help_tabs();
} );
#endregion Remove help tab


#region Admin bar
#region Clean up
add_action( 'wp_before_admin_bar_render', function () {
	global $wp_admin_bar;

	$wp_admin_bar->remove_menu( 'wp-logo' );
	$wp_admin_bar->remove_menu( 'customize' );
	$wp_admin_bar->remove_menu( 'updates' );
	$wp_admin_bar->remove_menu( 'comments' );
	$wp_admin_bar->remove_menu( 'new-content' );
	$wp_admin_bar->remove_menu( 'rank-math' );
	$wp_admin_bar->remove_menu( 'search' );
	$wp_admin_bar->remove_menu( 'archive' );
	$wp_admin_bar->remove_menu( 'view' );
	$wp_admin_bar->remove_menu( 'preview' );
}, 999999 );
#endregion Clean up


#region Remove
add_action( 'init', function () {
	if ( ! edg_is_public_role() ) return;

	show_admin_bar( false );
} );
#endregion Remove
#endregion Admin bar


#region Dashboard
#region Clean up
add_action( 'admin_menu', 'edg_admin_dashboard_clean_up', 999999 );
add_action( 'wp_dashboard_setup', 'edg_admin_dashboard_clean_up', 999999 );

function edg_admin_dashboard_clean_up() {
	# WP core
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_php_nag', 'dashboard', 'normal' );
	# Cookie Notice (https://wordpress.org/plugins/cookie-notice/)
	remove_meta_box( 'cn_dashboard_stats', 'dashboard', 'normal' );
	# Rank Math
	remove_meta_box( 'rank_math_dashboard_widget', 'dashboard', 'normal' );
	# WooCommerce
	remove_meta_box( 'wc_admin_dashboard_setup', 'dashboard', 'normal' );
}
#endregion Clean up


#region Add widgets
add_action( 'wp_dashboard_setup', function () {
	if ( edg_is_public_role() ) return;

	wp_add_dashboard_widget( 'edg-overview', __( 'Overview', 'edg-bricks-child' ), 'edg_dashboard_widget_overview' );
	wp_add_dashboard_widget( 'edg-system-info', __( 'System info', 'edg-bricks-child' ), 'edg_dashboard_widget_system_info' );
} );

function edg_dashboard_widget_overview() {
	$tutorial_url = 'https://www.youtube.com/watch?v=404';
	$credit_url = edg_credit_url( 'wp-admin-dashboard' );
	$email = 'info@edgarbollow.com';
	$pdf_compressor_url = 'https://pdfcompressor.com';
	$image_compressor_url = 'https://imagecompressor.com';
	$webp_converter_url = 'https://ezgif.com/jpg-to-webp';
	$avif_converter_url = 'https://ezgif.com/jpg-to-avif';
	$aac_converter_url = 'https://cloudconvert.com/mp3-to-aac';
	$webm_converter_url = 'https://cloudconvert.com/mp4-to-webm';
	$utm_builder_url = 'https://ga-dev-tools.google/ga4/campaign-url-builder/';
	$page_speed_analyzer_url = 'https://pagespeed.web.dev/analysis';
	$page_speed_analyzer_url = add_query_arg( [
		'url' => rawurlencode( home_url() ),
		'use_original_url' => 'true',
		'form_factor' => 'desktop',
	], $page_speed_analyzer_url );

	echo '
		<dl class="edg-dashboard-widget">
			<h3 class="edg-dashboard-widget__heading">' . __( 'Contact and help', 'edg-bricks-child' ) . '</h3>
			<div class="edg-dashboard-widget__rows edg-dashboard-widget__seperator">
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Video introduction', 'edg-bricks-child' ) . '</dt>
					<dd><a href="' . $tutorial_url . '" target="_blank">' . __( 'Watch tutorial', 'edg-bricks-child' ) . '</a></dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Website created by', 'edg-bricks-child' ) . '</dt>
					<dd><a href="' . $credit_url . '" target="_blank">Edgar Bollow</a></dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Email address', 'edg-bricks-child' ) . '</dt>
					<dd><a href="mailto:' . $email . '" target="_blank">' . $email . '</a></dd>
				</div>
			</div>
			<h3 class="edg-dashboard-widget__heading">' . __( 'Tools', 'edg-bricks-child' ) . '</h3>
			<div class="edg-dashboard-widget__rows">
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Compress PDFs', 'edg-bricks-child' ) . '</dt>
					<dd><a href="' . $pdf_compressor_url . '" target="_blank">' . __( 'Open link', 'edg-bricks-child' ) . '</a></dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Compress images', 'edg-bricks-child' ) . '</dt>
					<dd><a href="' . $image_compressor_url . '" target="_blank">' . __( 'Open link', 'edg-bricks-child' ) . '</a></dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Convert images to WEBP', 'edg-bricks-child' ) . '</dt>
					<dd><a href="' . $webp_converter_url . '" target="_blank">' . __( 'Open link', 'edg-bricks-child' ) . '</a></dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Convert images to AVIF', 'edg-bricks-child' ) . '</dt>
					<dd><a href="' . $avif_converter_url . '" target="_blank">' . __( 'Open link', 'edg-bricks-child' ) . '</a></dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Convert audio to AAC', 'edg-bricks-child' ) . '</dt>
					<dd><a href="' . $aac_converter_url . '" target="_blank">' . __( 'Open link', 'edg-bricks-child' ) . '</a></dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Convert video to WEBM', 'edg-bricks-child' ) . '</dt>
					<dd><a href="' . $webm_converter_url . '" target="_blank">' . __( 'Open link', 'edg-bricks-child' ) . '</a></dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'UTM URL builder', 'edg-bricks-child' ) . '</dt>
					<dd><a href="' . $utm_builder_url . '" target="_blank">' . __( 'Open link', 'edg-bricks-child' ) . '</a></dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Measure page speed', 'edg-bricks-child' ) . '</dt>
					<dd><a href="' . $page_speed_analyzer_url . '" target="_blank">' . __( 'Open link', 'edg-bricks-child' ) . '</a></dd>
				</div>
			</div>
		</dl>
	';
}

function edg_dashboard_widget_system_info() {
	$php_version = PHP_VERSION;
	$server_protocol = wp_get_server_protocol();
	$ini_memory_limit = ini_get( 'memory_limit' );
	$ini_post_max_size = ini_get( 'post_max_size' );
	$ini_upload_max_filesize = ini_get( 'upload_max_filesize' );
	$ini_max_input_vars = ini_get( 'max_input_vars' );
	$ini_max_input_time = ini_get( 'max_input_time' );
	$ini_max_execution_time = ini_get( 'max_execution_time' );
	$wp_version = get_bloginfo( 'version' );
	$wp_memory_limit = WP_MEMORY_LIMIT;
	$wp_max_memory_limit = WP_MAX_MEMORY_LIMIT;
	$debug_mode = WP_DEBUG ? __( 'Yes', 'edg-bricks-child' ) : __( 'No', 'edg-bricks-child' );
	global $wp_rewrite;
	$trailing_slashes = $wp_rewrite->use_trailing_slashes ? __( 'Yes', 'edg-bricks-child' ) : __( 'No', 'edg-bricks-child' );

	echo '
		<dl class="edg-dashboard-widget">
			<div class="edg-dashboard-widget__rows">
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'PHP version', 'edg-bricks-child' ) . '</dt>
					<dd>' . $php_version . '</dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Server protocol', 'edg-bricks-child' ) . '</dt>
					<dd>' . $server_protocol . '</dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>INI Memory Limit</dt>
					<dd>' . $ini_memory_limit . '</dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>INI Post Max Size</dt>
					<dd>' . $ini_post_max_size . '</dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>INI Upload Max Filesize</dt>
					<dd>' . $ini_upload_max_filesize . '</dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>INI Max Input Vars</dt>
					<dd>' . $ini_max_input_vars . '</dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>INI Max Input Time</dt>
					<dd>' . $ini_max_input_time . '</dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>INI Max Execution Time</dt>
					<dd>' . $ini_max_execution_time . '</dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'WordPress version', 'edg-bricks-child' ) . '</dt>
					<dd>' . $wp_version . '</dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>WordPress Memory Limit</dt>
					<dd>' . $wp_memory_limit . '</dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>WordPress Max Memory Limit</dt>
					<dd>' . $wp_max_memory_limit . '</dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Debug mode enabled', 'edg-bricks-child' ) . '</dt>
					<dd>' . $debug_mode . '</dd>
				</div>
				<div class="edg-dashboard-widget__row">
					<dt>' . __( 'Trailing slashes enabled', 'edg-bricks-child' ) . '</dt>
					<dd>' . $trailing_slashes . '</dd>
				</div>
			</div>
		</dl>
	';
}
#endregion Add widgets
#endregion Dashboard


#region Duplicate post row action
#region Add link
add_filter( 'page_row_actions', 'edg_admin_duplicate_post', 999999, 2 );
add_filter( 'post_row_actions', 'edg_admin_duplicate_post', 999999, 2 );

function edg_admin_duplicate_post( $actions, $post ) {
	$post_id = $post->ID;

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $actions;
	}

	$excluded_post_types = [
		'jet-form-builder',
	];
	$post_type = $post->post_type;

	if ( in_array( $post_type, $excluded_post_types ) ) {
		return $actions;
	}

	$nonce_name = 'edg-duplicate-post';
	$url = admin_url( 'admin.php' );
	$url = add_query_arg( [
		'action' => 'edg_duplicate_post',
		'id' => $post_id,
	], $url );
	$url = wp_nonce_url( $url, $nonce_name, 'token' );
	$actions[] = '<a href="' . $url . '" target="_blank">' . __( 'Duplicate', 'edg-bricks-child' ) . '</a>';

	return $actions;
}
#endregion Add link


#region Action
add_filter( 'admin_action_edg_duplicate_post', function () {
	$error_title = __( 'Duplication failed', 'edg-bricks-child' );
	$post_id = $_GET[ 'id' ] ?? '';
	$nonce = $_GET[ 'token' ] ?? '';

	if ( ! $post_id || ! $nonce ) {
		$error_message = __( 'Invalid URL', 'edg-bricks-child' );
		wp_die( $error_message, $error_title );
	}

	$nonce_name = 'edg-duplicate-post';

	if ( ! wp_verify_nonce( $nonce, $nonce_name ) ) {
		$error_message = __( 'Invalid token', 'edg-bricks-child' );
		wp_die( $error_message, $error_title );
	}

	$post = get_post( $post_id );

	if ( ! $post ) {
		$error_message = __( 'Invalid post ID', 'edg-bricks-child' );
		wp_die( $error_message, $error_title );
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		$error_message = __( 'Unauthorized', 'edg-bricks-child' );
		wp_die( $error_message, $error_title );
	}

	$post_type = $post->post_type;
	$new_post_title_string = __( 'Copy of "%s"', 'edg-bricks-child' );
	$new_post_slug_string = __( '%s-copy', 'edg-bricks-child' );
	$args = [
		'post_title' => sprintf( $new_post_title_string, $post->post_title ),
		'post_name' => sprintf( $new_post_slug_string, $post->post_name ),
		'post_parent' => $post->post_parent,
		'post_type' => $post_type,
		'post_status' => 'draft',
		'post_password' => $post->post_password,
		'post_author' => get_current_user_id(),
		'post_content' => $post->post_content,
		'post_excerpt' => $post->post_excerpt,
		'comment_status' => $post->comment_status,
		'ping_status' => $post->ping_status,
		'to_ping' => $post->to_ping,
		'menu_order' => $post->menu_order,
	];
	$new_post_id = wp_insert_post( $args );
	$taxonomies = get_object_taxonomies( $post_type );

	if ( $taxonomies ) {
		foreach ( $taxonomies as $taxonomy ) {
			$terms = wp_get_object_terms( $post_id, $taxonomy, [
				'fields' => 'slugs',
			] );
			wp_set_object_terms( $new_post_id, $terms, $taxonomy, false );
		}
	}

	global $wpdb;
	$meta_fields = "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=%d";
	$meta_fields = $wpdb->get_results( $wpdb->prepare( $meta_fields, $post_id ) );

	if ( $meta_fields ) {
		$query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value)";

		foreach ( $meta_fields as $field ) {
			$meta_key = sanitize_text_field( $field->meta_key );
			$meta_value = addslashes( $field->meta_value );
			$query_selection[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
		}

		$query .= implode( "UNION ALL ", $query_selection );
		$wpdb->query( $query );
	}

	$redirect_url = admin_url( 'post.php' );
	$redirect_url = add_query_arg( [
		'action' => 'edit',
		'post' => $new_post_id,
	], $redirect_url );
	wp_safe_redirect( $redirect_url );
	exit;
} );
#endregion Action
#endregion Duplicate post row action


#region Menu items
#region Remove
add_action( 'admin_menu', function () {
	if ( edg_is_admin_role() ) return;

	remove_menu_page( 'edit-comments.php' );
	remove_menu_page( 'edit.php?post_type=jet-smart-filters' );
	remove_menu_page( 'tools.php' );
}, 999999 );
#endregion Remove


#region Redirect
add_action( 'admin_init', function () {
	if ( edg_is_admin_role() ) return;

	global $pagenow;
	$excluded_pages = [
		'about.php',
		'credits.php',
		'freedoms.php',
		'privacy.php',
		'contribute.php',
		'edit-comments.php',
		'comment.php',
		'tools.php',
	];

	if ( in_array( $pagenow, $excluded_pages ) ) {
		edg_admin_abort();
	}

	$post_type = $_GET[ 'post_type' ] ?? '';
	$excluded_post_types = [
		'jet-smart-filters',
	];

	if ( in_array( $post_type, $excluded_post_types ) ) {
		edg_admin_abort();
	}

	$post_page = $pagenow === 'post.php';
	$post = $_GET[ 'post' ] ?? '';

	if ( $post_page && $post ) {
		$queried_post = get_post( $post );
		$post_type = $queried_post->post_type;

		if ( ! in_array( $post_type, $excluded_post_types ) ) return;

		$user_id = get_current_user_id();
		$post_author = intval( $queried_post->post_author );

		if ( $post_author === $user_id ) return;

		edg_admin_abort();
	}
}, 999999 );
#endregion Redirect
#endregion Menu items


#region Media library
#region Image editor quality
add_filter( 'jpeg_quality', 'edg_media_image_editor_quality' );
add_filter( 'wp_editor_set_quality', 'edg_media_image_editor_quality' );

function edg_media_image_editor_quality() {
	return 100;
}
#endregion Image editor quality


#region Allowed MIME types for upload
add_filter( 'upload_mimes', function ( $mimes ) {
	$mimes = [];

	$new_mimes_list = [
		'webp' => 'image/webp',
		// 'avif' => 'image/avif',
		// 'avifs' => 'image/avif-sequence',
		'svg' => 'image/svg+xml',
		'aac' => 'audio/aac',
		'webm' => 'video/webm',
		'txt' => 'text/plain',
		'pdf' => 'application/pdf',
		'zip' => 'application/zip',
	];

	foreach ( $new_mimes_list as $mime => $value ) {
		$mimes[ $mime ] = $value;
	}

	return $mimes;
}, 999999 );
#endregion Allowed MIME types for upload


#region Upload prefilters
#region Sanitize filename
add_filter( 'wp_handle_upload_prefilter', function ( $file ) {
	$filename = $file[ 'name' ];
	$extension = pathinfo( $filename, PATHINFO_EXTENSION );
	$extension = strtolower( $extension );
	$filename = pathinfo( $filename, PATHINFO_FILENAME );
	$filename = str_replace( '_', '-', $filename );
	$filename = sanitize_title( $filename );
	$string_limit = 120;
	$filename = strlen( $filename ) > $string_limit ? substr( $filename, 0, $string_limit ) : $filename;
	$file[ 'name' ] = "$filename.$extension";

	return $file;
} );
#endregion Sanitize filename


#region Remove image EXIF metadata
add_filter( 'wp_handle_upload_prefilter', function ( $file ) {
	$is_image = strpos( $file[ 'type' ], 'image/' ) !== false;

	if ( ! $is_image ) {
		return $file;
	}

	$file_path = $file[ 'tmp_name' ];
	exec( "exiftool -all= '$file_path'" );

	return $file;
} );
#endregion Remove image EXIF metadata


#region Limit image upload size
add_filter( 'wp_handle_upload_prefilter', function ( $file ) {
	if ( edg_is_admin_role() ) {
		return $file;
	}

	$is_image = strpos( $file[ 'type' ], 'image' ) !== false;

	if ( ! $is_image ) {
		return $file;
	}

	$file_size_limit = .5; /* in MB */
	$file_size = $file[ 'size' ] / MB_IN_BYTES;

	if ( $file_size <= $file_size_limit ) {
		return $file;
	}

	$error_message = __( 'Images must be a maximum of %s MB in size. Current size: %s MB', 'edg-bricks-child' );
	$file_size_decimals = 1;
	$error_file_size_limit = round( $file_size_limit, $file_size_decimals );
	$error_file_size_limit = number_format_i18n( $error_file_size_limit, $file_size_decimals );
	$error_file_size = round( $file_size, $file_size_decimals );
	$error_file_size = number_format_i18n( $error_file_size, $file_size_decimals );
	$file[ 'error' ] = sprintf( $error_message, $error_file_size_limit, $error_file_size );

	return $file;
} );
#endregion Limit image upload size
#endregion Upload prefilters
#endregion Media library
#endregion WP admin
