<?php
/**
 * shortcode
 * @package my google reviews
 */
function google_reviews_shortcode( $atts ) {
	
	$atts = shortcode_atts(
		array(
			'place_id' => '',
			'language' => 'ja' // デフォルトの言語は英語
		),
		$atts
	);

	$place_id = $atts['place_id'];
	$language = substr( get_locale(), 0, 2 ); // WordPress の言語設定から言語コードを取得

	if ( empty( $place_id ) ) {
		return 'Error: Place ID is required.';
	}

	$api_key = get_option( 'google_reviews_api_key' );

	if ( empty( $api_key ) ) {
		return 'Error: API Key is not set.';
	}

	ob_start(); // バッファリング開始

	get_google_reviews( $place_id );

	return ob_get_clean(); // バッファの内容を返す
}
add_shortcode( 'google_reviews', 'google_reviews_shortcode' );
