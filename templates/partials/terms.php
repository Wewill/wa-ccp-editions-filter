<?php
    // print_r($args['t_obj']);
    $t_obj = $args['t_obj'];
    $term_name = $t_obj->name;
    $term_description = $t_obj->description;
    $term_id = $t_obj->term_id;
?>
<div class="mb-3" id="term-<?php echo esc_attr($term_id); ?>">
    <div class="card mb-3">
        <?php if (has_post_thumbnail()) : ?>
            <img src="<?php the_post_thumbnail_url('medium'); ?>" class="card-img-top" alt="<?= esc_attr($term_name); ?>">
        <?php endif; ?>
        <!-- <div class="card-header"></div> -->
        <div class="card-body"></div>
        <!-- <div class="card-footer"></div> -->
        <a href="<?php get_term_link($t_obj); ?>" class="stretched-link"></a>
    </div>
    <h6 class="mt-2 card-title"><?= esc_attr($term_name); ?></h6>
</div>
