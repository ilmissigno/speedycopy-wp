<?php
/** Cart */
if(!defined('ABSPATH')) exit; get_header(); ?>
<div class="sc-cart-page">
  <?php wc_print_notices(); ?>
  <div class="sc-cart-grid">
  <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
    <table class="sc-cart-table">
      <thead><tr><th>Prodotto</th><th>Prezzo</th><th>Qty</th><th>Totale</th><th></th></tr></thead>
      <tbody>
      <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $_product   = $cart_item['data'];
        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) { ?>
          <tr>
            <td class="sc-cart-prod">
              <?php $custom_pid = get_option('custom_product_id'); ?>
              <?php if( (int)$cart_item['product_id'] === (int)$custom_pid ){ ?>
                <div class="sc-cart-print-item">
                  <?php echo $_product->get_image('woocommerce_thumbnail'); ?>
                  <span class="sc-cart-print-name"><?php echo esc_html( $_product->get_name() ); ?></span>
                </div>
              <?php } else { ?>
                <a href="<?php echo esc_url( get_permalink( $cart_item['product_id'] ) ); ?>">
                  <?php echo $_product->get_image('woocommerce_thumbnail'); ?>
                  <span><?php echo esc_html( $_product->get_name() ); ?></span>
                </a>
              <?php } ?>
            </td>
            <td><?php echo WC()->cart->get_product_price( $_product ); ?></td>
            <td>
              <?php
              if ( $_product->is_sold_individually() ) {
                echo '<div class="sc-qty sc-qty-single">1 <input type="hidden" name="cart['.$cart_item_key.'][qty]" value="1" /></div>';
              } else {
                $input_name = 'cart['.$cart_item_key.'][qty]';
                $val = $cart_item['quantity'];
                $min = 1;
                $max = $_product->get_max_purchase_quantity();
                echo '<div class="sc-qty" data-qty-wrapper>'; 
                echo '<button type="button" class="sc-qty-btn" data-qty-decr>-</button>';
                $max_attr = ($max && $max > 0) ? 'max="'.$max.'"' : '';
                if($val < $min) $val = $min; // harden
                echo '<input type="number" class="sc-qty-input" name="'.$input_name.'" value="'.esc_attr($val).'" min="'.$min.'" '.$max_attr.' step="1" inputmode="numeric" data-qty-input />';
                echo '<button type="button" class="sc-qty-btn" data-qty-incr>+</button>';
                echo '</div>';
              }
              ?>
            </td>
            <td><?php echo WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ); ?></td>
            <td><a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="sc-remove">×</a></td>
          </tr>
        <?php } } ?>
      </tbody>
    </table>
    <button type="submit" name="update_cart" value="1" style="display:none" aria-hidden="true">Update</button>
    <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
    <input type="hidden" name="cart_hash" value="<?php echo esc_attr( md5( wp_json_encode( WC()->cart->get_cart() ) ) ); ?>" />
  </form>
  <aside class="sc-cart-aside">
    <div class="sc-cart-totals sc-cart-totals-sticky">
      <?php woocommerce_cart_totals(); ?>
    </div>
    <div class="sc-return-shop">
      <?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
        <a class="sc-btn sc-btn-outline" href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">← Continua lo shopping</a>
      <?php endif; ?>
    </div>
  </aside>
  </div>
</div>
<?php get_footer(); ?>