<div class="mb-3">
    <div class="card mb-3">
        <?php if (has_post_thumbnail()) : ?>
            <img src="<?php the_post_thumbnail_url('medium'); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>">
        <?php endif; ?>
        <!-- <div class="card-header"></div> -->
        <div class="card-body"></div>
        <!-- <div class="card-footer"></div> -->
        <a href="<?php the_permalink(); ?>" class="stretched-link"></a>
    </div>
    <span class="tags fs-xs"><?php echo get_post_type(); ?></span>
    <h6 class="mt-2 card-title"><?php the_title(); ?></h6>
</div>
