<?php
/**
 * Registration form.
 *
 * @author 	Jeroen Sormani
 * @package 	WooCommerce-Simple-Registration
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;


?><div class="registration-form woocommerce">

	<?php wc_print_notices(); ?>

	<h2><?php _e( 'Register', 'woocommerce' ); ?></h2>

	<form method="post" class="register">

		<?php do_action( 'woocommerce_register_form_start' ); ?>

		<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

			<p class="form-row form-row-wide">
				<label for="reg_username"><?php _e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) echo esc_attr( $_POST['username'] ); ?>" />
			</p>

		<?php endif; ?>

		<p class="form-row form-row-wide">
			<label for="reg_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
			<input type="email" class="input-text" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) echo esc_attr( $_POST['email'] ); ?>" />
		</p>

		<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

			<p class="form-row form-row-wide">
				<label for="reg_password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="password" class="input-text" name="password" id="reg_password" />
			</p>

		<?php endif; ?>

		<!-- Spam Trap -->
		<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'woocommerce' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

		<?php do_action( 'woocommerce_register_form' ); ?>
		<?php do_action( 'register_form' ); ?>

		<p class="form-row">
			<?php wp_nonce_field( 'woocommerce-register' ); ?>
			<input type="submit" class="button" name="register" value="<?php _e( 'Register', 'woocommerce' ); ?>" />
		</p>

		<?php do_action( 'woocommerce_register_form_end' ); ?>

	</form>

</div>
