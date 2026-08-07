<?php
/**
 * Login / Register Form — SpeedyCopy override
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package SpeedyCopy\Templates
 * @version 9.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
$show_register        = $registration_enabled && ! empty( $_POST['register'] );

do_action( 'woocommerce_before_customer_login_form' );
?>

<section class="sc-auth" id="customer_login">
	<div class="sc-auth-inner">
		<header class="sc-auth-brand">
			<p class="sc-auth-eyebrow">SpeedyCopy</p>
			<h1 class="sc-auth-heading"><?php echo $show_register ? esc_html__( 'Crea un account', 'speedycopy-react' ) : esc_html__( 'Accedi al tuo account', 'speedycopy-react' ); ?></h1>
			<p class="sc-auth-lead"><?php esc_html_e( 'Gestisci ordini, indirizzi e le tue stampe da un’unica area personale.', 'speedycopy-react' ); ?></p>
		</header>

		<div class="sc-auth-panels <?php echo $registration_enabled ? 'has-register' : 'login-only'; ?>">
			<div class="sc-auth-panel sc-auth-login<?php echo $show_register ? ' is-hidden' : ''; ?>">
				<h2><?php esc_html_e( 'Accedi', 'speedycopy-react' ); ?></h2>

				<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>
					<?php do_action( 'woocommerce_login_form_start' ); ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="username"><?php esc_html_e( 'Email o username', 'speedycopy-react' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>
					</p>
					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="password"><?php esc_html_e( 'Password', 'speedycopy-react' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
						<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
					</p>

					<?php do_action( 'woocommerce_login_form' ); ?>

					<p class="form-row sc-auth-remember">
						<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
							<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
							<span><?php esc_html_e( 'Ricordami', 'speedycopy-react' ); ?></span>
						</label>
					</p>

					<p class="form-row">
						<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
						<button type="submit" class="sc-btn sc-btn-primary woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Accedi', 'speedycopy-react' ); ?>"><?php esc_html_e( 'Accedi', 'speedycopy-react' ); ?></button>
					</p>

					<div class="sc-auth-forgot">
						<a class="sc-link" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Password dimenticata?', 'speedycopy-react' ); ?></a>
					</div>

					<?php do_action( 'woocommerce_login_form_end' ); ?>
				</form>

				<?php if ( $registration_enabled ) : ?>
					<div class="sc-auth-alt">
						<p class="sc-small-info"><?php esc_html_e( 'Non hai ancora un account?', 'speedycopy-react' ); ?> <a href="#register" class="js-auth-switch" data-target="register"><?php esc_html_e( 'Registrati', 'speedycopy-react' ); ?></a></p>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $registration_enabled ) : ?>
				<div class="sc-auth-panel sc-auth-register<?php echo $show_register ? '' : ' is-hidden'; ?>" id="register">
					<h2><?php esc_html_e( 'Crea un account', 'speedycopy-react' ); ?></h2>

					<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
						<?php do_action( 'woocommerce_register_form_start' ); ?>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>
							<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
								<label for="reg_username"><?php esc_html_e( 'Username', 'speedycopy-react' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
								<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>
							</p>
						<?php endif; ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_email"><?php esc_html_e( 'Email', 'speedycopy-react' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
							<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" /><?php // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>
						</p>

						<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>
							<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
								<label for="reg_password"><?php esc_html_e( 'Password', 'speedycopy-react' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
								<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
							</p>
						<?php else : ?>
							<p class="sc-small-info"><?php esc_html_e( 'Il link per impostare la password arriverà via email.', 'speedycopy-react' ); ?></p>
						<?php endif; ?>

						<?php do_action( 'woocommerce_register_form' ); ?>

						<p class="woocommerce-form-row form-row">
							<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
							<button type="submit" class="sc-btn sc-btn-primary woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Registrati', 'speedycopy-react' ); ?>"><?php esc_html_e( 'Registrati', 'speedycopy-react' ); ?></button>
						</p>

						<?php do_action( 'woocommerce_register_form_end' ); ?>
					</form>

					<div class="sc-auth-alt">
						<p class="sc-small-info"><?php esc_html_e( 'Hai già un account?', 'speedycopy-react' ); ?> <a href="#login" class="js-auth-switch" data-target="login"><?php esc_html_e( 'Accedi', 'speedycopy-react' ); ?></a></p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php if ( $registration_enabled ) : ?>
<script>
(function () {
  var switches = document.querySelectorAll('.js-auth-switch');
  var loginPanel = document.querySelector('.sc-auth-login');
  var registerPanel = document.querySelector('.sc-auth-register');
  var heading = document.querySelector('.sc-auth-heading');
  if (!loginPanel || !registerPanel) return;

  function show(which) {
    var isRegister = which === 'register';
    loginPanel.classList.toggle('is-hidden', isRegister);
    registerPanel.classList.toggle('is-hidden', !isRegister);
    if (heading) {
      heading.textContent = isRegister ? 'Crea un account' : 'Accedi al tuo account';
    }
    var target = isRegister ? registerPanel : loginPanel;
    window.scrollTo({ top: Math.max(0, target.getBoundingClientRect().top + window.scrollY - 80), behavior: 'smooth' });
  }

  switches.forEach(function (sw) {
    sw.addEventListener('click', function (e) {
      e.preventDefault();
      show(sw.dataset.target);
    });
  });

  if (window.location.hash === '#register') {
    show('register');
  }
})();
</script>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
