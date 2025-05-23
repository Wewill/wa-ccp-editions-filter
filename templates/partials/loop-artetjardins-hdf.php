<?php
    global $query;
    //print_r($args['pt_obj']);

    if ( $args['pt_obj']->name === 'directory') {
        if ($query->have_posts()) {
            $post_ids = [];
            while ($query->have_posts()) : $query->the_post();
                $post_ids[] = get_the_ID();
            endwhile;
            wp_reset_postdata();
            // echo '<pre>' . esc_html(print_r($post_ids, true)) . '</pre>';

            // Display Directory AVP 
            $ids_string = implode(',', $post_ids);
            print(do_shortcode('[spb_directory_no_reload 
                el_project="ALL" 
                el_category="" 
                el_location="" 
                el_artist="" 
                el_course="" 
                el_ids="' . $ids_string . '" 
                width="1/1" el_position="first last"]'
            ));
        }

    } else {
        if ($query->have_posts()) {
            echo '<h3>' . esc_html($args['pt_obj']->label) . '</h3>';
            echo '<ul class="edition-posts">';
            while ($query->have_posts()) : $query->the_post();
                plugin_get_template_part('templates/partials/cards', $template_name);
            endwhile;
            echo '</ul>';
            wp_reset_postdata();
        }
    }
?>