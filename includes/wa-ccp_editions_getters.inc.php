<?php
/**
 * Getters and shortcodes for editions
 */

// Set globals vars
$current_edition = array();
$previous_editions = array();
$current_edition_id = 0;
$current_edition_slug = '';
$current_edition_films_are_online = false;
$current_edition_parent_term_id = 0;

add_action('init', 'get_edition_vars', 90); // wp_loaded
function get_edition_vars() {
	global $current_edition, $previous_editions, $current_edition_id, $current_edition_slug, $current_edition_films_are_online, $current_edition_parent_term_id;

	$args = array(
		'taxonomy'=>'edition',
		'hide_empty' => false, // Add to start the edition
	);

	$tmp_editions = get_terms($args);
	$current_edition = false;
	foreach($tmp_editions as $tmp_edition) {
		$current = get_term_meta($tmp_edition->term_id, WA_CCPEF_MIGRATE_FIELD_CURRENT ? WA_CCPEF_MIGRATE_FIELD_CURRENT : 'wpcf-e-current-edition', true);
		if ($current) {
			$current_edition = $tmp_edition;
			$current_edition_id = $tmp_edition->term_id;
			$current_edition_slug = $tmp_edition->slug;
			//break;
		} else {
			$previous_editions[] = $tmp_edition->slug;
		}
	}
	if (!$current_edition)
		add_action( 'admin_notices', 'edition_admin_notice__error' );

	unset($tmp_editions);
	$tmp_editions = null;
	
	// Get $current_edition_parent_term_id
	if ( WA_CCPEF_MIGRATE === true ){
		$args = array(
			'taxonomy'   	=> 'section',
			'hide_empty' 	=> false,
			'hierarchical' 	=> true,
			'orderby' 	 	=> 'term_order',
			'order' 		=> 'ASC',
			'parent' 		=> 0, 
			'meta_query' => array(
				array(
					'key'       => 'wpcf-select-edition',
					'value'     => $current_edition_id,
					'compare'   => '='
				)
			)
		);
		$get_edition_sections = get_terms($args);
		//print_r($get_edition_sections);

		foreach ($get_edition_sections as $section) {
			if ( $section->parent == 0 ) {
				$current_edition_parent_term_id = (int)$section->term_id;
			}
		}
	}

	// Get current_edition_films_are_online option
	$tmp_current_edition_films_are_online = get_option('current_edition_films_are_online');
	$current_edition_films_are_online = ( !empty($tmp_current_edition_films_are_online) && $tmp_current_edition_films_are_online == 1)?true:false; 

	// Verify if current edition is set 
	// wp_die('<pre>' . var_dump($current_edition) . var_dump($previous_editions) . var_dump($current_edition_id) . var_dump($current_edition_films_are_online) . var_dump($current_edition_parent_term_id) . '</pre>');		
}

function edition_admin_notice__error() {
	$class = 'notice notice-error error-message';
	$message = __( 'Oups ! Aucune édition en cours sélectionnée / No current edition selected', 'waaet' );
	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), '<span class="error-message dashicons-before dashicons-image-filter" aria-hidden="true"></span> ' . esc_html( $message ) ); 
}

