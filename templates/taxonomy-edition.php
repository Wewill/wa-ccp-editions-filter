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
                <li>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else : ?>
        <p><?php esc_html_e('No posts found.', 'wa-ccpef'); ?></p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>