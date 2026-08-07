<?php
/*
Template Name: Accesso / Registrazione
*/
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Opzione B: unica URL auth = WooCommerce My Account.
if ( function_exists( 'wc_get_page_permalink' ) ) {
	wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
	exit;
}

get_header();
echo '<div class="sc-container"><p>Configura la pagina account WooCommerce.</p></div>';
get_footer();
