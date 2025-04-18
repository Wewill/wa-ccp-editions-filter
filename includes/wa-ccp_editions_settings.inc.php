<?php
/*
Define admin settings
*/

add_filter( 'mb_settings_pages', 'wa_ccpef_settings' );
function wa_ccpef_settings( $settings_pages ) {
	$settings_pages[] = [
        'menu_title'      => __( 'Archives & edition filter', 'wa_ccpef' ),
        'id'              => 'archives-edition-filter',
        'position'        => 50,
        'parent'          => 'options-general.php',
        'class'           => 'wa_ccpef',
        'tabs'            => [
            'edition'  => 'Edition',
            'archives' => 'Archives',
        ],
        'tab_style'       => 'left',
        // 'help_tabs'       => [
        //     [
        //         'title'   => 'Help me !',
        //         'content' => 'Lorem ipsum...',
        //     ],
        // ],
        'customizer'      => false,
        'customizer_only' => false,
        'network'         => false,
        'icon_url'        => 'dashicons-filter',
    ];

	return $settings_pages;
}

// add_filter( 'rwmb_edition-settings_after_save_post', 'wa_ccpef_settings_save' );
// function wa_ccpef_settings_save( $setting_id ) {
//     do_action( 'update_option_wa_ccpef_setting_page' );
// }

add_filter( 'rwmb_meta_boxes', 'wa_ccpef_settings_fields' );
function wa_ccpef_settings_fields( $meta_boxes ) {
    $prefix = 'wa_ccpef_';

    $meta_boxes[] = [
        'title'          => __( 'Edition settings', 'wa_ccpef' ),
        'id'             => 'edition-settings',
        'settings_pages' => ['archives-edition-filter'],
        'tab'            => 'edition',
        'fields'         => [
            [
                'name'              => __( 'Choose edition filter name', 'wa_ccpef' ),
                'id'                => $prefix . 'choose_edition_filter_name',
                'type'              => 'text',
                'std'               => __( 'Édition', 'wa_ccpef' ),
                'required'          => true,
                'disabled'          => false,
                'readonly'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
                'limit_type'        => 'character',
            ],
            [
                'name'            => __( 'Allowed post type.s', 'wa_ccpef' ),
                'id'              => $prefix . 'allowed_post',
                'type'            => 'checkbox_list',
                'inline'          => true,
                'select_all_none' => true,
                'options'         => wa_ccpef_posts_options_callback(),
            ],
            [
                'name'            => __( 'Allowed taxonomy.s', 'wa_ccpef' ),
                'id'              => $prefix . 'allowed_taxonomy',
                'type'            => 'checkbox_list',
                'inline'          => true,
                'select_all_none' => true,
                'options'         => wa_ccpef_taxonomies_options_callback(),
            ],
        ],
    ];

    $meta_boxes[] = [
        'title'          => __( 'Archives settings', 'wa_ccpef' ),
        'id'             => 'archives-settings',
        'settings_pages' => ['archives-edition-filter'],
        'tab'            => 'archives',
        'fields'         => [
            [
                'name'              => __( 'Choose archives name', 'wa_ccpef' ),
                'id'                => $prefix . 'choose_archives_name',
                'type'              => 'text',
                'std'               => __( 'Archives', 'wa_ccpef' ),
                'required'          => true,
                'disabled'          => false,
                'readonly'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
                'limit_type'        => 'character',
            ],
        ],
    ];

    return $meta_boxes;
}

function wa_ccpef_posts_options_callback() {
    $post_types = get_post_types();
    $options = [];
    foreach ( $post_types as $post_type ) {
        // Exclude default post types
        if ( in_array( $post_type, [ 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block', 'wp_template', 'wp_template_part', 'wp_global_styles', 'wp_navigation', 'wp_font_family', 'wp_font_face', 'mb-post-type', 'mb-taxonomy', 'mb-settings-page', 'coblocks_pattern', 'meta-box'] ) ) {
            continue;
        }
        // Exclude custom post types
        if ( in_array( $post_type, [ '' ] ) ) {
            continue;
        }
        $options[ $post_type ] = __( $post_type, 'wa_ccpef' );
    }
    return $options;
}

function wa_ccpef_taxonomies_options_callback() {
    $taxonomies = get_taxonomies();
    $options = [];
    foreach ( $taxonomies as $taxonomy ) {
        // Exclude default taxonomies
        if ( in_array( $taxonomy, [ 'link_category', 'post_format', 'wp_theme', 'wp_template_part_area', 'wp_pattern_category', 'coblocks_pattern_type', 'coblocks_pattern_category' ] ) ) {
            continue;
        }
        // Exclude custom taxonomies
        if ( in_array( $taxonomy, [ 'edition' ] ) ) {
            continue;
        }
        $options[ $taxonomy ] = __( $taxonomy, 'wa_ccpef' );
    }
    return $options;
}

function wa_ccpef_get_posts_from_setting_page() {
    $prefix = 'wa_ccpef_';
    return rwmb_meta( $prefix . 'allowed_post', [ 'object_type' => 'setting' ], 'archives-edition-filter' );
}

function wa_ccpef_get_taxonomies_from_setting_page() {
    $prefix = 'wa_ccpef_';
    return rwmb_meta( $prefix . 'allowed_taxonomy', [ 'object_type' => 'setting' ], 'archives-edition-filter' );
}
