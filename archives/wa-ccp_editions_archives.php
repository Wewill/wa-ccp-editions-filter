<?php
/*
Archives
*/

// add_action( 'load-edit.php', function() {
// });

add_action('init', function() {
    add_rewrite_rule(
        '^edition/?$',
        'index.php?edition_archive=1',
        'top'
    );
});

// Self-heal : flush rewrite rules once if the archive rule is missing from the stored rules
// (e.g. rules flushed before this rule was registered). Transient guard avoids a flush loop.
add_action('init', function() {
    if ( ! get_option('permalink_structure') ) return; // Plain permalinks : no rewrite rules
    $rules = get_option('rewrite_rules');
    if ( is_array($rules) && ! isset($rules['^edition/?$']) && ! get_transient('wa_ccpef_rewrite_flushed') ) {
        set_transient('wa_ccpef_rewrite_flushed', 1, HOUR_IN_SECONDS);
        flush_rewrite_rules(false);
    }
}, 99);

add_filter('query_vars', function($vars) {
    $vars[] = 'edition_archive';
    return $vars;
});

// See wa-ccp-editions-filter.php for template redirection