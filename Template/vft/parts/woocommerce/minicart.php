<?
if(is_user_logged_in()){
$cartnlstyle = '';
}
else{
$cartnlstyle = ' style="display:none;"';
}
?>
<div class="minicart-container">
<div class="minicart"<? echo $cartnlstyle; ?>>
<button class="svg-btn minicart-container__close">
<img src="<?php echo get_bloginfo( 'template_directory' ); ?>/img/burger-close.svg" alt="Close button">
</button>
<p class="minicart__title">Recently added items</p>
<div class="minicart-content">
<div class="minicart-items">
<?  global $woocommerce;
    global $product;
    $items = $woocommerce->cart->get_cart();
        foreach($items as $item => $values) { 
            $_product =  wc_get_product( $values['data']->get_id());
            ?>
    <div class="minicart-item">
            <?
            $title = $_product->get_title();
            $price = $_product->get_price();
            $ps = $_product->get_attribute('productionstatus');
            $qty = $values['quantity'];
			if ($ps == 'Get now'){
				$attribute = '_avaliable';
			}
			elseif ($ps == 'Soon'){
				$attribute = '_request';
			}
			else{
				$attribute = '';
			}
            ?>
            <img class="minicart-item__thumbnail" src="<?php echo wp_get_attachment_url( $_product->get_image_id() ); ?>" alt="Cart item thumbnail">
            <? echo '<p class="minicart-item__status minicart-item__status'.$attribute.'">'.$ps.'</p>'; ?>
		<div class="minicart-item__text">
        <div class="minicart-item__details">
            <p class="minicart-item__title"><? echo $title; ?></p>
            <p class="minicart-item__price"><? echo get_woocommerce_currency_symbol(); echo $price; ?> / kg</p>
        </div>
        <div class="minicart-item__controls">
            <div class="minicart-item__qty"><? echo $qty; ?></div>
        </div>
				</div>
    </div>
    <?}?>
	<?$quote_items_qty = 0;?>
	<? foreach ( WC()->session->get( 'quotes' ) as $quote_item_key => $quote_item ) {

	if (  !isset( $quote_item['data'] ) || !is_object( $quote_item['data'] ) ) {
		continue;
	}

	$_product      = apply_filters( 'addify_quote_item_product', $quote_item['data'], $quote_item, $quote_item_key );
	$product_id    = apply_filters( 'addify_quote_item_product_id', $quote_item['product_id'], $quote_item, $quote_item_key );
	$price         = empty( $quote_item['addons_price'] ) ? $_product->get_price('edit') : $quote_item['addons_price'];
	$offered_price = isset( $quote_item['offered_price'] ) ? floatval( $quote_item['offered_price'] ) : $price;

	if ( $_product && $_product->exists() && $quote_item['quantity'] > 0 && apply_filters( 'addify_quote_item_visible', true, $quote_item, $quote_item_key ) ) {
		$product_permalink = apply_filters( 'addify_quote_item_permalink', $_product->is_visible() ? $_product->get_permalink( $quote_item ) : '', $quote_item, $quote_item_key );
		$quote_items_qty++;
	?>
	<div class="minicart-item">
            <?
            $title = $_product->get_title();
            $price = $_product->get_price();
            $ps = $_product->get_attribute('productionstatus');
            $qty = $quote_item['quantity'];
			if ($ps == 'Get now'){
				$attribute = '_avaliable';
			}
			elseif ($ps == 'Soon'){
				$attribute = '_request';
			}
			else{
				$attribute = '';
			}
            ?>
            <img class="minicart-item__thumbnail" src="<?php echo wp_get_attachment_url( $_product->get_image_id() ); ?>" alt="Cart item thumbnail">
            <? echo '<p class="minicart-item__status minicart-item__status'.$attribute.'">'.$ps.'</p>'; ?>
		<div class="minicart-item__text">
        <div class="minicart-item__details">
            <p class="minicart-item__title"><? echo $title; ?></p>
            <p class="minicart-item__price">Request</p>
        </div>
        <div class="minicart-item__controls">
            <div class="minicart-item__qty"><? echo $qty; ?></div>
        </div>
				</div>
    </div>
	<? } }?>
	</div>
    <div class="minicart-total">
		<?
        function count_items(){
            $count = 0;
            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                $count++;
            }
			return $count;
        }
        ?>
        <p class="minicart-total__items-qty"><? $cartItemsC = count_items(); echo $cartItemsC + $quote_items_qty; ?> <span class="order-summary__items-qty-title">items in cart</span></p>
        <div class="minicart-total__price total-price">
            <p class="total-price__title">Cart subtotal:</p>
			<p class="total-price__value"><?php wc_cart_totals_subtotal_html(); ?></p>
        </div>
        <div class="minicart-total__controls minicart-controls">
        <a href="/checkout" class="minicart-controls__checkout btn">Proceed to checkout</a>
        <a href="/cart" class="minicart-controls__edit btn btn-dark">View and edit cart</a>
        </div>
    </div>
</div>
</div>
</div>