<?php

$custom_order_options = get_option( 'custom_order__options' );
$order_post_type      = $custom_order_options['order_post_type'] ?? [];
$order_taxonomy       = $custom_order_options['order_taxonomy'] ?? [];

?>
<h2><?php _e( 'Order Settings', ADDONS_TEXT_DOMAIN ); ?></h2>
<div class="section section-checkbox" id="section_custom_order">
	<span class="heading block !fw-700"><?php _e( 'Check to Sort Post Types', ADDONS_TEXT_DOMAIN ); ?></span>
	<?php

	$post_types = get_post_types( [ 'show_ui' => true ], 'objects' );

	foreach ( $post_types as $post_type ) :

		// Exclude post-type
		$exclude_post_type = [
			'attachment',
			'wp_navigation',
			'product',
		];

		if ( ! current_user_can( 'manage_options' ) ) {
			$exclude_post_type = array_merge( $exclude_post_type, [
				'acf-taxonomy',
				'acf-post-type',
				'acf-ui-options-page',
				'acf-field-group',
			] );
		}

		if ( in_array( $post_type->name, $exclude_post_type ) ) {
			continue;
		}

		$label = $post_type->label;
		if ( str_starts_with( $post_type->name, 'shop_' ) ) {
			$label = 'Product ' . $label;
		}

		if ( str_starts_with( $post_type->name, 'acf-' ) ) {
			$label = 'ACF ' . $label;
		}

		$label .= ' <span class="!fw-400">(' . $post_type->name . ')</span>';

		?>
		<div class="option mb-20">
			<label class="controls">
				<input type="checkbox" class="hd-checkbox hd-control" name="order_post_type[]" value="<?php echo esc_attr_strip_tags( $post_type->name ); ?>" <?php in_array_checked( $order_post_type, $post_type->name ); ?>>
			</label>
			<div class="explain"><?php echo $label; ?></div>
		</div>
	<?php endforeach; ?>

	<span class="heading block !fw-700"><?php _e( 'Check to Sort Taxonomies', ADDONS_TEXT_DOMAIN ); ?></span>

	<?php

	$taxonomies = get_taxonomies( [ 'show_ui' => true ], 'objects' );

	foreach ( $taxonomies as $taxonomy ) :
		if ( in_array( $taxonomy->name, [
			'link_category',
			'wp_pattern_category',
			'product_cat',
		] ) ) {
			continue;
		}

		$label = $taxonomy->label;
		$label .= ' <span class="!fw-400">(' . $taxonomy->name . ')</span>';
		?>
		<div class="option mb-20">
			<label class="controls">
				<input type="checkbox" class="hd-checkbox hd-control" name="order_taxonomy[]" value="<?php echo esc_attr_strip_tags( $taxonomy->name ); ?>" <?php in_array_checked( $order_taxonomy, $taxonomy->name ); ?>>
			</label>
			<div class="explain"><?php echo $label; ?></div>
		</div>
	<?php endforeach; ?>

	<span class="heading block !fw-700"><?php _e( 'Check to reset order', ADDONS_TEXT_DOMAIN ); ?></span>

	<div class="option mb-20">
		<label class="controls">
			<input type="checkbox" class="hd-checkbox hd-control" name="order_reset" id="order_reset" value="1">
		</label>
		<div class="explain"><?php _e( 'Reset all', ADDONS_TEXT_DOMAIN ); ?></div>
	</div>
</div>
