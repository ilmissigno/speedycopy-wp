<?php get_header(); ?>
<?php speedycopy_react_container_open(); ?>
<?php if(have_posts()): while(have_posts()): the_post(); ?>
  <article class="sc-page">
    <h1 class="sc-page-title"><?php the_title(); ?></h1>
    <div class="sc-page-content"><?php the_content(); ?></div>
  </article>
<?php endwhile; endif; ?>
<?php speedycopy_react_container_close(); ?>
<?php get_footer(); ?>