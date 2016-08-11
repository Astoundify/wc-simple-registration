<?php
/**
 * Plugin Name: WooCommerce Simple Registration
 * Plugin URI: https://astoundify.com/
 * Description: A simple plugin to add a [woocommerce_simple_registration] shortcode to display the registration form on a separate page.
 * Version: 1.2.0
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
	public $version = '1.0.0';


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

		// Check if WooCommerce is active
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}

		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			if ( ! is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
				return;
			}
		}

		// Initialize plugin parts
		$this->init();

		// woocommerce_simple_registration shortcode
		add_shortcode( 'woocommerce_simple_registration', array( $this, 'registration_template' ) );

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

		if ( is_null( self::$instance ) ) :
			self::$instance = new self();
		endif;

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

		// Load textdomain
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

		// Load textdomain
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

WooCommerce_Simple_Registration();
