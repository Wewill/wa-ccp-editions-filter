<?php
/*
Filter
*/

add_action( 'load-edit.php', function() {
    if( is_admin()) {
		// Limit posts 
		add_filter( 'pre_get_posts', 'filter_posts');
	}
});

add_action( 'init', function() {
	if ( wa_ccpef_get_filtermedias_from_setting_page() === 1 ) {
		// Limit medias 
		add_filter( 'pre_get_posts', 'filter_medias' ); // #43 not working  > #44 ok 
		// Limit medias modal window 
		add_filter( 'ajax_query_attachments_args', 'ajax_filter_medias', 10, 1 );
	}
}, 20);

function filter_posts($query) {
    global $ccp_editions_filter;

    // Ensure the global instance exists and the method is callable
    if (!isset($ccp_editions_filter) || !method_exists($ccp_editions_filter, 'get_edition')) return $query;

    // Call the get_edition() method from the class instance
    $edition = $ccp_editions_filter->get_edition();

	// Get posts from settings page	
	$posts = wa_ccpef_get_posts_from_setting_page();

	// Do not query if all editions or empty or not array
	if ( !is_array($edition) || empty($edition) || $edition[0] === '-1' ) return $query;

	// Do not query if no posts settings
	if ( empty($posts) ) return $query;

	foreach ($posts as $post) {
		if ( $query->is_main_query() && 
			 $query->is_post_type_archive($post) && 
			 (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current')) 
		{
			$query->set( 'tax_query', 	array(
					array(
						'taxonomy' => 'edition',
						'field'    => 'slug',
						'terms'    => $edition[0],
					),
				)
			);
		}
	}

}

function filter_medias($query) {
	global $pagenow;
    global $ccp_editions_filter;

    // Ensure the global instance exists and the method is callable
    if (!isset($ccp_editions_filter) || !method_exists($ccp_editions_filter, 'get_edition')) return $query;

    // Call the get_edition() method from the class instance
    $edition = $ccp_editions_filter->get_edition();

	// Do not query if all editions or empty or not array
	if ( !is_array($edition) || empty($edition) || $edition[0] === '-1' ) return $query;

	// Get queried post_type 
	$q_vars_post_type = get_query_var('post_type');

	// Get year from current edition 
	$term = get_term_by('slug', (string) $edition[0], 'edition');
	$current_edition_year = get_term_meta( $term->term_id, WA_CCPEF_MIGRATE_FIELD_YEAR ? WA_CCPEF_MIGRATE_FIELD_YEAR : 'wpcf-e-year', true );

	// // From AJAX upload mode grid 
	// error_log(print_r($_GET,true) . ' / ' . print_r($_SERVER,true) . ' / ' . print_r($query,true));
	// // [SCRIPT_NAME] => /wp-admin/admin-ajax.php
	// // [HTTP_REFERER] => https://www.fifam.fr/wp-admin/upload.php?mode=grid && [HTTP_REFERER] => https://www.fifam.fr/wp-admin/post-new.php
	// if ( is_admin() && $_SERVER['HTTP_REFERER'] === 'https://www.fifam.fr/wp-admin/upload.php?mode=grid' && $_SERVER['SCRIPT_NAME'] === '/wp-admin/admin-ajax.php' && $query->query_vars['post_type'] === 'attachment' ) {
	// 	// echo '########MODE_'.$_GET['mode'].'########';
	// 	$query->set('date_query', array(
	// 		array(
	// 			'year' => $current_edition_year,
	// 		),
	// 	));	
	// }
	// >>>> Now done w/ ajax_filter_medias() hook 
	
	// From regular pagenow + post_type 
	if ( is_admin() && 
		$pagenow === 'upload.php' && 
		$q_vars_post_type == 'attachment' && 
		$query->is_main_query() && 
		(!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current') )
	{
		$query->set('date_query', array(
			array(
				'year' => $current_edition_year,
			),
		));
	}
	return $query;
}

function ajax_filter_medias( $query = array() ) {
	global $ccp_editions_filter;

    // Ensure the global instance exists and the method is callable
    if (!isset($ccp_editions_filter) || !method_exists($ccp_editions_filter, 'get_edition')) return $query;

    // Call the get_edition() method from the class instance
    $edition = $ccp_editions_filter->get_edition();

	// Do not query if all editions or empty or not array
	if ( !is_array($edition) || empty($edition) || $edition[0] === '-1' ) return $query;

	// Get year from current edition 
	$term = get_term_by('slug', (string) $edition[0], 'edition');
	$current_edition_year = get_term_meta( $term->term_id, WA_CCPEF_MIGRATE_FIELD_YEAR ? WA_CCPEF_MIGRATE_FIELD_YEAR : 'wpcf-e-year', true );


	// error_log('##ajax_filter_medias :: $query'.print_r($query,true));
	// error_log('##ajax_filter_medias :: $_POST'.print_r($_POST,true));
	// error_log('##ajax_filter_medias :: $_GET'.print_r($_GET,true));
	// error_log('##ajax_filter_medias :: $_REQUEST'.print_r($_REQUEST,true));
	// error_log('##ajax_filter_medias :: $query post_type'.print_r($query['post_type'],true));
	// error_log('##ajax_filter_medias :: $_POST action'.print_r($_POST['action'],true));
	// error_log('##ajax_filter_medias :: $query get edition'.print_r((!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current'),true));
	// error_log('##ajax_filter_medias :: $query get board'.print_r((!array_key_exists('board', $_GET) || $_GET['board'] == '' ),true));
	/*
	[07-Nov-2024 10:04:07 UTC] ##ajax_filter_medias :: $queryArray
	(
		[orderby] => date
		[order] => DESC
		[posts_per_page] => 80
		[paged] => 1
		[post_type] => attachment
		[post_status] => inherit,private
	)

	[07-Nov-2024 10:04:07 UTC] ##ajax_filter_medias :: $_POSTArray
	(
		[action] => query-attachments
		[post_id] => 0
		[query] => Array
			(
				[orderby] => date
				[post_parent__in] => Array
					(
						[0] => 93309
						[1] => 93318
					)

				[order] => DESC
				[posts_per_page] => 80
				[paged] => 1
			)

	)
	*/

	//From regular pagenow + post_type 
	if ( is_admin() && ( isset($_POST) && $_POST['action'] === 'query-attachments' ) && $query['post_type'] === 'attachment' && (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current'))
	$query['date_query'] = array(
		array(
			'year' => $current_edition_year,
		),
	);	
	// Limit to my medias ONLY
    // $user_id = get_current_user_id();
    // if( $user_id ) {
    //     $query['author'] = $user_id;
    // }
    return $query;
}