<?php
/**
 * Add edition columns to post & taxs  
 */

 /**
  * Posts
  */

add_action( 'init', 'wa_ccpef_edition_columns', 20 );
function wa_ccpef_edition_columns() {
	// Add a custom columns to posts
	if ( function_exists( 'wa_ccpef_get_posts_from_setting_page' ) && !empty(wa_ccpef_get_posts_from_setting_page()) )
	{
		foreach (wa_ccpef_get_posts_from_setting_page() as $post_type) {
			add_filter( 'manage_edit-'. $post_type .'_columns', 'wa_ccpef_columns' );
			add_filter( 'manage_edit-'. $post_type .'_sortable_columns', 'wa_ccpef_sortable_columns' );
			add_action( 'manage_'. $post_type .'_posts_custom_column', 'wa_ccpef_posts_custom_column' );
		}
	}
	
    // Add a custom columns to taxonomies
    if ( function_exists( 'wa_ccpef_get_taxonomies_from_setting_page' ) && !empty(wa_ccpef_get_taxonomies_from_setting_page()) )
    {
        foreach (wa_ccpef_get_taxonomies_from_setting_page() as $taxonomy) {
			add_filter( 'manage_edit-'.$taxonomy.'_columns', 'wa_ccpef_taxonomy_columns');
			add_filter( 'manage_'.$taxonomy.'_custom_column', 'wa_ccpef_taxonomy_custom_column', 10, 3);
        }
    }
}

// manage_edit-{$post_type}_columns
function wa_ccpef_columns( $columns ) {
	$columns['edition'] = __( 'Edition', 'wa_ccpef');
	return $columns;
}

// manage_edit-{$post_type}_sortable_columns
function wa_ccpef_sortable_columns( $columns ) {
	$columns['edition'] = 'edition';
	return $columns;
}

// manage_{$post_type}_posts_custom_column
function wa_ccpef_posts_custom_column($column) {
	global $post;
    switch ($column) {
		case 'edition' :
			get_edition_post_terms($post->ID, $column);
		break;
    }
}

// Order by taxonomy
add_action( 'pre_get_posts', 'manage_wp_posts_be_qe_pre_get_posts', 10, 1 );
function manage_wp_posts_be_qe_pre_get_posts( $query ) {
    if ( is_admin() && $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
        if ( $orderby === 'edition' ) {
            add_filter( 'posts_join', 'join_edition_taxonomy' );
            add_filter( 'posts_orderby', function( $orderby_sql ) use ( $query ) {
                return orderby_edition_name( $query );
            });
            add_filter( 'posts_groupby', 'groupby_post_id_for_edition' );
        }
    }
}

function join_edition_taxonomy( $join ) {
    global $wpdb;
    return $join . "
        LEFT JOIN {$wpdb->term_relationships} AS tr_edition ON {$wpdb->posts}.ID = tr_edition.object_id
        LEFT JOIN {$wpdb->term_taxonomy} AS tt_edition ON tr_edition.term_taxonomy_id = tt_edition.term_taxonomy_id AND tt_edition.taxonomy = 'edition'
        LEFT JOIN {$wpdb->terms} AS t_edition ON tt_edition.term_id = t_edition.term_id
    ";
}

function orderby_edition_name( $query ) {
    $order = strtoupper( $query->get( 'order' ) ) === 'DESC' ? 'DESC' : 'ASC';
    return "t_edition.name $order";
}

function groupby_post_id_for_edition( $groupby ) {
    global $wpdb;
    return "{$wpdb->posts}.ID";
}

/**
 * Taxonomies
 */

// manage_edit-{$taxonomy}_columns
function wa_ccpef_taxonomy_columns( $columns ) {
	$columns[WA_CCPEF_MIGRATE_TAXONOMY_FIELD ? WA_CCPEF_MIGRATE_TAXONOMY_FIELD : 'wpcf-select-edition'] = __( 'Selected edition', 'wa_ccpef');
	return $columns;
}

