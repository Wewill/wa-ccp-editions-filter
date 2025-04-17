<?php

add_action( 'load-edit.php', function() {
    if( is_admin()) {
		add_filter( 'pre_get_posts', 'filter_film_pages');

		add_filter( 'pre_get_posts', 'filter_ticket_pages');

		add_filter( 'pre_get_posts', 'filter_accreditation_pages');

		add_filter( 'pre_get_posts', 'filter_projection_pages');

		add_filter( 'pre_get_posts', 'filter_clients_pages');

		add_filter( 'pre_get_posts', 'filter_jury_pages');

		add_filter( 'pre_get_posts', 'filter_partenaire_pages');

		add_filter( 'pre_get_posts', 'filter_partner_pages');
	}
});

add_action( 'init', function() {
	// Limit medias menu 
	add_filter( 'pre_get_posts', 'filter_medias' ); // #43 not working  > #44 ok 
	// Limit medias modal window 
	add_filter( 'ajax_query_attachments_args', 'ajax_filter_medias', 10, 1 );
});


function filter_film_pages($query) {
	$q_vars_post_type = get_query_var('post_type');
	// Do not query if all editions 
	if ( get_edition()[0] === '-1') return $query;
	if ( is_admin() && $q_vars_post_type == 'film' && $query->is_main_query() && (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current') && (!array_key_exists('board', $_GET) || $_GET['board'] == '' ))
		$query->set( 'tax_query', 	array(
				array(
					'taxonomy' => 'edition',
					'field'    => 'slug',
					'terms'    => get_edition()[0],
				),
			)
		);
	return $query;
}

function filter_ticket_pages($query) {
	$q_vars_post_type = get_query_var('post_type');
	// Do not query if all editions 
	if ( get_edition()[0] === '-1') return $query;
	if ( $q_vars_post_type == 'ticket' && $query->is_main_query() && (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current') && (!array_key_exists('board', $_GET) || $_GET['board'] == '' ))
		$query->set( 'tax_query', 	array(
										array(
											'taxonomy' => 'edition',
											'field'    => 'slug',
											'terms'    => get_edition()[0],
										),
									)
		);
	return $query;
}

function filter_accreditation_pages($query) {
	$q_vars_post_type = get_query_var('post_type');
	// error_log("###filter_accreditation_pages query" . print_r($query, true));
	// error_log("###filter_accreditation_pages" . print_r($q_vars_post_type, true));
	// Do not query if all editions 
	if ( get_edition()[0] === '-1') return $query;
	if ( $q_vars_post_type == 'accreditation' && $query->is_main_query() && (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current') && (!array_key_exists('board', $_GET) || $_GET['board'] == '' ))
		$query->set( 'tax_query', 	array(
				array(
					'taxonomy' => 'edition',
					'field'    => 'slug',
					'terms'    => get_edition()[0],
				),
			)
		);
	return $query;
}

function filter_projection_pages($query) {
	$q_vars_post_type = $query->query['post_type'];
	// Do not query if all editions 
	if ( get_edition()[0] === '-1') return $query;
	if ( $q_vars_post_type == 'projection' && $query->is_main_query() && (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current') && (!array_key_exists('board', $_GET) || $_GET['board'] == '' )) {
		$query->set( 'tax_query', 	array(
				array(
					'taxonomy' => 'edition',
					'field'    => 'slug',
					'terms'    => get_edition()[0],
				),
			)
		);
	}
	return $query;
}

function filter_jury_pages($query) {
	$q_vars_post_type = $_GET['post_type'];
	// Do not query if all editions 
	if ( get_edition()[0] === '-1') return $query;
	if ( $q_vars_post_type == 'jury' && $query->is_main_query() && (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current') && (!array_key_exists('board', $_GET) || $_GET['board'] == '' )) {
		$query->set( 'tax_query', 	array(
				array(
					'taxonomy' => 'edition',
					'field'    => 'slug',
					'terms'    => get_edition()[0],
				),
			)
		);
	}
	return $query;
}

function filter_partenaire_pages($query) {
	$q_vars_post_type = $_GET['post_type'];
	// Do not query if all editions 
	if ( get_edition()[0] === '-1') return $query;
	if ( $q_vars_post_type == 'partenaire' && $query->is_main_query() && (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current') && (!array_key_exists('board', $_GET) || $_GET['board'] == '' )) {
		$query->set( 'tax_query', 	array(
				array(
					'taxonomy' => 'edition',
					'field'    => 'slug',
					'terms'    => get_edition()[0],
				),
			)
		);
	}
	return $query;
}

function filter_partner_pages($query) {
	$q_vars_post_type = $_GET['post_type'];
	// Do not query if all editions 
	if ( get_edition()[0] === '-1') return $query;
	if ( $q_vars_post_type == 'partner' && $query->is_main_query() && (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current') && (!array_key_exists('board', $_GET) || $_GET['board'] == '' )) {
		$query->set( 'tax_query', 	array(
				array(
					'taxonomy' => 'edition',
					'field'    => 'slug',
					'terms'    => get_edition()[0],
				),
			)
		);
	}
	return $query;
}

function filter_clients_pages($query) {
	$q_vars_post_type = get_query_var('post_type');
	// Do not query if all editions 
	if ( get_edition()[0] === '-1') return $query;
	if ( $q_vars_post_type == 'clients' && $query->is_main_query() && (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current') && (!array_key_exists('board', $_GET) || $_GET['board'] == '' ))
	$query->set( 'tax_query', 	array(
			array(
				'taxonomy' => 'edition',
				'field'    => 'slug',
				'terms'    => get_edition()[0],
			),
		)
	);
	return $query;
}

function filter_post($query) {
	$q_vars_post_type = get_query_var('post_type');
	// Do not query if all editions 
	if ( get_edition()[0] === '-1') return $query;
	if ( $q_vars_post_type == 'post' && $query->is_main_query() && (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current') && (!array_key_exists('board', $_GET) || $_GET['board'] == '' ))
	$query->set( 'tax_query', 	array(
			array(
				'taxonomy' => 'edition',
				'field'    => 'slug',
				'terms'    => get_edition()[0],
			),
		)
	);
	return $query;
}

function filter_medias($query) {
	global $pagenow;
	$q_vars_post_type = get_query_var('post_type');
	// Do not query if all editions 
	if ( !is_array(get_edition()) || get_edition()[0] === '-1') return $query;
	// Get year from current edition 
	$term = get_term_by('slug', (string) get_edition()[0], 'edition');
	$current_edition_year = get_term_meta( $term->term_id, 'wpcf-e-year', true );
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
	if ( is_admin() && $pagenow === 'upload.php' && $q_vars_post_type == 'attachment' && $query->is_main_query() && (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current') && (!array_key_exists('board', $_GET) || $_GET['board'] == '' ))
	$query->set('date_query', array(
		array(
			'year' => $current_edition_year,
		),
	));	
	return $query;
}

function ajax_filter_medias( $query = array() ) {
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
	// Do not query if all editions 
	if ( !is_array(get_edition()) || get_edition()[0] === '-1') return $query;
	// Get year from current edition 
	$term = get_term_by('slug', (string) get_edition()[0], 'edition');
	$current_edition_year = get_term_meta( $term->term_id, 'wpcf-e-year', true );
	//From regular pagenow + post_type 
	if ( is_admin() && ( isset($_POST) && $_POST['action'] === 'query-attachments' ) && $query['post_type'] === 'attachment' && (!array_key_exists('edition', $_GET) || $_GET['edition'] == '' || $_GET['edition'] == 'current') && (!array_key_exists('board', $_GET) || $_GET['board'] == '' ))
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