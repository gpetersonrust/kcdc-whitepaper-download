<?php

class Kcdc_Whitepaper_Post_Type {
    private $plugin_name;
    private $version;
    private $loader;

    public function __construct($plugin_name, $version, $loader) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->loader = $loader;
        $this->loader->add_action('init', $this, 'register');
        $this->loader->add_filter('single_template', $this, 'get_custom_post_type_template');
        $this->loader->add_action('add_meta_boxes', $this, 'add_documents_meta_box');
        $this->loader->add_action('save_post', $this, 'save_documents_meta_box');
     }

    public function register() {
        $labels = array(
            'name'                  => _x('Whitepapers', 'Post type general name', 'kcdc-whitepaper-download'),
            'singular_name'         => _x('Whitepaper', 'Post type singular name', 'kcdc-whitepaper-download'),
            'menu_name'            => _x('Whitepapers', 'Admin Menu text', 'kcdc-whitepaper-download'),
            'add_new'              => __('Add New', 'kcdc-whitepaper-download'),
            'add_new_item'         => __('Add New Whitepaper', 'kcdc-whitepaper-download'),
            'edit_item'            => __('Edit Whitepaper', 'kcdc-whitepaper-download'),
            'new_item'             => __('New Whitepaper', 'kcdc-whitepaper-download'),
            'view_item'            => __('View Whitepaper', 'kcdc-whitepaper-download'),
            'search_items'         => __('Search Whitepapers', 'kcdc-whitepaper-download'),
            'not_found'            => __('No whitepapers found', 'kcdc-whitepaper-download'),
            'not_found_in_trash'   => __('No whitepapers found in Trash', 'kcdc-whitepaper-download'),
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'whitepaper'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-media-document',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        );

        register_post_type('whitepaper', $args);
    }

    public function get_custom_post_type_template($single_template) {
        global $post;

        if ($post->post_type == 'whitepaper') {
            $single_template = KCDC_WHITEPAPER_DOWNLOAD_DIR . 'views/public/whitepaper-template.php';
        }

        return $single_template;
    }



    public function add_documents_meta_box() {
        add_meta_box(
            'whitepaper_documents',
            __('Document Resources', 'kcdc-whitepaper-download'),
            array($this, 'render_documents_meta_box'),
            'whitepaper',
            'normal',
            'high'
        );
    }

    public function render_documents_meta_box($post) {
        wp_nonce_field('whitepaper_documents_nonce', 'whitepaper_documents_nonce');
        
        // Get saved documents
        $documents = get_post_meta($post->ID, '_whitepaper_documents', true);
        
        // Include the view file
        include KCDC_WHITEPAPER_DOWNLOAD_DIR . 'views/admin/documents-meta-box.php';
    }

    public function save_documents_meta_box($post_id) {

        // Check if user is administrator
        if (!current_user_can('manage_options')) {
            return;
        }
        // Verify nonce
        if (!isset($_POST['whitepaper_documents_nonce']) || 
            !wp_verify_nonce($_POST['whitepaper_documents_nonce'], 'whitepaper_documents_nonce')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save documents data
        if (isset($_POST['document_links']) && isset($_POST['document_names'])) {
            $documents = array();
            $links = array_map('esc_url_raw', $_POST['document_links']);
            $names = array_map('sanitize_text_field', $_POST['document_names']);
            
            for ($i = 0; $i < count($links); $i++) {
                if (!empty($links[$i])) {
                    $documents[] = array(
                        'link' => $links[$i],
                        'name' => $names[$i]
                    );
                }
            }
            
            update_post_meta($post_id, '_whitepaper_documents', $documents);
        }
    }
}