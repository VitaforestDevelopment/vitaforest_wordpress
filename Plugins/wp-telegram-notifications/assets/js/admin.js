jQuery(document).ready(function ($) {
    jQuery(".chosen-select").chosen();

    // WP 3.5+ uploader
    var file_frame;
    window.formfield = '';

    $(document.body).on('click', '.wptn_settings_upload_button', function (e) {

        e.preventDefault();

        window.formfield = $(this).parent().prev();

        // If the media frame already exists, reopen it.
        if (file_frame) {
            //file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
            file_frame.open();
            return;
        }

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            frame: 'post',
            state: 'insert',
            library: {
                type: ['image']
            },
            multiple: false
        });

        file_frame.on('menu:render:default', function (view) {
            // Store our views in an object.
            var views = {};

            // Unset default menu items
            view.unset('library-separator');
            view.unset('gallery');
            view.unset('featured-image');
            view.unset('embed');
            view.unset('playlist');
            view.unset('video-playlist');

            // Initialize the views in our view object.
            view.set(views);
        });

        // When an image is selected, run a callback.
        file_frame.on('insert', function () {

            var selection = file_frame.state().get('selection');
            selection.each(function (attachment, index) {
                attachment = attachment.toJSON();
                window.formfield.val(attachment.url);
                $('#wptn-image-preview').attr('src', attachment.url);
                $('.wptn_settings_clear_upload_button').show();
            });
        });

        // Finally, open the modal
        file_frame.open();
    });

    $(document.body).on('click', '.wptn_settings_clear_upload_button', function (e) {

        e.preventDefault();

        $(this).parent().prev().val('');
        $('#wptn-image-preview').attr('src', wptn_vars.default_avatar_url);
        $('.wptn_settings_clear_upload_button').hide();

    });
});