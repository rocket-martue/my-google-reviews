<?php
/**
 * Uninstall the plugin
 *
 * @package my google reviews
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete cache directory and files.
$cache_directory = WP_CONTENT_DIR . '/cache';
if ( is_dir( $cache_directory ) ) {
	$cache_files = glob( $cache_directory . '/*.json' );
	if ( $cache_files ) {
		foreach ( $cache_files as $cache_file ) {
			unlink( $cache_file );
		}
	}
	rmdir( $cache_directory );
}

// Delete plugin options.
delete_option( 'google_reviews_api_key' );
delete_option( 'google_reviews_place_ids' );
