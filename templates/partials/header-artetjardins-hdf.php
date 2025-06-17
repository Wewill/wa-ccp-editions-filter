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

    // Dates
    $edition_start_date = get_term_meta($term_id, $prefix . 'e-start-date', true);
    $edition_end_date = get_term_meta($term_id, $prefix . 'e-end-date', true);
    // Format dates as "23 MAI - 12 OCT 2025"
    $edition_date_string = '';
    if ($edition_start_date && $edition_end_date) {
        $start = DateTime::createFromFormat('Y-m-d', $edition_start_date);
        $end = DateTime::createFromFormat('Y-m-d', $edition_end_date);
        if ($start && $end) {
            $months = [
                1 => 'JAN', 2 => 'FEV', 3 => 'MAR', 4 => 'AVR', 5 => 'MAI', 6 => 'JUN',
                7 => 'JUI', 8 => 'AOU', 9 => 'SEP', 10 => 'OCT', 11 => 'NOV', 12 => 'DEC'
            ];
            $start_str = $start->format('j') . ' ' . $months[(int)$start->format('n')];
            $end_str = $end->format('j') . ' ' . $months[(int)$end->format('n')] . ' ' . $end->format('Y');
            $edition_date_string = $start_str . ' - ' . $end_str;
        }
    }


// function wa_slider( $atts, $content = null ) {
    $parallax = 'false';
    $type = 'slider';
    $fullscreen = 'false';
    $max_height = '500';
    $autoplay = 'yes';
    $category = '';
    $transition = 'slide';
    $loop = 'yes';
    $nav = '0';
    $pagination = 'yes';
    $continue = 'true';
    $specific_post_id = '';

    
    // Instead of WP_Query, assign $edition_images as an array of image IDs to $wa_slider_slides
    $wa_slider_slides = is_array($edition_images) ? $edition_images : array();

    // Set the number of slides to display
    $slide_count        = count($wa_slider_slides);

    /* SLIDER VARIABLES
    ================================================== */
    $wa_slider_output = "";
    if ( ! $wa_slider_count ) {
        $wa_slider_count = 99;
    } else {
        $wa_slider_count ++;
    }
    $sliderID = 'wa-slider-' . $wa_slider_count;
    $slide_ID = 0;
    if ( $fullscreen == "yes" || $fullscreen == "1" ) {
        $fullscreen = "true";
    }
    if ( $loop == "yes" || $loop == "1" ) {
        $loop = "true";
    }
    if ( $slide_count <= 1 || $slides_count <= 1 ) {
        $loop = "false";
    }
    ?>

    <?php if ( !empty($wa_slider_slides) && is_array($wa_slider_slides) && count($wa_slider_slides) > 0 ) {

        $wa_slider_output .= '<div id="' . $sliderID . '" class="wa-slider swiper-container" data-slider-type="' . $type . '" data-fullscreen="' . $fullscreen . '" data-max-height="' . $max_height . '" data-transition="' . $transition . '" data-loop="' . $loop . '" data-slide-count="' . $slide_count . '" data-autoplay="' . $autoplay . '" data-continue="' . $continue . '">';
        $wa_slider_output .= '<div class="swiper-wrapper">';

        foreach ( $wa_slider_slides as $post_id ):

            // Increase Slide ID
            $slide_ID ++;


            // Setup slide 
            $image_url = wp_get_attachment_image_url($post_id, 'full');
            $image_url_mobile = wp_get_attachment_image_url($post_id, 'medium_large');
            $caption = wp_get_attachment_caption($post_id);
            $slide_title = get_the_title($post_id);
            $slide_link = get_post_meta($post_id, 'wa_slider_link', true);
            $slide_id_attr = 'slide-' . ($slide_ID);

            $wa_slider_output .= '<div class="swiper-slide image-slide dynamic-header-change" id="' . esc_attr($slide_id_attr) . '" data-slide-id="' . esc_attr($slide_ID) . '" data-slide-title="' . esc_attr($slide_title) . '" style="background-image: url(&quot;' . esc_url($image_url) . '&quot;);" data-bg-size="cover" data-bg-align="center" data-bg-horiz-align="center" data-mobile-bg-horiz-align="center" data-slide-img="' . esc_url($image_url) . '" data-style="light" data-header-style="light" title="">';
            $wa_slider_output .= '<style scoped>
                #' . esc_attr($slide_id_attr) . ' { background-image: url(' . esc_url($image_url) . '); }
                @media only screen and (max-width: 767px) {
                    #' . esc_attr($slide_id_attr) . ' { background-image: url(' . esc_url($image_url_mobile) . ') !important; }
                }
            </style>';
            if ($caption) {
                $wa_slider_output .= '<span class="caption">' . esc_html($caption) . '</span>';
            }
            if ($slide_link) {
                $wa_slider_output .= '<a href="' . esc_url($slide_link) . '" target="_blank" class="ss-slide-link"></a>';
            }
            $wa_slider_output .= '</div>';


        endforeach;

        $wa_slider_output .= '</div><!-- .swiper-wrapper -->';

        if ( $type == "slider" && ($slide_count > 1 || $slide_count == -1) && ( $nav == "1" || $nav == "yes" || $nav == "true" ) ) {
            $ss_prev_icon = apply_filters('wa_slider_prev_icon', '<i class="ss-navigateleft"></i>');
            $ss_next_icon = apply_filters('wa_slider_next_icon', '<i class="ss-navigateright"></i>');
            $wa_slider_output .= '<a class="wa-slider-prev" href="#">'.$ss_prev_icon.'<h4>' . __( 'Previous', 'swift-framework-plugin' ) . '</h4></a>';
            $wa_slider_output .= '<a class="wa-slider-next" href="#">'.$ss_next_icon.'<h4>' . __( 'Next', 'swift-framework-plugin' ) . '</h4></a>';
        }

        if ( $slide_count > 1 && ( $pagination == "1" || $pagination == "yes" || $pagination == "true" ) ) {
            $wa_slider_output .= '<div class="wa-slider-pagination">';
        } else {
            $wa_slider_output .= '<div class="wa-slider-pagination pagination-hidden">';
        }
        for ( $i = 0; $i < $slide_count; $i ++ ) {
            $wa_slider_output .= '<div class="dot"><span class=""></span></div>';
        }
        $wa_slider_output .= '</div>';

        if ( $continue == "true" || $continue == 1 ) {
            $wa_slider_output .= '<div class="swift-scroll-indicator">';
            $wa_slider_output .= '<span></span><span></span><span></span>';
            $wa_slider_output .= '</div>';
            $ss_continue_icon = apply_filters('wa_slider_continue_icon', '<i class="ss-navigatedown"></i>');
            $wa_slider_output .= '<a href="' . apply_filters( 'wa_slider_continue_href', '#' ) . '" data-href="#" class="wa-slider-continue">'.$ss_continue_icon.'</a>';
        }

        // LOADER
        if ( function_exists('sf_loading_animation') ) {
            $wa_slider_output .= sf_loading_animation( 'wa-slider-loader' );
        }

        // FULLSCREEN
        if ( $fullscreen == "true" ) {
            $wa_slider_output .= "<script>jQuery(document).ready(function() {
                var windowHeight = parseInt(jQuery(window).height(), 10);

                if (jQuery('#wpadminbar').length > 0) {
                    windowHeight = windowHeight - jQuery('#wpadminbar').height();
                }
                jQuery('#" . $sliderID . "').css('height', windowHeight);
            });</script>";
        }

        $wa_slider_output .= '</div><!-- .wa-slider -->';

    } else {

        $wa_slider_output .= '<div id="' . $sliderID . '" class="wa-slider no-slides">';
        $wa_slider_output .= __( "No slides found, please add some!", 'swift-framework-plugin' );
        $wa_slider_output .= '</div>';

    }

