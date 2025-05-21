<?php
require_once KCDC_WHITEPAPER_DOWNLOAD_DIR . 'includes/class-kcdc-whitepaper-db.php';

/**
 * Fired during plugin activation
 *
 * @link       https://moxcar.com
 * @since      1.0.0
 *
 * @package    Kcdc_Whitepaper_Download
 * @subpackage Kcdc_Whitepaper_Download/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Kcdc_Whitepaper_Download
 * @subpackage Kcdc_Whitepaper_Download/includes
 * @author     Gino Peterson <gpeterson@moxcar.com>
 */
class Kcdc_Whitepaper_Download_Activator {

	/**
	 * Runs during plugin activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Create custom database tables
		$db = new Kcdc_Whitepaper_DB();
		$db->create_tables();

		// Ensure custom rewrite rule is added immediately
		self::register_download_endpoint();

		// Flush rewrite rules to make new endpoint available immediately
		flush_rewrite_rules();
	}

	/**
	 * Register custom download endpoint for the whitepaper token handler.
	 * This must run on 'init' in the main plugin, and once on activation.
	 */
	public static function register_download_endpoint() {
		add_rewrite_rule('^kcdc/download/?$', 'index.php?kcdc_download=1', 'top');
		add_rewrite_tag('%kcdc_download%', '1');
	}
}
?>