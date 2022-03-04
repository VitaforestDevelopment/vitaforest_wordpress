<div id="wpcf7-wptn" class="contact-form-editor-wptn">
    <h3><?php _e( 'Send to channel', 'wp-telegram-notifications' ); ?></h3>
    <fieldset>
        <legend><?php _e( 'After submit form you can send a message to the channel', 'wp-telegram-notifications' ); ?>
            <br></legend>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wpcf7-wptn-channel"><?php _e( 'Channel', 'wp-telegram-notifications' ); ?>
                        :</label></th>
                <td>
                    <select name="wpcf7-wptn[channel]">
						<?php foreach ( \WPTN\Channels::getChannels() as $items ): ?>
                            <option value="<?php echo $items->channel_name; ?>" <?php selected( $cf7_options['channel'], $items->channel_name ); ?>><?php echo $items->channel_name; ?></option>
						<?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="wpcf7-wptn-message"><?php _e( 'Message body', 'wp-telegram-notifications' ); ?>:</label>
                </th>
                <td>
                    <div class="wptn-emoji-area" data-emojiarea="" data-type="unicode" data-global-picker="false">
                        <div class="emoji-button"><?php _e( '😊', 'wp-telegram-notifications' ); ?></div>
                        <textarea class="large-text" rows="4" cols="100" name="wpcf7-wptn[message]" id="wpcf7-wptn-message"><?php echo $cf7_options['message']; ?></textarea>
                    </div>
                    <p class="description"><?php _e( '<b>Note:</b> Use %% Instead of [], for example: %your-name%', 'wp-telegram-notifications' ); ?></p>
                </td>
            </tr>
        </table>
    </fieldset>
</div>