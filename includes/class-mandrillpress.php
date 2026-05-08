<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://flickerleap.com
 * @since      1.0.2
 *
 * @package    Mandrillpress
 * @subpackage Mandrillpress/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.2
 * @package    Mandrillpress
 * @subpackage Mandrillpress/includes
 * @author     Flicker Leap <admin@flickerleap.com>
 */
class Mandrillpress {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.2
	 * @access   protected
	 * @var      Mandrillpress_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.2
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.2
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The settings for the plugin.
	 *
	 * @since    1.0.2
	 * @access   private
	 * @var      string    $settings    Holds the options.
	 */
	private $settings;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.2
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_VERSION' ) ) {
			$this->version = PLUGIN_VERSION;
		} else {
			$this->version = '1.0.2';
		}
		$this->plugin_name = 'mandrillpress';

		$this->settings = get_network_option( 1, 'mandrillpress', array() );

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Mandrillpress_Loader. Orchestrates the hooks of the plugin.
	 * - Mandrillpress_i18n. Defines internationalization functionality.
	 * - Mandrillpress_Admin. Defines all hooks for the admin area.
	 * - Mandrillpress_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.2
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mandrillpress-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mandrillpress-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mandrillpress-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mandrillpress-public.php';

		$this->loader = new Mandrillpress_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Mandrillpress_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.2
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Mandrillpress_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.2
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Mandrillpress_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_settings() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		if ( get_current_blog_id() == 1 ) {
			$this->loader->add_action( 'admin_menu', $plugin_admin, 'options_page' );
			$this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
		}

		if ( is_multisite() ) {
			$this->loader->add_action( 'updated_option', $this, 'update_network_option', 10, 3 );
		}

		$this->loader->add_action( 'phpmailer_init', $this, 'use_mandrill' );
		$this->loader->add_action( 'wp_mail_failed', $this, 'log_mail_failure' );

	}

	/**
	 * Syncs an updated network option to the primary network site.
	 *
	 * @since  1.0.2
	 * @param  string $option    Name of the network option.
	 * @param  mixed  $old_value The old option value.
	 * @param  mixed  $value     The new option value.
	 * @return void
	 */
	public function update_network_option( $option, $old_value, $value ) {
		if ( 'mandrillpress' == $option ) {
			update_network_option( 1, $option, $value );
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.2
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Mandrillpress_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'phpmailer_init', $this, 'use_mandrill' );
		$this->loader->add_action( 'wp_mail_failed', $this, 'log_mail_failure' );

	}

	/**
	 * Configures PHPMailer to send via Mandrill's SMTP gateway.
	 *
	 * Hooked to `phpmailer_init`. Bails silently when required settings are
	 * absent and preserves any sender address that shares the configured
	 * domain and was not generated by WordPress itself.
	 *
	 * @since  1.0.2
	 * @param  \PHPMailer\PHPMailer\PHPMailer $phpmailer The PHPMailer instance.
	 * @return void
	 */
	public function use_mandrill( $phpmailer ) {

		if ( empty( $this->settings )
			|| empty( $this->settings['from_email'] )
			|| empty( $this->settings['from_name'] )
			|| empty( $this->settings['username'] )
			|| empty( $this->settings['api_key'] )
		) {
			$this->log( 'Mandrill not configured — missing required settings, falling back to default mailer', array(
				'has_from_email' => ! empty( $this->settings['from_email'] ),
				'has_from_name'  => ! empty( $this->settings['from_name'] ),
				'has_username'   => ! empty( $this->settings['username'] ),
				'has_api_key'    => ! empty( $this->settings['api_key'] ),
			) );
			return;
		}

		$phpmailer->isSMTP();
		$phpmailer->set( 'SMTPAuth', true );
		$phpmailer->set( 'SMTPSecure', 'tls' );

		$phpmailer->SMTPOptions = array(
		    'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
		    )
		);

		$phpmailer->set( 'Host', 'smtp.mandrillapp.com' );
		$phpmailer->set( 'Port', '587' );

		$from_email_address = $this->settings['from_email'];
		$requested_from_email_address = $phpmailer->From;

		$from_email_address_domain = explode('@', $from_email_address)[1];
		$requested_from_email_address_parts  = explode('@', $requested_from_email_address);
		$requested_from_email_address_user   = $requested_from_email_address_parts[0];
		$requested_from_email_address_domain = $requested_from_email_address_parts[1];
		if(
			$from_email_address_domain === $requested_from_email_address_domain
			&& false === stripos( $requested_from_email_address_user, 'wordpress' )
		) {
			$email = $requested_from_email_address;
		}else{
			$email = $from_email_address;
		}

		$phpmailer->setFrom($email, $this->settings['from_name'] );

		$phpmailer->set( 'Username', $this->settings['username'] );
		$phpmailer->set( 'Password', $this->settings['api_key'] );

		if ( isset( $this->settings['subaccount'] ) && ! empty( $this->settings['subaccount'] ) ) {
			$phpmailer->AddCustomHeader( sprintf( '%1$s: %2$s', 'X-MC-Subaccount', $this->settings['subaccount'] ) );
		}

		if ( isset( $this->settings['return_path'] ) && ! empty( $this->settings['return_path'] ) ) {
			$phpmailer->AddCustomHeader( sprintf( '%1$s: %2$s', 'X-MC-ReturnPathDomain', $this->settings['return_path'] ) );
		}

	}

	/**
	 * Logs a mail failure triggered by the `wp_mail_failed` action.
	 *
	 * @since  1.0.2
	 * @param  \WP_Error $wp_error The error object populated by wp_mail().
	 * @return void
	 */
	public function log_mail_failure( $wp_error ) {
		$this->log( 'FAILED — wp_mail_failed fired', array(
			'error' => $wp_error->get_error_message(),
			'data'  => $wp_error->get_error_data(),
		), 'error' );
	}

	/**
	 * Writes a message to the WooCommerce log under the mandrillpress source.
	 *
	 * No-ops when debug logging is disabled or WooCommerce is not active.
	 *
	 * @since  1.0.2
	 * @access private
	 * @param  string $message The log message.
	 * @param  array  $context Optional key/value pairs appended as JSON.
	 * @param  string $level   WC_Log_Levels level (e.g. 'info', 'error').
	 * @return void
	 */
	private function log( $message, $context = array(), $level = 'info' ) {
		if ( empty( $this->settings['debug_log'] ) || ! function_exists( 'wc_get_logger' ) ) {
			return;
		}

		if ( ! empty( $context ) ) {
			$message .= ' | ' . wp_json_encode( $context );
		}

		wc_get_logger()->log( $level, $message, array( 'source' => 'mandrillpress' ) );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.2
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.2
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.2
	 * @return    Mandrillpress_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.2
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the settings of the plugin.
	 *
	 * @since     1.0.2
	 * @return    string    The settings of the plugin.
	 */
	public function get_settings() {
		return $this->settings;
	}

}
