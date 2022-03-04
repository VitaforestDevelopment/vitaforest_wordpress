<script type="text/javascript">
    jQuery(document).ready(function () {

        jQuery('#wptn-attachment').hide();
        jQuery('#wptn-attachment-image').hide();
        jQuery('#wptn-attachment-video').hide();
        jQuery('#wptn-attachment-image-type').hide();

        if (jQuery('#wptn-send-message').val() == 'yes') {
            jQuery('.wptn-message-options').show();
        }

        jQuery("#wptn-send-message").change(function () {
            if (this.value === 'yes') {
                jQuery('.wptn-message-options').show();
            } else {
                jQuery('.wptn-message-options').hide();
            }
        });

        jQuery("#wptn-attachment-type").change(function () {
            if (this.value === 'image') {
                jQuery('#wptn-attachment').show();
                jQuery('#wptn-attachment-image').show();
                jQuery('#wptn-attachment-video').hide();
            } else if (this.value === 'video') {
                jQuery('#wptn-attachment').show();
                jQuery('#wptn-attachment-image').hide();
                jQuery('#wptn-attachment-video').show();
            } else {
                jQuery('#wptn-attachment').hide();
                jQuery('#wptn-attachment-image').hide();
                jQuery('#wptn-attachment-video').hide();
            }
        });

        jQuery("#wptn-image-type-thumbnail").change(function () {
            jQuery('#wptn-attachment-image-type').hide();
        });

        jQuery("#wptn-image-type-upload").change(function () {
            jQuery('#wptn-attachment-image-type').show();
        });

        // Uploading image file
        var image_file_frame;
        jQuery('#wptn-upload_image_button').on('click', function (event) {
            event.preventDefault();
            // If the media frame already exists, reopen it.
            if (image_file_frame) {
                // Open frame
                image_file_frame.open();
                return;
            }
            // Create the media frame.
            image_file_frame = wp.media.frames.file_frame = wp.media({
                title: '<?php _e('Select a image to upload', 'wp-telegram-notifications'); ?>',
                button: {
                    text: '<?php _e('Select', 'wp-telegram-notifications'); ?>',
                },
                library: {
                    type: ['image']
                },
                frame: "select",
                multiple: false	// Set to true to allow multiple files to be selected
            });
            // When an image is selected, run a callback.
            image_file_frame.on('select', function () {
                // We set multiple to false so only get one image from the uploader
                attachment = image_file_frame.state().get('selection').first().toJSON();
                // Do something with attachment.id and/or attachment.url here
                $('#wptn-image-preview').attr('src', attachment.url).css('width', 'auto');
                $('#wptn-image-final').val(attachment.url);
            });
            // Finally, open the modal
            image_file_frame.open();
        });

        // Uploading video file
        var video_file_frame;
        jQuery('#wptn-upload_video_button').on('click', function (event) {
            event.preventDefault();
            // If the media frame already exists, reopen it.
            if (video_file_frame) {
                // Open frame
                video_file_frame.open();
                return;
            }
            // Create the media frame.
            video_file_frame = wp.media.frames.file_frame = wp.media({
                title: '<?php _e('Select a video to upload', 'wp-telegram-notifications'); ?>',
                button: {
                    text: '<?php _e('Select', 'wp-telegram-notifications'); ?>',
                },
                library: {
                    type: ['video']
                },
                frame: "select",
                multiple: false	// Set to true to allow multiple files to be selected
            });
            // When an video is selected, run a callback.
            video_file_frame.on('select', function () {
                // We set multiple to false so only get one video from the uploader
                attachment = video_file_frame.state().get('selection').first().toJSON();
                // Do something with attachment.id and/or attachment.url here
                if (!attachment.url) {
                    $('#wptn-video-preview').html('');
                } else {
                    $('#wptn-video-preview').html('<p>Video URL: </p><span id="wptn-video-preview">' + attachment.url + '</span>');
                }

                $('#wptn-video-final').val(attachment.url);
                jQuery('.wptn-video-preview-wrapper').show();
            });
            // Finally, open the modal
            video_file_frame.open();
        });
    });
</script>

