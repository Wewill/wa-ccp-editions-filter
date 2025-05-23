<?php
/*
Register Edition taxonomy
*/

if ( WA_CCPEF_MIGRATE === true ) {
    // If the taxonomy edition is already registered, we don't need to register it again.
    return;
}

add_action( 'init', 'wa_ccpef_register_taxonomy', 10);
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
    // Register the taxonomy initially
    register_taxonomy( 'edition', wa_ccpef_get_posts_from_setting_page(), $args );

    // // Hook to re-register the taxonomy when the setting changes
    // add_action( 'update_option_wa_ccpef_setting_page', function() use ( $args ) {
    //     unregister_taxonomy( 'edition' );
    //     register_taxonomy( 'edition', wa_ccpef_get_posts_from_setting_page(), $args );
    // } );
}

add_filter( 'rwmb_meta_boxes', 'wa_ccpef_register_taxonomy_fields');
function wa_ccpef_register_taxonomy_fields( $meta_boxes ) {
    $prefix = 'waccpef-';

    $meta_boxes[] = [
        'title'      => __( 'Edition › General', 'wa_ccpef' ),
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
                'label_description' => __( 'Check if this is the current edition', 'wa_ccpef' ),
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
            [
                'name'              => __( 'Color', 'wa_ccpef' ),
                'id'                => $prefix . 'e-color',
                'type'              => 'color',
                'label_description' => __( 'Choose an edition color', 'wa_ccpef' ),
                'alpha_channel'     => false,
                'required'          => false,
                'disabled'          => false,
                'readonly'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
            ],
            [
                'name'              => __( 'Cover image', 'wa_ccpef' ),
                'id'                => $prefix . 'e-image',
                'type'              => 'image_advanced',
                'label_description' => __( 'Choose an edition cover image', 'wa_ccpef' ),
                'max_file_uploads'  => 1,
                'force_delete'      => false,
                'required'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
            ],
            [
                'name'              => __( 'Images', 'wa_ccpef' ),
                'id'                => $prefix . 'e-images',
                'type'              => 'image_advanced',
                'label_description' => __( 'Choose an edition slideshow images', 'wa_ccpef' ),
                'max_file_uploads'  => 3,
                'force_delete'      => false,
                'required'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
            ],
            [
                'name'              => __( 'Content ( before )', 'wa_ccpef' ),
                'id'                => $prefix . 'e-content-before',
                'type'              => 'wysiwyg',
                'options'           => [
                    'media_buttons'  => true,
                    'drag_drop_upload' => true,
                    // 'default_editor' => true,
                    'teeny' => true,
                    'textarea_rows'  => 4,
                ],
				'label_description' => __( '<span class="label">INFO</span> Fill with formatted text', 'wa_ccpef' ),
				'desc' => __( '<span class="label">TIPS</span> This content will be showed after title', 'wa_ccpef' ),
            ],
            [
                'name'              => __( 'Content ( after )', 'wa_ccpef' ),
                'id'                => $prefix . 'e-content-after',
                'type'              => 'wysiwyg',
                'options'           => [
                    'media_buttons'  => true,
                    'drag_drop_upload' => true,
                    // 'default_editor' => true,
                    'teeny' => true,
                    'textarea_rows'  => 4,
                ],
				'label_description' => __( '<span class="label">INFO</span> Fill with formatted text', 'wa_ccpef' ),
				'desc' => __( '<span class="label">TIPS</span> This content will be showed after edition posts', 'wa_ccpef' ),
            ],
        ],
    ];

    return $meta_boxes;
}


add_action( 'init', 'wa_ccpef_taxonomies_field', 20 );
function wa_ccpef_taxonomies_field() {
    // Add a custom field to the term edit form
    if ( function_exists( 'wa_ccpef_get_taxonomies_from_setting_page' ) && !empty(wa_ccpef_get_taxonomies_from_setting_page()) )
    {
        foreach (wa_ccpef_get_taxonomies_from_setting_page() as $taxonomy) {
            add_action( $taxonomy . '_add_form_fields', 'wa_ccpef_add_term_fields' );
            add_action( $taxonomy . '_edit_form_fields', 'wa_ccpef_edit_term_fields', 10, 2 );
            add_action( 'created_' . $taxonomy, 'wa_ccpef_save_term_fields' );
            add_action( 'edited_' . $taxonomy, 'wa_ccpef_save_term_fields' );
        }
    }
}

function wa_ccpef_add_term_fields( $taxonomy ) {
    ?>
        <div class="form-field waccpef-select-edition">
            <label class="waccpef-select-edition_title" for="<?= WA_CCPEF_MIGRATE_TAXONOMY_FIELD?>"><?php esc_html_e( 'Select editions', 'wa_ccpef' ); ?></label>
            <?php
            $terms = get_terms( [
                'taxonomy'   => 'edition',
                'hide_empty' => false,
            ] );

            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                foreach ( $terms as $term ) {
                    ?>
                    <label>
                        <input type="checkbox" name="<?= WA_CCPEF_MIGRATE_TAXONOMY_FIELD?>[]" value="<?= esc_attr( $term->term_id ); ?>">
                        <?= esc_html( $term->name ) . ' ( ' . esc_html( $term->description ) . ' )'; ?>
                    </label>
                    <?php
                }
            }
            ?>
            <p><?php esc_html_e( 'Select one or many editions from the list.', 'wa_ccpef' ); ?></p>
        </div>
    <?php
}

function wa_ccpef_edit_term_fields( $term, $taxonomy ) {
    // Get meta data value
    $selected_editions = get_term_meta( $term->term_id, WA_CCPEF_MIGRATE_TAXONOMY_FIELD, true );
    ?>
    <tr class="form-field waccpef-select-edition">
        <th><label class="waccpef-select-edition_title" for="<?= WA_CCPEF_MIGRATE_TAXONOMY_FIELD?>"><?php esc_html_e( 'Select editions', 'wa_ccpef' ); ?></label></th>
        <td>
            <?php
            $terms = get_terms( [
                'taxonomy'   => 'edition',
                'hide_empty' => false,
            ] );

            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                foreach ( $terms as $term_option ) {
                    $checked = ( is_array( $selected_editions ) && in_array( $term_option->term_id, $selected_editions ) ) ? 'checked' : '';
                    ?>
                    <label>
                        <input type="checkbox" name="<?= WA_CCPEF_MIGRATE_TAXONOMY_FIELD?>[]" value="<?= esc_attr( $term_option->term_id ); ?>" <?= $checked; ?>>
                        <?= esc_html( $term_option->name ) . ' ( ' . esc_html( $term_option->description ) . ' )'; ?>
                    </label><br>
                    <?php
                }
            }
            ?>
            <p class="description"><?php esc_html_e( 'Select one or more editions from the list.', 'wa_ccpef' ); ?></p>
        </td>
    </tr>
    <?php
}

function wa_ccpef_save_term_fields( $term_id ) {
    $selected_editions = isset( $_POST[ WA_CCPEF_MIGRATE_TAXONOMY_FIELD ] ) ? array_map( 'sanitize_text_field', $_POST[ WA_CCPEF_MIGRATE_TAXONOMY_FIELD ] ) : [];
    update_term_meta( $term_id, WA_CCPEF_MIGRATE_TAXONOMY_FIELD, $selected_editions );
}
