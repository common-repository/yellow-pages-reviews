<?php
/**
 *  Widget Frontend Display
 *
 * @description: Responsible for the frontend display of the Yellow Pages Reviews
 * @since      : 1.0
 */
?>
	<div class="ypr-<?php echo sanitize_title( $instance['widget_style'] ); ?>-style">

		<?php
		/**
		 * Business Information Header
		 */
		if ( $instance['hide_header'] !== '1' ) {
			?>

			<div class="ypr-business-header ypr-clearfix">

				<div class="ypr-business-avatar" style="background-image: url(<?php echo YPR_PLUGIN_URL, '/assets/images/yp_logo.png' ?>)"></div>

				<div class="ypr-header-content-wrap ypr-clearfix">
					<?php
					$listing_detail = (array) $instance['yp_response']['listingsDetailsResult']['listingsDetails']['listingDetail'][0];

					//Handle website link
					$website = ! empty( $listing_detail['websiteURL'] ) ? esc_url( $listing_detail['websiteURL'] ) : '';
					$yp_page = ! empty( $listing_detail['moreInfoURL'] ) ? $listing_detail['moreInfoURL'] : '';
					//use the location's YP page since they have no website
					if ( empty( $website ) ) {
						$website = ! empty( $yp_page ) ? esc_url( $yp_page ) : '#';
					}
					?>

					<span class="ypr-business-name"><a href="<?php echo $website; ?>" title="<?php echo $listing_detail['businessName']; ?>" <?php echo $instance['target_blank'] . $instance['no_follow']; ?>><span><?php echo $listing_detail['businessName']; ?></span></a></span>

					<?php
					//Overall Reviews
					$overall_rating = isset( $instance['yp_response']['ratingsAndReviewsResult']['metaProperties']['rating'] ) ? $instance['yp_response']['ratingsAndReviewsResult']['metaProperties']['rating'] : '';
					if ( ! empty( $overall_rating ) ) {

						//Rating Graphic & Text
						echo $this->get_star_rating( $overall_rating, null, $instance['hide_out_of_rating'] );

						//Link to leave a review
						echo '<span class="leave-review-link">' . sprintf( __( '%1$sWrite a review%2$s', 'ypr' ), '<a href="' . esc_url( $yp_page ) . '" class="leave-review" target="_blank" ' . $instance['no_follow'] . '>', '</a>' ) . '</span>';

					} ?>

				</div>
			</div>

			<?php
		}

		/**
		 * Yellow Pages Reviews
		 */
		$reviews = $instance['yp_response']['ratingsAndReviewsResult'];

		//Check for reviews
		if ( isset( $reviews ) && ! empty( $reviews['reviews'] ) ) { ?>

			<div class="ypr-reviews-wrap">
				<?php
				$reviews_array = isset( $reviews['reviews']['review'] ) ? $reviews['reviews']['review'] : '';

				//Ensure our data is in an array
				if ( ! is_array( $reviews_array ) ) {
					$reviews_array = array( $reviews['reviews']['review'] );
				}

				//Account for only one review
				if ( ! isset( $reviews_array[0]['rating'] ) ) {
					$reviews_array    = '';
					$reviews_array[0] = isset( $reviews['reviews']['review'] ) ? $reviews['reviews']['review'] : '';
				}

				$counter      = 0;
				$review_limit = isset( $review_limit ) ? $review_limit : 3;

				//Loop Google Places reviews
				foreach ( $reviews_array as $review ) {

					//Set review vars
					$author_name    = isset( $review['reviewer'] ) ? $review['reviewer'] : 'John Doe';
					$overall_rating = isset( $review['rating'] ) ? $review['rating'] : '5';
					$review_subject = isset( $review['reviewSubject'] ) ? $review['reviewSubject'] : '';
					$review_text    = isset( $review['reviewBody'] ) ? $review['reviewBody'] : __( 'No Review Text...', 'ypr' );
					$time           = isset( $review['reviewDate'] ) ? strtotime( $review['reviewDate'] ) : __( 'No Date for Review', 'ypr' );
					$avatar         = isset( $review['avatar'] ) ? $review['avatar'] : YPR_PLUGIN_URL . '/assets/images/mystery-man.png';
					$counter ++;

					//Review filter set OR count limit reached?
					if ( $counter <= $instance['review_limit'] ) {
						?>

						<div class="ypr-review">
							<div class="ypr-review-header ypr-clearfix">
								<div class="ypr-review-avatar">
									<img src="<?php echo $avatar; ?>" alt="<?php echo $author_name; ?>" title="<?php echo $author_name; ?>" />
								</div>
								<div class="ypr-review-info ypr-clearfix">

									<span class="ypr-reviewer-name"><?php echo $author_name; ?></span>

									<?php echo $this->get_star_rating( $overall_rating, $time, $instance['hide_out_of_rating'] ); ?>
								</div>

							</div>

							<div class="ypr-review-content">
								<?php echo wpautop( $review_text ); ?>
							</div>

						</div><!--/.ypr-review -->

					<?php } //endif review filter ?>

				<?php } //end review loop	?>

			</div><!--/.ypr-reviews -->

			<?php
		} else { //No review for this biz
			?>
			<div class="ypr-no-reviews-wrap">
				<p class="no-reviews"><?php echo sprintf( esc_attr__( 'There are no reviews yet for this business. %1$sBe the first to review%2$s', 'ypr' ), '<a href="' . esc_url( $yp_page ) . '" class="leave-review" target="_blank">', '</a>' ); ?></p>
			</div>

			<?php
		} //end review if
		?>

	</div>

<?php

//After widget
echo isset( $args['after_widget'] ) ? $args['after_widget'] : '</div>';