<div class="wptn-wrapper">
    <div class="wptn-options">
        <div class="wptn-options__header">
            <h4>Message Options</h4>
            <span class="wptn-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"/></svg></span>
        </div>
        <div class="wptn-options__body">
            <div class="wptn-input-box">

                <label for="wptn-send-message"><?php _e('Send this post to channel?', 'wp-telegram-notifications'); ?></label>
                <select name="wptn_send" id="wptn-send-message">
                    <option selected><?php _e('Please select', 'wp-telegram-notifications'); ?></option>
                    <option value="yes"><?php _e('Yes', 'wp-telegram-notifications'); ?></option>
                    <option value="no"><?php _e('No', 'wp-telegram-notifications'); ?></option>
                </select>
            </div>
            <div class="wptn-input-box">
                <label for="wptn-channel-name"><?php _e('Send to', 'wp-telegram-notifications'); ?>:</label>
                <select name="wptn_channel_name" id="wptn-channel-name">
                    <?php foreach (\WPTN\Channels::getChannels() as $items): ?>
                        <option value="<?php echo $items->channel_name; ?>"><?php echo $items->channel_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="wptn-options">
        <div class="wptn-options__header">
            <h4>Message Attachment</h4>
            <span class="wptn-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"/></svg></span>
        </div>
        <div class="wptn-options__body">
            <div class="wptn-input-box">
                <label for="wptn-attachment-type"><?php _e('Send Attachment?', 'wp-telegram-notifications'); ?></label>
                <select name="wptn_attachment_type" id="wptn-attachment-type">
                    <option selected><?php _e('No', 'wp-telegram-notifications'); ?></option>
                    <option value="image"><?php _e('Image', 'wp-telegram-notifications'); ?></option>
                    <option value="video"><?php _e('Video', 'wp-telegram-notifications'); ?></option>
                </select>
            </div>
            <div id="wptn-attachment">
                <div id="wptn-attachment-video">
                    <?php wp_enqueue_media(); ?>
                    <div class='wptn-video-preview-wrapper'>
                        <div id="wptn-video-preview"></div>
                    </div>
                    <input id="wptn-upload_video_button" type="button" class="button"
                           value="<?php _e('Upload Video'); ?>"/>
                    <input type='hidden' name='wptn_video_final' id='wptn-video-final' value=''>
                </div>
                <br/>
                <div id="wptn-attachment-image">
                    <br/><br/>
                    <input type="radio" name="wptn_image_type" id="wptn-image-type-thumbnail" value="thumbnail"
                           checked="checked">
                    Post
                    thumbnail
                    <br/><br/>
                    <input type="radio" name="wptn_image_type" id="wptn-image-type-upload" value="upload"> Upload
                    <div id="wptn-attachment-image-type">
                        <?php wp_enqueue_media(); ?>
                        <div class='wptn-image-preview-wrapper'>
                            <img id='wptn-image-preview' src='' height='100'>
                        </div>
                        <input id="wptn-upload_image_button" type="button" class="button"
                               value="<?php _e('Upload Image'); ?>"/>
                        <input type='hidden' name='wptn_image_final' id='wptn-image-final' value=''>
                    </div>
                    <br/><br/>
                </div>
                <label for="wptn-attachment-image-position"><?php _e('Attachment Position?', 'wp-telegram-notifications'); ?></label>
                <select name="wptn_attachment_position" id="wptn-attachment-image-position">
                    <option value="before"
                            selected><?php _e('Before text(Caption)', 'wp-telegram-notifications'); ?></option>
                    <option value="after"><?php _e('After text', 'wp-telegram-notifications'); ?></option>
                </select>
                <span style="font-size: 14px">
                <br/><br/><?php _e('Note*: <code>%post_content%</code> Does not supported if you are sending an attachment.', 'wp-telegram-notifications'); ?>
                    </span>
            </div>
        </div>
    </div>

    <div class="wptn-options">
        <div class="wptn-options__header">
            <h4>Message Content</h4>
            <span class="wptn-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"><path d="M0 7.33l2.829-2.83 9.175 9.339 9.167-9.339 2.829 2.83-11.996 12.17z"/></svg></span>
        </div>
        <div class="wptn-options__body">
            <label for="wptn-text-template"><?php _e('Text template', 'wp-telegram-notifications'); ?>:</label>
            <div class="wptn-emoji-area" data-emojiarea="" data-type="unicode" data-global-picker="false">
                <div class="emoji-button"><?php _e('😊', 'wp-telegram-notifications'); ?></div>
                <textarea dir="auto" cols="80" rows="8" name="wptn-text-template" id="wptn-text-template"><?php echo \WPTN\Option::getOption('wordpress_publish_new_post_template'); ?></textarea>
            </div>
            <div class="clearfix"></div>
            <span style="font-size: 14px"><?php _e('Input data:', 'wp-telegram-notifications'); ?>
                <br/><?php _e('Post title', 'wp-telegram-notifications'); ?>: <code>%post_title%</code>
                <br/><?php _e('Post content', 'wp-telegram-notifications'); ?>: <code>%post_content%</code>
                <br/><?php _e('Post url', 'wp-telegram-notifications'); ?>: <code>%post_url%</code>
                <br/><?php _e('Post date', 'wp-telegram-notifications'); ?>: <code>%post_date%</code>
            </span>
        </div>
    </div>
</div>