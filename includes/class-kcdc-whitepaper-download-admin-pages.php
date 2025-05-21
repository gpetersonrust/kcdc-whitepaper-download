<?php

/**
 * The admin pages functionality of the plugin.
 *
 * @link       https://moxcar.com
 * @since      1.0.0
 *
 * @package    KCDC_Whitepaper_Download
 * @subpackage KCDC_Whitepaper_Download/includes
 */

/**
 * The admin pages functionality of the plugin.
 *
 * Handles the creation and management of admin pages and settings.
 *
 * @package    KCDC_Whitepaper_Download
 * @subpackage KCDC_Whitepaper_Download/includes
 * @author     Gino Peterson <gpeterson@moxcar.com>
 */
class KCDC_Whitepaper_Download_Admin_Pages {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      KCDC_Whitepaper_Download_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

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
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version          The version of this plugin.
     * @param    KCDC_Whitepaper_Download_Loader    $loader    The loader object.
     */
    public function __construct($plugin_name, $version, $loader) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->loader = $loader;

     

        $this->init_admin_pages();
    }

    /**
     * Initialize admin pages and settings.
     *
     * @since    1.0.0
     * @access   private
     */
    private function init_admin_pages() {
        // Add menu items and register settings
        $this->loader->add_action('admin_menu', $this, 'add_plugin_admin_menu');
 
    }

    /**
     * Add menu items to the WordPress admin menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            'KCDC Whitepaper', // Page title
            'KCDC Whitepaper', // Menu title
            'manage_options',   // Capability required
            'kcdc-whitepaper', // Menu slug
            function() {       // Callback function
            echo '<div class="wrap">';
            echo '<h1>KCDC Whitepaper Download</h1>';
            echo '<p>Hello World!</p>';
            echo '</div>';
            },
            'dashicons-download', // Icon
            30                    // Position
        );
    }
 
}