<?php
get_header();
?>
<section class="sc-hero">
  <div class="sc-hero-inner">
    <h1>Creatività in ogni penna.</h1>
    <p>Forniture di cancelleria curate per studio, ufficio e ispirazione quotidiana.</p>
    <div class="sc-hero-extra">
      <form class="sc-hero-search" role="search" method="get" action="<?php echo esc_url( home_url('/') ); ?>">
        <svg fill="none" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="search" name="s" placeholder="Cerca penne, quaderni, articoli..." value="<?php echo get_search_query(); ?>" />
        <input type="hidden" name="post_type" value="product" />
      </form>
      <div class="sc-hero-highlights">
        <div class="sc-hero-bullet"><span>🚚</span>Spedizioni rapide 24/48h</div>
        <div class="sc-hero-bullet"><span>⭐</span>Selezione qualità premium</div>
        <div class="sc-hero-bullet"><span>💳</span>Pagamenti sicuri</div>
        <div class="sc-hero-bullet"><span>🖨️</span>Servizio stampa personalizzata</div>
      </div>
      <div>
        <a class="sc-btn sc-btn-primary" href="<?php echo esc_url( get_permalink( wc_get_page_id('shop') ) ); ?>">Vai al negozio</a>
      </div>
    </div>
  </div>
</section>
<section class="sc-section">
  <h2 class="sc-section-title">Categorie popolari</h2>
  <div class="sc-grid">
    <?php
    $terms = get_terms(['taxonomy'=>'product_cat','hide_empty'=>true,'number'=>6]);
    foreach($terms as $term){
      $thumb_id = get_term_meta($term->term_id,'thumbnail_id', true);
      $img = $thumb_id ? wp_get_attachment_image($thumb_id,'medium') : '<div class="sc-cat-fallback">'.$term->name.'</div>';
      echo '<a class="sc-card sc-card-cat" href="'.esc_url(get_term_link($term)).'">'.$img.'<h3>'.$term->name.'</h3></a>';
    }
    ?>
  </div>
</section>
<section class="sc-section">
  <h2 class="sc-section-title">Prodotti in evidenza</h2>
  <div class="sc-grid">
    <?php
    // WooCommerce moderno usa la tassonomia "product_visibility" (termine "featured") oppure la funzione helper wc_get_featured_product_ids
    $featured_ids = wc_get_featured_product_ids();
    // Limito a 6 e preservo l'ordine recente
    if( ! empty($featured_ids) ) {
      $featured_q = new WP_Query([
        'post_type'      => 'product',
        'post__in'       => array_slice($featured_ids, 0, 12), // prendo un po' di margine e poi taglio a 6 nel loop
        'posts_per_page' => 12,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
      ]);
    }
    if( ! empty($featured_q) && $featured_q->have_posts() ):
      $shown = 0;
      while($featured_q->have_posts()): $featured_q->the_post(); global $product; if($shown >= 6) break; $shown++; ?>
        <div class="sc-card sc-card-product">
          <a href="<?php the_permalink(); ?>" class="sc-card-thumb"><?php echo woocommerce_get_product_thumbnail('woocommerce_thumbnail'); ?></a>
          <?php if( $product->is_on_sale() ) : ?>
            <span class="sc-badge-offerta">OFFERTA</span>
          <?php endif; ?>
          <h3 class="sc-card-title"><?php the_title(); ?></h3>
          <div class="sc-price"><?php echo $product->get_price_html(); ?></div>
          <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="sc-btn sc-btn-sm sc-btn-outline add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>">Aggiungi</a>
        </div>
      <?php endwhile; wp_reset_postdata(); else: echo '<p>Nessun prodotto in evidenza o non ancora contrassegnato come "In evidenza".</p>'; endif; ?>
  </div>
</section>
<section class="sc-section sc-section-alt">
  <div class="sc-cta">
    <h2>Offerte della settimana</h2>
    <p>Sconti speciali su set premium e articoli scelti.</p>
    <a href="<?php echo esc_url( get_permalink( wc_get_page_id('shop') ) ); ?>" class="sc-btn sc-btn-primary">Scopri ora</a>
  </div>
</section>
<?php
get_footer();
