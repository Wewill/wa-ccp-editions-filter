<?php
/*
Block
*/

//**
// Allow
// */


add_filter( 'admin_init', 'wa_ccpef_allow_blocks');
function wa_ccpef_allow_blocks() {
	// Blocks 
	add_filter( 'allowed_block_types_all', 'wa_ccpef_post_type_allowed_block_types', 20, 2 );
}

// Allow custom blocks considering post_type 
function wa_ccpef_post_type_allowed_block_types( $allowed_blocks, $editor_context ) {

	//wp_die(print_r($allowed_blocks, true));

	// Because the theme restricts the blocks, add here custom blocks created in the plugin
	if ( is_array($allowed_blocks) && isset( $editor_context->post ) && $editor_context->name !== 'core/edit-widgets' ) {
		// Add metabox.io testimony block
		$allowed_blocks[] = 'meta-box/wa-editions';
	}
	
	//wp_die(print_r($allowed_blocks, true));

	return $allowed_blocks;
}		

//**	
// Register
// */

add_filter( 'rwmb_meta_boxes', 'wa_ccpef_register_block');
function wa_ccpef_register_block( $meta_boxes ) {
    $prefix = 'wa_ccpef_';

    $meta_boxes[] = [
		'title'          => esc_html__( '(WA) Editions', 'wa_ccpef' ),
		'id'             => 'wa-editions',
		'fields'         => [
			[
                'id'   => $prefix . 'title',
                'type' => 'text',
                'name' => esc_html__( 'Title', 'wa_ccpef' ),
                // 'std'  => esc_html__( 'An awesome edition', 'wa_ccpef' ),
                'placeholder' => esc_html__( 'An awesome title', 'wa_ccpef' ),
            ],
            [
                'id'   => $prefix . 'subtitle',
                'type' => 'text',
                'name' => esc_html__( 'Subtitle', 'wa_ccpef' ),
                // 'std'  => esc_html__( 'Edito', 'wa_ccpef' ),
				'placeholder' => esc_html__( 'An awesome subtitle', 'wa_ccpef' ),
			],
            [
                'name'              => __( 'Show edition header ?', 'wa_ccpef' ),
                'id'                => $prefix . 'header',
                'type'              => 'switch',
                'label_description' => __( 'Check if you want to display the edition  header', 'wa_ccpef' ),
                'std'               => true,
                'required'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
            ],
            [
                'name'              => __( 'Show current edition ?', 'wa_ccpef' ),
                'id'                => $prefix . 'current_edition',
                'type'              => 'switch',
                'label_description' => __( 'Check if you want to display only the current edition', 'wa_ccpef' ),
                'std'               => true,
                'required'          => false,
                'clone'             => false,
                'clone_empty_start' => false,
                'hide_from_rest'    => false,
            ],
			[
                'name'            => __( 'Displayed edition', 'wa_ccpef' ),
                'id'              => $prefix . 'displayed_edition',
                'type'            => 'select',
                'inline'          => true,
                'select_all_none' => true,
                'options'         => wa_ccpef_edition_terms_callback(),
				'visible'         => [$prefix . 'current_edition', 0],

            ],
			[
                'name'            => __( 'Displayed post type.s', 'wa_ccpef' ),
                'id'              => $prefix . 'displayed_post_types',
                'type'            => 'checkbox_list',
                'inline'          => true,
                'select_all_none' => true,
                'options'         => wa_ccpef_get_posts_from_setting_page_as_options(),
            ],
		],
		'category'       => 'layout',
		// 'icon'           => 'format-quote',
		'icon'            => [
			'foreground' 	=> '#9a7300',
			'src' 			=> 'image-filter',
		],
		'description'     => esc_html__( 'Display all edition posts', 'wa_ccpef' ),
		'keywords'       => ['post', 'content'],
		'supports'       => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide', 'full'],
		],
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'wa_editions_callback',
		'type'           => 'block',
		'context'        => 'side',
	];

//  wp_die('<pre>'.print_r($meta_boxes, true));

    return $meta_boxes;
}

function wa_ccpef_edition_terms_callback() {
	$terms = get_terms( [
		'taxonomy'   => 'edition',
		'hide_empty' => false,
	] );

	$options = [];
	if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
		foreach ( $terms as $term ) {
			$options[ $term->term_id ] = $term->name;
		}
	}

	return $options;
}

