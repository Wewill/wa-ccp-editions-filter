<?php
    global $query;
    if ($query->have_posts()) {
        echo '<h3>' . esc_html($args['pt_obj']->labels->name) . '</h3>';
        echo '<ul class="edition-posts">';
        while ($query->have_posts()) : $query->the_post();
            plugin_get_template_part('templates/partials/cards', $template_name);
        endwhile;
        echo '</ul>';
        wp_reset_postdata();
    }
?>