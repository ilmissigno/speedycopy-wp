<?php get_header(); ?>
<?php speedycopy_react_container_open(); ?>
<h1 class="sc-archive-title"><?php the_archive_title(); ?></h1>
<?php if(have_posts()): echo '<div class="sc-grid">'; while(have_posts()): the_post(); ?>
  <article class="sc-card sc-card-post">
    <a href="<?php the_permalink(); ?>" class="sc-card-thumb"><?php if(has_post_thumbnail()) the_post_thumbnail('medium'); ?></a>
    <h2 class="sc-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <div class="sc-card-excerpt"><?php the_excerpt(); ?></div>
  </article>
<?php endwhile; echo '</div>'; the_posts_pagination(); else: echo '<p>Nessun contenuto.</p>'; endif; ?>
<?php speedycopy_react_container_close(); ?>
<?php get_footer(); ?>