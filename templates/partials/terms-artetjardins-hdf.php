<?php
    // print_r($args['t_obj']);
    $t_obj = $args['t_obj'];
    $term_name = $t_obj->name;
    $term_description = $t_obj->description;
    $term_id = $t_obj->term_id;
    
    // Image as custom field : a_general_image ? 
    $image_id = get_term_meta($term_id, 'a_general_image', true);
?>
<a href="<?php echo esc_url(get_term_link($t_obj)); ?>" class="term-card-link" style="text-decoration: none; color: inherit;">
    <div class="term-card" id="term-<?php echo esc_attr($term_id); ?>" style="display: flex; align-items: center; padding: 12px; border: none; border-radius: 12px; background: #fff; transition: box-shadow 0.2s; cursor: pointer; margin-bottom: 16px;">
        <?php
        if ($image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
        }
        ?>
        <div class="term-card-img" style="width: 56px; height: 56px; border-radius: 50%; overflow: hidden; background: #1976d2; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <?php if (!empty($image_url)): ?>
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($term_name); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            <?php endif; ?>
        </div>
        <div class="term-card-info" style="margin-left: 16px; display: flex; flex-direction: column; justify-content: center;">
            <span class="term-card-title" style="color: #1976d2; font-weight: 600; font-size: 1.1em; line-height: 1.2;"><?php echo esc_html($term_name); ?></span>
            <!-- <?php if (!empty($term_description)): ?>
                <span class="term-card-desc" style="color: #444; font-size: 0.95em; line-height: 1.2;"><?php echo esc_html($term_description); ?></span>
            <?php endif; ?> -->
        </div>
    </div>
</a>