// manage_{$taxonomy}_custom_column
function wa_ccpef_taxonomy_custom_column($out, $column_name, $term_id) {
    switch ($column_name) {
		case WA_CCPEF_MIGRATE_TAXONOMY_FIELD ? WA_CCPEF_MIGRATE_TAXONOMY_FIELD : 'wpcf_field_select-edition' :
			$out = '';
			get_edition_termmeta(WA_CCPEF_MIGRATE_TAXONOMY_FIELD ? WA_CCPEF_MIGRATE_TAXONOMY_FIELD : 'wpcf-select-edition', $term_id);			
			break;
        default:
            break;
    }

    return $out;    
}

/**
 * Edition ( taxonomy )
 */

add_filter( 'manage_edit-edition_columns', 'wa_ccpef_taxonomy_edition_columns' ) ;
function wa_ccpef_taxonomy_edition_columns( $columns ) {
	//$columns['p_general_image'] = __( 'Image', 'wa-rsfp');

	$newcols = array();
	foreach($columns as $col_key => $col_title) {
		print( $col_key );
		if ($col_key=='name') // Put the Thumbnail column before the Title column
			$newcols['e-image'] = __('<span class="dashicons-before dashicons-visibility" style="color:silver;"></span>', 'wa-rsfp');
			if ($col_key=='description') // Put the Thumbnail column before the Desc. column
			$newcols['e-color'] = __('Color', 'wa-rsfp');
		$newcols[$col_key] = $col_title;
	}
	return $newcols;
	//return $columns;
}

add_filter( 'manage_edition_custom_column', 'wa_ccpef_taxonomy_manage_edition_columns', 10, 3);
function wa_ccpef_taxonomy_manage_edition_columns($out, $column_name, $term_id) {
	$out = '';
	$prefix = 'waccpef-';
    switch ($column_name) {
		case 'e-image' :
			wa_ccpef_get_image_fromid(get_term_meta( $term_id, $prefix . $column_name, true));			
			break;
		case 'e-color' :
			wa_ccpef_get_color(get_term_meta( $term_id, $prefix . $column_name, true));			
			break;
			default:
            break;
    }
    return $out;    
}


/**
 * Functions 
 */

 //Get post terms
function get_edition_post_terms($post_id, $taxonomy) {
	$terms = get_the_terms( $post_id, $taxonomy );
	if ( !empty( $terms ) ) {
		foreach ( $terms as $term ) {
			$term_link = get_term_link( $term );
			echo '<span class="editions-tag"><span class="dashicons dashicons-image-filter" aria-hidden="true"></span> <a href="' . esc_url( $term_link ) . '">' . esc_html( $term->name ) . '</a></span>';
		}
	} else {
		echo __( '<span style="color:silver;">—</span>' );
	}
}

// Get term meta
function get_edition_termmeta($taxonomy, $term_id) {
	$meta = get_term_meta($term_id, $taxonomy, true );
	// If meta is a single value, get the term
	if ( !is_array( $meta ) && !empty( $meta ) ) {
		$meta = array( $meta );
	}
	
	if ( empty( $meta ) || is_wp_error( $meta ) ) //|| $term->name == '' 
		echo __( '<span style="color:silver;">—</span>' );
	else {
		foreach ( $meta as $term_id ) {
			$term = get_term( $term_id, 'edition');
			$term_link = get_term_link( $term);
			printf('<span class="editions-tag"><span class="dashicons dashicons-image-filter" aria-hidden="true"></span> <a href="%s">%s</a></span>',
				esc_url( $term_link ),
				esc_html( $term->name ),
			);
		}
	}
}

// Get Image 
function wa_ccpef_get_image_fromid($attachment_id) {
	$image = wp_get_attachment_image_src( $attachment_id, 'thumbnail');
	if ( empty( $image ) )
		echo __( '<span class="empty">—</span>' ); // marker.png
	else
		printf( __( '<div class="tax-img-holder"><img class="wpcf-offre-visuel" src="%s" alt="Image" width="50" /></div>' ), $image[0] );
}

// Get color 
function wa_ccpef_get_color($fd) {
	if ( empty( $fd ) )
		echo __( '<span style="color:silver;">—</span>' ); // marker.png
	else
		printf( __( '<div style="background-color:%s;border:1px solid #bababa;width:20px;height:20px;"></span>' ), $fd );
}
