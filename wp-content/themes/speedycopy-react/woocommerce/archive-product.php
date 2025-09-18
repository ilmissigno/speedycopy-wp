<?php
/** WooCommerce Shop Archive */
get_header(); ?>
<section class="sc-shop-hero"><h1>Negozio</h1></section>
<div class="sc-shop-wrapper">
  <?php if (woocommerce_product_loop()) { ?>
    <div class="sc-grid sc-grid-products">
      <?php while ( have_posts() ) { the_post(); global $product; ?>
        <div class="sc-card sc-card-product">
            <a href="<?php the_permalink(); ?>" class="sc-card-thumb"><?php echo woocommerce_get_product_thumbnail('woocommerce_thumbnail'); ?></a>
                  <?php if( $product->is_on_sale() ) : ?>
                      <span class="sc-badge-offerta">OFFERTA</span>
                  <?php endif; ?>
          <h3 class="sc-card-title"><a href="<?php the_permalink(); ?>" class="sc-card-link-title"><?php the_title(); ?></a></h3>
          <div class="sc-price"><?php echo $product->get_price_html(); ?></div>
          <?php woocommerce_template_loop_add_to_cart(); ?>
        </div>
      <?php } ?>
    </div>
    <?php woocommerce_pagination(); ?>
  <?php } else { echo '<p>Nessun prodotto.</p>'; } ?>
</div>
<?php get_footer(); ?>