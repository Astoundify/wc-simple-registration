<?php
/**
 * Plugin Name: Simple Registration for WooCommerce
 * Plugin URI: https://astoundify.com/products/woocommerce-simple-registration/
 * Description: A simple plugin to add a [woocommerce_simple_registration] shortcode to display the registration form on a separate page.
 * Version: 1.4.0
 * Author: Astoundify
 * Author URI: https://astoundify.com/
 * Text Domain: woocommerce-simple-registration
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WooCommerce_Simple_Registration.
 *
 * Main WooCommerce_Simple_Registration class initializes the plugin.
 *
 * @class		WooCommerce_Simple_Registration
 * @version		1.0.0
 * @author		Jeroen Sormani
 */
class WooCommerce_Simple_Registration {


	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string $version Plugin version number.
	 */
	public $version = '1.4.0';


	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 * @var string $file Plugin file path.
	 */
	public $file = __FILE__;


	/**
	 * Instace of WooCommerce_Simple_Registration.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance The instance of WooCommerce_Simple_Registration.
	 */
	private static $instance;


	/**
	 * Construct.
	 *
	 * Initialize the class and plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		// Initialize plugin parts
		$this->init();

		// woocommerce_simple_registration shortcode
		add_shortcode( 'woocommerce_simple_registration', array( $this, 'registration_template' ) );

		// add a body class on this page
		add_filter( 'body_class', array( $this, 'body_class' ) );

		// add first name and last name to register form
		add_action( 'woocommerce_register_form_start', array( $this, 'add_name_input' ) );
		add_action( 'woocommerce_created_customer', array( $this, 'save_name_input' ) );

		// Settings.
		add_filter( 'woocommerce_account_settings', array( $this, 'account_settings' ) );

		// Filter WP Register URL.
		add_filter( 'register_url', array( $this, 'register_url' ) );

		/**
		 * WooCommerce Social Login Support
		 * @link http://www.woothemes.com/products/woocommerce-social-login/
		 * @since 1.3.0
		 */
		if( function_exists( 'init_woocommerce_social_login' ) ){
			require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'includes/wc-social-login.php' );
		}
	}

	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.0
	 * @return object Instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) )  {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * init.
	 *
	 * Initialize plugin parts.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->load_textdomain();
	}

	/**
	 * Textdomain.
	 *
	 * Load the textdomain based on WP language.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-simple-registration', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}


	/**
	 * Registartion template.
	 *
	 * Return the registration template contents.
	 *
	 * @return string HTML registration form template.
	 */
	public function registration_template() {

		ob_start();

			if ( ! is_user_logged_in() ) :

				$message = apply_filters( 'woocommerce_registration_message', '' );

				if ( ! empty( $message ) ) :
					wc_add_notice( $message );
				endif;

				wc_get_template( 'registration-form.php', array(), 'woocommerce-simple-registration/', plugin_dir_path( __FILE__ ) . 'templates/' );

			else :
				echo do_shortcode( '[woocommerce_my_account]' );
			endif;

			$return = ob_get_contents();
		ob_end_clean();

		return $return;

	}

	/**
	* Add body classes for WC Simple Register page.
	*
	* @since 1.2.0
	* @param  array $classes
	* @return array
	*/
	public function body_class( $classes ) {
		if( is_singular() && $post_data = get_post( get_queried_object_id() ) ){
			if ( isset( $post_data->post_content ) && has_shortcode( $post_data->post_content, 'woocommerce_simple_registration' ) ) {
				$classes[] = 'woocommerce-register';
				$classes[] = 'woocommerce-account';
				$classes[] = 'woocommerce-page';
			}
		}
		return $classes;
	}

	/**
	 * Add First Name & Last Name
	 * To disable this simply use this code:
	 * `add_filter( 'woocommerce_simple_registration_name_fields', '__return_false' );`
	 * @since 1.3.0
	 */
	public function add_name_input(){
		// Name Field Option.
		$enabled = 'yes' === WC_Admin_Settings::get_option( 'woocommerce_simple_registration_name_fields', 'yes' ) ? true : false;
		$required = 'yes' === WC_Admin_Settings::get_option( 'woocommerce_simple_registration_name_fields_required', 'no' ) ? true : false;

		/* Filter to disable this feature. */
		if( ! apply_filters( 'woocommerce_simple_registration_name_fields', true ) || ! $enabled ){
			return;
		}
		?>
		<p class="woocommerce-FormRow woocommerce-FormRow--first form-row form-row-first">
			<label for="reg_sr_firstname"><?php _e( 'First Name', 'woocommerce-simple-registration' ); ?><?php echo( $required ? ' <span class="required">*</span>' : '' ) ?></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="sr_firstname" id="reg_sr_firstname" value="<?php if ( ! empty( $_POST['sr_firstname'] ) ) echo esc_attr( $_POST['sr_firstname'] ); ?>" <?php echo( $required ? ' required' : '' ) ?>/>
		</p>

		<p class="woocommerce-FormRow woocommerce-FormRow--last form-row form-row-last">
			<label for="reg_sr_lastname"><?php _e( 'Last Name', 'woocommerce-simple-registration' ); ?><?php echo( $required ? ' <span class="required">*</span>' : '' ) ?></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="sr_lastname" id="reg_sr_lastname" value="<?php if ( ! empty( $_POST['sr_lastname'] ) ) echo esc_attr( $_POST['sr_lastname'] ); ?>" <?php echo( $required ? ' required' : '' ) ?> />
		</p>
		<?php
	}

	/**
	 * Save First Name and Last Name
	 * @since 1.3.0
	 * @see WC/includes/wc-user-functions.php line 114
	 */
	public function save_name_input( $customer_id ){
		// Name Field Option.
		$enable = 'yes' === WC_Admin_Settings::get_option( 'woocommerce_simple_registration_name_fields', 'yes' ) ? true : false;

		/* Filter to disable this feature. */
		if( ! apply_filters( 'woocommerce_simple_registration_name_fields', true ) || ! $enable ){
			return;
		}

		/* Strip slash everything */
		$request = stripslashes_deep( $_POST );

		/* Save First Name */
		if ( isset( $request['sr_firstname'] ) && !empty( $request['sr_firstname'] ) ) {
			update_user_meta( $customer_id, 'first_name', sanitize_text_field( $request['sr_firstname'] ) );
		}
		/* Save Last Name */
		if ( isset( $request['sr_lastname'] ) && !empty( $request['sr_lastname'] ) ) {
			update_user_meta( $customer_id, 'last_name', sanitize_text_field( $request['sr_lastname'] ) );
		}
	}

	/**
	 * Settings
	 *
	 * @since 1.5.0
	 *
	 * @param array $settings WooCommerce Accounts Settings.
	 * @return array
	 */
	public function account_settings( $settings ) {
		$page = array(
			array(
				'title'         => __( 'Registration page', 'woocommerce-simple-registration' ),
				'desc'          => __( 'Use this page as WordPress registration URL. Page contents: [woocommerce_simple_registration]', 'woocommerce-simple-registration' ),
				'id'            => 'woocommerce_simple_registration_register_page',
				'default'       => '',
				'type'          => 'single_select_page',
				'args'          => array(
					'option_none_value' => 0,
					'show_option_none' => esc_html__( 'Select a page&hellip;', 'woocommerce-simple-registration' ),
				),
				'class'         => 'wc-enhanced-select',
				'css'           => 'min-width:300px;',
				'desc_tip'      => true,
			),
		);

		array_splice( $settings, 2, 0, $page );

		$name = array(
			array(
				'desc'          => __( 'Enable first and last name fields.', 'woocommerce-simple-registration' ),
				'id'            => 'woocommerce_simple_registration_name_fields',
				'default'       => 'yes',
				'checkboxgroup'   => '',
				'type'          => 'checkbox',
			),
			array(
				'desc'          => __( 'Require first and last name fields.', 'woocommerce-simple-registration' ),
				'id'            => 'woocommerce_simple_registration_name_fields_required',
				'default'       => 'no',
				'checkboxgroup'   => '',
				'type'          => 'checkbox',
			),
		);

		array_splice( $settings, 9, 0, $name );

		return $settings;
	}

	/**
	 * Register URL
	 *
	 * @since 1.5.0
	 *
	 * @param string $url Registration URL.
	 * @return string $url
	 */
	public function register_url( $url ) {
		$register_page = WC_Admin_Settings::get_option( 'woocommerce_simple_registration_register_page', '' );

		if ( $register_page && get_permalink( $register_page ) ) {
			$url = esc_url( get_permalink( $register_page ) );
		}

		return $url;
	}

}


/**
 * The main function responsible for returning the WooCommerce_Simple_Registration object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php WooCommerce_Simple_Registration()->method_name(); ?>
 *
 * @since 1.0.0
 *
 * @return object WooCommerce_Simple_Registration class object.
 */
if ( ! function_exists( 'WooCommerce_Simple_Registration' ) ) :

 	function WooCommerce_Simple_Registration() {
		return WooCommerce_Simple_Registration::instance();
	}

endif;

add_action( 'plugins_loaded', 'WooCommerce_Simple_Registration' );
