<?php
/* Load Class */
WooCommerce_Simple_Registration_WC_Social_Login::get_instance();

/**
 * WooCommerce Social Login Support
 * @link http://www.woothemes.com/products/woocommerce-social-login/
 * @since 1.3.0
**/
class WooCommerce_Simple_Registration_WC_Social_Login{

	/**
	 * Returns the instance.
	 */
	public static function get_instance(){
		static $instance = null;
		if ( is_null( $instance ) ) $instance = new self;
		return $instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

		/* Add Simple Registration Shortcode in "Display" Settings  */
		add_filter( 'woocommerce_social_login_settings', array( $this, 'settings' ) );

		/* Render Buttons */
		add_action( 'woocommerce_register_form_end', array( $this, 'render_social_login_buttons' ) );
	}

	/**
	 * Display Settings
	 */
	public function settings( $settings ){

		/* Get the section */
		$key = false;
		foreach( $settings as $k => $v ){
			if( isset( $v['id'] ) && 'wc_social_login_display' == $v['id'] ){
				$key = $k;
				break;
			}
		}
		/* Bail, if section not found */
		if( false === $key ){
			return $settings;
		}

		/* Add "Simple Registration" in Display Option So User can enable/disable it. */
		$settings[$key]['options']['woocommerce_simple_registration'] = __( 'Simple Registration', 'woocommerce-simple-registration' );

		return $settings;
	}

	/**
	 * Whether social login buttons are displayed on simple registration shortcode
	 */
	public function is_displayed_on( $handle = 'woocommerce_simple_registration' ) {
		return in_array( 'woocommerce_simple_registration', apply_filters( 'wc_social_login_display', (array) get_option( 'wc_social_login_display', array() ) ) );
	}

	/**
	 * Render social login buttons on frontend
	 */
	public function render_social_login_buttons() {
		/* Bail if not enabled in simple registration output */
		if( ! $this->is_displayed_on() ){
			return;
		}
		/* Bail if in account page and this page already have login button. */
		if ( is_account_page() && $this->is_displayed_on( 'my_account' ) ) {
			return;
		}
		/* Render button. */
		woocommerce_social_login_buttons( wc_get_page_permalink( 'myaccount' ) );
	}

}

