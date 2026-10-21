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

add_action( 'wp_enqueue_scripts', 'john_project_enqueue_scripts' );
function john_project_enqueue_scripts() {
	wp_enqueue_script(
		'john-project-sticky-sidebar',
		get_theme_file_uri( 'assets/js/sticky-sidebar.js' ),
		[],
		filemtime( get_theme_file_path( 'assets/js/sticky-sidebar.js' ) ),
		[ 'strategy' => 'defer' ]
	);
}

add_action( 'after_setup_theme', 'john_project_add_editor_styles' );
function john_project_add_editor_styles() {
	add_editor_style( 'style.css' );
}

// Favicons: always the theme's (the logo's waves), never a Site Icon set in
// WordPress. Without a Site Icon, WordPress redirects /favicon.ico to its own
// "W" logo, so its site icon URL is pointed at the theme's icons too.
remove_action( 'wp_head', 'wp_site_icon', 99 );
remove_action( 'login_head', 'wp_site_icon', 99 );

// The admin adds its hook after the theme loads.
add_action( 'admin_init', 'john_project_remove_admin_site_icon' );
function john_project_remove_admin_site_icon() {
	remove_action( 'admin_head', 'wp_site_icon' );
}

add_action( 'wp_head', 'john_project_favicons' );
add_action( 'admin_head', 'john_project_favicons' );
add_action( 'login_head', 'john_project_favicons' );
function john_project_favicons() {
	$base = get_theme_file_uri( 'assets/images/favicons' );
	echo '<link rel="icon" href="' . esc_url( $base . '/favicon.ico' ) . '" sizes="32x32">' . "\n";
	echo '<link rel="icon" href="' . esc_url( $base . '/favicon.svg' ) . '" type="image/svg+xml">' . "\n";
	echo '<link rel="icon" href="' . esc_url( $base . '/favicon-192.png' ) . '" type="image/png" sizes="192x192">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $base . '/apple-touch-icon.png' ) . '">' . "\n";
}

add_filter( 'get_site_icon_url', 'john_project_site_icon_url', 10, 2 );
function john_project_site_icon_url( $url, $size ) {
	if ( $size <= 48 ) {
		$file = 'favicon.ico';
	} elseif ( $size <= 192 ) {
		$file = 'favicon-192.png';
	} else {
		$file = 'favicon-512.png';
	}
	return get_theme_file_uri( 'assets/images/favicons/' . $file );
}

// Block styles
add_action( 'init', 'john_project_register_block_styles' );
function john_project_register_block_styles() {
	register_block_style(
		'core/list',
		[
			'name'  => 'link-list',
			'label' => __( 'Link list', 'john-project' ),
		]
	);
	register_block_style(
		'core/paragraph',
		[
			'name'  => 'eyebrow',
			'label' => __( 'Eyebrow', 'john-project' ),
		]
	);
	register_block_style(
		'core/separator',
		[
			'name'  => 'wave',
			'label' => __( 'Wave', 'john-project' ),
		]
	);
	register_block_style(
		'core/button',
		[
			'name'  => 'text-link',
			'label' => __( 'Text link', 'john-project' ),
		]
	);
}

// Pages: excerpts (used by "See also" cards) and the "See also" page picker
add_action( 'init', 'john_project_add_page_excerpts' );
function john_project_add_page_excerpts() {
	add_post_type_support( 'page', 'excerpt' );
}

add_action( 'add_meta_boxes_page', 'john_project_add_see_also_box' );
function john_project_add_see_also_box() {
	add_meta_box(
		'john-project-see-also',
		__( 'See also', 'john-project' ),
		'john_project_render_see_also_box',
		'page',
		'side'
	);
}

function john_project_render_see_also_box( $post ) {
	wp_nonce_field( 'john_project_save_see_also', 'john_project_see_also_nonce' );
	?>
	<p>
		<label for="john-project-see-also"><?php esc_html_e( 'Page to suggest in the sidebar', 'john-project' ); ?></label>
		<?php
		wp_dropdown_pages(
			[
				'id'                => 'john-project-see-also',
				'name'              => 'john_project_see_also',
				'selected'          => (int) get_post_meta( $post->ID, '_john_project_see_also', true ),
				'exclude'           => [ $post->ID ],
				'show_option_none'  => __( '-- None --', 'john-project' ),
				'option_none_value' => '0',
				'class'             => 'widefat',
			]
		);
		?>
	</p>
	<p class="description"><?php esc_html_e( 'The card shows that page\'s title and excerpt.', 'john-project' ); ?></p>
	<?php
}

