<div class="kcdc-whitepaper-download-wrap">
    <h2>Export Whitepaper Requests</h2>
    <form id="kcdc-whitepaper-exporter-form" method="post">
        <?php wp_nonce_field('kcdc_export_requests_nonce', 'kcdc-whitepaper-exporter-nonce'); ?>
        <p>
            <label for="kcdc-whitepaper-select">Select Whitepaper:</label>
            <select name="post_id" id="kcdc-whitepaper-select" required>
                <option value="">-- Select Whitepaper --</option>
                <?php foreach ($whitepapers as $whitepaper): ?>
                    <option value="<?php echo esc_attr($whitepaper->ID); ?>">
                        <?php echo esc_html($whitepaper->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <input type="submit" value="Export to CSV" class="button button-primary">
    </form>
</div>
