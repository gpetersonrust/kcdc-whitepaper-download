<?php

 class Kcdc_Whitepaper_Shortcode {
    private $plugin_name;
    private $version;
    private $loader;

    public function __construct($plugin_name, $version, $loader) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->loader = $loader;
        $this->loader->add_action('init', $this, 'register');
        
    }

   public function register() {
    add_shortcode('kcdc-whitepaper-form', [$this, 'render_shortcode']);
}

    /**
     * Renders the whitepaper form by loading a view file.
     *
     * @return string The HTML form output.
     */
    public function render_shortcode() {
        ob_start();
        $this->render_form();
        return ob_get_clean();
    }

    /**
     * Outputs the form HTML.
    */
    private function render_form() {
        $form_action = esc_url(admin_url('admin-post.php'));
        $nonce = wp_create_nonce('kcdc_form_nonce');

        include KCDC_WHITEPAPER_DOWNLOAD_DIR . 'views/public/form.php';
    }
}
?>