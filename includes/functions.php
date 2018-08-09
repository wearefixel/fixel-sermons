<?php

function fs_get_series_order() {
	if ( false == ( $series_order = get_transient( 'fs_series_order' ) ) ) {
		$sermons = new WP_Query( [
			'post_type' => 'fs_sermon',
			'posts_per_page' => -1,
			'no_found_rows' => true,
			'fields' => 'ids',
		] );

		$series_order = [];

		foreach ( $sermons->posts as $sermon_id ) {
			$series = fs_get_series( $sermon_id );

			if ( ! in_array( $series->term_id, $series_order ) ) {
				$series_order[] = $series->term_id;
			}
		}

		set_transient( 'fs_series_order', $series_order );
	}

	return $series_order;
}

function fs_get_ordered_series( $args = [] ) {
	$args = wp_parse_args( $args, [
		'number' => get_option( 'posts_per_page' ),
		'offset' => 0,
	] );

	$args = array_map( 'intval', $args );

	$series = [];
	foreach ( fs_get_series_order() as $term_id ) {
		$series[] = get_term_by( 'id', $term_id, 'fs_series' );
	}

	$series = array_slice( $series, $args['offset'] );

	if ( $args['number'] > -1 ) {
		$series = array_slice( $series, 0, $args['number'] );
	}

	return $series;
}

function fs_pagenum() {
	return get_query_var( 'paged' ) ?: get_query_var( 'page' ) ?: 1;
}

function fs_get_term( $post_id, $taxonomy ) {
	$terms = wp_get_post_terms( $post_id, $taxonomy );
	return array_shift( $terms );
}

function fs_get_series( $sermon_id ) {
	return fs_get_term( $sermon_id, 'fs_series' );
}

function fs_get_speaker( $sermon_id ) {
	return fs_get_term( $sermon_id, 'fs_speaker' );
}

function fs_get_book( $sermon_id ) {
	return fs_get_term( $sermon_id, 'fs_book' );
}

function fs_get_topic( $sermon_id ) {
	return fs_get_term( $sermon_id, 'fs_topic' );
}
