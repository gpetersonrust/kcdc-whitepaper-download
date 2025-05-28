<?php
 
$token  = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : null;

$error_message = '';
$valid_request = false;
$title = get_the_title($post_id); // Fixed typo: $tile -> $title

if (empty($token) || $action !== 'kcdc_download_whitepaper' || empty($post_id)) {
    $error_message = __('Invalid request. Please use the proper download link.', 'kcdc-whitepaper-download');
} else {
    $db = new Kcdc_Whitepaper_DB();
    $request = $db->get_request_by_token($token);

    if (!$request) {
        $error_message = __('No matching request found. Please check your link.', 'kcdc-whitepaper-download');
    }
    elseif ((int) $request->post_id !== (int) $post_id) {
        $error_message = __('Invalid download link for this whitepaper.', 'kcdc-whitepaper-download'); // Fixed typo
    }
    elseif (!is_download_still_valid($request)) {
        $error_message = __('This download link has expired or has already been used. Return back to the form <a href="' . esc_url(get_permalink($post_id)) . '">here</a>.', 'kcdc-whitepaper-download');
    } else {
        $valid_request = true;
        // Mark as used here since validation passed
        $db->mark_request_as_used($token);

        if (class_exists('wpecommon') && method_exists('wpecommon', 'purge_varnish_cache')) {
            wpecommon::purge_varnish_cache(home_url($_SERVER['REQUEST_URI']));
        }
    }
}

/**
 * Check if the download link is still valid (unused or used within last 5 minutes)
 */
function is_download_still_valid($request) {
    // Debug output - remove in production
    // print_r($request);

    if (!$request) {
        return false;
    }

    // If never used, it's valid
    if (empty($request->used_at) || $request->used_at === null || $request->used_at === '0000-00-00 00:00:00') {
        return true;
    }

    // Check if used within the allowed time window
    $used_time = strtotime($request->used_at);
    $current_time = current_time('timestamp');
    $time_diff = $current_time - $used_time;

  
    // Allow 5 minutes (300 seconds) after first use
    return $time_diff <= 300; // Changed from 10 to 300 seconds (5 minutes)
}
?>

<div class="kcdc-download-wrapper">
    <h1 class="kcdc-whitepaper__heading">
        <?php echo esc_html($title); ?>
    </h1>

    <?php if (!empty($error_message)) : ?>
        <div class="kcdc-download-error">
            <p><?=  ($error_message); ?></p>
        </div>
        <?php elseif ($valid_request) : ?>
            <h2 class="kcdc-download-heading">
                <?php esc_html_e('Please download at the link(s) below. The link will expire in 5 minutes.', 'kcdc-whitepaper-download'); ?>
            </h2>

            <?php
            // Get the documents from post meta
            $documents = get_post_meta($post_id, '_whitepaper_documents', true);
            
            if (!empty($documents)) : ?>
                <div class="kcdc-download-documents">
                    <?php foreach ($documents as $document) : ?>
                        <a 
                         data-document_name="<?php echo esc_attr($document['name']); ?>"
                        href="<?php echo esc_url($document['link']); ?>" class="kcdc-document-link" target="_blank">
                            <?php echo esc_html($document['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p><?php esc_html_e('No documents available for download.', 'kcdc-whitepaper-download'); ?></p>
            <?php endif; ?>

        <?php endif; ?>

</div>