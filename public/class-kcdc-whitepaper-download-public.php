<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://moxcar.com
 * @since      1.0.0
 *
 * @package    Kcdc_Whitepaper_Download
 * @subpackage Kcdc_Whitepaper_Download/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Kcdc_Whitepaper_Download
 * @subpackage Kcdc_Whitepaper_Download/public
 * @author     Gino Peterson <gpeterson@moxcar.com>
 */
class Kcdc_Whitepaper_Download_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The hash for the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $hash    The hash for the plugin.
	 */
	private $hash;

	/**
	 * The URL to the plugin directory.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_url    The URL to the plugin directory.
	 */
	private $plugin_url;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->hash = $this->dynamic_hash();

		 
		$this->plugin_url = KCDC_WHITEPAPER_DOWNLOAD_URL;



	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
	wp_enqueue_style(
		$this->plugin_name,
		$this->plugin_url . 'dist/kcdc-download/kcdc-download' . $this->hash . '.css',
		array(),
		$this->version,
		'all'
	);

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script(
			$this->plugin_name,
			$this->plugin_url . 'dist/kcdc-download/kcdc-download' . $this->hash . '.js',
			array(),
			$this->version,
			true
		);

	}

	/**
	 * Adds defer and async attributes to the plugin's enqueued script.
	 *
	 * @since    1.0.0
	 * @param    string $tag    The <script> tag for the enqueued script.
	 * @param    string $handle The script's registered handle.
	 * @param    string $src    The script's source URL.
	 * @return   string Modified script tag with defer and/or async.
	 */
	public function add_defer_async_attributes( $tag, $handle, $src ) {
		if ( $handle === $this->plugin_name ) {
			// You can choose 'defer', 'async', or both
			return str_replace( '<script ', '<script defer async ', $tag );
		}
		return $tag;
	}


	function dynamic_hash() {
		$directory_path = plugin_dir_path(dirname(__FILE__, 1)) . 'dist/app/';
		$files = scandir($directory_path);
		$first_file = '';
		foreach ($files as $file) {
			if (!is_dir($directory_path . $file)) {
				$first_file = $file;
				break;
			}
		}
		$hash_parts = explode('-wp', $first_file);
		$hash = isset($hash_parts[1]) ? $hash_parts[1] : '';
		$hash_parts = explode('.', $hash);
		$hash = isset($hash_parts[0]) ? $hash_parts[0] : '';
		return '-wp' . $hash;
	}

}
