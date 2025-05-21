<?php

/**
 * Class Kcdc_Whitepaper_Download_Public
 *
 * The public-facing functionality of the WordPress plugin for KCDC whitepaper downloads.
 * Handles the registration and enqueuing of styles and scripts for the public interface.
 *
 * This class is responsible for:
 * - Initializing plugin name and version
 * - Enqueuing required CSS and JS files with dynamic hash versioning
 * - Adding defer/async attributes to script tags
 * - Generating dynamic hash for asset versioning
 *
 * @package    Kcdc_Whitepaper_Download
 * @subpackage Kcdc_Whitepaper_Download/public
 * @author     Gino Peterson <gpeterson@moxcar.com>
 * @since      1.0.0
 *
 * @property-read string $plugin_name The ID/name of this plugin
 * @property-read string $version The current version of this plugin
 * @property-read string $hash Dynamically generated hash for asset versioning
 * @property-read string $plugin_url The URL to the plugin directory
 */

class  Kcdc_Whitepaper_Download_Admin  {
 
    private $plugin_name;
    private $version;
    private $hash;
    private $plugin_url;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
       
        $this->hash = $this->dynamic_hash();
        $this->plugin_url = KCDC_WHITEPAPER_DOWNLOAD_URL;

   
    }

 

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name . '-admin',
            $this->plugin_url . 'dist/kcdc-download-admin/kcdc-download-admin' . $this->hash . '.css',
            array(),
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name . '-admin',
            $this->plugin_url . 'dist/kcdc-download-admin/kcdc-download-admin' . $this->hash . '.js',
            array(),
            $this->version,
            true
        );
    }

   

    function dynamic_hash() {
        $directory_path = plugin_dir_path(dirname(__FILE__)) . 'dist/app/';
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
