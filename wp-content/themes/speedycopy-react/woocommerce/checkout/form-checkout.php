<?php
/** Checkout */
if(!defined('ABSPATH')) exit; get_header(); ?>
<div class="sc-checkout-wrapper">
  <h1>Checkout</h1>
  <?php wc_print_notices(); ?>
  <?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
  <form name="checkout" method="post" class="checkout sc-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
    <div class="sc-checkout-grid">
      <div class="sc-checkout-col">
        <h2>Dati di fatturazione</h2>
        <?php do_action( 'woocommerce_checkout_billing' ); ?>
        <h2>Spedizione</h2>
        <?php do_action( 'woocommerce_checkout_shipping' ); ?>
      </div>
      <div class="sc-checkout-col sc-checkout-review">
        <h2>Riepilogo ordine</h2>
        <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
        <div id="order_review" class="woocommerce-checkout-review-order">
          <?php do_action( 'woocommerce_checkout_order_review' ); ?>
        </div>
      </div>
    </div>
    <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
  </form>
  <?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
</div>
<?php get_footer(); ?>