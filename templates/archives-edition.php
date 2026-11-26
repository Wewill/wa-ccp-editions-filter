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


get_header(); ?>

<main class="container page-content">
    <h1><?php esc_html_e( 'Archives', 'wa-ccpef' ); ?></h1>
    <h5><?php esc_html_e( 'Browse previous years', 'wa-ccpef' ); ?></h5>

    <?php
    // Edition : name, description, custom fields :  waccpef-e-year, waccpef-e-current-edition, waccpef-e-start-date, waccpef-e-end-date, waccpef-e-color, waccpef-e-image



    // Order by year (e-year) DESC : most recent first, editions without year last
    $terms = get_terms([
        'taxonomy'   => 'edition',
        'hide_empty' => false,
        'meta_query' => [
            'relation' => 'OR',
            'e_year'   => [
                'key'     => $prefix . 'e-year',
                'type'    => 'NUMERIC',
                'compare' => 'EXISTS',
            ],
            [
                'key'     => $prefix . 'e-year',
                'compare' => 'NOT EXISTS',
            ],
        ],
        'orderby'    => 'e_year',
        'order'      => 'DESC',
    ]);

    if (!empty($terms) && !is_wp_error($terms)) {
        echo '<div class="edition-grid">';
        foreach ($terms as $term) {
            $year = get_term_meta($term->term_id, $prefix . 'e-year', true);
            $start_date = get_term_meta($term->term_id, $prefix . 'e-start-date', true);
            $end_date = get_term_meta($term->term_id, $prefix . 'e-end-date', true);
            $image_id = get_term_meta($term->term_id, $prefix . 'e-image', true);
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
            $name = esc_html($term->name);
            $desc = esc_html($term->description);

            // Format dates
            $start = $start_date ? date_i18n('d M', strtotime($start_date)) : '';
            $end = $end_date ? date_i18n('d M Y', strtotime($end_date)) : '';
            $date_range = ($start && $end) ? "$start - $end" : '';

            // Card link to single edition (taxonomy-edition.php)
            $term_link = get_term_link($term);
            $has_link = !is_wp_error($term_link);

            echo $has_link ? '<a class="edition-card" href="' . esc_url($term_link) . '">' : '<div class="edition-card">';
            if ($image_url) {
                echo '<div class="edition-image"><img src="' . esc_url($image_url) . '" alt="' . $name . '"></div>';
            }
            echo '<div class="edition-info">';
            $color = get_term_meta($term->term_id, $prefix . 'e-color', true);
            $color_style = $color ? ' style="color:' . esc_attr($color) . ';"' : '';
            echo '<h2 class="edition-title"' . $color_style . '>' . $name . '</h2>';
            if ($desc) {
                echo '<div class="edition-desc">' . $desc . ' <span class="edition-year">' . esc_html($year) . '</span> </div>';
            }
            // if ($year) {
            //     echo '<div class="edition-year">' . esc_html($year) . '</div>';
            // }
            if ($date_range) {
                echo '<h3 class="edition-dates">' . esc_html($date_range) . '</h3>';
            }
            echo '</div>';
            echo $has_link ? '</a>' : '</div>';
        }
        echo '</div><div class="clearfix"></div>';
    }
    ?>

    <style>
    .edition-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 2rem;
        margin-top: 4rem;
        margin-bottom: 4rem;
    }
    .edition-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        padding-bottom: 1.5rem;
    }
    a.edition-card {
        color: inherit;
        text-decoration: none;
        transition: box-shadow .2s ease, transform .2s ease;
    }
    a.edition-card:hover,
    a.edition-card:focus-visible {
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        transform: translateY(-3px);
    }
    .edition-image img {
        width: 100%;
        height: 420px;
        object-fit: cover;
        display: block;
    }
    .edition-info {
        padding: 1rem 1rem 0 1rem;
        text-align: left;
        width: 100%;
    }
    .edition-title {
        font-size: 1.2rem;
        margin: 0.5rem 0 0.3rem 0;
        font-weight: 600;
    }
    .edition-desc {
        color: #555;
        margin-bottom: 0.5rem;
        line-height: 1.5;
    }
    .edition-year {
        font-size: 0.95rem;
        color: #888;
        margin-bottom: 0.2rem;
    }
    .edition-dates {
        font-size: 1rem;
        color: #222;
        font-weight: 500;
    }
    @media (max-width: 1024px) {
        .edition-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 600px) {
        .edition-grid {
            grid-template-columns: 1fr;
        }
        .edition-image img {
            height: 180px;
        }
    }
    </style>

    
</main>

<?php get_footer(); ?>