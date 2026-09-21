<?php
/**
 * JOHN Project theme functions.
 */

add_action( 'wp_enqueue_scripts', 'john_project_enqueue_styles' );
function john_project_enqueue_styles() {
	wp_enqueue_style(
		'parent-style',
		get_template_directory_uri() . '/style.css',
		[],
		wp_get_theme( get_template() )->get( 'Version' )
	);
	wp_enqueue_style(
		'john-project-style',
		get_stylesheet_directory_uri() . '/style.css',
		[ 'parent-style' ],
		filemtime( get_stylesheet_directory() . '/style.css' )
	);
}
