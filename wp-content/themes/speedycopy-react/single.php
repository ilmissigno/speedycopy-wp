<?php get_header(); ?>
<?php speedycopy_react_container_open(); ?>
<?php if(have_posts()): while(have_posts()): the_post(); ?>
  <article class="sc-article">
    <h1 class="sc-article-title"><?php the_title(); ?></h1>
    <div class="sc-article-meta"><?php the_time('d/m/Y'); ?></div>
    <div class="sc-article-body"><?php the_content(); ?></div>
  </article>
<?php endwhile; comments_template(); endif; ?>
<?php speedycopy_react_container_close(); ?>
<?php get_footer(); ?>