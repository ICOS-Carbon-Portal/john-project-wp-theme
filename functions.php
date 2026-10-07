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

// Event post type
//
// Event times are stored as site-local "Y-m-d\TH:i" strings (the value format
// of a datetime-local input), so they sort and compare correctly as strings.
const JOHN_PROJECT_EVENT_TIME_FORMAT = 'Y-m-d\TH:i';
const JOHN_PROJECT_REWRITE_VERSION   = '1';

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
			'has_archive'  => 'events',
			'rewrite'      => [
				'slug'       => 'events',
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
	john_project_update_or_delete_meta( $post_id, '_event_visible_until', $start ? max( $start, $end ) : '' );
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

// Event lists: upcoming and past
//
// Query Loop blocks with the class john-upcoming-events or john-past-events
// list events by their own times. The class is read when the query block
// starts rendering and kept for all of its inner blocks, because pagination
// and no-results blocks each rebuild the query through the same filter.
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

// Block bindings, so .html templates can show event details and links whose
// URLs depend on settings. Bound values are passed through wp_kses_post() by
// core, not escaped, so they are escaped here.
add_action( 'init', 'john_project_register_block_bindings' );
function john_project_register_block_bindings() {
	register_block_bindings_source(
		'john-project/event',
		[
			'label'              => __( 'Event details', 'john-project' ),
			'get_value_callback' => 'john_project_get_event_binding_value',
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

function john_project_get_event_binding_value( $source_args, $block_instance ) {
	$post_id = $block_instance->context['postId'] ?? get_the_ID();
	switch ( $source_args['key'] ?? '' ) {
		case 'when':
			return john_project_format_event_when( $post_id );
		case 'location':
			return esc_html( get_post_meta( $post_id, '_event_location', true ) );
	}
	return null;
}

function john_project_format_event_when( $post_id ) {
	$start = john_project_parse_event_time( get_post_meta( $post_id, '_event_start', true ) );
	if ( ! $start ) {
		return '';
	}
	$end         = john_project_parse_event_time( get_post_meta( $post_id, '_event_end', true ) );
	$date_format = get_option( 'date_format' );
	$time_format = get_option( 'time_format' );

	$when = esc_html( wp_date( $date_format . ', ' . $time_format, $start->getTimestamp() ) );
	if ( $end ) {
		$end_format = $start->format( 'Y-m-d' ) === $end->format( 'Y-m-d' ) ? $time_format : $date_format . ', ' . $time_format;
		$when      .= ' &ndash; ' . esc_html( wp_date( $end_format, $end->getTimestamp() ) );
	}
	return $when;
}

function john_project_get_link_binding_value( $source_args ) {
	switch ( $source_args['key'] ?? '' ) {
		case 'news':
			$posts_page_id = (int) get_option( 'page_for_posts' );
			return esc_url( $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' ) );
		case 'events':
			return esc_url( get_post_type_archive_link( 'event' ) );
	}
	return null;
}

// An event without a location would otherwise leave an empty <p> behind.
add_filter( 'render_block_core/paragraph', 'john_project_remove_empty_event_paragraph', 10, 2 );
function john_project_remove_empty_event_paragraph( $block_content, $block ) {
	$source = $block['attrs']['metadata']['bindings']['content']['source'] ?? '';
	if ( 'john-project/event' === $source && '' === trim( wp_strip_all_tags( $block_content ) ) ) {
		return '';
	}
	return $block_content;
}
