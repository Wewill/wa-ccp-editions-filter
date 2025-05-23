<?php
    // Get settings
    $order_by_post_types = wa_ccpef_get_orderbyposttypes_from_setting_page(); 
?>
<div class="mb-3">
    <div class="card mb-3">
        <?php if (has_post_thumbnail()) : ?>
            <img src="<?php the_post_thumbnail_url('medium'); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>">
        <?php endif; ?>
        <!-- <div class="card-header"></div> -->
        <div class="card-body">
        </div>
        <!-- <div class="card-footer"></div> -->
        <a href="<?php the_permalink(); ?>" class="stretched-link"></a>
    </div>
    <?php if ( $order_by_post_types === 0) : ?>
        <span class="tags fs-xs"><?= get_post_type(); ?></span>
    <?php endif; ?>
    <?php
    $taxonomies = get_object_taxonomies(get_post_type(), 'names');
    if ($taxonomies) :
        foreach ($taxonomies as $taxonomy) :
            if ($taxonomy === 'edition') continue;
            $terms = get_the_terms( get_the_ID(), $taxonomy );
            if ( $terms && ! is_wp_error( $terms ) ) : 
                $term_links = array();
                foreach ( $terms as $term ) {
                    $term_links[] = '<a href="' . esc_attr( get_term_link( $term->slug, $taxonomy ) ) . '">' . __( $term->name ) . '</a>';
                }
                $all_terms = join( ', ', $term_links );
                echo '<span class="terms-' . esc_attr( $term->slug ) . '">' . __( $all_terms ) . '</span>';
            endif;
        endforeach;
    endif;
    ?>
    <h6 class="mt-2 card-title"><?php the_title(); ?></h6>
</div>
