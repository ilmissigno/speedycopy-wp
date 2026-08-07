<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$legal = get_option( 'sc_legal_pages', array() );
$privacy_url = ! empty( $legal['privacy'] ) ? get_permalink( $legal['privacy'] ) : home_url( '/privacy-policy/' );
$cookie_url  = ! empty( $legal['cookie'] ) ? get_permalink( $legal['cookie'] ) : home_url( '/cookie-policy/' );
$terms_url   = ! empty( $legal['terms'] ) ? get_permalink( $legal['terms'] ) : home_url( '/termini-e-condizioni/' );
$returns_url = ! empty( $legal['returns'] ) ? get_permalink( $legal['returns'] ) : home_url( '/resi-e-rimborsi/' );
?>
</main>
<footer class="sc-footer">
  <div class="sc-footer-grid">
    <div>
      <h4>SpeedyCopy</h4>
      <p>Cancelleria e stampa personalizzata a Napoli.</p>
      <p class="sc-footer-legal-entity">Speedy Copy di Monica Gragnano<br>P.IVA 10126031219</p>
    </div>
    <div>
      <h4>Info</h4>
      <?php
      wp_nav_menu([
        'theme_location' => 'footer',
        'container'      => false,
        'fallback_cb'    => false,
      ]);
      ?>
    </div>
    <div>
      <h4>Contatti</h4>
      <p>Email: <a href="mailto:speedycopypoliclinico@gmail.com">speedycopypoliclinico@gmail.com</a><br/>Tel: +39 338 715 1199</p>
      <div class="sc-newsletter">
        <h4>Newsletter</h4>
        <p class="sc-newsletter-lead">Novità, offerte e coupon. Niente spam.</p>
        <form id="sc-newsletter-form" class="sc-newsletter-form" novalidate>
          <label class="screen-reader-text" for="sc-newsletter-email">Email</label>
          <input type="email" id="sc-newsletter-email" name="email" placeholder="La tua email" required>
          <label class="sc-newsletter-consent">
            <input type="checkbox" name="consent" value="1" required>
            <span>Acconsento al trattamento per l’invio della newsletter (<a href="<?php echo esc_url( $privacy_url ); ?>">Privacy</a>).</span>
          </label>
          <button type="submit" class="sc-btn sc-btn-sm">Iscriviti</button>
          <p class="sc-newsletter-msg" hidden></p>
        </form>
      </div>
    </div>
  </div>
  <div class="sc-copy">&copy; <?php echo esc_html( date( 'Y' ) ); ?> SpeedyCopy. Tutti i diritti riservati.</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
