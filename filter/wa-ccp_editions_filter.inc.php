<?php

class ccp_editions_filter {
	private $__editions = array();
	private $__term_ids = array();
	private $__uncategorized_term_ids = array();
	private $__excluded_term_ids = array();
	// private $filtered_taxonomy = 'section';
	private $current_edition = false;
	private $current_edition_id = false;
	private $current_edition_year = false;
	private $year_current_edition = false;
	private $current_edition_films_are_online = false;
	private $cookie_name = 'EDITION';
	private $cookie_id = 'EDITION_ID';

	public function run() {
		if ( is_admin() ) {
			// Redefine cookie name if we have a setup VAR > No need cookies are localized
			// $this->cookie_name = (defined('WAFF_THEME'))?WAFF_THEME.'_EDITION':'FIFAM_EDITION';
			// $this->cookie_id = (defined('WAFF_THEME'))?WAFF_THEME.'_EDITION_ID':'FIFAM_EDITION_ID';
		
			// Init
			$this->init_editions();
			add_action( 'init', array( $this, 'init_filtered_taxonomies'), 20);
			// add_filter( 'get_terms_args', array( $this, 'filter_taxonomies_by_editions'), 10, 2); // No settings
			add_action( 'wp_before_admin_bar_render', array( $this, 'modify_admin_bar') );
			global $ccp_editions_filter;
			$ccp_editions_filter = $this;
		}
	}
	/* Add wil */
	public function init_filtered_taxonomies() {

		// Run filter after init to get settings 
		add_filter( 'get_terms_args', array( $this, 'filter_taxonomies_by_editions'), 10, 2);

		// Get section terms by meta 
		// wp_die("current_edition_id=" . $this->current_edition_id);
		// wp_die(print_r(wa_ccpef_get_posts_from_setting_page(), true));

	   	if (empty($this->__term_ids)) {
			
			if ( $this->current_edition_id != -1 )
				$search_args = array(
					'taxonomy'               => wa_ccpef_get_taxonomies_from_setting_page(),
					'hide_empty'             => false,
					'number'				 => 0,
					'meta_query' => array(
						array(
						'key'       => WA_CCPEF_MIGRATE_TAXONOMY_FIELD ? WA_CCPEF_MIGRATE_TAXONOMY_FIELD : 'wpcf-select-edition',
						'value'     => $this->current_edition_id,
						'compare'   => 'LIKE'
						)
					),
				);
			else 
				$search_args = array(
					'taxonomy'               => wa_ccpef_get_taxonomies_from_setting_page(),
					'hide_empty'             => false,
					'number'				 => 0,
					// 'meta_query' => array(
					// 	array(
					// 	'key'       => WA_CCPEF_MIGRATE_TAXONOMY_FIELD ? WA_CCPEF_MIGRATE_TAXONOMY_FIELD : 'wpcf-select-edition',
					// 	'compare' => 'NOT EXISTS'
					// 	)
					// ),
				);

		
			$term_query = new WP_Term_Query( $search_args );
		
			if ( ! empty( $term_query ) && ! is_wp_error( $term_query ) ) {
				foreach ( $term_query->terms as $term )
			        $this->__term_ids[] = $term->term_id;
			}
			else
				wp_die("Nothing found in args", "Term Query error");

		}
		
		// Get terms without editions 
		unset($term_query);
		if (empty($this->__uncategorized_term_ids)) {
			$search_args = array(
				'taxonomy'               => wa_ccpef_get_taxonomies_from_setting_page(),
				'hide_empty'             => false,
				'number'				 => 0,
				'meta_query' => array(
				    array(
				       'key'       => WA_CCPEF_MIGRATE_TAXONOMY_FIELD ? WA_CCPEF_MIGRATE_TAXONOMY_FIELD : 'wpcf-select-edition',
						'compare' => 'NOT EXISTS'
				    )
				),
			);
		
			$term_query = new WP_Term_Query( $search_args );
		
			if ( ! empty( $term_query ) && ! is_wp_error( $term_query ) ) {
				if (is_array($term_query->terms))
					foreach ( $term_query->terms as $term )
						$this->__uncategorized_term_ids[] = $term->term_id;
			}
			else
				wp_die("Nothing found in args", "Term Query error");

		}
		// DEBUG $this->__uncategorized_term_ids = array(230, 231);

		// Get all terms 
		unset($term_query);
		if (empty($this->__excluded_term_ids)) {
			$search_args = array(
				'taxonomy'               => wa_ccpef_get_taxonomies_from_setting_page(),
				'hide_empty'             => false,
				'number'				 => 0
			);
		
			$term_query = new WP_Term_Query( $search_args );
		
			if ( ! empty( $term_query ) && ! is_wp_error( $term_query ) ) {
				foreach ( $term_query->terms as $term )
			        $this->__excluded_term_ids[] = $term->term_id;
			}
			else
				wp_die("Nothing found in args", "Term Query error");
				
			$toinclude = array_merge( $this->__term_ids, $this->__uncategorized_term_ids );
			$toexclude = array_diff( $this->__excluded_term_ids, $toinclude);
			$this->__excluded_term_ids = $toexclude;
			
			// print_r($toinclude);
			// print_r($toexclude);

		}
	}

