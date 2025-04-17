<?php
/*
Register Edition taxonomy
*/

// echo WA_CCPEF_MIGRATE ? '>>>>>>>>>>>>>>>>>>> Migrate : from ccp-editions-filter to wa-ccp-editions-filter' : '>>>>>>>>>>>>>>>>>>> New : Register edition taxonomy';
if ( WA_CCPEF_MIGRATE === true ) {
    // If the taxonomy is already registered, we don't need to register it again.
    return;
}

add_action( 'init', 'wa_ccpef_register_taxonomy' );
function wa_ccpef_register_taxonomy() {
	$labels = [
		'name'                       => esc_html__( 'Editions', 'wa_ccpef' ),
		'singular_name'              => esc_html__( 'Edition', 'wa_ccpef' ),
		'menu_name'                  => esc_html__( 'Editions', 'wa_ccpef' ),
		'search_items'               => esc_html__( 'Search Editions', 'wa_ccpef' ),
		'popular_items'              => esc_html__( 'Popular Editions', 'wa_ccpef' ),
		'all_items'                  => esc_html__( 'All Editions', 'wa_ccpef' ),
		'parent_item'                => esc_html__( 'Parent Edition', 'wa_ccpef' ),
		'parent_item_colon'          => esc_html__( 'Parent Edition:', 'wa_ccpef' ),
		'edit_item'                  => esc_html__( 'Edit Edition', 'wa_ccpef' ),
		'view_item'                  => esc_html__( 'View Edition', 'wa_ccpef' ),
		'update_item'                => esc_html__( 'Update Edition', 'wa_ccpef' ),
		'add_new_item'               => esc_html__( 'Add New Edition', 'wa_ccpef' ),
		'new_item_name'              => esc_html__( 'New Edition Name', 'wa_ccpef' ),
		'separate_items_with_commas' => esc_html__( 'Separate editions with commas', 'wa_ccpef' ),
		'add_or_remove_items'        => esc_html__( 'Add or remove editions', 'wa_ccpef' ),
		'choose_from_most_used'      => esc_html__( 'Choose most used editions', 'wa_ccpef' ),
		'not_found'                  => esc_html__( 'No editions found.', 'wa_ccpef' ),
		'no_terms'                   => esc_html__( 'No editions', 'wa_ccpef' ),
		'filter_by_item'             => esc_html__( 'Filter by edition', 'wa_ccpef' ),
		'items_list_navigation'      => esc_html__( 'Editions list pagination', 'wa_ccpef' ),
		'items_list'                 => esc_html__( 'Editions list', 'wa_ccpef' ),
		'most_used'                  => esc_html__( 'Most Used', 'wa_ccpef' ),
		'back_to_items'              => esc_html__( '&larr; Go to Editions', 'wa_ccpef' ),
		'text_domain'                => esc_html__( 'wa_ccpef', 'wa_ccpef' ),
	];
	$args = [
		'label'              => esc_html__( 'Editions', 'wa_ccpef' ),
		'labels'             => $labels,
		'description'        => esc_html__( 'Categorizes post.s & taxonomy.s in a edition (year)', 'wa_ccpef' ),
		'public'             => true,
		'publicly_queryable' => true,
		'hierarchical'       => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_nav_menus'  => true,
		'show_in_rest'       => true,
		'show_tagcloud'      => true,
		'show_in_quick_edit' => true,
		'show_admin_column'  => false,
		'query_var'          => true,
		'sort'               => false,
		// 'capabilities'       => [object Object],
		'capability_type'     => 'post',
		'meta_box_cb'        => 'post_tags_meta_box',
		'rest_base'          => '',
		'rewrite'            => [
			'with_front'   => false,
			'hierarchical' => false,
		],
	];
	register_taxonomy( 'edition', ['post'], $args );
}


add_filter( 'rwmb_meta_boxes', 'wa_ccpef_register_taxonomy_fields', 10);
function wa_ccpef_register_taxonomy_fields( $meta_boxes ) {
    $prefix = 'waccpef-';

    $meta_boxes[] = [
        'title'      => __( 'Edition › General', 'wa-rsfp' ),
        'id'         => 'edition-general',
        'taxonomies' => ['edition'],
        'class'      => 'wa_ccpef',
        'fields'     => [
            [
                'name'              => __( 'Year', 'wa_ccpef' ),
                'id'                => $prefix . 'e-year',
                'type'              => 'number',
                'required'          => true,
                'disabled'          => false,
                'readonly'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
            ],
            [
                'name'              => __( 'Current edition ?', 'wa_ccpef' ),
                'id'                => $prefix . 'e-current-edition',
                'type'              => 'checkbox',
                'std'               => false,
                'required'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
            ],
            [
                'name'              => __( 'Start date', 'wa_ccpef' ),
                'id'                => $prefix . 'e-start-date',
                'type'              => 'date',
                'timestamp'         => false,
                'inline'            => false,
                'required'          => true,
                'disabled'          => false,
                'readonly'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
            ],
            [
                'name'              => __( 'End date', 'wa_ccpef' ),
                'id'                => $prefix . 'e-end-date',
                'type'              => 'date',
                'timestamp'         => false,
                'inline'            => false,
                'required'          => true,
                'disabled'          => false,
                'readonly'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
            ],
        ],
    ];

    return $meta_boxes;
}