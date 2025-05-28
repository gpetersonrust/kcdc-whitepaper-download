<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://moxcar.com
 * @since      1.0.0
 *
 * @package    Kcdc_Whitepaper_Download
 * @subpackage Kcdc_Whitepaper_Download/includes
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
 * @package    Kcdc_Whitepaper_Download
 * @subpackage Kcdc_Whitepaper_Download/includes
 * @author     Gino Peterson <gpeterson@moxcar.com>
 */
class Kcdc_Whitepaper_Download {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Kcdc_Whitepaper_Download_Loader    $loader    Maintains and registers all hooks for the plugin.
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
		if ( defined( 'KCDC_WHITEPAPER_DOWNLOAD_VERSION' ) ) {
			$this->version = KCDC_WHITEPAPER_DOWNLOAD_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'kcdc-whitepaper-download';

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
	 * - Kcdc_Whitepaper_Download_Loader. Orchestrates the hooks of the plugin.
	 * - Kcdc_Whitepaper_Download_i18n. Defines internationalization functionality.
	 * - Kcdc_Whitepaper_Download_Admin. Defines all hooks for the admin area.
	 * - Kcdc_Whitepaper_Download_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kcdc-whitepaper-download-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kcdc-whitepaper-download-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-kcdc-whitepaper-download-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-kcdc-whitepaper-download-public.php';



		$this->loader = new Kcdc_Whitepaper_Download_Loader();
		// $this->loader->add_action('init', $this, 'kcdc_register_download_endpoint');

		#custom includes 

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kcdc-whitepaper-download-admin-pages.php';

		$admin_pages = new KCDC_Whitepaper_Download_Admin_Pages($this->get_plugin_name(), $this->get_version(), $this->loader);
 

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kcdc-whitepaper-download-form-handler.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kcdc-whitepaper-db.php';

		$db = new Kcdc_Whitepaper_DB();
		$form_handler = new Kcdc_Whitepaper_Form_Handler($db);
		$form_handler->register($this->loader);

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kcdc-whitepaper-download-shortcode.php';

		$shortcode = new Kcdc_Whitepaper_Shortcode($this->get_plugin_name(), $this->get_version(), $this->loader);

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-kcdc-white-paper-post-type.php';

		$post_type = new Kcdc_Whitepaper_Post_Type($this->get_plugin_name(), $this->get_version(), $this->loader);

		 
 

	


	} 

 

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Kcdc_Whitepaper_Download_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Kcdc_Whitepaper_Download_i18n();

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

		$plugin_admin = new Kcdc_Whitepaper_Download_Admin($this->get_plugin_name(), $this->get_version());
		$plugin_admin->register_hooks();

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Kcdc_Whitepaper_Download_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter('script_loader_tag', $plugin_public, 'add_defer_async_attributes', 10, 3);

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
	 * @return    Kcdc_Whitepaper_Download_Loader    Orchestrates the hooks of the plugin.
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
?>