	public function init_editions() {
		// Get terms
		global $wpdb;
		$field = WA_CCPEF_MIGRATE_FIELD_CURRENT ? WA_CCPEF_MIGRATE_FIELD_CURRENT : 'wpcf-e-current-edition';
		$sql="SELECT ".$wpdb->prefix."terms.term_id, name, slug, description, meta_value as current FROM ".$wpdb->prefix."term_taxonomy, ".$wpdb->prefix."terms LEFT JOIN ".$wpdb->prefix."termmeta ON ".$wpdb->prefix."termmeta.term_id = ".$wpdb->prefix."terms.term_id AND meta_key = '".$field."' WHERE ".$wpdb->prefix."term_taxonomy.term_id = ".$wpdb->prefix."terms.term_id AND ".$wpdb->prefix."term_taxonomy.taxonomy = 'edition' ORDER BY slug DESC;";
		$this->__editions = $wpdb->get_results($sql , ARRAY_A);
		if (array_key_exists('ccp_editions_filter_bar_edition_term_slug', $_POST)) {
			$term_slug = $_POST['ccp_editions_filter_bar_edition_term_slug'];
			//echo "term_slug:".$term_slug;
			setcookie($this->cookie_name, $term_slug, time()+3600 * 24 * 7, '/');
			$_COOKIE[$this->cookie_name] = $term_slug;
		}
		if (array_key_exists($this->cookie_name, $_COOKIE)) {
			$this->current_edition = $_COOKIE[$this->cookie_name];
		} elseif (count($this->__editions)) {
			foreach($this->__editions as $edition)
				if ($edition['current'])
					$this->current_edition = $this->__editions[0]['slug'];
			if ($this->current_edition) {
				setcookie($this->cookie_name, $this->current_edition, time()+3600 * 24 * 7, '/');
				$_COOKIE[$this->cookie_name] = $this->current_edition;
			}
		}
		foreach($this->__editions as $edition) {
			if ($edition['current'])
				$this->year_current_edition = $this->__editions[0]['slug'];
			if ($edition['slug'] == $_COOKIE[$this->cookie_name]) {
				$_COOKIE[$this->cookie_id] = $edition['term_id'];
				setcookie($this->cookie_id, $edition['term_id'], time()+3600 * 24 * 7, '/');
				$this->current_edition_id = $edition['term_id'];
			}
		}
		if (array_key_exists('edition', $_GET) && $_GET['edition'] == 'current') {
			$_GET['edition'] = $this->current_edition;
		}

		// If All edition showing
		if($_COOKIE[$this->cookie_name] == '0') {
			setcookie($this->cookie_id, '0', time()+3600 * 24 * 7, '/');
			$this->current_edition = "-1";
			$this->current_edition_id = -1;
		}

		// ADD WIL Get current year over
		$this->get_current_edition_year = get_term_meta( $this->current_edition_id, WA_CCPEF_MIGRATE_FIELD_YEAR ? WA_CCPEF_MIGRATE_FIELD_YEAR : 'wpcf-e-year', true );
		//wp_die( $this->get_current_edition_year );
	
		// ADD WIL Get current_edition_films_are_online option
		$current_edition_films_are_online = get_option('current_edition_films_are_online');
		$this->current_edition_films_are_online = ( !empty($current_edition_films_are_online) && $current_edition_films_are_online == 1)?true:false; 
		//wp_die('<pre>'.$this->current_edition_films_are_online.'</pre>');	

		
	}
	/* Add wil */
	public function filter_taxonomies_by_editions($args = array(), $taxonomies = '') {
	    global $typenow;
    	global $pagenow;

		// wp_die(print_r(wa_ccpef_get_posts_from_setting_page(), true));
    	// print_r($taxonomies);
    	// print_r($args);
    	// print_r(wa_ccpef_get_taxonomies_from_setting_page());
    	// print_r(count($this->__term_ids));
    	// print_r(count($this->__uncategorized_term_ids));
    	// print_r($this->__uncategorized_term_ids);
    	// print_r($this->__excluded_term_ids);
    	
	    if ( !empty(wa_ccpef_get_posts_from_setting_page()) && in_array($typenow, wa_ccpef_get_posts_from_setting_page()) && 'edit-tags.php' == $pagenow ) { 
	        // check whether we're currently filtering selected taxonomy
			if (in_array(implode('', $taxonomies), wa_ccpef_get_taxonomies_from_setting_page())) {
	        	//$args['hide_empty'] = 0; // Ne change a rien ..
	        	//$args['page'] = -1; // Ne change a rien.. 
	        	//$args['hierarchical'] = 0; // Permet d'afficher un term perdu qui ne serait rattaché ni a une edition, ni a une cat >> Ne change rien ;) 
	        	//$include = array_merge($this->__term_ids, $this->__uncategorized_term_ids);
	            //if ( empty($include) ) {
	            if ( empty($this->__excluded_term_ids) ) {
	                //$args['include'] = array(-1); // no available categories
	                $args['exclude'] = array(-1); // Ne change rien non plus ar rapport a include 
	            } else {
	                //$args['include'] = $include; //It will only show the category that you mentioned in above array
	                $args['exclude'] = $this->__excluded_term_ids; // Ne change rien non plus ar rapport a include 
	            }
	        }
	    }
	    
    	//print_r($args);
	    return $args;	
	}

