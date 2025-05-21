<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://moxcar.com
 * @since      1.0.0
 *
 * @package    Kcdc_Whitepaper_Download
 * @subpackage Kcdc_Whitepaper_Download/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Kcdc_Whitepaper_Download
 * @subpackage Kcdc_Whitepaper_Download/includes
 * @author     Gino Peterson <gpeterson@moxcar.com>
 */
class Kcdc_Whitepaper_Download_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'kcdc-whitepaper-download',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
