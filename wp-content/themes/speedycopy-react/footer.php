<?php ?>
</main>
<footer class="sc-footer">
  <div class="sc-footer-grid">
    <div>
      <h4>SpeedyCopy</h4>
      <p>Soluzioni di cancelleria smart e creative.</p>
    </div>
    <div>
      <h4>Info</h4>
      <?php wp_nav_menu(['theme_location'=>'footer','container'=>false,'fallback_cb'=>false]); ?>
    </div>
    <div>
      <h4>Contatti</h4>
      <p>Email: <a href="mailto:info@speedycopy.it">info@speedycopy.it</a><br/>Tel: 0123 456789</p>
    </div>
  </div>
  <div class="sc-copy">&copy; <?php echo date('Y'); ?> SpeedyCopy. Tutti i diritti riservati.</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