	public function get_edition() {
		return array($this->current_edition, array());
	}

	public function get_current_edition() {
		return $this->current_edition;
	}

	public function get_current_edition_id() {
		return $this->current_edition_id;
	}

	public function get_current_edition_year() {
		return $this->current_edition_year;
	}

	public function modify_admin_bar() {
		global $wp_admin_bar;

		$wp_admin_bar->add_menu(
			array(
				'id'        => 'ccp_editions_filter',
				'title'     => $this->display_editions_filter(),
			)
		);
	}

	public function display_editions_filter() {
		$html = '<form method="POST" id="ccp_editions_filter_bar_form">';
		/* Badge */ 
		$html .= '<span class="badge-editions-filter dashicons-before dashicons-image-filter" aria-hidden="true"></span>';
		if ( WA_CCPEF_DEBUG === true ) {
			$html .= "<code class='debug'>c:".$this->current_edition."</code>";
			$html .= "<code class='debug'>s:".$edition['slug']."</code>";
		}
		/* Select button */
		$html .= '<select class="editions-filter" name="ccp_editions_filter_bar_edition_term_slug" onchange="jQuery(\'#ccp_editions_filter_bar_form\').submit();">';
		foreach($this->__editions as $edition) {
			// Get localized description
			$description =  ( function_exists('qtranxf_split') && function_exists('qtranxf_getLanguage') ) ? qtranxf_split($edition['description'])[qtranxf_getLanguage()] : $edition['description'];
			$html .= '<option value="'.$edition['slug'].'"'.(($this->current_edition == $edition['slug'])?' selected="selected"':'').'>'.$description.(($this->year_current_edition == $edition['slug'])?' *':'').'</option>';
		}
		$html .= '<option value="0"'.(($this->current_edition === "-1")?' selected="selected"':'').'>'.__('Show all editions', WA_CCPEF_TEXTDOMAIN).'</option>';
		$html .= '</select>';
		/* Is edition online ? */
		$html .= '<a class="button button-link button-small button-submit button-online"style="'.(($this->current_edition_films_are_online===true)?'background:#00ba5f;border-color:#00ba5f;':'background:#d84900;border-color:#d84900;').'" href="http://www.fifam.fr/wp-admin/options-general.php#current_edition_films_are_online">'.(($this->current_edition_films_are_online===true)?'ONLINE':'OFFLINE').'</a>';
		$html .= '</form>';
		
		return $html;
	}
}
