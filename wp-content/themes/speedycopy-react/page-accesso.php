<?php
/*
Template Name: Accesso / Registrazione
*/
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<section class="sc-auth">
  <div class="sc-auth-inner">
    <div class="sc-auth-panels">
      <div class="sc-auth-panel sc-auth-login">
        <h2>Accedi</h2>
        <?php if ( ! is_user_logged_in() ) : ?>
          <?php do_action( 'woocommerce_before_customer_login_form' ); ?>
          <?php
            woocommerce_login_form( [
              'redirect' => wc_get_page_permalink( 'myaccount' ),
              'remember' => true,
            ] );
          ?>
          <div class="sc-auth-forgot">
            <a class="sc-link" href="<?php echo esc_url( wp_lostpassword_url() ); ?>">Password dimenticata?</a>
          </div>
          <div class="sc-auth-alt">
            <p class="sc-small-info">Non hai ancora un account? <a href="#" class="js-auth-switch" data-target="register">Registrati</a></p>
          </div>
        <?php else: ?>
          <p>Sei già autenticato. <a class="sc-link" href="<?php echo esc_url( wc_get_page_permalink('myaccount') ); ?>">Vai alla tua area</a>.</p>
        <?php endif; ?>
      </div>
      <div class="sc-auth-panel sc-auth-register">
        <h2>Crea un account</h2>
        <?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>
          <?php if ( ! is_user_logged_in() ) : ?>
          <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action('woocommerce_register_form_tag'); ?>>
            <?php do_action( 'woocommerce_register_form_start' ); ?>
            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ): ?>
              <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="reg_username">Username&nbsp;<span class="required">*</span></label>
                <input type="text" name="username" id="reg_username" autocomplete="username" />
              </p>
            <?php endif; ?>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
              <label for="reg_email">Email&nbsp;<span class="required">*</span></label>
              <input type="email" name="email" id="reg_email" autocomplete="email" />
            </p>
            <?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
              <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="reg_password">Password&nbsp;<span class="required">*</span></label>
                <input type="password" name="password" id="reg_password" autocomplete="new-password" />
              </p>
            <?php else: ?>
              <p class="sc-small-info">La password verrà inviata via email.</p>
            <?php endif; ?>
            <?php do_action( 'woocommerce_register_form' ); ?>
            <p class="woocommerce-form-row form-row">
              <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
              <button type="submit" class="sc-btn sc-btn-primary" name="register" value="<?php esc_attr_e( 'Registrati', 'woocommerce' ); ?>">Registrati</button>
            </p>
            <?php do_action( 'woocommerce_register_form_end' ); ?>
          </form>
          <div class="sc-auth-alt">
            <p class="sc-small-info">Hai già un account? <a href="#" class="js-auth-switch" data-target="login">Accedi</a></p>
          </div>
          <?php else: ?>
            <p>Sei già autenticato. <a class="sc-link" href="<?php echo esc_url( wc_get_page_permalink('myaccount') ); ?>">Vai alla tua area</a>.</p>
          <?php endif; ?>
        <?php else: ?>
          <p>La registrazione è disabilitata nelle impostazioni WooCommerce.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<script>
  (function(){
    const switches = document.querySelectorAll('.js-auth-switch');
    const loginPanel = document.querySelector('.sc-auth-login');
    const registerPanel = document.querySelector('.sc-auth-register');
    if(!loginPanel || !registerPanel) return;
    function show(which){
      if(which==='register'){ loginPanel.classList.add('is-hidden'); registerPanel.classList.remove('is-hidden'); }
      else { registerPanel.classList.add('is-hidden'); loginPanel.classList.remove('is-hidden'); }
      window.scrollTo({ top: loginPanel.offsetTop - 40, behavior:'smooth'});
    }
    switches.forEach(sw=> sw.addEventListener('click', e=>{ e.preventDefault(); show(sw.dataset.target); }));
  })();
</script>
<?php get_footer(); ?>
