<?php

get_header(); 
$success = isset($_GET['success']) && $_GET['success'] === 'true';
$token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) :  get_the_ID();
$form_action = esc_url(home_url('/kcdc-form-handler'));

// 🛠️ Create the nonce
$nonce = wp_create_nonce('kcdc_form_nonce');


       
?>

   <main style="max-width:1280px; width: 95%; margin: 0 auto; padding: 20px; min-height: 525px;">
     <h1 class="kcdc-whitepaper__heading"><?php the_title(); ?>  </h1>
<?php if ($success && !empty($token) && !empty($post_id)): ?>
    <div class="kcdc-confirmation">
       
        <h2 class="kcdc-download-heading"><?php esc_html_e('Click here to proceed to the download page.', 'kcdc-whitepaper-download'); ?></h2>

        <div class="kcdc-download-button-wrapper">
            <a href="<?php echo esc_url(add_query_arg([
            'action' => 'kcdc_download_whitepaper',
            'token' => $token,
            'post_id' => $post_id
            ], home_url('/white-paper-download'))); ?>" class="kcdc-confirmation__download-button">
            <?php esc_html_e('Click Here', 'kcdc-whitepaper-download'); ?>
            </a>
        </div>
    </div>

<?php else: ?>
    <form method="POST" action="<?php echo esc_url($form_action); ?>" class="kcdc-whitepaper-form">

        <input type="hidden" name="action" value="kcdc_submit_form">
        <input type="hidden" name="kcdc_nonce" value="<?php echo esc_attr($nonce); ?>">
        <input type="hidden" name="kcdc_post_id" value="<?php echo esc_attr(get_the_ID()); ?>">
        <input type="hidden" name="kcdc_post_url" value="<?php echo esc_url(get_permalink()); ?>">

        <div class="kcdc-name-fields-wrapper">
            <div class="kcdc-field kcdc-first-name-field">
            <label for="kcdc_first_name" class="kcdc-label">
            <?php esc_html_e('First Name', 'kcdc-whitepaper-download'); ?>
            </label>
            <input type="text" name="kcdc_first_name" id="kcdc_first_name" class="kcdc-input" required>
            </div>

            <div class="kcdc-field kcdc-last-name-field">
            <label for="kcdc_last_name" class="kcdc-label">
            <?php esc_html_e('Last Name', 'kcdc-whitepaper-download'); ?>
            </label>
            <input type="text" name="kcdc_last_name" id="kcdc_last_name" class="kcdc-input" required>
            </div>
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
            <button
             data-post_title="<?php echo esc_attr(get_the_title()); ?>"
            type="submit" class="kcdc-submit-button">
            <?php esc_html_e('Request Download', 'kcdc-whitepaper-download'); ?>
            </button>
        </div>

    </form>
<?php endif; ?>
</main>

<?php 

get_footer(); ?>