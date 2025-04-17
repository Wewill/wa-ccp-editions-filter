<?php

/*
Plugin Name: WA CCP WAFF Edition Filter
Plugin URI: http://www.wilhemarnoldy.fr
Description: Adds an Edition filter and an archives system to the admin head and filter selected posts and taxonomies
Author: Wilhem Arnoldy & Justin Petermann ( concipio )
Version: 2.0.0
Author URI: http://www.wilhemarnoldy.fr
Text Domain: wa-ccpef
Domain Path: /languages
Tags: post, filter, pre_get_posts, editions, archives
Requires at least: 4.0
Tested up to: 6.4.1
*/

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( !is_login() && is_admin() && !function_exists('rwmb_meta') ) {
	wp_die('Error : please install Meta Box plugin.');
}

if ( !is_login() && is_admin() && !function_exists('mb_settings_page_load') ) {
	wp_die('Error : please install Meta Box Settings plugin.');
}


/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WA_CCPEF_VERSION', '2.0.0');
define('WA_CCPEF_DEBUG', false);

// Load text domain
// -------------------
define('WA_CCPEF_DIR', plugin_dir_path( __FILE__ ) );
define('WA_CCPEF_PO_PLUGINPATH', '/' . dirname(plugin_basename( __FILE__ )));
define('WA_CCPEF_TEXTDOMAIN', 'wace');

add_action('plugins_loaded', 'wa_ccpef_load_textdomain');
function wa_ccpef_load_textdomain() {
	load_plugin_textdomain( WA_CCPEF_TEXTDOMAIN, false, WA_CCPEF_PO_PLUGINPATH.'/languages/' );
}

// General admin styles
// -------------------
add_action( 'admin_enqueue_scripts', 'wa_ccpef_admin_styles' );
function wa_ccpef_admin_styles() {
	wp_enqueue_style( 'wa-ccpef-admin-style', plugins_url( '/css/admin-style.css', __FILE__ ) );
}


add_action('plugins_loaded', 'wa_ccpef_load');
function wa_ccpef_load() {
	/* Migrate from old versions */
	require_once(WA_CCPEF_DIR . 'includes/wa-ccp_editions_migrate.inc.php');

	/* Register Edition taxonomy */
	require_once(WA_CCPEF_DIR . 'includes/wa-ccp_editions_register.inc.php');

	/* Load settings page */
	require_once(WA_CCPEF_DIR . 'includes/wa-ccp_editions_settings.inc.php');

	/* Load title badge */
	require_once(WA_CCPEF_DIR . 'includes/wa-ccp_editions_title.inc.php');

	/* Load editions filter class */
	require_once(WA_CCPEF_DIR . 'filter/wa-ccp_editions_filter.inc.php');
	$ccpef = new ccp_editions_filter();
	$ccpef->run();

	/* Admin filter */
	if ( is_admin() ) {
		// include(WA_CCPEF_DIR . 'filter/wa-ccp_editions_pre_get_posts.php');
	}

	/* Frontend filter */
	if ( ! is_admin() ) {
		include(WA_CCPEF_DIR . 'archives/wa-ccp_editions_archives.php');
	}
}