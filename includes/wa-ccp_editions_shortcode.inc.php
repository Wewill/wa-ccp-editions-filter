<?php
/**
 * SPECIAL A&J.. To be modified
 * 
 */
// Shortcode pour afficher le header de l'édition courante
// [wa_current_edition_header]
add_shortcode('wa_current_edition_header', 'wa_ccpef_shortcode_current_edition_header');

function wa_ccpef_shortcode_current_edition_header($atts) {
    $prefix = 'waccpef-';
    // Récupérer l'édition marquée comme courante
    $args = [
        'taxonomy'   => 'edition',
        'hide_empty' => false,
        'meta_query' => [
            [
                'key'     => $prefix . 'e-current-edition',
                'value'   => '1',
                'compare' => '=='
            ]
        ]
    ];
    $current_editions = get_terms($args);
    if (empty($current_editions) || is_wp_error($current_editions)) {
        return '<!-- Aucune édition courante trouvée -->';
    }
    $edition = $current_editions[0];

    // Préparer les données
    $edition_name = esc_html($edition->name);
    $edition_description = $edition->description;
    $edition_link = get_term_link($edition);
    $edition_slug = esc_html($edition->slug);

    // Color
    $edition_color = get_term_meta($edition->term_id, $prefix . 'e-color', true);
    $edition_color_style = $edition_color ? 'style="background-color:'.$edition_color.'!important;"' : '';
    
    // Image cover 
    $edition_image = get_term_meta($edition->term_id, $prefix . 'e-image', true);
    if (!empty($edition_image)) {
        $edition_image_url = wp_get_attachment_url($edition_image);
    }

    // Dates
    $edition_start_date = get_term_meta($edition->term_id, $prefix . 'e-start-date', true);
    $edition_end_date = get_term_meta($edition->term_id, $prefix . 'e-end-date', true);
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


    ob_start();
    ?>
    <div class="wa-edition-header page-content" style="background-color: rgb(221, 239, 248);padding:2rem;border-radius:8px;display:flex;align-items:flex-start;gap:4rem;flex-wrap:wrap;">
        <div style="flex:1 1 220px;min-width:220px;max-width:320px;text-align:center;">
            <?php
            // Afficher l'image si elle existe (champ personnalisé ACF ou autre)
            if ($edition_image_url) {
                    echo '<img src="' . esc_url($edition_image_url) . '" alt="Affiche ' . $edition_name . '" style="max-width:100%;border-radius:8px;box-shadow:0 2px 12px #0001;">';
            }
            ?>
        </div>
        <div class="" style="flex:2 1 350px;min-width:250px;align-self:center;text-align:left;justify-content:center;">
            <h6 class="edition-label" style="color:#b0b0b0;">EDITION EN COURS</h6>
            
            <div class="heading-text" >
                <h2 class="edition-title" style="font-size:2.2rem;margin:0.5rem 0;margin-bottom:4rem;"><?php echo $edition_name; ?></h2>
            
                <div class="edition-description" style="font-size:2.2rem;margin-bottom:4rem;">
                    <?php echo $edition_description; ?>

                                    <?php if ($edition_date_string) : ?>
                    <h3 class="edition-dates" style="
                        display: inline-block;
                        padding: 5px 8px;
                        position: relative;
                        top: -3px;
                        height: 30px;
                        line-height: 30px;
                        border-bottom: 2px solid #ff2d17;
                        color: #ff2d17;
                        text-transform: uppercase;
                        font-weight: 700;
                        font-size: 12px;
                        letter-spacing: 0;
                        margin: 0;
                    ">
                        <?php echo esc_html($edition_date_string); ?>
                    </h3>
                <?php endif; ?>

                </div>
            </div>

            <?php if (!is_wp_error($edition_link)) : ?>
                <a class="edition-btn" href="<?php echo esc_url($edition_link); ?>" style="background:#ff2d17;color:#fff;padding:0.7rem 2rem;border-radius:8px;text-decoration:none;font-weight:bold;display:inline-block;transition:background 0.2s;font-family:Fabrikat;">
                    CONSULTER <span style="margin-left:1rem;">→</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Shortcode pour afficher le bouton "Voir les éditions précédentes"
// [wa_previous_editions_button]
add_shortcode('wa_previous_editions_button', 'wa_ccpef_shortcode_previous_editions_button');

function wa_ccpef_shortcode_previous_editions_button($atts) {
    ob_start();
    ?>
    <div id="base-promo" class="sf-promo-bar promo-arrow container previous-editions" style="background-image: url('<?php echo esc_url(get_stylesheet_directory_uri() . '/images/previous_editions_bg.png'); ?>');"> 
        <a href="/edition/" target="_self">Consultez<br>les éditions précédentes<br><i class="icon-right"></i></a>
        <!--// CLOSE #base-promo //-->
    </div>
    <?php
    return ob_get_clean();
}; 

