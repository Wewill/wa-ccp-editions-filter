<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$prefix = 'waccpef-';

$term_id = get_queried_object_id();

// Get settings
$order_by_post_types = wa_ccpef_get_orderbyposttypes_from_setting_page(); 

// Get metas
$edition_color = get_term_meta($term_id, $prefix . 'e-color', true);
$edition_color_style = $edition_color ? 'style="background-color:'.$edition_color.'!important;"' : '';
$edition_image = get_term_meta($term_id, $prefix . 'e-image', true);
if (!empty($edition_image)) {
    $edition_image_url = wp_get_attachment_url($edition_image);
}

get_header(); ?>

<section id="pagetitle" class="mt-10 pt-5 pt-md-9 pb-5 pb-md-9 contrast--light --f-w shadow-md rounded-top-4 " <?= $edition_color_style ?>>
    <div class="jumbotron">
        <div class="container-fluid">
            <hgroup data-aos="fade-down">
                <h1 class="title mb-0 fw-bold"><?php single_term_title(); ?></h1>
                <small><?= term_description(); ?></small>
            </hgroup>
        </div>
    </div>
</section>

<?php if (!empty($edition_image)) : ?>
<section id="pageheader" class="mt-0 mb-0 contrast--light --f-w rounded-bottom-4" data-aos="slide-down" data-aos-id="pageheader">
    <figure title="">
        <picture class="lazy">
        <img src="<?= esc_url($edition_image_url); ?>" alt="<?php esc_attr_e('Edition Image', 'wa-ccpef'); ?>" style="object-fit: cover; max-height: 25vh; width: 100%; height: auto;">
        </picture>
    </figure>
</section>
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

                    if ($query->have_posts()) {
                        echo '<h3>' . esc_html($pt_obj->labels->name) . '</h3>';
                        echo '<ul class="edition-posts">';
                        while ($query->have_posts()) : $query->the_post();
                            plugin_get_template_part('templates/partials/cards');
                        endwhile;
                        echo '</ul>';
                        wp_reset_postdata();
                    }
                }
                

            else : 
                /* Do not display post_types hierachy */
            ?>
                <ul class="edition-posts">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php plugin_get_template_part('templates/partials/cards'); ?>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
    <?php else : ?>
        <p><?php esc_html_e('No posts found.', 'wa-ccpef'); ?></p>
    <?php endif; ?>

</div>

<?php get_footer(); ?>