add_action( 'save_post_page', 'john_project_save_see_also' );
function john_project_save_see_also( $post_id ) {
	$nonce = isset( $_POST['john_project_see_also_nonce'] ) ? sanitize_key( wp_unslash( $_POST['john_project_see_also_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'john_project_save_see_also' ) ) {
		return;
	}
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$see_also_id = isset( $_POST['john_project_see_also'] ) ? absint( $_POST['john_project_see_also'] ) : 0;
	john_project_update_or_delete_meta( $post_id, '_john_project_see_also', $see_also_id ? (string) $see_also_id : '' );
}

function john_project_page_url( $slug ) {
	$pages = get_posts(
		[
			'post_type'   => 'page',
			'name'        => $slug,
			'post_status' => 'publish',
			'numberposts' => 1,
		]
	);
	return $pages ? get_permalink( $pages[0] ) : '';
}

// Event post type
//
// Event times are stored as site-local "Y-m-d\TH:i" strings (the value format
// of a datetime-local input), so they sort and compare correctly as strings.
const JOHN_PROJECT_EVENT_TIME_FORMAT = 'Y-m-d\TH:i';
const JOHN_PROJECT_REWRITE_VERSION   = '2';

add_action( 'init', 'john_project_register_event_post_type' );
function john_project_register_event_post_type() {
	register_post_type(
		'event',
		[
			'labels'       => [
				'name'               => __( 'Events', 'john-project' ),
				'singular_name'      => __( 'Event', 'john-project' ),
				'add_new_item'       => __( 'Add New Event', 'john-project' ),
				'edit_item'          => __( 'Edit Event', 'john-project' ),
				'new_item'           => __( 'New Event', 'john-project' ),
				'view_item'          => __( 'View Event', 'john-project' ),
				'search_items'       => __( 'Search Events', 'john-project' ),
				'not_found'          => __( 'No events found.', 'john-project' ),
				'not_found_in_trash' => __( 'No events found in Trash.', 'john-project' ),
				'all_items'          => __( 'All Events', 'john-project' ),
				'archives'           => __( 'Events', 'john-project' ),
			],
			'public'       => true,
			'has_archive'  => 'news-events/events',
			'rewrite'      => [
				'slug'       => 'news-events/events',
				'with_front' => false,
			],
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-calendar-alt',
			'supports'     => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
		]
	);
}

// Deploys are a git pull with no activation step, so new rewrite rules (the
// /events/ URLs) are flushed once per JOHN_PROJECT_REWRITE_VERSION instead.
add_action( 'init', 'john_project_flush_rewrite_rules_once', 20 );
function john_project_flush_rewrite_rules_once() {
	if ( get_option( 'john_project_rewrite_version' ) === JOHN_PROJECT_REWRITE_VERSION ) {
		return;
	}
	flush_rewrite_rules();
	update_option( 'john_project_rewrite_version', JOHN_PROJECT_REWRITE_VERSION );
}

add_action( 'after_switch_theme', 'john_project_reset_rewrite_version' );
function john_project_reset_rewrite_version() {
	delete_option( 'john_project_rewrite_version' );
}

function john_project_parse_event_time( $value ) {
	if ( ! is_string( $value ) || '' === $value ) {
		return null;
	}
	$time = DateTimeImmutable::createFromFormat( '!' . JOHN_PROJECT_EVENT_TIME_FORMAT, $value, wp_timezone() );
	return $time ?: null;
}

// Event details meta box
add_action( 'add_meta_boxes_event', 'john_project_add_event_details_box' );
function john_project_add_event_details_box() {
	add_meta_box(
		'john-project-event-details',
		__( 'Event details', 'john-project' ),
		'john_project_render_event_details_box',
		'event',
		'side',
		'high'
	);
}

function john_project_render_event_details_box( $post ) {
	wp_nonce_field( 'john_project_save_event_details', 'john_project_event_details_nonce' );
	$start    = get_post_meta( $post->ID, '_event_start', true );
	$end      = get_post_meta( $post->ID, '_event_end', true );
	$location = get_post_meta( $post->ID, '_event_location', true );
	?>
	<p>
		<label for="john-project-event-start"><?php esc_html_e( 'Start (required)', 'john-project' ); ?></label><br>
		<input type="datetime-local" id="john-project-event-start" name="john_project_event_start" value="<?php echo esc_attr( $start ); ?>">
	</p>
	<p>
		<label for="john-project-event-end"><?php esc_html_e( 'End', 'john-project' ); ?></label><br>
		<input type="datetime-local" id="john-project-event-end" name="john_project_event_end" value="<?php echo esc_attr( $end ); ?>">
	</p>
	<p>
		<label for="john-project-event-location"><?php esc_html_e( 'Location', 'john-project' ); ?></label><br>
		<input type="text" class="widefat" id="john-project-event-location" name="john_project_event_location" value="<?php echo esc_attr( $location ); ?>">
	</p>
	<p class="description">
		<?php
		/* translators: %s: the site's timezone, e.g. Europe/Helsinki. */
		printf( esc_html__( 'Times are in the site timezone (%s). Events without a start time are not listed.', 'john-project' ), esc_html( wp_timezone_string() ) );
		?>
	</p>
	<?php
}

add_action( 'save_post_event', 'john_project_save_event_details' );
function john_project_save_event_details( $post_id ) {
	$nonce = isset( $_POST['john_project_event_details_nonce'] ) ? sanitize_key( wp_unslash( $_POST['john_project_event_details_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'john_project_save_event_details' ) ) {
		return;
	}
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$start    = john_project_event_time_from_request( 'john_project_event_start' );
	$end      = john_project_event_time_from_request( 'john_project_event_end' );
	$location = isset( $_POST['john_project_event_location'] ) ? sanitize_text_field( wp_unslash( $_POST['john_project_event_location'] ) ) : '';

	john_project_update_or_delete_meta( $post_id, '_event_start', $start );
	john_project_update_or_delete_meta( $post_id, '_event_end', $end );
	john_project_update_or_delete_meta( $post_id, '_event_location', $location );
	// '0' sorts before every real time, so events without a start are never listed as upcoming.
	john_project_update_or_delete_meta( $post_id, '_event_visible_until', $start ? max( $start, $end ) : '0' );
}

function john_project_event_time_from_request( $field_name ) {
	if ( ! isset( $_POST[ $field_name ] ) ) {
		return '';
	}
	$time = john_project_parse_event_time( sanitize_text_field( wp_unslash( $_POST[ $field_name ] ) ) );
	return $time ? $time->format( JOHN_PROJECT_EVENT_TIME_FORMAT ) : '';
}

function john_project_update_or_delete_meta( $post_id, $key, $value ) {
	if ( '' === $value ) {
		delete_post_meta( $post_id, $key );
	} else {
		update_post_meta( $post_id, $key, $value );
	}
}

// Event lists: upcoming, past, and news mixed with upcoming events
//
// Query Loop blocks with the class john-upcoming-events, john-past-events or
// john-news-and-events list events by their own times. The class is read when
// the query block starts rendering and kept for all of its inner blocks,
// because pagination and no-results blocks each rebuild the query through the
// same filter.
$GLOBALS['john_project_current_event_list'] = null;

add_filter( 'pre_render_block', 'john_project_detect_event_list', 10, 2 );
function john_project_detect_event_list( $pre_render, $parsed_block ) {
	if ( 'core/query' !== $parsed_block['blockName'] ) {
		return $pre_render;
	}
	$classes = explode( ' ', $parsed_block['attrs']['className'] ?? '' );
	if ( in_array( 'john-upcoming-events', $classes, true ) ) {
		$GLOBALS['john_project_current_event_list'] = 'upcoming';
	} elseif ( in_array( 'john-past-events', $classes, true ) ) {
		$GLOBALS['john_project_current_event_list'] = 'past';
	} elseif ( in_array( 'john-news-and-events', $classes, true ) ) {
		$GLOBALS['john_project_current_event_list'] = 'news-and-events';
	} else {
		$GLOBALS['john_project_current_event_list'] = null;
	}
	return $pre_render;
}

add_filter( 'query_loop_block_query_vars', 'john_project_event_list_query_vars' );
function john_project_event_list_query_vars( $query ) {
	$event_list = $GLOBALS['john_project_current_event_list'];
	if ( ! $event_list ) {
		return $query;
	}

	if ( 'news-and-events' === $event_list ) {
		// News posts have no _event_visible_until, so they match the first clause.
		$query['post_type']  = [ 'post', 'event' ];
		$query['meta_query'] = [
			'relation' => 'OR',
			[
				'key'     => '_event_visible_until',
				'compare' => 'NOT EXISTS',
			],
			[
				'key'     => '_event_visible_until',
				'value'   => current_time( JOHN_PROJECT_EVENT_TIME_FORMAT ),
				'compare' => '>',
			],
		];
		return $query;
	}

	$is_upcoming         = 'upcoming' === $event_list;
	$query['post_type']  = 'event';
	$query['meta_query'] = [
		'start' => [
			'key'     => '_event_start',
			'compare' => 'EXISTS',
		],
		[
			'key'     => '_event_visible_until',
			'value'   => current_time( JOHN_PROJECT_EVENT_TIME_FORMAT ),
			'compare' => $is_upcoming ? '>' : '<=',
		],
	];
	$query['orderby']    = [ 'start' => $is_upcoming ? 'ASC' : 'DESC' ];
	return $query;
}

// Block bindings, so .html templates can show post and event details and
// links whose URLs depend on settings. Bound values are passed through
// wp_kses_post() by core, not escaped, so they are escaped here.
add_action( 'init', 'john_project_register_block_bindings' );
function john_project_register_block_bindings() {
	register_block_bindings_source(
		'john-project/post',
		[
			'label'              => __( 'Post and event details', 'john-project' ),
			'get_value_callback' => 'john_project_get_post_binding_value',
			'uses_context'       => [ 'postId' ],
		]
	);
	register_block_bindings_source(
		'john-project/links',
		[
			'label'              => __( 'Site links', 'john-project' ),
			'get_value_callback' => 'john_project_get_link_binding_value',
		]
	);
}

function john_project_get_post_binding_value( $source_args, $block_instance ) {
	$post = get_post( $block_instance->context['postId'] ?? get_the_ID() );
	if ( ! $post ) {
		return null;
	}
	$is_event = 'event' === $post->post_type;
	switch ( $source_args['key'] ?? '' ) {
		case 'type':
			return $is_event ? esc_html__( 'Event', 'john-project' ) : esc_html__( 'News', 'john-project' );
		case 'date':
			return $is_event ? john_project_format_event_dates( $post->ID ) : esc_html( get_the_date( 'j F Y', $post ) );
		case 'when':
			return john_project_format_event_when( $post->ID );
		case 'location':
			return esc_html( get_post_meta( $post->ID, '_event_location', true ) );
		case 'lead':
			return has_excerpt( $post ) ? esc_html( get_the_excerpt( $post ) ) : '';
		case 'featured-image-caption':
			return esc_html( wp_get_attachment_caption( get_post_thumbnail_id( $post ) ) );
	}
	return null;
}

function john_project_event_times( $post_id ) {
	return [
		john_project_parse_event_time( get_post_meta( $post_id, '_event_start', true ) ),
		john_project_parse_event_time( get_post_meta( $post_id, '_event_end', true ) ),
	];
}

function john_project_format_event_dates( $post_id ) {
	[ $start, $end ] = john_project_event_times( $post_id );
	if ( ! $start ) {
		return '';
	}
	$end_date = esc_html( wp_date( 'j F Y', ( $end ?: $start )->getTimestamp() ) );
	if ( ! $end || $start->format( 'Y-m-d' ) === $end->format( 'Y-m-d' ) ) {
		return $end_date;
	}
	if ( $start->format( 'Y-m' ) === $end->format( 'Y-m' ) ) {
		return esc_html( wp_date( 'j', $start->getTimestamp() ) ) . '&ndash;' . $end_date;
	}
	$start_format = $start->format( 'Y' ) === $end->format( 'Y' ) ? 'j F' : 'j F Y';
	return esc_html( wp_date( $start_format, $start->getTimestamp() ) ) . ' &ndash; ' . $end_date;
}

function john_project_format_event_when( $post_id ) {
	[ $start, $end ] = john_project_event_times( $post_id );
	if ( ! $start ) {
		return '';
	}
	$date_time_format = 'j F Y, ' . get_option( 'time_format' );

	$when = esc_html( wp_date( $date_time_format, $start->getTimestamp() ) );
	if ( $end ) {
		$end_format = $start->format( 'Y-m-d' ) === $end->format( 'Y-m-d' ) ? get_option( 'time_format' ) : $date_time_format;
		$when      .= ' &ndash; ' . esc_html( wp_date( $end_format, $end->getTimestamp() ) );
	}
	return $when;
}

function john_project_get_link_binding_value( $source_args ) {
	$posts_page_id = (int) get_option( 'page_for_posts' );
	switch ( $source_args['key'] ?? '' ) {
		case 'news':
			return esc_url( $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' ) );
		case 'news-and-events':
			$section_id = $posts_page_id ? wp_get_post_parent_id( $posts_page_id ) : 0;
			return esc_url( $section_id ? get_permalink( $section_id ) : get_post_type_archive_link( 'event' ) );
		case 'events':
			return esc_url( get_post_type_archive_link( 'event' ) );
		case 'page':
			return esc_url( john_project_page_url( $source_args['slug'] ?? '' ) ?: '#' );
	}
	return null;
}

// Details a post doesn't have (no location, no lead) would otherwise leave an empty <p> behind.
add_filter( 'render_block_core/paragraph', 'john_project_remove_empty_bound_paragraph', 10, 2 );
function john_project_remove_empty_bound_paragraph( $block_content, $block ) {
	$source = $block['attrs']['metadata']['bindings']['content']['source'] ?? '';
	if ( 'john-project/post' === $source && '' === trim( wp_strip_all_tags( $block_content ) ) ) {
		return '';
	}
	return $block_content;
}

// Shortcodes for sidebar and sharing markup that blocks can't express
add_shortcode( 'john_section_nav', 'john_project_section_nav_shortcode' );
function john_project_section_nav_shortcode() {
	$page = get_queried_object();
	if ( ! $page instanceof WP_Post || 'page' !== $page->post_type ) {
		return '';
	}
	$ancestor_ids = get_post_ancestors( $page );
	$root_id      = $ancestor_ids ? end( $ancestor_ids ) : $page->ID;
	$children     = get_pages(
		[
			'parent'      => $root_id,
			'sort_column' => 'menu_order,post_title',
		]
	);
	if ( ! $children ) {
		return '';
	}

	$items = '';
	foreach ( array_merge( [ get_post( $root_id ) ], $children ) as $section_page ) {
		$is_current = $section_page->ID === $page->ID;
		$items     .= sprintf(
			'<li><a href="%s"%s>%s</a></li>',
			esc_url( get_permalink( $section_page ) ),
			$is_current ? ' aria-current="page"' : '',
			esc_html( get_the_title( $section_page ) )
		);
	}
	return sprintf(
		'<nav class="john-section-nav" aria-labelledby="john-section-nav-title"><p class="john-section-nav__title" id="john-section-nav-title">%s</p><ul>%s</ul></nav>',
		esc_html__( 'In this section', 'john-project' ),
		$items
	);
}

add_shortcode( 'john_see_also', 'john_project_see_also_shortcode' );
function john_project_see_also_shortcode() {
	$see_also_id = (int) get_post_meta( get_queried_object_id(), '_john_project_see_also', true );
	// get_post( 0 ) would return the current page, not nothing.
	$see_also = $see_also_id ? get_post( $see_also_id ) : null;
	if ( ! $see_also || 'publish' !== $see_also->post_status ) {
		return '';
	}
	$url = esc_url( get_permalink( $see_also ) );
	return sprintf(
		'<aside class="john-see-also"><p class="john-see-also__eyebrow">%s</p><p class="john-see-also__title"><a href="%s">%s</a></p><p>%s</p><p class="john-see-also__link"><a href="%s" aria-label="%s">%s <span aria-hidden="true">&rarr;</span></a></p></aside>',
		esc_html__( 'See also', 'john-project' ),
		$url,
		esc_html( get_the_title( $see_also ) ),
		esc_html( get_the_excerpt( $see_also ) ),
		$url,
		/* translators: %s: page title. */
		esc_attr( sprintf( __( 'Read more: %s', 'john-project' ), get_the_title( $see_also ) ) ),
		esc_html__( 'Read more', 'john-project' )
	);
}

add_shortcode( 'john_share', 'john_project_share_shortcode' );
function john_project_share_shortcode() {
	$url   = get_permalink();
	$title = get_the_title();
	if ( ! $url ) {
		return '';
	}
	$icon = static function ( $service ) {
		return function_exists( 'block_core_social_link_get_icon' ) ? block_core_social_link_get_icon( $service ) : '';
	};
	$link_icon = '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/></svg>';

	$html  = '<div class="john-share"><span class="john-share__label">' . esc_html__( 'Share', 'john-project' ) . '</span>';
	$html .= sprintf(
		'<a class="john-share__button" href="%s" target="_blank" rel="noopener" aria-label="%s">%s</a>',
		esc_url( 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $url ) ),
		esc_attr__( 'Share on LinkedIn', 'john-project' ),
		$icon( 'linkedin' )
	);
	$html .= sprintf(
		'<a class="john-share__button" href="%s" target="_blank" rel="noopener" aria-label="%s">%s</a>',
		esc_url( 'https://bsky.app/intent/compose?text=' . rawurlencode( $title . ' ' . $url ) ),
		esc_attr__( 'Share on Bluesky', 'john-project' ),
		$icon( 'bluesky' )
	);
	$html .= sprintf(
		'<button type="button" class="john-share__button john-share__copy" data-url="%s" aria-label="%s">%s</button>',
		esc_url( $url ),
		esc_attr__( 'Copy link', 'john-project' ),
		$link_icon
	);
	static $script_printed = false;
	if ( ! $script_printed ) {
		$script_printed = true;
		$html          .= wp_get_inline_script_tag(
			"document.addEventListener('click', function (event) {
				var button = event.target.closest('.john-share__copy');
				if (!button || !navigator.clipboard) return;
				navigator.clipboard.writeText(button.dataset.url).then(function () {
					button.classList.add('is-copied');
					button.setAttribute('aria-label', " . wp_json_encode( __( 'Link copied', 'john-project' ) ) . ");
				});
			});"
		);
	}
	return $html . '</div>';
}

// Navigation: news and events aren't pages in the menu, so mark the menu item
// for the posts page's parent ("News & Events") as the current section.
add_filter( 'render_block_core/navigation-submenu', 'john_project_mark_news_section', 10, 2 );
add_filter( 'render_block_core/navigation-link', 'john_project_mark_news_section', 10, 2 );
function john_project_mark_news_section( $block_content, $block ) {
	$is_news_or_events = is_home() || is_singular( [ 'post', 'event' ] ) || is_post_type_archive( 'event' );
	$posts_page_id     = (int) get_option( 'page_for_posts' );
	$section_id        = $posts_page_id ? ( wp_get_post_parent_id( $posts_page_id ) ?: $posts_page_id ) : 0;
	if ( ! $is_news_or_events || ! $section_id || (int) ( $block['attrs']['id'] ?? 0 ) !== $section_id ) {
		return $block_content;
	}
	$tags = new WP_HTML_Tag_Processor( $block_content );
	if ( $tags->next_tag( 'li' ) ) {
		$tags->add_class( 'current-menu-ancestor' );
	}
	return $tags->get_updated_html();
}

// Breadcrumbs: put news and events under the posts page's parent ("News &
// Events"), and leave out post categories, to match the site map.
add_filter( 'block_core_breadcrumbs_items', 'john_project_breadcrumb_items' );
function john_project_breadcrumb_items( $items ) {
	$posts_page_id = (int) get_option( 'page_for_posts' );
	$is_news       = is_home() || is_singular( 'post' );
	$is_events     = is_post_type_archive( 'event' ) || is_singular( 'event' );
	if ( ! $posts_page_id || ! ( $is_news || $is_events ) || empty( $items ) ) {
		return $items;
	}

	$trail = [ $items[0] ];
	foreach ( array_reverse( get_post_ancestors( $posts_page_id ) ) as $ancestor_id ) {
		$trail[] = [
			'label' => get_the_title( $ancestor_id ),
			'url'   => get_permalink( $ancestor_id ),
		];
	}

	$listing_label = $is_news ? get_the_title( $posts_page_id ) : get_post_type_object( 'event' )->labels->archives;
	if ( is_singular() ) {
		$trail[] = [
			'label' => $listing_label,
			'url'   => $is_news ? get_permalink( $posts_page_id ) : get_post_type_archive_link( 'event' ),
		];
		$trail[] = [
			'label'      => get_the_title(),
			'allow_html' => true,
		];
	} else {
		$trail[] = [ 'label' => $listing_label ];
	}
	return $trail;
}
