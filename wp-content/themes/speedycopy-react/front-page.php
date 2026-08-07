<?php
get_header();

$logo_url = '';
$hero_logo_id = 78;
if ( $hero_logo_id ) {
	$logo_url = wp_get_attachment_image_url( $hero_logo_id, 'full' );
}
if ( ! $logo_url ) {
	$custom_logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id ) {
		$logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
	}
}
if ( ! $logo_url ) {
	$logo_url = get_template_directory_uri() . '/assets/img/logo.jpg';
}

$shop_url  = function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' );
$print_url = get_permalink( 20 );
?>
<section class="sc-home-hero">
  <div class="sc-home-hero-inner">
    <div class="sc-home-brand">
      <img class="sc-home-logo" src="<?php echo esc_url( $logo_url ); ?>" alt="SpeedyCopy" width="220" height="220" />
      <h1>Da sempre, la tua copisteria di fiducia</h1>
      <p class="sc-home-lead">Stampa i tuoi appunti, dispense, cancelleria. In 24/72H a casa tua in tutta Italia</p>
      <div class="sc-home-cta">
        <a class="sc-btn sc-btn-primary" href="<?php echo esc_url( $shop_url ); ?>">Vai al negozio</a>
        <?php if ( $print_url ) : ?>
          <a class="sc-btn sc-btn-outline" href="<?php echo esc_url( $print_url ); ?>">Stampa ora</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="sc-home-visual">
      <div class="sc-home-slogan-panel">
        <p class="sc-home-slogan-kicker">Veloci. Precisi. Vicini a te.</p>
        <h2 class="sc-home-slogan">Dalla copisteria alla tua scrivania, senza stress.</h2>
        <ul class="sc-home-slogan-list">
          <li>Stampa e rilegatura su misura</li>
          <li>Cancelleria per lo studio</li>
          <li>Spedizione in tutta Italia in 24/72h</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<div class="sc-home-search-wrap">
  <form class="sc-home-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <input type="search" name="s" placeholder="Cerca dispense, penne, evidenziatori..." value="<?php echo esc_attr( get_search_query() ); ?>" />
    <input type="hidden" name="post_type" value="product" />
    <button type="submit" class="sc-btn sc-btn-sm sc-btn-primary">Cerca</button>
  </form>
</div>

<section class="sc-section">
  <div class="sc-home-section-head">
    <h2>Prodotti in evidenza</h2>
    <p>Una selezione pronta per lo studio e l’ufficio.</p>
  </div>
  <div class="sc-grid">
    <?php
    $featured_ids = function_exists( 'wc_get_featured_product_ids' ) ? wc_get_featured_product_ids() : array();
    $featured_q   = null;
    if ( ! empty( $featured_ids ) ) {
      $featured_q = new WP_Query([
        'post_type'      => 'product',
        'post__in'       => array_slice( $featured_ids, 0, 12 ),
        'posts_per_page' => 12,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
      ]);
    }
    if ( $featured_q && $featured_q->have_posts() ) :
      $shown = 0;
      while ( $featured_q->have_posts() ) :
        $featured_q->the_post();
        global $product;
        if ( $shown >= 6 ) {
          break;
        }
        $shown++;
        ?>
        <div class="sc-card sc-card-product">
          <a href="<?php the_permalink(); ?>" class="sc-card-thumb"><?php echo woocommerce_get_product_thumbnail( 'woocommerce_thumbnail' ); ?></a>
          <?php if ( $product && $product->is_on_sale() ) : ?>
            <span class="sc-badge-offerta">OFFERTA</span>
          <?php endif; ?>
          <h3 class="sc-card-title"><?php the_title(); ?></h3>
          <div class="sc-price"><?php echo $product ? $product->get_price_html() : ''; ?></div>
          <a href="<?php echo esc_url( $product ? $product->add_to_cart_url() : get_permalink() ); ?>" class="sc-btn sc-btn-sm sc-btn-outline add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr( get_the_ID() ); ?>">Aggiungi</a>
        </div>
        <?php
      endwhile;
      wp_reset_postdata();
    else :
      echo '<p class="sc-home-empty">Presto nuovi prodotti in evidenza. Nel frattempo esplora il negozio.</p>';
    endif;
    ?>
  </div>
</section>

<section class="sc-section sc-section-alt">
  <div class="sc-home-trust">
    <div class="sc-home-trust-item">
      <strong>Due sedi a Napoli</strong>
      <span>Via De Amicis e Galleria Policlinico</span>
    </div>
    <div class="sc-home-trust-item">
      <strong>Stampa su misura</strong>
      <span>Finiture, carta e consegna rapide</span>
    </div>
    <div class="sc-home-trust-item">
      <strong>Sempre con te</strong>
      <span>WhatsApp / Tel. +39 338 715 1199</span>
    </div>
  </div>
</section>
<?php
get_footer();
