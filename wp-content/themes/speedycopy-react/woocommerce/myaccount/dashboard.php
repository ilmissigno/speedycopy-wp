<?php
/**
 * My Account Dashboard Override
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$current_user = wp_get_current_user();
?>
<div class="sc-account-page-wrapper">
<div class="sc-account-dashboard">
  <div class="sc-account-hero">
    <h1>Ciao <?php echo esc_html( $current_user->first_name ?: $current_user->display_name ); ?> 👋</h1>
    <p>Benvenuto nella tua area personale. Qui puoi tenere traccia degli ordini, gestire i tuoi indirizzi e scaricare i file.</p>
  </div>
  <div class="sc-account-grid">
    <a class="sc-account-card" href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>">
      <div class="sc-account-card-icon">📦</div>
      <h3>I miei ordini</h3>
      <p>Storico e stato ordini.</p>
    </a>
    <a class="sc-account-card" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' ) ); ?>">
      <div class="sc-account-card-icon">🏠</div>
      <h3>Indirizzi</h3>
      <p>Fatturazione e spedizione.</p>
    </a>
    <a class="sc-account-card" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-account' ) ); ?>">
      <div class="sc-account-card-icon">👤</div>
      <h3>Dettagli account</h3>
      <p>Nome, email e password.</p>
    </a>
    <a class="sc-account-card" href="<?php echo esc_url( wc_get_endpoint_url( 'downloads' ) ); ?>">
      <div class="sc-account-card-icon">⬇️</div>
      <h3>Download</h3>
      <p>I tuoi file disponibili.</p>
    </a>
    <a class="sc-account-card" href="<?php echo esc_url( wc_logout_url() ); ?>">
      <div class="sc-account-card-icon">🚪</div>
      <h3>Logout</h3>
      <p>Esci in sicurezza.</p>
    </a>
  </div>
  <div class="sc-account-recent">
    <h2>Ultimi ordini</h2>
    <?php
    // Mostra ultimi 3 ordini
    $customer_orders = wc_get_orders([
      'customer' => get_current_user_id(),
      'limit'    => 3,
      'orderby'  => 'date',
      'order'    => 'DESC',
      'return'   => 'objects'
    ]);
    if ( $customer_orders ) {
      echo '<ul class="sc-account-orders">';
      foreach ( $customer_orders as $order ) {
        $order_date = $order->get_date_created();
        echo '<li>';
        echo '<a href="'.esc_url( $order->get_view_order_url() ).'">Ordine #'.$order->get_order_number().'</a> · ';
        echo esc_html( $order_date ? $order_date->date_i18n( get_option('date_format') ) : '' );
        echo ' · <span class="status status-'.esc_attr( $order->get_status() ).'">'.wc_get_order_status_name( $order->get_status() ).'</span>';
        echo ' · <strong>'.$order->get_formatted_order_total().'</strong>';
        echo '</li>';
      }
      echo '</ul>';
    } else {
      echo '<p>Nessun ordine recente.</p>';
    }
    ?>
  </div>
  </div>
</div>
