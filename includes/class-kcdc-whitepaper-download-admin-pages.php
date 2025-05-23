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
        $this->loader->add_action('admin_menu', $this, 'add_export_requests_entries');
        $this->loader->add_action('wp_ajax_kcdc_export_whitepaper_csv', $this, 'handle_ajax_export_csv');

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
            array($this, 'export_requests_data'), // Callback function <-- CORRECTED: Added comma here
            'dashicons-download', // Icon
            30                    // Position
        );
    }

    /**
     * Add export requests entries menu item.
     *
     * @since    1.0.0
     */
    public function add_export_requests_entries() {
        add_submenu_page(
            'kcdc-whitepaper',                // Parent slug
            'Export Requests',                 // Page title
            'Export Requests',                 // Menu title
            'manage_options',                  // Capability
            'kcdc-whitepaper-export',         // Menu slug
            array($this, 'export_requests_data') // Callback function
        );
    }

    /**
     * Handle the export of requests data and display the export page.
     *
     * @since    1.0.0
     */
       /**
     * Handle the export of requests data and display the export page.
     *
     * @since    1.0.0
     */
public function export_requests_data() {
    // Ensure Kcdc_Whitepaper_DB class is available
    if (!class_exists('Kcdc_Whitepaper_DB')) {
        echo '<div class="error"><p>Error: Kcdc_Whitepaper_DB class not found. Please ensure it is loaded correctly.</p></div>';
        return;
    }

    // Load the whitepapers for the dropdown
    $whitepapers = get_posts(array(
        'post_type' => 'whitepaper',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'post_status' => 'publish',
    ));

    // Add nonce for AJAX security
    wp_nonce_field('kcdc_export_requests_nonce', 'kcdc_export_nonce_field');

    // Load the view for displaying the export form
    $view_file_path = KCDC_WHITEPAPER_DOWNLOAD_DIR . 'views/admin/export-whitepaper-requests.php';

    if (file_exists($view_file_path)) {
        include $view_file_path;
    } else {
        echo '<div class="error"><p>Error: View file for export page not found at ' . esc_html($view_file_path) . '</p></div>';
    }
}


public function handle_ajax_export_csv() {
    check_ajax_referer('kcdc_export_requests_nonce', 'security');

    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;

    if (!class_exists('Kcdc_Whitepaper_DB')) {
        wp_send_json_error(['message' => 'Database class not found.']);
    }

    $db = new Kcdc_Whitepaper_DB();
    $requests = $db->get_requests_by_post_id($post_id);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=whitepaper_requests_' . ($post_id ? 'post_' . $post_id : 'all') . '_' . date('Y-m-d') . '.csv');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
    fputcsv($output, ['Name', 'Email', 'Agency', 'Requested At', 'Download Link Used At']);

    if (!empty($requests)) {
        foreach ($requests as $request) {
            fputcsv($output, [
                sanitize_text_field($request->name),
                sanitize_email($request->email),
                sanitize_text_field($request->agency),
                sanitize_text_field($request->created_at),
                sanitize_text_field($request->used_at)
            ]);
        }
    } else {
        fputcsv($output, ['No requests found for the selected whitepaper.']);
    }

    fclose($output);
    exit;
}
}
?>