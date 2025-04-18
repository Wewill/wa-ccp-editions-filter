<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div class="taxonomy-edition-archive">
    <h1><?php single_term_title(); ?></h1>
    <div class="taxonomy-description">
        <?php echo term_description(); ?>
    </div>

    <?php if (have_posts()) : ?>
        <ul class="edition-posts">
            <?php while (have_posts()) : the_post(); ?>
                <?php plugin_get_template_part('templates/partials/cards'); ?>
            <?php endwhile; ?>
        </ul>
    <?php else : ?>
        <p><?php esc_html_e('No posts found.', 'wa-ccpef'); ?></p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>