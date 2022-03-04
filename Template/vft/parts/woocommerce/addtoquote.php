<p class="price"><? draw_price(); ?></p>
<!-- <a href="?add-to-cart=16238&amp;quantity=10" data-quantity="1" class="button product__btn">Request</a> -->
<? 
global $product;
$link = $product->get_permalink();
if(has_term('soon', 'product_cat')){
	echo '<a href="'.$link.'" data-quantity="1" class="button product__btn">Request</a>';
}
?>