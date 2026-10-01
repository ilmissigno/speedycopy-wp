<?php
/**
 * Plugin Name: SpeedyCopy SMTP
 * Description: Invia tutte le email di WordPress/WooCommerce via SMTP autenticato (OVH Zimbra).
 *              Le credenziali stanno nelle costanti SPEEDYCOPY_SMTP_* definite in wp-config.php.
 * Author:      SpeedyCopy Dev
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'SPEEDYCOPY_SMTP_HOST' ) || ! defined( 'SPEEDYCOPY_SMTP_USER' ) || ! defined( 'SPEEDYCOPY_SMTP_PASS' ) ) {
	return;
}

/** Mittente coerente con l'utente autenticato (allineamento SPF/DKIM del dominio). */
add_filter( 'wp_mail_from', function ( $from ) {
	return defined( 'SPEEDYCOPY_SMTP_FROM' ) ? SPEEDYCOPY_SMTP_FROM : SPEEDYCOPY_SMTP_USER;
}, 99 );

add_filter( 'wp_mail_from_name', function ( $name ) {
	return defined( 'SPEEDYCOPY_SMTP_FROM_NAME' ) ? SPEEDYCOPY_SMTP_FROM_NAME : $name;
}, 99 );

/** Configura PHPMailer per usare l'SMTP OVH. */
add_action( 'phpmailer_init', function ( $phpmailer ) {
	$phpmailer->isSMTP();
	$phpmailer->Host       = SPEEDYCOPY_SMTP_HOST;
	$phpmailer->Port       = defined( 'SPEEDYCOPY_SMTP_PORT' ) ? (int) SPEEDYCOPY_SMTP_PORT : 465;
	$phpmailer->SMTPAuth   = true;
	$phpmailer->Username    = SPEEDYCOPY_SMTP_USER;
	$phpmailer->Password    = SPEEDYCOPY_SMTP_PASS;
	$phpmailer->SMTPSecure  = defined( 'SPEEDYCOPY_SMTP_SECURE' ) ? SPEEDYCOPY_SMTP_SECURE : 'ssl';
	$phpmailer->SMTPAutoTLS = true;
	$phpmailer->Timeout     = 20;

	if ( ( defined( 'SPEEDYCOPY_SMTP_FROM' ) ? SPEEDYCOPY_SMTP_FROM : '' ) ) {
		$phpmailer->setFrom(
			SPEEDYCOPY_SMTP_FROM,
			defined( 'SPEEDYCOPY_SMTP_FROM_NAME' ) ? SPEEDYCOPY_SMTP_FROM_NAME : '',
			false
		);
	}
}, 99 );

/** Log degli errori di invio in wp-content/debug.log (solo con WP_DEBUG_LOG). */
add_action( 'wp_mail_failed', function ( $error ) {
	if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		error_log( '[speedycopy-smtp] invio fallito: ' . $error->get_error_message() );
	}
} );