function wa_editions_callback( $attributes ) {
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;

	// print_r($attributes);

	// No data no render.
	// if ( empty( $attributes['data'] ) ) return;
	
	// Unique HTML ID if available.
	$id = '';
	if ( $attributes['name'] ) {
		$id = $attributes['name'] . '-';
	} elseif (  $attributes['data']['name'] ) {
		$id = $attributes['data']['name'] . '-';
	}
	$id .= ( $attributes['id'] && $attributes['id'] !== $attributes['name']) ? $attributes['id'] : wp_generate_uuid4();
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	$themeClass = 'editions mt-md-4 mb-md-4 mt-2 mb-2 contrast--light --fix-vh-100'; // Responsive issue fix
	$class = $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

    $prefix = 'wa_ccpef_';
	$title                          	= mb_get_block_field( $prefix . 'title');
	$subtitle                          	= mb_get_block_field( $prefix . 'subtitle');
	$displayed_post_types               = mb_get_block_field( $prefix . 'displayed_post_types');
	$header                    			= mb_get_block_field( $prefix . 'header');
	$display_current_edition             = mb_get_block_field( $prefix . 'current_edition');
	$displayed_edition                  = mb_get_block_field( $prefix . 'displayed_edition');
	// $hide_center_column				= (mb_get_block_field( 'waff_e_hide_center_column' ))?'1':'0'; 

	// Get current edition
	global $current_edition, $current_edition_id;
	if ( $display_current_edition === 1 )
		$displayed_edition = $current_edition_id;
	
	// Get metas
	$prefix_meta = 'waccpef-';
	$edition_color = get_term_meta($displayed_edition, $prefix_meta . 'e-color', true);
	$edition_color_style = $edition_color ? 'style="background-color:'.$edition_color.'!important;"' : '';
	$edition_image = get_term_meta($displayed_edition, $prefix_meta . 'e-image', true);
	if (!empty($edition_image)) {
		$edition_image_url = wp_get_attachment_url($edition_image);
	}


	?>
	<!-- #Editions -->	
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid">

			<?php if ( $title || $subtitle ) : ?>
				<hgroup class="">
					<?php if ( $subtitle ) : ?>
						<h6 class="subline" style="<?= !$is_preview ?: 'color:white;' ?>"><?= $subtitle ?></h6>
					<?php endif; ?>
					<?php if ( $title ) : ?>
						<h4 class="" style="<?= !$is_preview ?: 'color:white;' ?>"><?= $title ?></h4>
					<?php endif; ?>
				</hgroup>
			<?php endif; ?>

			<?php if (!empty($header) && $header === 1) : ?>
				<section id="pagetitle" class="pt-5 pt-md-9 pb-5 pb-md-9 contrast--light --f-w shadow-md rounded-top-4 " <?= $edition_color_style ?>>
					<div class="jumbotron">
						<div class="container-fluid">
							<hgroup data-aos="fade-down">
								<h1 class="title mb-0 fw-bold"><?= get_term($displayed_edition)->name; ?></h1>
								<small><?= term_description($displayed_edition); ?></small>
							</hgroup>
						</div>
					</div>
				</section>

				<?php if (!empty($edition_image)) : ?>
				<section id="pageheader" class="mt-0 mb-0 contrast--light --f-w rounded-bottom-4" data-aos="slide-down" data-aos-id="pageheader">
					<figure title="">
						<picture class="lazy">
						<img src="<?php echo esc_url($edition_image_url); ?>" alt="<?php esc_attr_e('Edition Image', 'wa-ccpef'); ?>" style="object-fit: cover; max-height: 25vh; width: 100%; height: auto;">
						</picture>
					</figure>
				</section>
				<?php endif; ?>
			<?php endif; ?>

			<div class="col-12 taxonomy-edition-archive mt-8">
				<?php
					// Get all post types that use this taxonomy
					foreach ($displayed_post_types as $post_type) {
						$query = new WP_Query([
							'post_type'      => $post_type,
							'posts_per_page' => -1,
							'orderby'        => 'title',
							'order'          => 'ASC',
							'tax_query'      => [
								[
									'taxonomy' => 'edition',
									'field'    => 'term_id',
									'terms'    => $displayed_edition, //$term_id,
								],
							],
							'post_status'    => 'publish',
						]);

						$pt_obj = get_post_type_object( $post_type );

						if ($query->have_posts()) {
							if (count($displayed_post_types) > 1) {
								echo '<h3>' . esc_html($pt_obj->labels->name) . '</h3>';
							}
							echo '<ul class="edition-posts">';
							while ($query->have_posts()) : $query->the_post();
								plugin_get_template_part('templates/partials/cards');
							endwhile;
							echo '</ul>';
							wp_reset_postdata();
						}
					}
				?>
			</div>

		</div>
	</section>

	<!-- END: #Editions -->
    <?php
}