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

		// Check if page already exists
		$existing_page = get_page_by_title('White Paper Download', OBJECT, 'page');
		
		if (!$existing_page) {
			$my_post = array(
			'post_title'    => 'White Paper Download',
			'post_content'  => '[kcdc_whitepaper_download]',
			'post_status'   => 'publish',
			'post_type'     => 'page'
			);
			
			wp_insert_post($my_post);
		}
	 
 
	$db = new Kcdc_Whitepaper_DB();
	$db->create_tables();
    $db->update_requests_table_names();
 
}
}
?>