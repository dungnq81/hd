<?php

use Cores\Helper;

$custom_order_options = Helper::getOption( 'custom_order__options' );
$order_post_type      = $custom_order_options['order_post_type'] ?? [];
$order_taxonomy       = $custom_order_options['order_taxonomy'] ?? [];

?>
<h2><?php _e( 'Order Settings', TEXT_DOMAIN ); ?></h2>
<div class="section section-checkbox" id="section_custom_order">
    <span class="heading block !fw-700"><?php _e( 'Check to Sort Post Types', TEXT_DOMAIN ); ?></span>

	<?php
	$hd_order_post_types_args = [
		//'public' => true,
		//'show_in_menu' => true,
		'show_ui' => true,
	];
	$post_types = get_post_types( $hd_order_post_types_args, 'objects' );
	foreach ( $post_types as $post_type ) :

		if ( in_array( $post_type->name, [
			'attachment',
			'wp_navigation',
			'acf-taxonomy',
			'acf-post-type',
			'acf-ui-options-page',
			'acf-field-group',
            'product',
		] ) ) {
			continue;
		}

        $label = $post_type->label;
        if ( 'shop_order' == $post_type->name || 'shop_coupon' == $post_type->name ) {
	        $label = 'Product ' . $label;
        }
    ?>
    <div class="option mb-15">
        <label class="controls">
            <input type="checkbox" class="hd-checkbox hd-control" name="order_post_type[]" value="<?php echo esc_attr( $post_type->name ); ?>" <?php in_array_checked( $order_post_type, $post_type->name ); ?>>
        </label>
        <div class="explain"><?php echo $label; ?></div>
    </div>
	<?php endforeach; ?>

    <span class="heading block !fw-700"><?php _e( 'Check to Sort Taxonomies', TEXT_DOMAIN ); ?></span>

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
    ?>
    <div class="option mb-15">
        <label class="controls">
            <input type="checkbox" class="hd-checkbox hd-control" name="order_taxonomy[]" value="<?php echo esc_attr( $taxonomy->name ); ?>" <?php in_array_checked( $order_taxonomy, $taxonomy->name ); ?>>
        </label>
        <div class="explain"><?php echo $taxonomy->label; ?></div>
    </div>
    <?php endforeach; ?>

    <span class="heading block !fw-700"><?php _e( 'Check to reset order', TEXT_DOMAIN ); ?></span>

    <div class="option mb-15">
        <label class="controls">
            <input type="checkbox" class="hd-checkbox hd-control" name="order_reset" id="order_reset" value="1">
        </label>
        <div class="explain"><?php _e( 'Reset all', TEXT_DOMAIN ); ?></div>
    </div>
</div>
