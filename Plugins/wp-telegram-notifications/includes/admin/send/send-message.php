<div class="wrap">
    <h2><?php _e( 'Send message', 'wp-telegram-notifications' ); ?></h2>
    <div class="postbox-container" style="padding-top: 20px;">
        <div class="meta-box-sortables">
            <div class="postbox">
                <h2 class="hndle" style="cursor: default;padding: 0 10px 10px 10px;font-size: 13px;">
                    <span><?php _e( 'Send message form', 'wp-telegram-notifications' ); ?></span></h2>

                <div class="inside">
                    <form method="post" action="">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row">
                                    <label for="channel_name"><?php _e( 'Send to', 'wp-telegram-notifications' ); ?>
                                        :</label>
                                </th>
                                <td>
                                    <select name="channel_name" id="channel_name">
										<?php foreach ( \WPTN\Channels::getChannels() as $items ): ?>
                                            <option value="<?php echo $items->channel_name; ?>"><?php echo $items->channel_name; ?></option>
										<?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row">
                                    <label for="message"><?php _e( 'Message content', 'wp-telegram-notifications' ); ?>
                                        :</label>
                                </th>
                                <td>
                                    <div class="wptn-emoji-area" data-emojiarea="" data-type="unicode" data-global-picker="false">
                                        <div class="emoji-button"><?php _e( '😊', 'wp-telegram-notifications' ); ?></div>
                                        <textarea dir="auto" cols="80" rows="8" name="message" id="message"></textarea>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p class="submit" style="padding: 0;">
                                        <input type="submit" class="button-primary" name="do_send" value="<?php _e( 'Send message', 'wp-telegram-notifications' ); ?>"/>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>