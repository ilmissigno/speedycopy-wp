<?php get_header(); ?>
<?php speedycopy_react_container_open(); ?>
<?php if(have_posts()): while(have_posts()): the_post(); ?>
  <?php
    $hide_title = function_exists('is_account_page') && is_account_page() && ! is_user_logged_in();
  ?>
  <article class="sc-page<?php echo $hide_title ? ' sc-page--auth' : ''; ?>">
    <?php if ( ! $hide_title ) : ?>
      <h1 class="sc-page-title"><?php the_title(); ?></h1>
    <?php endif; ?>
    <div class="sc-page-content"><?php the_content(); ?></div>
  </article>
<?php endwhile; endif; ?>
<?php speedycopy_react_container_close(); ?>
<?php get_footer(); ?>
