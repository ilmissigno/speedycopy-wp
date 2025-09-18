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
      <a href="<?php echo esc_url(home_url('/')); ?>">
        <?php if( function_exists('the_custom_logo') && has_custom_logo() ) { the_custom_logo(); } else { bloginfo('name'); } ?>
      </a>
    </div>
    <nav class="sc-nav" id="scMainNav">
    <?php wp_nav_menu([
      'theme_location'=>'primary',
      'container'=>false,
      'fallback_cb'=>'speedycopy_react_menu_fallback'
    ]); ?>
    </nav>
    <button class="sc-nav-toggle" aria-label="Menu" data-toggle-nav>☰</button>
  </div>
</header>
<main class="sc-main">
