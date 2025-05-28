<?php

/**
 * The API functionality of the plugin.
 *
 * @link       https://moxcar.com
 * @since      1.0.0
 *
 * @package    Kcdc_Whitepaper_Download
 * @subpackage Kcdc_Whitepaper_Download/includes
 */

class Kcdc_White_Paper_Download_API {
    private $plugin_name;
    private $version;
    private $loader;
    private $namespace = 'kcdc-whitepaper/v1';
    private $db;

    public function __construct($plugin_name, $version, $loader) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->loader = $loader;
        $this->db = new Kcdc_Whitepaper_DB();
        
        $this->loader->add_action('rest_api_init', $this, 'register_routes');
    }

    public function register_routes() {
        register_rest_route($this->namespace, '/download', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_download_data'),
                'permission_callback' => '__return_true',
                'args' => array(
                    'token' => array(
                        'required' => true,
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_text_field'
                    ),
                    'post_id' => array(
                        'required' => true,
                        'type' => 'integer',
                        'sanitize_callback' => 'absint'
                    )
                )
            )
        ));
    }

    public function get_download_data($request) {
        // Sanitize inputs
        $token = sanitize_text_field($request->get_param('token'));
        $post_id = absint($request->get_param('post_id'));
        $action = sanitize_text_field($request->get_param('action'));

 

    
        
        if ($action !== 'kcdc_download_whitepaper') {
            return new WP_Error(
                'invalid_action',
                'Invalid action parameter.',
                array('status' => 400)
            );
        }

   
        
        if (empty($token) || $post_id <= 0) {
            return new WP_Error(
                'invalid_parameters',
                'Invalid request parameters.',
                array('status' => 400)
            );
        }

        

        // Get request from database
        $request = $this->db->get_request_by_token($token);

          
        
        if (!$request) {
            return new WP_Error(
                'invalid_token',
                'No matching request found. Please check your link.',
                array('status' => 404)
            );
        }

        if ((int) $request->post_id !== $post_id) {
            return new WP_Error(
                'invalid_post',
                'Invalid download link for this whitepaper.',
                array('status' => 400)
            );
        }


         

        if ($request->used) {
            return new WP_Error(
                'used_token',
                'This download link has already been used.',
                array(
                    'status' => 400,
                    'post_url' => esc_url(get_permalink($post_id))
                )
            );
        }

        // Verify post exists and is published
        if (!get_post_status($post_id) === 'publish') {
            return new WP_Error(
                'invalid_post',
                'The requested document is not available.',
                array('status' => 404)
            );
        }
      
        // Get documents
        $documents = get_post_meta($post_id, '_whitepaper_documents', true);
        if (is_array($documents)) {
            $documents = array_map(function($doc) {
            return isset($doc['link'], $doc['name']) ? [
                'link' => esc_url($doc['link']),
                'name' => sanitize_text_field($doc['name'])
            ] : null;
            }, $documents);
            $documents = array_filter($documents);
        } else {
            $documents = array();
        }
        
        // Mark request as used
        $this->db->mark_request_as_used($token);

        // Purge Varnish cache if WP Engine plugin is active
        if (class_exists('wpecommon') && method_exists('wpecommon', 'purge_varnish_cache')) {
            $url = home_url('/white-paper-download/?action=kcdc_download_whitepaper&token=' . $token . '&post_id=' . $post_id);
            wpecommon::purge_varnish_cache($url);
        }

        // Return success response
        return new WP_REST_Response(array(
            'success' => true,
            'documents' => $documents,
            'messages' => array(
                'success' => 'Your whitepaper resources are ready to download. Click the link(s) below to begin your download.',
                'no_documents' => 'No documents available for download.'
            )
        ), 200);
    }
}