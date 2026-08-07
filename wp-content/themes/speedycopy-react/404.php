<?php get_header(); ?>
<section class="sc-full-center">
  <div class="sc-error-box">
    <h1>404</h1>
    <p>Pagina non trovata.</p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="sc-btn sc-btn-primary">Torna alla Home</a>
  </div>
</section>
<?php get_footer(); ?>