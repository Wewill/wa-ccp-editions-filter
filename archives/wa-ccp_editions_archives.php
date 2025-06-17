<?php
/*
Archives
*/

// add_action( 'load-edit.php', function() {
// });

add_action('init', function() {
    // flush_rewrite_rules(); // TEMPORAIRE : ne pas laisser en production
    add_rewrite_rule(
        '^edition/?$',
        'index.php?edition_archive=1',
        'top'
    );
});

add_filter('query_vars', function($vars) {
    $vars[] = 'edition_archive';
    return $vars;
});

// See wa-ccp-editions-filter.php for template redirection