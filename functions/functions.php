<?php
/**
 * functions
 *
 * @package my google reviews
 */

/**
 * Display Google Reviews list.
 *
 * @param array  $place_id
 */
function get_google_reviews( $place_id ) {
	// APIキーの取得
	$api_key = get_option( 'google_reviews_api_key' );

	$language = substr( get_locale(), 0, 2 ); // WordPress の言語設定から言語コードを取得

	$url      = "https://maps.googleapis.com/maps/api/place/details/json?key={$api_key}&place_id={$place_id}&language={$language}&fields=reviews";
	$reviews_data = wp_remote_get( esc_url_raw( $url ) ); // Google Places APIへのリクエストを送信

	/* Will result in $api_response being an array of data,
	parsed from the JSON response of the API listed above */
	$api_response = json_decode( wp_remote_retrieve_body( $reviews_data ), true );

	// Make API request to fetch place details
	$place_details_url      = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$place_id}&fields=name,rating,user_ratings_total,formatted_address&language={$language}&key={$api_key}";
	$place_details_response = wp_remote_get( $place_details_url );

	// Check if place details API request was successful
	if ( is_wp_error( $place_details ) || wp_remote_retrieve_response_code( $place_details_response ) !== 200 ) {
		echo 'Error retrieving place details.';
		error_log( 'Google Place Details API Error: ' . wp_remote_retrieve_response_message( $place_details_response ) );
		return;
	}

	$place_data = json_decode( wp_remote_retrieve_body( $place_details_response ) );

	// Get place details from API response
	$place_name     = isset( $place_data->result->name ) ? $place_data->result->name : '';
	$rating         = isset( $place_data->result->rating ) ? $place_data->result->rating : '';
	$review_count   = isset( $place_data->result->user_ratings_total ) ? $place_data->result->user_ratings_total : '';
	$address        = isset( $place_data->result->formatted_address ) ? $place_data->result->formatted_address : '';
	$logo_icon      = isset( $place_data->result->icon ) ? $place_data->result->icon : '';

	// Start output buffering
	ob_start();

	// レビューデータが正常に取得できたか確認
	if ( $api_response ) {

		// Display place details and reviews
		?>
	<h2 class="heading">
		<?php if ( $logo_icon ) { ?><span class="icon"><img src="<?php echo esc_url( $logo_icon ); ?>" alt="<?php echo esc_attr( $place_name ); ?> Logo"></span><?php } ?>
		<?php echo esc_html( $place_name ); ?> Google口コミレビュー
	</h2>
	<p class="vicinity"><?php echo esc_html( $address ); ?></p>
	<p class="rating"><span class="number"><?php echo esc_html( $rating ); ?></span> <span class="rating-star"><?php echo generate_star_rating( $rating ); ?></span> <a href="https://search.google.com/local/reviews?placeid=<?php echo $place_id; ?>" target="_blank" rel="nofollow" class="count"><?php echo $review_count; ?> reviews</a></p>
		<?php
		if ( isset( $api_response['result']['reviews'] ) && ! empty( $api_response['result']['reviews'] ) ) {
			$reviews_list = $api_response['result']['reviews'];
			?>
	<ul class="listing">
			<?php
			foreach ( $reviews_list as $index => $review ) :
				$author                    = $review['author_name'];
				$author_url                = $review['author_url'];
				$author_avatar             = $review['profile_photo_url'];
				$rating                    = $review['rating'];
				$relative_time_description = $review['relative_time_description'];
				$comment                   = $review['text'];
				?>
		<li class="rating-<?php echo $rating; ?>" data-index="<?php echo $index; ?>">
			<span class="author-avatar">
				<a href="<?php echo $author_url; ?>" target="_blank" rel="nofollow">
					<img src="<?php echo $author_avatar; ?>" alt="Avatar">
				</a>
			</span>
			<span class="review-meta">
				<span class="author-name">
						<a href="<?php echo $author_url; ?>" target="_blank" rel="nofollow"><?php echo $author; ?></a>
					</span>
				<span class="rating">
					<?php echo generate_star_rating( $rating ); ?>
				</span>
				<span class="relative-time-description"><?php echo $relative_time_description; ?></span>
			</span>
			<div class="text">
				<?php echo esc_html( $comment ); ?>
			</div>
		</li>
			<?php endforeach; ?>
	</ul>
		<?php
	} else {
		// レビューデータが存在しない場合のメッセージを表示
		echo '<p>No reviews found.</p>';
	}

	// Get buffered content and clean the buffer
	$output = ob_get_clean();
	// Output the content
	}
	?>
<div id="google-business-reviews-rating" class="google-business-reviews-rating gmbrr contrast stars-css">
	<?php
	echo $output;
	?>
	<p class="attribution"><span class="powered-by-google" title="Powered by Google"></span></p>
	<div class="review-link">
		<a href="https://search.google.com/local/reviews?placeid=<?php echo $place_id; ?>" class="button" target="_blank" rel="nofollow">もっとみる ＞＞</a>
	</div>
</div>
	<?php
}

/**
 * Generate star rating HTML.
 *
 * @param float $rating The rating value.
 *
 * @return string The generated star rating HTML.
 */
function generate_star_rating( $rating ) {
	$stars       = '';
	$full_star   = '<span class="star"></span>';
	$half_star   = '<span class="star half"></span>';
	$empty_star  = '<span class="star empty"></span>';

	$rating      = floatval( $rating );
	$full_stars  = floor( $rating );
	$has_half_star = false;

	if ( $rating - $full_stars >= 0.5 ) {
		$has_half_star = true;
	}

	for ( $i = 0; $i < $full_stars; $i++ ) {
		$stars .= $full_star;
	}

	if ( $has_half_star ) {
		$stars .= $half_star;
	}

	$remaining_stars = 5 - ceil( $rating );

	for ( $i = 0; $i < $remaining_stars; $i++ ) {
		$stars .= $empty_star;
	}

	return $stars;
}
