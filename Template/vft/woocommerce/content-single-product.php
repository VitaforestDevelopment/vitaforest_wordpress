<?php
defined( 'ABSPATH' ) || exit;
global $product;
do_action( 'woocommerce_before_single_product' );
if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div class="product-container">
	<div class="product">
				<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
				<div class="product-before-content">
					<div class="product-head">
						<div class="mobile-head">
							<p class="product-price-calc">
								<? echo $product->get_price(); ?>
							</p>
							<h4 class="mobile-head__title">
								<? echo $product->get_title(); ?>
							</h4>
							<p class="mobile-head__sku">SKU: 
								<? $skuval = $product->get_sku(); if($skuval == null){echo 'NOT SETUP';}else{echo $skuval;} ?>
							</p>
						</div>
						<div class="woocommerce-product-gallery">
								<?
								function production_status(){
									global $product;
									$status = $product->get_attribute('productionstatus');
									if ($status == 'Get now'){
										echo '<p class="product-card-status product-card-status_avaliable">Get now</p>';
									}
									elseif ($status == 'Soon'){
										echo '<p class="product-card-status product-card-status_request">Soon</p>';
									}
									else{
										null;
									}
								}
							production_status();
								?>
							<div class="product-card-slider">
							<?php $post_thumbnail_id = $product->get_image_id(); ?>
								<div class="product-card-slide" itemscope itemtype="http://schema.org/ImageObject"><a rel="lightbox" href="<?php echo wp_get_attachment_url( $post_thumbnail_id ); ?>"><img src="<?php echo wp_get_attachment_url( $post_thumbnail_id ); ?>" alt="<? echo $product->get_title(); ?>" itemprop="contentUrl"><p hidden="true" itemprop="name"><? echo $product->get_title(); ?></p><p hidden="true" itemprop="description"><? echo $product->get_title(); ?> photo</p></a></div>
								<?php $attachment_ids = $product->get_gallery_image_ids(); ?>
								<?php foreach ( $attachment_ids as $attachment_id ) { ?>
								<div class="product-card-slide"><a rel="lightbox" href="<?php echo wp_get_attachment_url( $attachment_id ); ?>" data-fancybox="product-gallery"><img src="<?php echo wp_get_attachment_url( $attachment_id ); ?>" alt="<? echo $product->get_title(); ?>"></a></div>
								<?php } ?>
							</div>
								<div class="product-card-slider-nav">
								<?php $post_thumbnail_id = $product->get_image_id(); ?>
									<div class="product-card-slide"><img src="<?php echo wp_get_attachment_url( $post_thumbnail_id ); ?>" alt="<? echo $product->get_title(); ?>"></div>
								<?php $attachment_ids = $product->get_gallery_image_ids(); ?>
								<?php foreach ( $attachment_ids as $attachment_id ) { ?>
								<div class="product-card-slide"><img src="<?php echo wp_get_attachment_url( $attachment_id ); ?>" alt="<? echo $product->get_title(); ?>"></div>
								<?php } ?>
								</div>
						</div>
						<?
						function summary_nologin(){
							if(is_user_logged_in()){
								echo ' '.'entry-summary-login';
							}
							else{
								echo ' '.'entry-summary-nologin';
							}
						}
						?>
						<div class="summary entry-summary<? summary_nologin(); ?>">
							<?php
							do_action( 'woocommerce_single_product_summary' );
							?>
													<script src="https://yastatic.net/share2/share.js"></script>
			<div class="ya-share2" data-curtain data-limit="0" data-more-button-type="short" data-services="facebook,twitter,linkedin"></div>
						</div>
					</div>
