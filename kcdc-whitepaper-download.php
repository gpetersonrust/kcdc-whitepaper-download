<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://moxcar.com
 * @since             1.0.0
 * @package           Kcdc_Whitepaper_Download
 *
 * @wordpress-plugin
 * Plugin Name:       KCDC Whitepaper Download
 * Plugin URI:        https://kcdc.org
 * Description:       Provides a secure, token-based system to gate whitepaper downloads behind a form. Includes custom database storage, rate-limiting with IP blocking, one-time-use download links, admin-configurable email notifications, and a backend interface to manage submissions and settings.
 * Version:           1.0.0
 * Author:            Gino Peterson
 * Author URI:        https://moxcar.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       kcdc-whitepaper-download
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'KCDC_WHITEPAPER_DOWNLOAD_VERSION', '1.0.0' );

/**
 * Define plugin directory and URL constants
 */
define('KCDC_WHITEPAPER_DOWNLOAD_DIR', plugin_dir_path(__FILE__));
define('KCDC_WHITEPAPER_DOWNLOAD_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kcdc-whitepaper-download-activator.php
 */
function activate_kcdc_whitepaper_download() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kcdc-whitepaper-download-activator.php';
	Kcdc_Whitepaper_Download_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kcdc-whitepaper-download-deactivator.php
 */
function deactivate_kcdc_whitepaper_download() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kcdc-whitepaper-download-deactivator.php';
	Kcdc_Whitepaper_Download_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_kcdc_whitepaper_download' );
register_deactivation_hook( __FILE__, 'deactivate_kcdc_whitepaper_download' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kcdc-whitepaper-download.php';
 

	

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_kcdc_whitepaper_download() {

	$plugin = new Kcdc_Whitepaper_Download();
	$plugin->run();

}
run_kcdc_whitepaper_download();
?>