<div class="wrap">
    <?php add_thickbox(); ?>
    <h2><?php _e( 'Outbox message', 'wp-telegram-notifications' ); ?></h2>

    <form id="outbox-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
		<?php $list_table->search_box( __( 'Search', 'wp-telegram-notifications' ), 'search_id' ); ?>
		<?php $list_table->display(); ?>
    </form>
</div>