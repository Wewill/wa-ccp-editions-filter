<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$prefix = 'waccpef-';
$term_id = get_queried_object_id();

// Get template name 
$template_name = '';
if (!empty(wa_ccpef_get_template_name_from_setting_page())) {
    $template_name = wa_ccpef_get_template_name_from_setting_page();
}

// Get settings
$order_by_post_types = wa_ccpef_get_orderbyposttypes_from_setting_page(); 

// Get metas
$edition_content_before = get_term_meta($term_id, $prefix . 'e-content-before', true);
$edition_content_after = get_term_meta($term_id, $prefix . 'e-content-after', true);


get_header(); ?>

<div class="taxonomy-edition-header">
    <?php plugin_get_template_part('templates/partials/header', $template_name); ?>
</div>

<?php if (!empty($edition_content_before)) : ?>
    <div class="taxonomy-edition-content mt-8">
        <?= $edition_content_before; ?>
    </div>
<?php endif; ?>

<div class="taxonomy-edition-archive mt-8">

    <?php if (have_posts()) : ?>
            <?php
            if (!empty($order_by_post_types) && $order_by_post_types === 1) :
                /* Display post_types hierachy */

                // Get all post types that use this taxonomy
                $all_post_types = get_post_types(['public' => true], 'objects');
                $used_post_types = [];

                foreach ($all_post_types as $post_type => $pt_obj) {
                    if (in_array($taxonomy, get_object_taxonomies($post_type))) {
                        $used_post_types[$post_type] = $pt_obj;
                    }
                }

                // Loop posttypes with posts 
                foreach ($used_post_types as $post_type => $pt_obj) {
                    $query = new WP_Query([
                        'post_type'      => $post_type,
                        'posts_per_page' => -1,
                        'orderby'        => 'title',
                        'order'          => 'ASC',
                        'tax_query'      => [
                            [
                                'taxonomy' => 'edition',
                                'field'    => 'term_id',
                                'terms'    => $term_id,
                            ],
                        ],
                        'post_status'    => 'publish',
                    ]);

                    // Post template is calling cards template
                    plugin_get_template_part('templates/partials/loop', $template_name, array('pt_obj' => $pt_obj));
                }
                

            else : 
                /* Do not display post_types hierachy */
            ?>
                <ul class="edition-posts">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php plugin_get_template_part('templates/partials/cards', $template_name); ?>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
    <?php else : ?>
        <p><?php esc_html_e('No posts found.', 'wa-ccpef'); ?></p>
    <?php endif; ?>

</div>

<?php if (!empty($edition_content_after)) : ?>
    <div class="taxonomy-edition-content mt-8">
        <?= $edition_content_after; ?>
    </div>
<?php endif; ?>

<?php get_footer(); ?>