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
	wp_enqueue_style( 'wa-ccpef-admin-style', plugins_url( '/css/archives-admin-style.css', __FILE__ ) );
}

// General front styles
// -------------------
add_action( 'wp_enqueue_scripts', 'wa_ccpef_front_styles' );
function wa_ccpef_front_styles() {
	wp_enqueue_style( 'wa-ccpef-front-style', plugins_url( '/css/archives-front-style.css', __FILE__ ) );
}

add_action('plugins_loaded', 'wa_ccpef_load');
function wa_ccpef_load() {
	/* Migrate from old versions */
	require_once(WA_CCPEF_DIR . 'includes/wa-ccp_editions_migrate.inc.php');

	/* Load helpers */
	require_once(WA_CCPEF_DIR . 'helpers/wa-ccp_editions_templateloader.inc.php');

	/* Load settings page */
	require_once(WA_CCPEF_DIR . 'includes/wa-ccp_editions_settings.inc.php');

	/* Register Edition taxonomy */
	require_once(WA_CCPEF_DIR . 'includes/wa-ccp_editions_register.inc.php');

	/* Load custom columns */
	require_once(WA_CCPEF_DIR . 'includes/wa-ccp_editions_columns.inc.php');

	/* Load getters & shortcodes */
	require_once(WA_CCPEF_DIR . 'includes/wa-ccp_editions_getters.inc.php');

	/* Load autoadd edition tag */
	require_once(WA_CCPEF_DIR . 'includes/wa-ccp_editions_autoadd.inc.php');

	/* Load title badge */
	require_once(WA_CCPEF_DIR . 'includes/wa-ccp_editions_title.inc.php');

	/* Load editions blocks */
	require_once(WA_CCPEF_DIR . 'archives/wa-ccp_editions_block.inc.php');

	/* Load shortcode for current edition header */
	require_once(WA_CCPEF_DIR . 'includes/wa-ccp_editions_shortcode.inc.php');

	/* Load editions filter class */
	require_once(WA_CCPEF_DIR . 'filter/wa-ccp_editions_filter.inc.php');
	$ccpef = new ccp_editions_filter();
	$ccpef->run();

	/* Admin filter */
	if ( is_admin() ) {
		include(WA_CCPEF_DIR . 'filter/wa-ccp_editions_pre_get_posts.php');
	}

	/* Frontend filter */
	if ( !is_admin() ) {
		include(WA_CCPEF_DIR . 'archives/wa-ccp_editions_archives.php');
	}

}

/**
 * Load custom template for 'edition' taxonomy
 */
add_filter('template_include', 'wa_ccpef_load_taxonomy_template');
function wa_ccpef_load_taxonomy_template($template) {

	// print_r(get_query_var('edition_archive')); // Devrait être 1
	// print_r($_SERVER['REQUEST_URI']); // Pour vérifier l'URL

	if (get_query_var('edition_archive')) {
		// Path to your custom template file
        $custom_template = plugin_dir_path(__FILE__) . 'templates/archives-edition.php';

        // Check if the custom template exists
        if (file_exists($custom_template)) {
            return $custom_template;
        }
	}

    // Check if the current query is for the 'edition' taxonomy
    if (is_tax('edition')) {
        // Path to your custom template file
        $custom_template = plugin_dir_path(__FILE__) . 'templates/taxonomy-edition.php';

        // Check if the custom template exists
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }

    // Return the default template if no custom template is found
    return $template;
}