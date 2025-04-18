<?php
/*
Adds an icon (filter dashicons) before the title of the post types and taxonomies handled by the plugin in edit.php & edit-tags.php
*/

add_action('admin_head', function () {
	$screen = get_current_screen();
	if ($screen && in_array($screen->post_type, wa_ccpef_get_posts_from_setting_page()) || $screen && in_array($screen->taxonomy, wa_ccpef_get_taxonomies_from_setting_page())) {
		echo '<style>
			#screen-meta-links::before {
				content: "\f533";
				font-family: "Dashicons";
				margin-left: 10px;
				display: inline-block;
				width: 30px;
				background-color: #efd589;
				height: 18px;
				text-align: center;
				padding-top: 6px;
				padding-bottom: 6px;
				color: #9a7300;
				border-bottom-left-radius: 5px;
				border-bottom-right-radius: 5px;
			}
		</style>';
	}
});