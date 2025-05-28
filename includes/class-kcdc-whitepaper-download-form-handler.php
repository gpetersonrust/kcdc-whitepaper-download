<?php

require_once KCDC_WHITEPAPER_DOWNLOAD_DIR . 'utils/kcdc-whitepaper-helper.php';

class Kcdc_Whitepaper_Form_Handler {
    private $loader;
    private $db;

    public function __construct(Kcdc_Whitepaper_DB $db) {
        $this->db = $db;
     }

    public function register($loader) {
        $this->loader = $loader;
   

        $this->loader->add_action('init', $this, 'add_rewrite_rule');
        $this->loader->add_action('template_redirect', $this, 'maybe_handle_form');

    }


      public function add_rewrite_rule() {
        add_rewrite_rule('^kcdc-form-handler/?$', 'index.php?kcdc_form=1', 'top');
        add_rewrite_tag('%kcdc_form%', '1');
    }

    public function maybe_handle_form() {
        if (get_query_var('kcdc_form') == 1 && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handle();
            exit;
        }
    }

   public function handle() {
    error_log('KCDC FORM: handler started');
    $nonce_value = $_POST['kcdc_nonce'] ?? '';

    if (!isset($_POST['kcdc_nonce']) || !wp_verify_nonce($_POST['kcdc_nonce'], 'kcdc_form_nonce')) {
        $this->redirect_with_error('security');
    }

    // Get and sanitize First Name and Last Name
    $first_name = sanitize_text_field($_POST['kcdc_first_name'] ?? '');
    $last_name  = sanitize_text_field($_POST['kcdc_last_name'] ?? '');
    
    // Combine first and last name
    $name = trim($first_name . ' ' . $last_name); // trim ensures no leading/trailing spaces if one is empty

    $agency   = sanitize_text_field($_POST['kcdc_agency'] ?? '');
    $email    = sanitize_email($_POST['kcdc_email'] ?? '');
    $post_id  = intval($_POST['kcdc_post_id'] ?? 0);     
    $post_url = sanitize_url($_POST['kcdc_post_url'] ?? '');

    // Updated validation for first_name and last_name
    if (empty($first_name) || empty($last_name) || empty($agency) || empty($email) || empty($post_id)) {
        $errors = [];
        if (empty($first_name)) $errors[] = 'first_name'; // Updated
        if (empty($last_name))  $errors[] = 'last_name';  // Updated
        if (empty($agency))     $errors[] = 'agency';
        if (empty($email))      $errors[] = 'email';
        if (empty($post_id))    $errors[] = 'post_id';
        
        if (!empty($errors)) {
            // The error string now includes specific missing fields
            $this->redirect_with_error('missing-fields-' . implode(',', $errors));
        }
    }

    $user_ip = Kcdc_Whitepaper_Helper::get_user_ip();
    if ($this->db->is_ip_blocked($user_ip)) {
        $this->redirect_with_error('blocked-ip');
    }

    // Basic rate limit: 5 submissions per 10 minutes per IP
    $ip_key = 'kcdc_rate_' . md5($user_ip);
    $count = get_transient('kcdc_limit_' . $ip_key) ?: 0;
    if ($count >= 10) {
        $this->db->insert_blocked_ip($user_ip, $_SERVER['HTTP_USER_AGENT'] ?? '', 'Rate limit exceeded');
        $this->redirect_with_error('rate-limit');
    }
    set_transient('kcdc_limit_' . $ip_key, $count + 1, 60); // 60 seconds = 1 minute

    $token = bin2hex(random_bytes(16));

    $result = $this->db->insert_request([
        'name'     => $name, // This will now be "Firstname Lastname"
        'first_name' => $first_name, // Optional: Store first name separately if your DB schema supports it
        'last_name'  => $last_name,  // Optional: Store last name separately if your DB schema supports it
        'post_id'  => $post_id,
        'wp_nonce' => $nonce_value,
        'agency'   => $agency,
        'email'    => $email,
        'token'    => $token,
    ]);

    if (false === $result) {
        error_log("KCDC: Failed DB insert for $email. Name: $name, Agency: $agency, Post ID: $post_id");
        $this->redirect_with_error('db-error');
    }

     
 

    // Redirect to the confirmation page which contains the download link
    wp_safe_redirect($post_url . '?success=true&token=' . urlencode($token) . '&post_id=' . urlencode($post_id));
    exit;
}




    private function redirect_with_error($type) {
        wp_safe_redirect(home_url('/error?reason=' . urlencode($type)));
        exit;
    }

    /**
     * Loads a simple email template from views/emails directory.
     *
     * @param string $template_file Template file name.
     * @param array $vars Variables to extract for use in template.
     * @return string Rendered HTML email content.
     */
    private function render_template($template_file, array $vars = []) {
        $template_path = plugin_dir_path(__FILE__) . '../views/emails/' . $template_file;
        if (!file_exists($template_path)) {
            return 'There is no email view template available.'; // fail silently
        }

        ob_start();
        extract($vars, EXTR_SKIP);
        include $template_path;
        return ob_get_clean();
    }
}
?>