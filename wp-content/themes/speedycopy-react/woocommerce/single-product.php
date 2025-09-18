<?php
/** Single Product advanced layout */
defined('ABSPATH') || exit;
get_header();
if ( post_password_required() ) { echo get_the_password_form(); get_footer(); return; }
if ( have_posts() ) { the_post(); }
global $product;
if( !$product ) { $product = wc_get_product( get_the_ID() ); }
?>
<div class="sc-single-wrapper">
  <div class="sc-single-gallery">
    <?php if( $product->is_on_sale() ) : ?><span class="sc-badge-offerta">OFFERTA</span><?php endif; ?>
    <?php
      $main_image_id = $product->get_image_id();
      $main_img_html = $main_image_id ? wp_get_attachment_image( $main_image_id, 'large', false, ['id'=>'scMainImage'] ) : wc_placeholder_img( 'large' );
      echo '<div class="sc-single-image">'.$main_img_html.'</div>';
      $gallery_ids = $product->get_gallery_image_ids();
      if($gallery_ids){
        echo '<div class="sc-single-thumbs" id="scThumbs">';
        foreach($gallery_ids as $gid){
          $thumb = wp_get_attachment_image($gid,'thumbnail', false,[ 'class'=>'sc-thumb-img' ]);
          $full  = wp_get_attachment_image_url($gid,'large');
          echo '<button type="button" class="sc-thumb" data-full="'.esc_url($full).'">'.$thumb.'</button>';
        }
        echo '</div>';
      }
    ?>
  </div>
  <div class="sc-single-summary">
    <h1 class="sc-single-title"><?php the_title(); ?></h1>
    <div class="sc-single-price"><?php echo $product->get_price_html(); ?></div>
    <div class="sc-single-meta-top">
      <?php
        // Stock
        if( $product->is_in_stock() ) {
          echo '<span class="sc-stock in">Disponibile</span>';
        } else {
          echo '<span class="sc-stock out">Non disponibile</span>';
        }
        // SKU
        if( $product->get_sku() ) echo '<span class="sc-sku">SKU: '.esc_html($product->get_sku()).'</span>';
      ?>
    </div>
    <div class="sc-single-short">
      <?php echo apply_filters('woocommerce_short_description', $product->get_short_description()); ?>
    </div>
    <div class="sc-single-cart"><?php woocommerce_template_single_add_to_cart(); ?></div>
    <div class="sc-single-taxonomies">
      <?php
        $cats = wc_get_product_category_list( $product->get_id(), ', ' );
        if($cats) echo '<div class="sc-meta-line"><strong>Categorie:</strong> '.$cats.'</div>';
        $tags = wc_get_product_tag_list( $product->get_id(), ', ' );
        if($tags) echo '<div class="sc-meta-line"><strong>Tag:</strong> '.$tags.'</div>';
      ?>
    </div>
  </div>
</div>

<div class="sc-single-tabs" id="scSingleTabs">
  <?php
    $tabs = apply_filters( 'woocommerce_product_tabs', [] );
    // We'll render only Description & Reviews for simplicity
  ?>
  <div class="sc-tabs-nav" role="tablist">
    <button class="sc-tab-btn is-active" data-tab-target="desc" type="button">Descrizione</button>
    <button class="sc-tab-btn" data-tab-target="reviews" type="button">Recensioni (<?php echo esc_html( $product->get_review_count() ); ?>)</button>
  </div>
  <div class="sc-tabs-panels">
    <div class="sc-tab-panel is-active" id="tab-desc" role="tabpanel">
      <?php the_content(); ?>
    </div>
    <div class="sc-tab-panel" id="tab-reviews" role="tabpanel">
      <?php comments_template(); ?>
    </div>
  </div>
</div>

<section class="sc-related">
  <?php woocommerce_output_related_products(); ?>
</section>
<?php get_footer(); ?>