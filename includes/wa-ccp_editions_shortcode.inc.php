<?php
// Shortcode pour afficher le header de l'édition courante
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
    // Simuler la page de taxonomie pour le template
    $GLOBALS['wp_query']->queried_object = $edition;
    $GLOBALS['wp_query']->queried_object_id = $edition->term_id;
    ob_start();
    include WA_CCPEF_DIR . 'templates/partials/header-artetjardins-hdf.php';
    // Ajouter un bouton vers le slug de l'édition
    $edition_link = get_term_link($edition);
    if (!is_wp_error($edition_link)) {
        echo '<div style="text-align:center;margin:20px 0;"><a href="' . esc_url($edition_link) . '" class="button wa-edition-link" style="padding:10px 24px;background:#222;color:#fff;border-radius:4px;text-decoration:none;font-weight:600;">Voir l’édition</a></div>';
    }
    return ob_get_clean();
}
