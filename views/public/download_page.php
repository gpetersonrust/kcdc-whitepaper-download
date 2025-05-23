<?php
$token  = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
$action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : null;

$error_message = '';
$valid_request = false;

if (empty($token) || $action !== 'kcdc_download_whitepaper' || empty($post_id)) {
    $error_message = __('Invalid request. Please use the proper download link.', 'kcdc-whitepaper-download');
} else {
    $db = new Kcdc_Whitepaper_DB();
    $request = $db->get_request_by_token($token);



    if (!$request) {
        $error_message = __('No matching request found. Please check your link.', 'kcdc-whitepaper-download');
    }
    elseif ((int) $request->post_id !== (int) $post_id) {
        
        $error_message = __('Invalid downlodddad link for this whitepaper.', 'kcdc-whitepaper-download');
    }
    elseif ($request->used) {
        $error_message = __('This download link has already been used. Return back to the form <a href="' . esc_url(get_permalink($post_id)) . '">here</a>.', 'kcdc-whitepaper-download');
    } else {
        $valid_request = true;
        // Optionally mark as used here (if not in handler)
        $db->mark_request_as_used($token);
    }
}

 
?>

<div class="kcdc-download-wrapper">

    <?php if (!empty($error_message)) : ?>
        <div class="kcdc-download-error">
            <p><?=  ($error_message); ?></p>
        </div>
        <?php elseif ($valid_request) : ?>
            <h2 class="kcdc-download-heading">
                <?php esc_html_e('Your whitepaper resources are ready to download. Click the link(s) below to begin your download.', 'kcdc-whitepaper-download'); ?>
            </h2>

            <?php
            // Get the documents from post meta
            $documents = get_post_meta($post_id, '_whitepaper_documents', true);
            
            if (!empty($documents)) : ?>
                <div class="kcdc-download-documents">
                    <?php foreach ($documents as $document) : ?>
                        <a href="<?php echo esc_url($document['link']); ?>" class="kcdc-document-link" download>
                            <?php echo esc_html($document['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p><?php esc_html_e('No documents available for download.', 'kcdc-whitepaper-download'); ?></p>
            <?php endif; ?>

        <?php endif; ?>

</div>
