<?php
$token  = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';

$error_message = '';
$valid_request = false;

if (empty($token) || $action !== 'kcdc_download_whitepaper') {
    $error_message = __('Invalid request. Please use the proper download link.', 'kcdc-whitepaper-download');
} else {
    $db = new Kcdc_Whitepaper_DB();
    $request = $db->get_request_by_token($token);

    if (!$request) {
        $error_message = __('No matching request found. Please check your link.', 'kcdc-whitepaper-download');
    } elseif ($request->used) {
        $error_message = __('This download link has already been used.', 'kcdc-whitepaper-download');
    } else {
        $valid_request = true;
        // Optionally mark as used here (if not in handler)
        // $db->mark_request_as_used($token);
    }
}
?>

<div class="kcdc-download-wrapper">

    <?php if (!empty($error_message)) : ?>
        <div class="kcdc-download-error">
            <p><?php echo esc_html($error_message); ?></p>
        </div>

    <?php elseif ($valid_request) : ?>
        <h2 class="kcdc-download-heading">
            <?php esc_html_e('Your whitepaper is ready to download.', 'kcdc-whitepaper-download'); ?>
        </h2>

        <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="kcdc_download_whitepaper">
            <input type="hidden" name="token" value="<?php echo esc_attr($token); ?>">
            <input type="hidden" name="kcdc_nonce" value="<?php echo esc_attr(wp_create_nonce('kcdc_download_nonce')); ?>">
            <button type="submit" class="kcdc-download-button">
                <?php esc_html_e('Download Whitepaper', 'kcdc-whitepaper-download'); ?>
            </button>
        </form>

    <?php endif; ?>

</div>
