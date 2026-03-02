<?php
/**
 * Settings
 *
 * @package MyGoogleReviews
 */

/**
 * Initialize plugin settings.
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

	register_setting(
		'general',
		'google_reviews_api_key',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
}

/**
 * Callback for Google Reviews settings section.
 */
function google_reviews_section_callback() {
	echo '<p>' . esc_html__( 'Enter your Google Places API Key below:', 'my-google-reviews' ) . '</p>';
}

/**
 * Callback for Google Reviews API Key field.
 */
function google_reviews_api_key_callback() {
	$api_key = get_option( 'google_reviews_api_key' );

	echo '<input type="text" name="google_reviews_api_key" value="' . esc_attr( $api_key ) . '" class="regular-text">';
}

add_action( 'admin_init', 'google_reviews_settings_init' );
