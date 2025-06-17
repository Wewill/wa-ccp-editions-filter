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
$used_taxonomies = wa_ccpef_get_taxonomies_from_setting_page(); // Assume this returns array of taxonomy slugs

// Get metas
$edition_content_before = get_term_meta($term_id, $prefix . 'e-content-before', true);
$edition_content_after = get_term_meta($term_id, $prefix . 'e-content-after', true);

// Get taxonomy ( assumed to be 'edition' / $taxonomy is already set by WordPress in loop )
$taxonomy = get_queried_object()->taxonomy;

// If taxonomy is not edition, return
if ($taxonomy !== 'edition') {
    wp_redirect(home_url());
    exit;
}

get_header(); ?>

<div class="taxonomy-edition-header">
    <?php plugin_get_template_part('templates/partials/header', $template_name); ?>
</div>

<?php if (!empty($edition_content_before)) : ?>
    <div class="container taxonomy-edition-content mt-8 page-content">
        <?= $edition_content_before; ?>
    </div>
    <hr/>
<?php endif; ?>

<div class="container taxonomy-edition-archive mt-8" id="creations">

    <?php if (have_posts()) : ?>
            <?php
            if (!empty($order_by_post_types) && $order_by_post_types === 1) :
                /* Display post_types hierachy */

                // Get all post types that use this taxonomy
                $all_post_types = get_post_types(['public' => true], 'objects');
                $used_post_types = [];

                // Get all post types that use the 'edition' taxonomy
                foreach ($all_post_types as $post_type => $pt_obj) {
                    if (in_array($taxonomy, get_object_taxonomies($post_type))) {
                        $used_post_types[$post_type] = $pt_obj;
                    }
                }

                // echo "used_post_types : <pre>";
                // print_r($used_post_types);
                // echo "</pre>";

                // Loop posttypes with posts that use edition taxonomy
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
                    plugin_get_template_part('templates/partials/loop', $template_name, array('pt_obj' => $pt_obj)); // @TODO rename this template to post instead of loop
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

<?php if (!empty($used_taxonomies) && is_array($used_taxonomies)) : ?>
    <div class="container taxonomy-edition-terms mt-8 page-content" id="artistes">
        <?php
            // Then loop terms which have edition selected 
            // Example, list all terms from user  settings who choose wich tax to add edition filter and display, then filter if term have terms which have edition selected in a custom field : waccpef-select-edition to double check, then display them in a termloop template 
                foreach ($used_taxonomies as $tax_slug) {
                    // Get tax object to get title 
                    $tax_obj = get_taxonomy($tax_slug);
                    if (!$tax_obj) {
                        continue; // Skip if taxonomy does not exist
                    }
                    $tax_name = $tax_obj->labels->singular_name; // Get the singular name of the taxonomy
                    // Get terms from this taxonomy that have selected this edition in their custom field
                    $terms_with_edition = get_terms([
                        'taxonomy'   => $tax_slug,
                        'hide_empty' => false,
                        'meta_query' => [
                            [
                                'key'     => $prefix . 'select-edition',
                                'value'   => $term_id,
                                'compare' => 'LIKE',
                            ],
                        ],
                    ]);
                    // @TODO V2 Ici LOOP ! avec tax_slug et rechercher le term object depuis le slug 

                    if (!empty($terms_with_edition) && !is_wp_error($terms_with_edition)) {
                        // Output the term name as a heading
                        echo '<h3 class="taxonomy-term-title" style="color: #1976d2;">' . esc_html($tax_name) . '</h3>';

                        echo '<div class="taxonomy-term-list" style="display: flex; flex-wrap: wrap; gap: 1rem;">';
                        foreach ($terms_with_edition as $t_obj) {
                            // Output term using a template part
                            plugin_get_template_part('templates/partials/terms', $template_name, array('t_obj' => $t_obj));
                        }
                        echo '</div>'; // Close taxonomy-term-list
                    }
                }
        ?>
    </div>
<?php endif; ?>         


<?php if (!empty($edition_content_after)) : ?>
    <hr/>
    <div class="container taxonomy-edition-content mt-8 page-content" id="informations">
        <?= $edition_content_after; ?>
    </div>
<?php endif; ?>

<div class="blank_spacer col-sm-12" style="height:80px;"></div>
<div class="clearfix"></div>

<?php get_footer(); ?>