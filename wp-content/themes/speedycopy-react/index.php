<?php get_header(); ?>
<?php speedycopy_react_container_open(); ?>
<h1><?php bloginfo('name'); ?></h1>
<?php if(have_posts()): while(have_posts()): the_post(); ?>
  <article class="sc-post">
    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <div class="sc-meta"><?php the_time('d/m/Y'); ?></div>
    <div class="sc-excerpt"><?php the_excerpt(); ?></div>
  </article>
<?php endwhile; the_posts_pagination(); else: echo '<p>Nessun contenuto.</p>'; endif; ?>
<?php speedycopy_react_container_close(); ?>
<?php get_footer(); ?>