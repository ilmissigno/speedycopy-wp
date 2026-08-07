<?php
/**
 * Template override: Simple product add to cart con componente quantità custom.
 * Versione base WooCommerce 7.0.1
 */

defined('ABSPATH') || exit;

global $product;

if(!$product->is_purchasable()) { return; }

echo wc_get_stock_html($product);

if($product->is_in_stock()):
  do_action('woocommerce_before_add_to_cart_form');
?>
<form class="cart" action="<?php echo esc_url( apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink()) ); ?>" method="post" enctype='multipart/form-data'>
  <?php do_action('woocommerce_before_add_to_cart_button'); ?>
  <?php do_action('woocommerce_before_add_to_cart_quantity'); ?>
  <?php
    $min  = apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product);
    $max  = apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product);
    $val  = isset($_POST['quantity']) ? wc_stock_amount( wp_unslash($_POST['quantity']) ) : $product->get_min_purchase_quantity();
    $step = apply_filters('woocommerce_quantity_input_step', 1, $product);
    $pattern = apply_filters('woocommerce_quantity_input_pattern', '[0-9]*', $product);
  ?>
  <div class="sc-qty" data-qty-wrapper>
    <button type="button" class="sc-qty-btn" data-qty-decr aria-label="Diminuisci quantità">-</button>
    <input
      type="number"
      class="sc-qty-input qty"
      name="quantity"
      value="<?php echo esc_attr( $val ); ?>"
      <?php echo $min ? 'min="'.esc_attr($min).'"' : ''; ?>
      <?php echo ($max && $max>0) ? 'max="'.esc_attr($max).'"' : ''; ?>
      step="<?php echo esc_attr($step); ?>"
      pattern="<?php echo esc_attr($pattern); ?>"
      inputmode="numeric"
      data-qty-input
    />
    <button type="button" class="sc-qty-btn" data-qty-incr aria-label="Aumenta quantità">+</button>
  </div>
  <?php do_action('woocommerce_after_add_to_cart_quantity'); ?>
  <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button button sc-btn sc-btn-primary">
    <?php echo esc_html($product->single_add_to_cart_text()); ?>
  </button>
  <?php do_action('woocommerce_after_add_to_cart_button'); ?>
</form>
<?php do_action('woocommerce_after_add_to_cart_form'); ?>
<?php endif; ?>
