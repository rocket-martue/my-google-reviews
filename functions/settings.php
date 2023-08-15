<?php
/**
 * setting
 * @package my google reviews
 */

function google_reviews_settings_init() {
	add_settings_section(
		'google_reviews_section',
		'Google Reviews Settings',
		'google_reviews_section_callback',
		'general'
	);

	add_settings_field(
		'google_reviews_api_key',
		'API Key',
		'google_reviews_api_key_callback',
		'general',
		'google_reviews_section'
	);

	register_setting( 'general', 'google_reviews_api_key' );
}

function google_reviews_section_callback() {
	echo '<p>Enter your Google Places API Key below:</p>';
}

function google_reviews_api_key_callback() {
	$api_key = get_option( 'google_reviews_api_key' );

	echo '<input type="text" name="google_reviews_api_key" value="' . esc_attr( $api_key ) . '" class="regular-text">';
}

add_action( 'admin_init', 'google_reviews_settings_init' );
