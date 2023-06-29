<?php
/**
 * Plugin Name: My Google Reviews
 * Description: Fetches Google reviews using the Places API and displays them on your WordPress site.
 */

// Enqueue necessary scripts and styles
function enqueue_google_reviews_scripts() {
	wp_enqueue_style( 'google-reviews-style', plugins_url( 'google-reviews.min.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'enqueue_google_reviews_scripts' );

// Display Google reviews
function display_google_reviews( $place_id ) {
	$api_key   = 'Your_API_Key';
	$language  = 'ja'; // 言語を日本語に設定

	// Check if the reviews data is available in cache
	$cache_key = 'google_reviews_' . $place_id;
	$reviews_data = get_transient( $cache_key );

	if ( $reviews_data !== false ) {
		// Display reviews from cache
		display_reviews_from_data( $reviews_data );
		return;
	}

	// Make API request to fetch place details
	$place_details_url      = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$place_id}&fields=name,rating,user_ratings_total,formatted_address&language={$language}&key={$api_key}";
	$place_details_response = wp_remote_get( $place_details_url );

	// Check if place details API request was successful
	if ( is_wp_error( $place_details_response ) || wp_remote_retrieve_response_code( $place_details_response ) !== 200 ) {
		echo 'Error retrieving place details.';
		error_log( 'Google Place Details API Error: ' . wp_remote_retrieve_response_message( $place_details_response ) );
		return;
	}

	$place_details_data = json_decode( wp_remote_retrieve_body( $place_details_response ) );

	// Get place details from API response
	$place_name     = isset( $place_details_data->result->name ) ? esc_html( $place_details_data->result->name ) : '';
	$rating         = isset( $place_details_data->result->rating ) ? esc_html( $place_details_data->result->rating ) : '';
	$review_count   = isset( $place_details_data->result->user_ratings_total ) ? esc_html( $place_details_data->result->user_ratings_total ) : '';
	$address        = isset( $place_details_data->result->formatted_address ) ? esc_html( $place_details_data->result->formatted_address ) : '';
	$logo_icon      = isset( $place_details_data->result->icon ) ? esc_url( $place_details_data->result->icon ) : '';

	// Make API request to fetch reviews
	$reviews_url      = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$place_id}&fields=rating,reviews&language={$language}&key={$api_key}";
	$reviews_response = wp_remote_get( $reviews_url );

	// Check if reviews API request was successful
	if ( is_wp_error( $reviews_response ) || wp_remote_retrieve_response_code( $reviews_response ) !== 200 ) {
		echo 'Error retrieving reviews.';
		error_log( 'Google Reviews API Error: ' . wp_remote_retrieve_response_message( $reviews_response ) );
		return;
	}

	$reviews_data = json_decode( wp_remote_retrieve_body( $reviews_response ) );

	// Check if reviews are available
	if ( isset( $reviews_data->result->reviews ) && ! empty( $reviews_data->result->reviews ) ) {
		$reviews = $reviews_data->result->reviews;

		// Save the reviews data to cache for 24 hours
		set_transient( $cache_key, $reviews, 24 * HOUR_IN_SECONDS );

		// Display reviews
		display_reviews_from_data( $reviews );
	} else {
		echo 'No reviews found.';
	}
}

// Function to display reviews from data
function display_reviews_from_data( $reviews ) {
	// Start output buffering
	ob_start();

	// Display place details and reviews
	?>
	<h2 class="heading">
		<?php if ( $logo_icon ) : ?>
			<span class="icon"><img src="<?php echo $logo_icon; ?>" alt="<?php echo $place_name; ?> Logo"></span>
		<?php endif; ?>
		<?php echo $place_name; ?> Google口コミレビュー
	</h2>
	<p class="vicinity"><?php echo $address; ?></p>
	<p class="rating"><span class="number"><?php echo esc_html( $rating ); ?></span> <span class="rating-star""><?php echo get_rating_stars( $rating ); ?></span> <a href="https://search.google.com/local/reviews?placeid=<?php echo $place_id; ?>" target="_blank" rel="nofollow" class="count"><?php echo $review_count; ?> reviews</a></p>
	<?php

	// Make API request to fetch reviews
	$reviews_url      = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$place_id}&fields=rating,reviews&language={$language}&key={$api_key}";
	$reviews_response = wp_remote_get( $reviews_url );

	// Check if reviews API request was successful
	if ( is_wp_error( $reviews_response ) || wp_remote_retrieve_response_code( $reviews_response ) !== 200 ) {
		echo 'Error retrieving reviews.';
		error_log( 'Google Reviews API Error: ' . wp_remote_retrieve_response_message( $reviews_response ) );
		return;
	}

	$reviews_data = json_decode( wp_remote_retrieve_body( $reviews_response ) );

	// Check if reviews are available
	if ( isset( $reviews_data->result->reviews ) && ! empty( $reviews_data->result->reviews ) ) {
		$reviews = $reviews_data->result->reviews;

		// Display reviews
		?>
		<ul class="listing"> <!-- Modified: Opening ul tag -->
			<?php foreach ( $reviews as $index => $review ) : ?>
				<?php
				$rating                 = $review->rating;
				$comment                = $review->text;
				$author                 = $review->author_name;
				$date                   = date( 'F j, Y', strtotime( $review->time ) );
				$author_url             = isset( $review->author_url ) ? esc_url( $review->author_url ) : '';
				$author_avatar          = isset( $review->profile_photo_url ) ? esc_url( $review->profile_photo_url ) : '';
				$relative_time_description = isset( $review->relative_time_description ) ? esc_html( $review->relative_time_description ) : '';
				?>
				<li class="rating-<?php echo $rating; ?>" data-index="<?php echo $index; ?>"> <!-- Modified: Opening li tag -->
					<span class="author-avatar"><a href="<?php echo $author_url; ?>" target="_blank" rel="nofollow"><img src="<?php echo $author_avatar; ?>" alt="Avatar"></a></span>
					<span class="review-meta">
						<span class="author-name"><a href="<?php echo $author_url; ?>" target="_blank" rel="nofollow"><?php echo $author; ?></a></span>
						<span class="rating">
							<?php
							for ( $i = 0; $i < $rating; $i++ ) {
								echo '★';
							}
							?>
						</span>
						<span class="relative-time-description"><?php echo $relative_time_description; ?></span>
					</span>
					<div class="text">
						<?php echo esc_html( $comment ); ?>
					</div>
				</li> <!-- Modified: Closing li tag -->
			<?php endforeach; ?>
		</ul> <!-- Modified: Closing ul tag -->
		<?php
	} else {
		echo 'No reviews found.';
	}

	// Output the content
		?>
	<div id="google-business-reviews-rating" class="google-business-reviews-rating gmbrr contrast stars-css">
		<?php
		echo $output;
		?>
	</div>
	<div class="review-link">
		<a href="https://search.google.com/local/reviews?placeid=<?php echo $place_id; ?>" class="button" target="_blank" rel="nofollow">もっとみる ＞＞</a>
	</div>
		<?php
}

