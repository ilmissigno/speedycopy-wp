<?php
/**
 * Shop + product category archives.
 * Parent categories with children show subcategory cards and direct products.
 */
defined( 'ABSPATH' ) || exit;
get_header();

$is_cat     = function_exists( 'is_product_category' ) && is_product_category();
$term       = $is_cat ? get_queried_object() : null;
$title      = $term && ! is_wp_error( $term ) ? $term->name : __( 'Negozio', 'speedycopy-react' );
$children   = array();

if ( $term && ! empty( $term->term_id ) ) {
	$children = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'parent'     => (int) $term->term_id,
	) );
	if ( is_wp_error( $children ) ) {
		$children = array();
	}
}

$show_subcats = $is_cat && ! empty( $children );
$parent_products = null;

if ( $show_subcats ) {
  $visibility = function_exists( 'wc_get_product_visibility_term_ids' ) ? wc_get_product_visibility_term_ids() : array();
  $excluded_visibility = array_filter( array(
    $visibility['exclude-from-catalog'] ?? 0,
    $visibility['exclude-from-search'] ?? 0,
  ) );
  $tax_query = array(
    array(
      'taxonomy'         => 'product_cat',
      'field'            => 'term_id',
      'terms'            => array( (int) $term->term_id ),
      'include_children' => false,
    ),
  );

  if ( ! empty( $excluded_visibility ) ) {
    $tax_query[] = array(
      'taxonomy' => 'product_visibility',
      'field'    => 'term_id',
      'terms'    => $excluded_visibility,
      'operator' => 'NOT IN',
    );
  }

  $parent_products = new WP_Query( array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => (int) get_option( 'posts_per_page', 12 ),
    'paged'          => max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) ),
    'tax_query'      => $tax_query,
  ) );
}
?>
<section class="sc-shop-hero">
  <h1><?php echo esc_html( $title ); ?></h1>
  <?php if ( $term && ! empty( $term->description ) ) : ?>
    <p class="sc-shop-hero-desc"><?php echo esc_html( $term->description ); ?></p>
  <?php elseif ( $show_subcats ) : ?>
    <p class="sc-shop-hero-desc"><?php esc_html_e( 'Scegli una sottocategoria', 'speedycopy-react' ); ?></p>
  <?php endif; ?>
</section>

<div class="sc-shop-wrapper">
  <?php if ( $show_subcats ) : ?>
    <div class="sc-grid sc-grid-cats">
      <?php foreach ( $children as $child ) :
        $thumb_id = get_term_meta( $child->term_id, 'thumbnail_id', true );
        $link     = get_term_link( $child );
        if ( is_wp_error( $link ) ) {
          continue;
        }
        ?>
        <a class="sc-card sc-card-cat" href="<?php echo esc_url( $link ); ?>">
          <?php
          if ( $thumb_id ) {
            echo wp_get_attachment_image( (int) $thumb_id, 'woocommerce_thumbnail' );
          } else {
            echo '<div class="sc-cat-fallback">' . esc_html( mb_substr( $child->name, 0, 1 ) ) . '</div>';
          }
          ?>
          <h3><?php echo esc_html( $child->name ); ?></h3>
          <span class="sc-cat-count"><?php echo esc_html( sprintf( _n( '%d prodotto', '%d prodotti', (int) $child->count, 'speedycopy-react' ), (int) $child->count ) ); ?></span>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if ( $parent_products && $parent_products->have_posts() ) : ?>
      <h2 class="sc-shop-section-title"><?php esc_html_e( 'Prodotti disponibili', 'speedycopy-react' ); ?></h2>
      <div class="sc-grid sc-grid-products">
        <?php while ( $parent_products->have_posts() ) : $parent_products->the_post();
          $product = wc_get_product( get_the_ID() );
          if ( ! $product ) {
            continue;
          }
          ?>
          <div class="sc-card sc-card-product">
            <a href="<?php the_permalink(); ?>" class="sc-card-thumb"><?php echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' ); ?></a>
            <?php if ( $product->is_on_sale() ) : ?>
              <span class="sc-badge-offerta">OFFERTA</span>
            <?php endif; ?>
            <h3 class="sc-card-title"><a href="<?php the_permalink(); ?>" class="sc-card-link-title"><?php the_title(); ?></a></h3>
            <div class="sc-price"><?php echo $product->get_price_html(); ?></div>
            <?php woocommerce_template_loop_add_to_cart(); ?>
          </div>
        <?php endwhile; ?>
      </div>
      <?php if ( $parent_products->max_num_pages > 1 ) : ?>
        <nav class="woocommerce-pagination">
          <?php echo wp_kses_post( paginate_links( array(
            'base'      => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'format'    => '',
            'current'   => max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) ),
            'total'     => (int) $parent_products->max_num_pages,
            'type'      => 'list',
            'prev_text' => '&larr;',
            'next_text' => '&rarr;',
          ) ) ); ?>
        </nav>
      <?php endif; ?>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  <?php elseif ( woocommerce_product_loop() ) : ?>
    <div class="sc-grid sc-grid-products">
      <?php
      while ( have_posts() ) {
        the_post();
        global $product;
        if ( ! $product ) {
          continue;
        }
        ?>
        <div class="sc-card sc-card-product">
          <a href="<?php the_permalink(); ?>" class="sc-card-thumb"><?php echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' ); ?></a>
          <?php if ( $product->is_on_sale() ) : ?>
            <span class="sc-badge-offerta">OFFERTA</span>
          <?php endif; ?>
          <h3 class="sc-card-title"><a href="<?php the_permalink(); ?>" class="sc-card-link-title"><?php the_title(); ?></a></h3>
          <div class="sc-price"><?php echo $product->get_price_html(); ?></div>
          <?php woocommerce_template_loop_add_to_cart(); ?>
        </div>
        <?php
      }
      ?>
    </div>
    <?php woocommerce_pagination(); ?>
  <?php else : ?>
    <p class="sc-home-empty"><?php esc_html_e( 'Nessun prodotto in questa categoria.', 'speedycopy-react' ); ?></p>
  <?php endif; ?>
</div>
<?php
get_footer();
