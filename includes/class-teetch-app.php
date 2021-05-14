<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       reinhard@wearetraction.io
 * @since      1.0.0
 *
 * @package    Teetch_App
 * @subpackage Teetch_App/includes
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
 * @since      1.0.0
 * @package    Teetch_App
 * @subpackage Teetch_App/includes
 * @author     Rein Torres <reinhard@wearetraction.io>
 */
class Teetch_App {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Teetch_App_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TEETCH_APP_VERSION' ) ) {
			$this->version = TEETCH_APP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'teetch-app';

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
	 * - Teetch_App_Loader. Orchestrates the hooks of the plugin.
	 * - Teetch_App_i18n. Defines internationalization functionality.
	 * - Teetch_App_Admin. Defines all hooks for the admin area.
	 * - Teetch_App_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-teetch-app-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-teetch-app-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-teetch-app-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-teetch-app-public.php';

		
	


		

		$this->loader = new Teetch_App_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Teetch_App_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Teetch_App_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Teetch_App_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Teetch_App_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_get_schedule', $plugin_public, 'get_schedule' );
        $this->loader->add_action( 'wp_ajax_update_schedule', $plugin_public, 'update_schedule' );
		$this->loader->add_action( 'wp_ajax_get_teacher_availabilities', $plugin_public, 'get_teacher_availabilities' );
		$this->loader->add_action( 'wp_ajax_reserve_schedules', $plugin_public, 'reserve_schedules' );
		$this->loader->add_action( 'wp_ajax_get_student_schedules', $plugin_public, 'get_student_schedules' );
		$this->loader->add_action( 'wp_ajax_cancel_schedule', $plugin_public, 'cancel_schedule' );
		$this->loader->add_action( 'wp_ajax_get_user_messages', $plugin_public, 'get_user_messages' );
		$this->loader->add_action( 'wp_ajax_open_user_messages', $plugin_public, 'open_user_messages' );


		$this->loader->add_shortcode( 'teetchapp_calendar', $plugin_public, 'teetchapp_calendar_shortcode' );
		$this->loader->add_shortcode( 'teetchapp_set_availability', $plugin_public, 'teetchapp_set_availability_shortcode' );
		$this->loader->add_shortcode( 'teacher_show_today_schedule', $plugin_public, 'teacher_show_today_schedule_shortcode' );
		$this->loader->add_shortcode( 'student_booking_calendar', $plugin_public, 'student_booking_calendar_shortcode' );
		$this->loader->add_shortcode( 'modifier_calendar', $plugin_public, 'modifier_calendar_shortcode' );

		$this->loader->add_shortcode( 'mes_messages_popup', $plugin_public, 'mes_messages_popup_shortcode' );
		

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Teetch_App_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