// Function to generate rating stars based on rating value
function get_rating_stars( $rating ) {
	$full_star   = '★';
	$empty_star  = '☆';
	$rating_stars = '';

	$rating          = floatval( $rating );
	$rounded_rating  = round( $rating * 2 ) / 2;

	for ( $i = 1; $i <= 5; $i++ ) {
		if ( $i <= $rounded_rating ) {
			$rating_stars .= $full_star;
		} else {
			$rating_stars .= $empty_star;
		}
	}

	return $rating_stars;
}

// Display Google reviews for Place A
function display_google_reviews_place_a() {
	$place_id = 'PLACE_A_ID';
	display_google_reviews( $place_id );
}
add_action( 'display_google_reviews_place_a', 'display_google_reviews_place_a' );

// Display Google reviews for Place B
function display_google_reviews_place_b() {
	$place_id = 'PLACE_B_ID';
	display_google_reviews( $place_id );
}
add_action( 'display_google_reviews_place_b', 'display_google_reviews_place_b' );

// Display Google reviews for Place C
function display_google_reviews_place_c() {
	$place_id = 'PLACE_C_ID';
	display_google_reviews( $place_id );
}
add_action( 'display_google_reviews_place_c', 'display_google_reviews_place_c' );

// Display Google reviews for Place D
function display_google_reviews_place_d() {
	$place_id = 'PLACE_D_ID';
	display_google_reviews( $place_id );
}
add_action( 'display_google_reviews_place_d', 'display_google_reviews_place_d' );

// Display Google reviews for Place E
function display_google_reviews_place_e() {
	$place_id = 'PLACE_E_ID';
	display_google_reviews( $place_id );
}
add_action( 'display_google_reviews_place_e', 'display_google_reviews_place_e' );
