<?php
/**
 * Plugin Name: SpeedyCopy - Supplemento pagamento alla consegna
 * Description: Aggiunge un supplemento di spedizione quando il cliente sceglie "Pagamento alla consegna" (cod).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Campo importo in: WooCommerce > Impostazioni > Spedizione > Opzioni di spedizione.
 * (Non nelle impostazioni del gateway: in WC 11 quella pagina e' un form React
 * hardcoded - assets/client/admin/chunks/settings-payments-cod.js - e ignora
 * i campi aggiunti via woocommerce_settings_api_form_fields_cod.)
 */
add_filter( 'woocommerce_get_settings_shipping', function ( $settings, $section ) {
	if ( '' !== $section && 'options' !== $section ) {
		return $settings;
	}
	$settings[] = array(
		'title' => __( 'Pagamento alla consegna', 'speedycopy' ),
		'type'  => 'title',
		'id'    => 'speedycopy_cod_options',
	);
	$settings[] = array(
		'title'             => __( 'Supplemento spedizione', 'speedycopy' ),
		'desc'              => __( 'Importo NETTO aggiunto al totale quando il cliente sceglie il contrassegno. Vuoto o 0 = nessun supplemento.', 'speedycopy' ),
		'id'                => 'speedycopy_cod_surcharge',
		'type'              => 'text',
		'default'           => '',
		'css'               => 'width:80px;',
		'desc_tip'          => false,
		'custom_attributes' => array( 'inputmode' => 'decimal' ),
	);
	$settings[] = array(
		'type' => 'sectionend',
		'id'   => 'speedycopy_cod_options',
	);

	return $settings;
}, 10, 2 );

function speedycopy_cod_surcharge() {
	return (float) wc_format_decimal( get_option( 'speedycopy_cod_surcharge', 0 ) );
}

add_action( 'woocommerce_cart_calculate_fees', function ( $cart ) {
	if ( 'cod' !== WC()->session->get( 'chosen_payment_method' ) ) {
		return;
	}
	$surcharge = speedycopy_cod_surcharge();
	if ( $surcharge <= 0 ) {
		return;
	}
	$cart->add_fee( __( 'Supplemento pagamento alla consegna', 'speedycopy' ), $surcharge, true );
} );

/**
 * Il checkout a blocchi scrive chosen_payment_method in sessione solo al POST finale,
 * quindi i totali non si aggiornerebbero al cambio metodo: questo callback lo anticipa.
 */
add_action( 'woocommerce_blocks_loaded', function () {
	woocommerce_store_api_register_update_callback( array(
		'namespace' => 'speedycopy-cod-fee',
		'callback'  => function ( $data ) {
			$method = isset( $data['payment_method'] ) ? sanitize_text_field( $data['payment_method'] ) : '';
			if ( array_key_exists( $method, WC()->payment_gateways->get_available_payment_gateways() ) ) {
				WC()->session->set( 'chosen_payment_method', $method );
			}
		},
	) );
} );

add_action( 'wp_enqueue_scripts', function () {
	if ( ! is_checkout() ) {
		return;
	}
	wp_register_script( 'speedycopy-cod-fee', '', array( 'wp-data', 'wc-blocks-checkout' ), null, true );
	wp_enqueue_script( 'speedycopy-cod-fee' );
	wp_add_inline_script( 'speedycopy-cod-fee', <<<'JS'
( function () {
	var last = null;
	wp.data.subscribe( function () {
		var store = wp.data.select( 'wc/store/payment' );
		if ( ! store ) { return; }
		var method = store.getActivePaymentMethod();
		if ( ! method || method === last ) { return; }
		last = method;
		wc.blocksCheckout.extensionCartUpdate( {
			namespace: 'speedycopy-cod-fee',
			data: { payment_method: method }
		} );
	} );
} )();
JS
	);
} );
