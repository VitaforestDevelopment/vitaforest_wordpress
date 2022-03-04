<?php
/**
 * Email Header
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-header.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
	</head>
	<body>
    <main class="main">
        <div class="header">
            <div class="container">
                <div class="header__wrapper">
                    <img src="./img/logo-light.svg" alt="" class="logo" width="120px" height="31px">
                    <div class="header__addres">
                        <p> Harju maakond, Tallinn,<br>
                            Lasnamäe linnaosa,<br>
                            Väike-Paala<br>
                            tn 2, 11415<br>
                            +3728801043
                        </p>
                    </div>
                </div>
            </div>
        </div>
		<div class="body">
