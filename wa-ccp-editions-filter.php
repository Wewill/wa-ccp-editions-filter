<?php

/*
Plugin Name: WA CCP WAFF Edition Filter 
Author: Wilhem Arnoldy & Justin Petermann ( concipio )
Version: 2.0
Description: Adds an Edition filter and an archives system to the admin head and filter selected posts and taxonomies
*/

add_action('plugins_loaded', 'wa_ccp_editions_filter');

function wa_ccp_editions_filter() {
	require_once('ccp_editions_filter.inc.php');
	$ccpef = new ccp_editions_filter();
	$ccpef->run();

	if ( is_admin() ) {
		include('ccp_editions_pre_get_posts.php');
	}
}



