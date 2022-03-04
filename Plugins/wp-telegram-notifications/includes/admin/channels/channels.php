<div class="wrap">
    <h2><?php _e( 'Channels', 'wp-telegram-notifications' ); ?></h2>
	<?php add_thickbox(); ?>
    <div class="wptn-button-group">
        <a name="<?php _e( 'Create New channel', 'wp-telegram-notifications' ); ?>" href="admin.php?page=wptn-channels#TB_inline?&width=400&height=110&inlineId=add-channel" class="thickbox button"><span class="dashicons dashicons-groups"></span> <?php _e( 'Add Channel', 'wp-telegram-notifications' ); ?>
        </a>
    </div>
    <div id="add-channel" style="display:none;">
        <form action="" method="post">
            <table>
                <tr>
                    <td>
                        <span class="wptn_channels_label" for="wp_subscribe_name"><?php _e( 'Name', 'wp-telegram-notifications' ); ?></span>
                        <input type="text" id="channel_name" name="channel_name" class="wptn_channels_input_text" required/>
                        <span><?php echo sprintf( __( 'Please read this <a href="%s" target="_blank">Article</a> for more information.', 'wp-telegram-notifications' ), 'https://wp-telegram.com/resources/channels/' ); ?></span>
                    </td>
                </tr>

                <tr>
                    <td>
                        <input type="submit" class="button-primary" name="add_channel" value="<?php _e( 'Create', 'wp-telegram-notifications' ); ?>"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <form id="outbox-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
		<?php $list_table->search_box( __( 'Search', 'wp-telegram-notifications' ), 'search_id' ); ?>
		<?php $list_table->display(); ?>
    </form>
</div>