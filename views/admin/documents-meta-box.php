<?php
// Get documents array or initialize empty array if none exists
$documents = !empty($documents) ? $documents : array();
?>

<div class="kcdc-whitepaper-documents-container">
    <div id="kcdc-whitepaper-documents-list">
        <?php if (!empty($documents)): ?>
            <?php foreach ($documents as $index => $document): ?>
                <div class="kcdc-whitepaper-document-row" data-index="<?php echo esc_attr($index); ?>">
                    <div class="kcdc-whitepaper-document-input-group">
                        <label class="kcdc-whitepaper-document-label">
                            <?php _e('Document Name:', 'kcdc-whitepaper-download'); ?>
                            <input type="text" 
                                   name="document_names[]" 
                                   class="kcdc-whitepaper-document-name-input" 
                                   value="<?php echo esc_attr($document['name']); ?>" />
                        </label>
                        <label class="kcdc-whitepaper-document-label">
                            <?php _e('Document Link:', 'kcdc-whitepaper-download'); ?>
                            <input type="url" 
                                   name="document_links[]" 
                                   class="kcdc-whitepaper-document-link-input" 
                                   value="<?php echo esc_url($document['link']); ?>" 
                                   readonly />
                        </label>
                        <div class="kcdc-whitepaper-document-buttons">
                            <button type="button" class="button kcdc-select-document">
                                <?php _e('Select File', 'kcdc-whitepaper-download'); ?>
                            </button>
                            <button type="button" class="button kcdc-whitepaper-remove-document">
                                <?php _e('Remove', 'kcdc-whitepaper-download'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Template Row -->
    <div class="kcdc-whitepaper-document-row kcdc-whitepaper-document-template" style="display: none;">
        <div class="kcdc-whitepaper-document-input-group">
            <label class="kcdc-whitepaper-document-label">
                <?php _e('Document Name:', 'kcdc-whitepaper-download'); ?>
                <input type="text" name="document_names[]" class="kcdc-whitepaper-document-name-input" value="" />
            </label>
            <label class="kcdc-whitepaper-document-label">
                <?php _e('Document Link:', 'kcdc-whitepaper-download'); ?>
                <input type="url" name="document_links[]" class="kcdc-whitepaper-document-link-input" value="" readonly />
            </label>
            <div class="kcdc-whitepaper-document-buttons">
                <button type="button" class="button kcdc-select-document">
                    <?php _e('Select File', 'kcdc-whitepaper-download'); ?>
                </button>
                <button type="button" class="button kcdc-whitepaper-remove-document">
                    <?php _e('Remove', 'kcdc-whitepaper-download'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Add Document Button -->
    <div class="kcdc-whitepaper-document-actions">
        <button type="button" id="kcdc-whitepaper-add-document" class="button-secondary">
            <?php _e('Add Document', 'kcdc-whitepaper-download'); ?>
        </button>
    </div>
</div>

<script>
jQuery(document).ready(function($) {

    // Function to initialize media selector on a button
    function bindMediaSelector(button) {
        button.on('click', function(e) {
            e.preventDefault();

            // Find the correct input field in the same row
            var container = $(this).closest('.kcdc-whitepaper-document-row');
            var input = container.find('.kcdc-whitepaper-document-link-input');

            var file_frame = wp.media({
                title: '<?php _e("Select or Upload a Document", "kcdc-whitepaper-download"); ?>',
                button: {
                    text: '<?php _e("Use this document", "kcdc-whitepaper-download"); ?>'
                },
                multiple: false
            });

            file_frame.on('select', function() {
                var attachment = file_frame.state().get('selection').first().toJSON();
                input.val(attachment.url).trigger('change');
            });

            file_frame.open();
        });
    }

    // Initialize media selector for existing buttons
    $('.kcdc-select-document').each(function() {
        bindMediaSelector($(this));
    });

    // Add new document row
    $('#kcdc-whitepaper-add-document').on('click', function() {
        var template = $('.kcdc-whitepaper-document-template').clone();
        template.removeClass('kcdc-whitepaper-document-template').show();

        // Re-bind the media selector for new row
        bindMediaSelector(template.find('.kcdc-select-document'));

        $('#kcdc-whitepaper-documents-list').append(template);
    });

    // Remove document row
    $(document).on('click', '.kcdc-whitepaper-remove-document', function() {
        $(this).closest('.kcdc-whitepaper-document-row').remove();
    });

});
</script>
