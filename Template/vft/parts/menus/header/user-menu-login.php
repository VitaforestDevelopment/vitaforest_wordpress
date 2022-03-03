<div class="user-menu__container">
<div class="user-menu modal-menu user-menu_closer">
    <button class="svg-btn user-menu__close">
    <img src="<?php echo get_bloginfo( 'template_directory' ); ?>/img/burger-close.svg" alt="Close button">
    </button>
    <a href="/my-account" class="user-menu__link">Account</a>
    <a href="<?
			 foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {
				 if (  !isset( $quote_item['data'] ) || !is_object( $quote_item['data'] ) ) {
					 continue;
				 }
				 $quote_product  = apply_filters( 'addify_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key );
				 if ( $quote_product && $quote_product->exists() && $quote_item['quantity'] > 0 && apply_filters( 'addify_quote_item_visible', true, $quote_item, $quote_item_key ) ) {
					 $quote_items_qty_check++;
				 }}; 

			 if ( WC()->cart->get_cart_contents_count() == 0 && is_user_logged_in() && $quote_items_qty_check > 0) {
				 echo '/request-a-quote';
			 }
			 elseif(!is_user_logged_in()){
				 echo '/request-a-quote';
			 }
			 else{
				 echo '/cart';
			 }
			 ?>" class="user-menu__link">Shopping cart</a>
    <a href="/my-account/webtoffee-wishlist/" class="user-menu__link">Wish list</a>
	<a class="user-menu__link logout-btn">Sign out</a>
</div>
</div>