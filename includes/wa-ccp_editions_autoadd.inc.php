<?php
/**
 * Add edition tag functions 
 */

// Method 
// $tags = array('html', 'css', 'javascript');
// wp_set_post_tags( $post_id, 'tag', true );
// wp_set_object_terms( $post_id, $tags, 'post_tag', true ); 

add_action('save_post', 'waaet_save_post', 20, 3);
function waaet_save_post( $post_id, $post, $update ){
	global $current_edition, $previous_editions, $current_edition_id, $current_edition_films_are_online, $current_edition_parent_term_id;

	// Retreive posts from settings page 
    // wp_die(print_r(wa_ccpef_get_posts_from_setting_page(), true));
    // wp_die(print_r($current_edition_id, true));

	$slug = wa_ccpef_get_posts_from_setting_page();
    $term = 'edition';

    // If this isn't a slug post, don't update it.
	// if ( $slug != $post->post_type ) {
    if ( !in_array( $post->post_type, $slug ) ) {
        return;
    }

	// If this is just a revision, don't to that
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	// Get edition terms 	
	$terms = get_terms( 'edition', array(
	    'hide_empty' => false,
	));
		
	// Get object if not empty
	$waaet_get_object = wp_get_object_terms( $post_id, 'edition', true );

	if ( empty( $waaet_get_object ) ) {
		// Add these categories, note the last argument is true.
		$waaet_set_object = wp_set_object_terms( $post_id, $current_edition_id, 'edition', true );
	
		if ( is_wp_error( $waaet_set_object ) ) {
			// There was an error somewhere and the terms couldn't be set.
			wp_die('ERROR @ WA ADD EDITION TAG : There was an error somewhere and the terms couldn\'t be set');
		} else {
			// Success! These categories were added to the post.
		}

	}

}
