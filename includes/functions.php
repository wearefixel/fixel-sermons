<?php

function fxs_get_series_order() {
	if (false == ($series_order = get_transient('fxs_series_order'))) {
		$sermons = new WP_Query([
			'post_type' => 'fxs_sermon',
			'posts_per_page' => -1,
			'no_found_rows' => true,
			'fields' => 'ids',
		]);

		$series_order = [];

		foreach ($sermons->posts as $sermon_id) {
			$series = fxs_get_series($sermon_id);

			if ($series && ! in_array($series->term_id, $series_order)) {
				$series_order[] = $series->term_id;
			}
		}

		set_transient('fxs_series_order', $series_order);
	}

	return $series_order;
}

function fxs_get_ordered_series($args = []) {
	$args = wp_parse_args($args, [
		'number' => get_option('posts_per_page'),
		'offset' => 0,
	]);

	$args = array_map('intval', $args);

	$series = [];

	foreach (fxs_get_series_order() as $term_id) {
		$series[] = get_term_by('id', $term_id, 'fxs_series');
	}

	$series = array_slice($series, $args['offset']);

	if ($args['number'] > -1) {
		$series = array_slice($series, 0, $args['number']);
	}

	return $series;
}

function fxs_pagenum() {
	return get_query_var('paged') ?: get_query_var('page') ?: 1;
}

function fxs_get_audio_url($post_id = null) {
	global $ss_podcasting;
	return $ss_podcasting->get_episode_download_link($post_id ?: get_the_ID());
}

function fxs_get_term($post_id, $taxonomy) {
	$terms = wp_get_post_terms($post_id ?: get_the_ID(), $taxonomy);
	return array_shift($terms);
}

function fxs_get_series($post_id = null) {
	return fxs_get_term($post_id, 'fxs_series');
}

function fxs_get_speaker($post_id = null) {
	return fxs_get_term($post_id, 'fxs_speaker');
}

function fxs_get_book($post_id = null) {
	return fxs_get_term($post_id, 'fxs_book');
}

function fxs_get_topic($post_id = null) {
	return fxs_get_term($post_id, 'fxs_topic');
}