<?php
do_action( 'woocommerce_after_single_product_summary' );
?>
				</div>
				<? function pricing_table(){
		// only for simple products
		global $post;
		$product = wc_get_product($post->ID);
		if (is_object($product)){
			if( $product->is_type( 'simple' ) ){
				// get if 1) pricing table is enabled and 2) there are tiered prices set up
				$is_enabled = get_post_meta($post->ID, 'b2bking_show_pricing_table', true);
				if (!$product->is_purchasable()){
					$is_enabled = 'no';
				}
				if ($is_enabled !== 'no'){
					// get user's group
					$user_id = get_current_user_id();
			    	$account_type = get_user_meta($user_id,'b2bking_account_type', true);
			    	if ($account_type === 'subaccount'){
			    		// for all intents and purposes set current user as the subaccount parent
			    		$parent_user_id = get_user_meta($user_id, 'b2bking_account_parent', true);
			    		$user_id = $parent_user_id;
			    	}

					$currentusergroupidnr = get_user_meta($user_id, 'b2bking_customergroup', true );

					$price_tiers = get_post_meta($post->ID, 'b2bking_product_pricetiers_group_'.$currentusergroupidnr, true);

					// if didn't find anything as a price tier, give regular price tiers
					if (!(!empty($price_tiers) && strlen($price_tiers) > 1 )){
						$price_tiers = get_post_meta($post->ID, 'b2bking_product_pricetiers_group_b2c', true);
					}

					if (!empty($price_tiers) && strlen($price_tiers) > 1 ){
						?>
						<table class="shop_table b2bking_shop_table" style="display: none;">
							<thead>
								<tr>
									<th><?php esc_html_e('Product Quantity','b2bking'); ?></th>
									<th><?php esc_html_e('Price per Unit','b2bking'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$price_tiers_array = explode(';', $price_tiers);
								$price_tiers_array = array_filter($price_tiers_array);

								// need to order this array by the first number (elemnts of form 1:5, 2:5, 6:5)
								$helper_array = array();							
								foreach ($price_tiers_array as $index=> $pair){
									$pair_array = explode(':', $pair);
									$helper_array[$pair_array[0]] = $pair_array[1];
								}
								ksort($helper_array);
								$price_tiers_array = array();
								foreach ($helper_array as $index=>$value){
									array_push($price_tiers_array,$index.':'.$value);
								}
								// finished sort

								$number_of_tiers = count($price_tiers_array);
								if ($number_of_tiers === 1){
									$tier_values = explode(':', $price_tiers_array[0]);
									?>
									<tr>
										<td><?php echo esc_html($tier_values[0]).'+'; do_action('b2bking_tiered_table_after_quantity', $post->ID); ?></td>

										<?php 
										// adjust price for tax
										require_once ( B2BKING_DIR . 'public/class-b2bking-helper.php' );
										$helper = new B2bking_Helper();
										$tier_values[1] = $helper->b2bking_wc_get_price_to_display( $product, array( 'price' => $tier_values[1] ) ); // get sale price
										?>
										<td><?php echo wc_price($tier_values[1]); do_action('b2bking_tiered_table_after_price', $post->ID);?></td>
									</tr>
									<?php
								} else {
									$previous_tier = 'no';
									$previous_value = 'no';
									foreach ($price_tiers_array as $index => $tier){
										$tier_values = explode(':', $tier);
										if ($previous_tier !== 'no'){
											?>
												<tr>
													<td><?php
													if (floatval($previous_tier) !== floatval($tier_values[0]-1)){
														echo esc_html($previous_tier).' - '.esc_html($tier_values[0]-1);
													} else {
														echo esc_html($previous_tier);
													}
													do_action('b2bking_tiered_table_after_quantity', $post->ID);
													?></td>

													<?php 
													// adjust price for tax
													require_once ( B2BKING_DIR . 'public/class-b2bking-helper.php' );
													$helper = new B2bking_Helper();
													$previous_value = $helper->b2bking_wc_get_price_to_display( $product, array( 'price' => $previous_value ) ); // get sale price
													?>
													<td><?php echo wc_price($previous_value); do_action('b2bking_tiered_table_after_price', $post->ID);?></td>
												</tr>
											<?php
										}
										$previous_tier = $tier_values[0];
										$previous_value = $tier_values[1];

										// if this tier is the last tier
										if (intval($index+1) === intval($number_of_tiers)){
											?>
											<tr>
												<td><?php echo esc_html($previous_tier).'+'; do_action('b2bking_tiered_table_after_quantity', $post->ID);?></td>
												<?php 
												// adjust price for tax
												require_once ( B2BKING_DIR . 'public/class-b2bking-helper.php' );
												$helper = new B2bking_Helper();
												$previous_value = $helper->b2bking_wc_get_price_to_display( $product, array( 'price' => $previous_value ) ); // get sale price
												?>
												<td><?php echo wc_price($previous_value); do_action('b2bking_tiered_table_after_price', $post->ID);?></td>
											</tr>
											<?php
										}
									}
								}
								?>
							</tbody>
						</table>
						<?php
					}
				}
			}
		}
	} ?>
	<? pricing_table(); ?>

			</div>
		<div class="product__ask-сontainer">
  <a href="" class="product__ask-link">Ask question</a>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>
<? do_action( 'vft_js_jquery' ); ?>
<? do_action( 'vft_js_slickslider' ); ?>
<? do_action( 'vft_js_pcslider' ); ?>

