<?php
$success = isset($_GET['success']) && $_GET['success'] === 'true';
$token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
?>

<?php if ($success && !empty($token)): ?>
    <div class="kcdc-confirmation">
        <h2 class="kcdc-confirmation__heading"><?php esc_html_e('Thank you for your request!', 'kcdc-whitepaper-download'); ?></h2>
        <p class="kcdc-confirmation__message"><?php esc_html_e('Click here to proceed to the download page.', 'kcdc-whitepaper-download'); ?></p>

        <a href="<?php echo esc_url(add_query_arg([
            'action' => 'kcdc_download_whitepaper',
            
            'token' => $token
        ], home_url('/white-paper-download'))); ?>" class="kcdc-confirmation__download-button">
            <?php esc_html_e('Click Here', 'kcdc-whitepaper-download'); ?>
        </a>
    </div>

<?php else: ?>
    <form method="POST" action="<?php echo esc_url($form_action); ?>" class="kcdc-whitepaper-form">

        <input type="hidden" name="action" value="kcdc_submit_form">
        <input type="hidden" name="kcdc_nonce" value="<?php echo esc_attr($nonce); ?>">

        <div class="kcdc-field kcdc-name-field">
            <label for="kcdc_name" class="kcdc-label">
                <?php esc_html_e('Your Name', 'kcdc-whitepaper-download'); ?>
            </label>
            <input type="text" name="kcdc_name" id="kcdc_name" class="kcdc-input" required>
        </div>

        <div class="kcdc-field kcdc-agency-field">
            <label for="kcdc_agency" class="kcdc-label">
                <?php esc_html_e('Agency/Organization', 'kcdc-whitepaper-download'); ?>
            </label>
            <input type="text" name="kcdc_agency" id="kcdc_agency" class="kcdc-input" required>
        </div>

        <div class="kcdc-field kcdc-email-field">
            <label for="kcdc_email" class="kcdc-label">
                <?php esc_html_e('Email Address', 'kcdc-whitepaper-download'); ?>
            </label>
            <input type="email" name="kcdc_email" id="kcdc_email" class="kcdc-input" required>
        </div>

        <div class="kcdc-field kcdc-submit-field">
            <button type="submit" class="kcdc-submit-button">
                <?php esc_html_e('Request Download', 'kcdc-whitepaper-download'); ?>
            </button>
        </div>

    </form>
<?php endif; ?>
