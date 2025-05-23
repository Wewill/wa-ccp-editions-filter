<?php
/**
 * The below function will help to load template file from plugin directory of wordpress
 *  Extracted from : http://wordpress.stackexchange.com/questions/94343/get-template-part-from-plugin
 * load_template( string $_template_file, bool $load_once = true, array $args = array() )
 */ 
 
function plugin_get_template_part($slug, $name = null, $args = array()) {
  do_action("plugin_get_template_part_{$slug}", $slug, $name);

  $templates = array();
  if (isset($name))
      $templates[] = "{$slug}-{$name}.php";

  $templates[] = "{$slug}.php";

  plugin_get_template_path($templates, true, false, $args);
}

/* Extend locate_template from WP Core 
* Define a location of your plugin file dir to a constant in this case = WA_CCPEF_DIR 
* Note: WA_CCPEF_DIR - can be any folder/subdirectory within your plugin files 
*/ 

function plugin_get_template_path($template_names, $load = false, $require_once = true, $args = array() ) {

    $located = ''; 
    foreach ( (array) $template_names as $template_name ) { 
      if ( !$template_name ) 
        continue; 

      /* search file within the WA_CCPEF_DIR only */ 
      if ( file_exists(WA_CCPEF_DIR . $template_name)) { 
        $located = WA_CCPEF_DIR . $template_name; 
        break; 
      } 
    }

    if ( $load && '' != $located )
        load_template( $located, $require_once, $args);

    return $located;
}