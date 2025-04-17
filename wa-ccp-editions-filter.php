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
define( 'WA_CCPEF_VERSION', '2.0.0' );


add_action('plugins_loaded', 'wa_ccp_editions_filter');

function wa_ccp_editions_filter() {
	/* Load editions filter class */
	require_once(plugin_dir_path( __FILE__ ) . '/filter/ccp_editions_filter.inc.php');
	$ccpef = new ccp_editions_filter();
	$ccpef->run();

	/* Admin filter */
	if ( is_admin() ) {
		include(plugin_dir_path( __FILE__ ) . '/filter/ccp_editions_pre_get_posts.php');
	}

	/* Frontend filter */
	if ( ! is_admin() ) {
		include(plugin_dir_path( __FILE__ ) . '/archives/ccp_editions_archives.php');
	}
}