// Create shortocde 
add_shortcode( 'getcurrenteditionsc', 'getcurrentedition_func' );
function getcurrentedition_func($atts, $content = ''){
	global $current_edition, $previous_editions, $current_edition_id, $current_edition_films_are_online;

	// Abord if no edition
	if ( empty($current_edition) )
		return '';

  	// Extract atts
    extract( shortcode_atts( array(
        'display' => 'current',
        'content_before' => '',
        'content_after' => '',
        'content_during' => ''
    ), $atts ));


	setlocale(LC_TIME, 'fr_FR.UTF8');

	$today = getdate();
	$edition_start_date_meta 		= get_term_meta($current_edition->term_id, 'wpcf-e-start-date', True);
	$edition_end_date_meta 			= get_term_meta($current_edition->term_id, 'wpcf-e-end-date', True);
	$edition_start_date 			= date('d', $edition_start_date_meta);//Y-m-d
	$edition_end_date 				= date('d', $edition_end_date_meta);
	$edition_start_date_uk 			= date('jS', $edition_start_date_meta);//Y-m-d
	$edition_end_date_uk 			= date('jS', $edition_end_date_meta);
	$edition_year 					= date('Y', $edition_start_date_meta);
	$edition_month 					= date('M', $edition_start_date_meta);
	$edition_month_end 				= date('M', $edition_end_date_meta);
	$edition_monthnumeric 			= date('m', $edition_start_date_meta);
	$edition_start_date_formatted 	= date('c', $edition_start_date_meta);

	// Starts at 8:00 instead 0:00
	$edition_start_date_meta 		= $edition_start_date_meta + (60 * 60 * 8); 
	// Ends at 23:59 instaead 0:00
	$edition_end_date_meta 			= $edition_end_date_meta + ((60*60*(24))-60);

	switch (esc_attr($display)) {
  		case "current":
  			return $current_edition->slug;
  		break;
  		case "publicsafecurrent":
  			return ($current_edition_films_are_online === true)?$current_edition->slug:'-1';
  		break;
		case "previous":
  			return $previous_editions;
		  break;
  		case "publicsafeprevious":
			return ($current_edition_films_are_online === true)?$previous_editions:'-1';
		break;
		case "startdate":
  			return $edition_start_date;
  		break;
  		case "enddate":
  			return $edition_end_date;
  		break;
  		case "dates":
			return ( function_exists('qtranxf_getLanguage') ) ? __( '[:fr]' . $edition_start_date . ' au ' . $edition_end_date . '[:en]' . $edition_start_date_uk . ' until ' . $edition_end_date_uk . '[:]' ) : $edition_start_date . ' au ' . $edition_end_date;
  		break;
		  case "full":
			if ( $edition_month != $edition_month_end)	
				return ( function_exists('qtranxf_getLanguage') ) ? __( '[:fr]du ' . $edition_start_date . '[:en]from ' . $edition_start_date_uk . '[:]') . ' ' . strtolower($edition_month) . '. ' . __( '[:fr]au ' . $edition_end_date . '[:en]until ' . $edition_end_date_uk . '[:]' ) . ' ' . strtolower($edition_month_end) . '. ' . $edition_year : 'du ' . $edition_start_date . ' ' . strtolower($edition_month) . '. ' . 'au ' . $edition_end_date . ' ' . strtolower($edition_month_end) . '. ' . $edition_year;
			else 
				return ( function_exists('qtranxf_getLanguage') ) ? __( '[:fr]du ' . $edition_start_date . '[:en]from ' . $edition_start_date_uk . '[:]') . ' ' . __( '[:fr]au ' . $edition_end_date . '[:en]until ' . $edition_end_date_uk . '[:]' ) . ' ' . strtolower($edition_month) . '. ' . $edition_year : 'du ' . $edition_start_date . ' '  . 'au ' . $edition_end_date . ' ' . strtolower($edition_month_end) . '. ' . $edition_year;
			break;
  		case "year":
  			return $edition_year;
  		break;
  		case "month":
  			return $edition_month;
  		break;
  		case "monthnumeric":
  			return $edition_monthnumeric;
  		break;
  		case "id":
  			return $current_edition_id;
  		break;
  		case "publicsafeid":
  			return ($current_edition_films_are_online === true)?$current_edition_id:-1;
  		break;
		case "content":
  			if ( $today[0] < $edition_start_date_meta ) {
  				$contenttoshow = esc_attr($content_before);
  			} else if ( $today[0] > $edition_end_date_meta ) {
  				$contenttoshow = esc_attr($content_after);
  			} else {
  				$contenttoshow = esc_attr($content_during);
  			}
  			return do_shortcode($contenttoshow);
  		break;
  		case "section":
  			if ( $today[0] < $edition_start_date_meta ) {
  				$contenttoshow = esc_attr($content_before);
  			} else if ( $today[0] > $edition_end_date_meta ) {
  				$contenttoshow = esc_attr($content_after);
  			} else {
  				$contenttoshow = esc_attr($content_during);
  			}
  			return do_shortcode('[spb_section spb_section_id="'.$contenttoshow.'" width="1/1" el_position="first last"]');
		break;
		case "during":
			if ( $today[0] < $edition_start_date_meta ) {
				return false;
			} else if ( $today[0] > $edition_end_date_meta ) {
				return false;
			} else {
				return true;
			}
		break; // #41
		case "duringafter":
			if ( $today[0] < $edition_start_date_meta ) {
				return false;
			} else if ( $today[0] > $edition_end_date_meta ) {
				return true;
			} else {
				return true;
			}
		break; // #41
		case "formatted": 
			return $edition_start_date_formatted;
		break;  
  		default:
  			return '';
  		break;
  	}
}