
<?php
/**
 * Class Kcdc_Whitepaper_Download_Router
 * 
 * Handles routing for whitepaper download functionality.
 */

/**
 * Registers the template filter with the loader.
 *
 * @param object $loader The loader object that manages all filters and actions.
 * @return void
 */

/**
 * Loads the download template when the kcdc_download query variable is set.
 *
 * @param string $template The path of the template to load.
 * @return string Returns either the download template path or the original template path.
 */

class Kcdc_Whitepaper_Download_Router {
	public function register($loader) {
		$loader->add_filter('template_include', $this, 'load_download_template');
	}

	public function load_download_template($template) {
		if (get_query_var('kcdc_download') === '1') {
			return KCDC_WHITEPAPER_DOWNLOAD_DIR . 'views/public/download-page.php';
		}
		return $template;
	}
}
?>