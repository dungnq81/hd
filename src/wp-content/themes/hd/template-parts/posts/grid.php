<?php

\defined( 'ABSPATH' ) || die;

$sidebar = $args['sidebar'] ?? false;
$is_sidebar = false;
if ( is_active_sidebar( 'post-archive-sidebar' ) && ! is_search() ) {
	$is_sidebar = true;
}

$grid_class = 'grid-x grid gap m-up-2 t-up-3 d-up-4';
if ( $is_sidebar && $sidebar ) {
	$grid_class = 'grid-x grid gap[20] m-up-2 t-up-3';

	echo '<div class="sidebar-col">';
	dynamic_sidebar( 'post-archive-sidebar' );
	echo '</div>';
}

// check have posts
if ( have_posts() ) :

if ( $is_sidebar && $sidebar ) { echo '<div class="content-col">';  }

echo '<div class="' . $grid_class . '">';

	// Start the Loop.
	while ( have_posts() ) : the_post();

		echo "<div class=\"cell\">";
		get_template_part( 'template-parts/posts/loop', null, [ 'title-tag' => 'h2' ] );
		echo "</div>";

		// End the loop.
	endwhile;
	wp_reset_postdata();

echo '</div>';

	// Previous/next page navigation.
	the_paginate_links();
else :
	get_template_part( 'template-parts/no-results' );
endif;

if ( $is_sidebar && $sidebar ) { echo '</div>';  }
