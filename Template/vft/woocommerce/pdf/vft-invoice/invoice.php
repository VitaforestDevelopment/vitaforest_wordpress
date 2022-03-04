<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'wpo_wcpdf_before_document', $this->get_type(), $this->order ); ?>
<?php do_action( 'wpo_wcpdf_before_order_details', $this->get_type(), $this->order ); ?>
<div class="left-line"><picture><source srcset="<?php echo get_bloginfo( 'template_directory' ); ?>/woocommerce/pdf/vft-invoice/src/img/left.webp"type="image/webp"><img src="<?php echo get_bloginfo( 'template_directory' ); ?>/woocommerce/pdf/vft-invoice/src/img/left.png" alt="" /></picture></div>
    <div class="container">
      <div class="header">
        <div class="logo">
          <picture><source srcset="<?php echo get_bloginfo( 'template_directory' ); ?>/woocommerce/pdf/vft-invoice/src/img/logo.webp"type="image/webp"><img src="<?php echo get_bloginfo( 'template_directory' ); ?>/woocommerce/pdf/vft-invoice/src/img/logo.png" alt="" srcset="" /></picture>
        </div>
      </div>
      <?
      $order = wc_get_order( $order_id );

      // Get the Customer ID (User ID)
      $customer_id = $order->get_customer_id(); // Or $order->get_user_id();
      
      // Get the WP_User Object instance
      $user = $order->get_user();
      
      // Get the WP_User roles and capabilities
      $user_roles = $user->roles;

    $user_id = $order->get_user_id();
    $user_country_get = $user->billing_country;
	$user_country_ship = $user->shipping_country;
	$country_arr = WC()->countries->get_countries();
	$full_country_name = $country_arr[$user_country_get];
	$full_country_shipping = $country_arr[$user_country_ship];
    $vatNum = get_user_meta($user_id, b2bking_custom_field_15829);
	$codeNum = get_user_meta($user_id, b2bking_custom_field_15821);
      
      // Get the Customer billing email
      $billing_email  = $order->get_billing_email();
      
      // Get the Customer billing phone
      $billing_phone  = $order->get_billing_phone();
      
      // Customer billing information details
      $billing_first_name = $order->get_billing_first_name();
      $billing_last_name  = $order->get_billing_last_name();
      $billing_company    = $order->get_billing_company();
      $billing_address_1  = $order->get_billing_address_1();
      $billing_address_2  = $order->get_billing_address_2();
      $billing_city       = $order->get_billing_city();
      $billing_state      = $order->get_billing_state();
      $billing_postcode   = $order->get_billing_postcode();
      $billing_country    = $order->get_billing_country();
      
      // Customer shipping information details
      $shipping_first_name = $order->get_shipping_first_name();
      $shipping_last_name  = $order->get_shipping_last_name();
      $shipping_company    = $order->get_shipping_company();
      $shipping_address_1  = $order->get_shipping_address_1();
      $shipping_address_2  = $order->get_shipping_address_2();
      $shipping_city       = $order->get_shipping_city();
      $shipping_state      = $order->get_shipping_state();
      $shipping_postcode   = $order->get_shipping_postcode();
      $shipping_country    = $order->get_shipping_country();
      ?>
      <div class="body">
        <h1 class="title">PROFORMA INVOICE</h1>
        <table class="table billing-table">
          <thead class="table__head">
            <tr>
              <th class="table__header table__billed">BILLED TO</th>
              <th class="table__header table__date">DATE OF ISSUE</th>
              <th class="table__header table__number">PR-INVOICE NUMBER</th>
              <th class="table__header table__deposit">DEPOSIT DUE (EUR)</th>
            </tr>
          </thead>
          <tbody class="table__body">
            <tr>
              <td>
              Company name: <? echo $billing_company; ?></br>
              Address: <? echo  $billing_address_1.', '.$billing_address_2.', '.$billing_postcode.', '.$billing_city.', '.$full_country_name; ?></br>
              Register code: <? echo $codeNum[0]; ?></br>
              VAT: <? echo $vatNum[0]; ?>
              </td>
              <td><?php $this->invoice_date(); ?></td>
              <td><?php $this->invoice_number(); ?></td>
              <td>100%</td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td></td>
              <td>00/00/0000</td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>
        <table class="table shoping-table">
          <thead class="table__head">
            <tr>
              <th class="table__header table__billed">DESCRIPTION</th>
              <th class="table__header table__date">RATE</th>
              <th class="table__header table__number">QTY/KG</th>
              <th class="table__header table__deposit">LINE TOTAL</th>
            </tr>
          </thead>
          <tbody class="table__body">
          <?php foreach ( $this->get_order_items() as $item_id => $item ) : ?>
            <tr>
              <td><?php echo $item['name']; ?></td>
              <td><?php 
              $baseVal = $item['order_price'];
              $newVal = preg_replace('/[^0-9 .]/', '', $baseVal);
              $resultVal = $newVal/$item['quantity'];
              echo '€'.$resultVal;
              ?></td>
              <td><?php echo $item['quantity']; ?></td>
              <td><?php echo $item['order_price']; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <table class="hz-kak-nazvat">
        <tr class="hz-kak-nazvat-row">
        <td class="hz-kak-nazvat__left">
			<? echo $shipping_address_1; ?> <br />
          	<? echo $shipping_address_2; ?> <br />
			<? echo $billing_postcode; ?> <br />
			<? echo $full_country_shipping; ?>
        </td>
		<td></td>
        <td>
                  <table class="hz-kak-nazvat__table hz-table">
          <tr>
            <th class="hz-table__header hz-table__header_first">Tax</th>
            <td class="hz-table__content hz-table__content_first">0</td>
          </tr>
          <tr>
            <th class="hz-table__header">Total</th>
            <td class="hz-table__content"><?php echo $item['order_price']; ?></td>
          </tr>
          <tr>
            <th class="hz-table__header">Amount Pal</th>
            <td class="hz-table__content">0</td>
          </tr>
          <tr>
            <th class="hz-table__header">Deposit request (EUR)</th>
            <td class="hz-table__content">100%</td>
          </tr>
          <tr>
            <th class="hz-table__header">Deposit Due (EUR</th>
            <td class="hz-table__content">100%</td>
          </tr>
        </table>
        </td>

        </tr>
      </div>

      <table class="subtotal">
        <tr>
          <th class="subtotal__header">SUBTOTAL</th>
          <td class="subtotal__content"><?php echo $item['order_price']; ?></td>
        </tr>
      </table>
    </div>