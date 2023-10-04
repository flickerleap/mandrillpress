<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://flickerleap.com
 * @since      1.0.2
 *
 * @package    Mandrillpress
 * @subpackage Mandrillpress/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mandrillpress
 * @subpackage Mandrillpress/admin
 * @author     Flicker Leap <admin@flickerleap.com>
 */
class Mandrillpress_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.2
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.2
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The settings for the plugin.
	 *
	 * @since    1.0.2
	 * @access   private
	 * @var      string    $settings    Holds the options.
	 */
	private $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.2
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $settings ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->settings = $settings;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.2
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mandrillpress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mandrillpress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mandrillpress-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.2
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mandrillpress_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mandrillpress_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mandrillpress-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register options page.
	 *
	 * @since    1.0.2
	 */
	public function options_page() {

		add_options_page(
			'Mandrill',
			'Mandrill',
			'manage_options',
			'mandrillpress',
			array( $this, 'options_page_html' )
		);

	}

	/**
	 * Output options page.
	 *
	 * @since    1.0.2
	 */
	public function options_page_html() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/mandrillpress-admin-display.php';

	}

	/**
	 * Register settings.
	 *
	 * @since    1.0.2
	 */
	public function register_settings() {

		register_setting( 'mandrillpress', 'mandrillpress' );

		add_settings_section(
			'email_settings',
			'Email Settings',
			array( $this, 'emails_settings_cb' ),
			'mandrillpress'
		);

		add_settings_field(
			'from_email',
			'From Email',
			array( $this, 'from_email_cb' ),
			'mandrillpress',
			'email_settings'
		);

		add_settings_field(
			'from_name',
			'From Name',
			array( $this, 'from_name_cb' ),
			'mandrillpress',
			'email_settings'
		);

		add_settings_field(
			'username',
			'Username',
			array( $this, 'username_cb' ),
			'mandrillpress',
			'email_settings'
		);

		add_settings_field(
			'api_key',
			'API Key',
			array( $this, 'api_key_cb' ),
			'mandrillpress',
			'email_settings'
		);

		add_settings_field(
			'subaccount',
			'Subaccount',
			array( $this, 'subaccount_cb' ),
			'mandrillpress',
			'email_settings'
		);

		add_settings_field(
			'return_path',
			'Return Path Domain',
			array( $this, 'return_path_cb' ),
			'mandrillpress',
			'email_settings'
		);

	}

	public function emails_settings_cb() {
		echo '<p>It is very important that you set the following information correctly as it will break email sending.</p>';
	}

	public function from_email_cb() {
		?>
		<input type="text" name="mandrillpress[from_email]" value="<?php echo isset( $this->settings['from_email'] ) ? esc_attr( $this->settings['from_email'] ) : ''; ?>">
		<?php
	}

	public function from_name_cb() {
		?>
		<input type="text" name="mandrillpress[from_name]" value="<?php echo isset( $this->settings['from_name'] ) ? esc_attr( $this->settings['from_name'] ) : ''; ?>">
		<?php
	}

	public function username_cb() {
		?>
		<input type="text" name="mandrillpress[username]" value="<?php echo isset( $this->settings['username'] ) ? esc_attr( $this->settings['username'] ) : ''; ?>">
		<?php
	}

	public function api_key_cb() {
		?>
		<input type="text" name="mandrillpress[api_key]" value="<?php echo isset( $this->settings['api_key'] ) ? esc_attr( $this->settings['api_key'] ) : ''; ?>">
		<?php
	}

	public function subaccount_cb() {
		?>
		<input type="text" name="mandrillpress[subaccount]" value="<?php echo isset( $this->settings['subaccount'] ) ? esc_attr( $this->settings['subaccount'] ) : ''; ?>">
		<?php
	}

	public function return_path_cb() {
		?>
		<input type="text" name="mandrillpress[return_path]" value="<?php echo isset( $this->settings['return_path'] ) ? esc_attr( $this->settings['return_path'] ) : ''; ?>">
		<?php
	}

}
