<?php
    // Get settings
    $prefix = 'waccpef-';
    $term_id = get_queried_object_id();
    $edition_color = get_term_meta($term_id, $prefix . 'e-color', true);
    $edition_color_style = $edition_color ? 'style="background-color:'.$edition_color.'!important;"' : '';
    
    // Image cover 
    $edition_image = get_term_meta($term_id, $prefix . 'e-image', true);
    if (!empty($edition_image)) {
        $edition_image_url = wp_get_attachment_url($edition_image);

    // Images slideshow 
    $edition_images = get_term_meta($term_id, $prefix . 'e-images', false);

}

?>
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

<?php if (!empty($edition_images)) : ?>
<section id="pageheader" class="mt-0 mb-0 contrast--light --f-w rounded-bottom-4" data-aos="slide-down" data-aos-id="pageheader">
    <?php print_r($edition_images); ?>
</section>
<?php endif; ?>
