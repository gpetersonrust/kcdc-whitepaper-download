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
        $this->loader->add_action('admin_post_kcdc_submit_form', $this, 'handle'); // For logged-in users
        $this->loader->add_action('admin_post_nopriv_kcdc_submit_form', $this, 'handle'); // For visitors

    }

    public function handle() {
        error_log('KCDC FORM: handler started');

        if (!isset($_POST['kcdc_nonce']) || !wp_verify_nonce($_POST['kcdc_nonce'], 'kcdc_form_nonce')) {
            $this->redirect_with_error('security');
        }

        $name   = sanitize_text_field($_POST['kcdc_name'] ?? '');
        $agency = sanitize_text_field($_POST['kcdc_agency'] ?? '');
        $email  = sanitize_email($_POST['kcdc_email'] ?? '');

        if (empty($name) || empty($agency) || empty($email)) {
            $this->redirect_with_error('missing-fields');
        }

        $user_ip = Kcdc_Whitepaper_Helper::get_user_ip();
        if ($this->db->is_ip_blocked($user_ip)) {
            $this->redirect_with_error('blocked-ip');
        }

        // Basic rate limit: 5 submissions per 10 minutes per IP
        $ip_key = 'kcdc_rate_' . md5($user_ip);
        $count = get_transient('kcdc_limit_' . $ip_key) ?: 0;
        if ($count >= 5) {
            $this->db->insert_blocked_ip($user_ip, $_SERVER['HTTP_USER_AGENT'] ?? '', 'Rate limit exceeded');
            $this->redirect_with_error('rate-limit');
        }
        set_transient('kcdc_limit_' . $ip_key, $count + 1, 10 * MINUTE_IN_SECONDS);

        $token = bin2hex(random_bytes(16));

        $result = $this->db->insert_request([
            'name'   => $name,
            'agency' => $agency,
            'email'  => $email,
            'token'  => $token,
        ]);

        if (false === $result) {
            error_log("KCDC: Failed DB insert for $email");
            $this->redirect_with_error('db-error');
        }

         $site_url = get_site_url();
        $slug = "white-paper-download";
         
        $download_url = $site_url . '/' . $slug . '/?token=' . $token;

     
        
        // Email user
        $user_email_subject = __('Your KCDC Whitepaper Download Link', 'kcdc-whitepaper-download');
        $user_email_body = $this->render_template('user-email.php', [
            'name' => $name,
            'download_url' => $download_url,
        ]);

        wp_mail($email, $user_email_subject, $user_email_body, ['Content-Type: text/html; charset=UTF-8']);

        // Email admin(s)
        $admin_emails = get_option('kcdc_admin_emails');
        if ($admin_emails) {
            $recipients = array_filter(array_map('sanitize_email', explode(',', $admin_emails)));
            if (!empty($recipients)) {
                $admin_subject = __('New Whitepaper Request Submitted', 'kcdc-whitepaper-download');
                $admin_body = $this->render_template('admin-email.php', [
                    'name' => $name,
                    'agency' => $agency,
                    'email' => $email,
                ]);
                wp_mail($recipients, $admin_subject, $admin_body, ['Content-Type: text/html; charset=UTF-8']);
            }
        }

        wp_safe_redirect(home_url('/kcdc/white-paper-form/?success=true&token=' . urlencode($token)));
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