//}

if (isset($show_page_title) && !$show_page_title) {
    return;
}

?>

<style type="text/css">
    .page-heading {
        display:none;
    }
    h1.over {
        line-height: 40px; font-weight: 600; font-style: normal; font-size: 2.2em;
        color: #fff; text-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }
    h3.dates {
        font-family: "fabrikat";
        display: inline;
        padding: 5px 8px;
        position: relative;
        top: -3px;
        height: 30px;
        line-height: 15px;
        border-bottom: 2px solid #fff;
        text-transform: uppercase;
        font-weight: 700;
        font-size: 14px;
        letter-spacing: 0;
        color: #fff; text-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }
</style>

<div class="wa-slider-outer">

    <!-- BEGIN .wa-slider -->
    <?= $wa_slider_output; ?>
    <!-- END .wa-slider -->

    <?php if (!empty($edition_image)) : ?>
    <div style="position: absolute; top: 0; right: calc(90px + 20px); width: auto; height: calc(500px - 90px); z-index: 100; display: flex; align-items: center; justify-content: end;">
        <img src="<?= esc_url($edition_image_url); ?>" alt="<?php esc_attr_e('Edition Image', 'wa-ccpef'); ?>" style="object-fit: cover; max-height: calc( 500px - 130px ); width: auto; height: 100%; position:relative; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);">
    </div>
    <?php endif; ?>

    <?php if (!empty($edition_color)) : ?>
        <div style="position: absolute; top: 0; left: 0; width: 90px; height: calc(500px - 90px); background-color: <?= esc_attr($edition_color); ?>; z-index:100;"></div>
    <?php endif; ?>

    <div style="position: absolute; top: 0; left: calc(90px + 20px); width: 40%; height: calc(500px - 90px); z-index: 100; display: flex; align-items: center; justify-content: start;">

            <hgroup class="heading-text" style="">
                <h1 class="over"><?php single_term_title(); ?></h1>
                <?php
                $desc = term_description();
                if (!empty($desc)) {
                    // Remove wrapping <p> tags if present
                    $desc = preg_replace('/^<p>(.*)<\/p>$/s', '$1', $desc);
                    echo '<h1 class="over">' . $desc . '</h1>';
                }
                ?>
                <h3 class="dates"><?= esc_html($edition_date_string); ?></h3>
            </hgroup>

    </div>


    <!-- <div class="slider-page-title">
        <a href="#main-container">Festival international de jardins | Hortillonnages Amiens <i class="icon-right"></i></a>
    </div> -->
    <div class="slider-menu-anchors">
        <div class="spb-section spb_content_element col-sm-12">
            <p></p>
            <section class="row ">
                <div class="spb_content_element col-sm-12 spb_raw_html">
                    <div class="spb-asset-content">
                        <div class="spb_anchors" style="--display:none;">
                            <ul class="nav nav-anchors">
                                <li><a href="#creations" data-toggle="tab"><span>Créations</span></a></li>
                                <li><a href="#artistes" data-toggle="tab"><span>Artistes</span></a></li>
                                <li><a href="#informations" data-toggle="tab"><span>Informations</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
            <p></p>
        </div>
    </div>
    <div class="slider-social" style="display:flex; justify-content: center; align-items: center;">
        <ul class="social-icons standard ">
            <li class="twitter">
                <a href="http://www.twitter.com/art_jardins" target="_blank"><i class="fa-twitter"></i><i class="fa-twitter"></i></a>
            </li>
            <li class="facebook">
                <a href="http://www.facebook.com/artetjardinsHDF" target="_blank"><i class="fa-facebook"></i><i class="fa-facebook"></i></a>
            </li>
            <li class="instagram">
                <a href="http://instagram.com/art_jardins_hdf" target="_blank"><i class="fa-instagram"></i><i class="fa-instagram"></i></a>
            </li>
        </ul>
    </div>
</div>