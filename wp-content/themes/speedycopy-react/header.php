<?php ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="sc-header">
  <div class="sc-header-inner">
    <div class="sc-logo">
      <?php if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
      <?php else : ?>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
      <?php endif; ?>
    </div>
    <button class="sc-nav-toggle" type="button" aria-label="<?php esc_attr_e( 'Apri menu', 'speedycopy-react' ); ?>" aria-controls="scMainNav" aria-expanded="false" data-toggle-nav>
      <span class="sc-nav-toggle-bars" aria-hidden="true">
        <span></span><span></span><span></span>
      </span>
    </button>
    <nav class="sc-nav" id="scMainNav" aria-label="<?php esc_attr_e( 'Menu principale', 'speedycopy-react' ); ?>">
    <?php wp_nav_menu([
      'theme_location'=>'primary',
      'container'=>false,
      'fallback_cb'=>'speedycopy_react_menu_fallback'
    ]); ?>
    </nav>
  </div>
</header>
<main class="sc-main">
