<?php

namespace WPTN\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use WPTN\BOT;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Channels_List_Table extends \WP_List_Table {

	protected $db;
	protected $limit;
	protected $count;
	var $data;

	function __construct() {
		global $wpdb;

		//Set parent defaults
		parent::__construct( array(
			'singular' => 'ID',     //singular name of the listed records
			'plural'   => 'ID',    //plural name of the listed records
			'ajax'     => false        //does this table support ajax?
		) );
		$this->db    = $wpdb;
		$this->count = $this->get_total();
		$this->limit = 25;
		$this->data  = $this->get_data();
	}

	function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'channel_name':
				return $item[ $column_name ];
				break;

			case 'channel_members':
				$channel_members = BOT::getChatMember( $item['channel_name'] );

				if ( is_wp_error( $channel_members ) ) {
					$final = $channel_members->get_error_message();
				} else {
					$final = $channel_members;
				}

				if ( $final ) {
					return $final;
				} else {
					return '-';
				}

				break;

			case 'channel_administrator':
				$administrators = BOT::getChatAdministrators( $item['channel_name'] );

				if ( is_wp_error( $administrators ) ) {
					$error = $administrators->get_error_message();
				} else {
					if ( $administrators ) {
						foreach ( $administrators as $administrator ) {
							if ( $administrator['status'] == 'creator' ) {
								$icon = 'star-filled';
							} else {
								$icon = 'admin-users';
							}

							echo sprintf(
								'<div><span class="dashicons dashicons-%s" title="%s"></span>%s (%s)</div>',
								$icon,
								ucwords( $administrator['status'] ),
								$administrator['user']['first_name'],
								$administrator['user']['username']
							);
						}
					} else {
						return '-';
					}
				}

				if ( isset( $error ) ) {
					return $error;
				}

				break;

			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
				break;
		}
	}

	function column_channel_name( $item ) {

		//Build row actions
		$actions = array(
			'delete' => sprintf( '<a href="?page=%s&action=%s&ID=%s">' . __( 'Delete', 'wp-telegram-notifications' ) . '</a>', $_REQUEST['page'], 'delete', $item['ID'] ),
		);

		//Return the title contents
		return sprintf( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
			/*$1%s*/
			$item['channel_name'],
			/*$2%s*/
			$item['ID'],
			/*$3%s*/
			$this->row_actions( $actions )
		);
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/
			$this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/
			$item['ID']                //The value of the checkbox should be the record's id
		);
	}

	function get_columns() {
		$columns = array(
			'cb'                    => '<input type="checkbox" />', //Render a checkbox instead of text
			'channel_name'          => __( 'Channel name', 'wp-telegram-notifications' ),
			'channel_members'       => __( 'Channel members', 'wp-telegram-notifications' ),
			'channel_administrator' => __( 'Channel administrators', 'wp-telegram-notifications' ),
		);

		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'ID'           => array( 'ID', true ),     //true means it's already sorted
			'channel_name' => array( 'channel_name', false ),     //true means it's already sorted
		);

		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = array(
			'bulk_delete' => __( 'Delete', 'wp-telegram-notifications' )
		);

		return $actions;
	}

	function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		// Search action
		if ( isset( $_GET['s'] ) ) {
			$prepare     = $this->db->prepare( "SELECT * from `{$this->db->prefix}tn_channels` WHERE channel_name LIKE %s", '%' . $this->db->esc_like( $_GET['s'] ) . '%' );
			$this->data  = $this->get_data( $prepare );
			$this->count = $this->get_total( $prepare );
		}

		// Bulk delete action
		if ( 'bulk_delete' == $this->current_action() ) {
			foreach ( $_GET['id'] as $id ) {
				$this->db->delete( $this->db->prefix . "tn_channels", array( 'ID' => $id ) );
			}
			$this->data  = $this->get_data();
			$this->count = $this->get_total();
			Helper::notice( __( 'Items removed.', 'wp-telegram-notifications' ), 'success' );
		}
		// Single delete action
		if ( 'delete' == $this->current_action() ) {
			$this->db->delete( $this->db->prefix . "tn_channels", array( 'ID' => $_GET['ID'] ) );
			$this->data  = $this->get_data();
			$this->count = $this->get_total();
			Helper::notice( __( 'Items removed.', 'wp-telegram-notifications' ), 'success' );
		}
	}

	function prepare_items() {
		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = $this->limit;

		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();

		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our data. In a real-world implementation, you will probably want to
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$data = $this->data;

		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		usort( $data, '\WPTN\Admin\Channels_List_Table::usort_reorder' );

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = $this->count;

		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil( $total_items / $per_page )   //WE have to calculate the total number of pages
		) );
	}

	/**
	 * Usort Function
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return array
	 */
	function usort_reorder( $a, $b ) {
		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'ID'; //If no sort, default to sender
		$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
		$result  = strcmp( $a[ $orderby ], $b[ $orderby ] ); //Determine sort order

		return ( $order === 'asc' ) ? $result : - $result; //Send final sort direction to usort
	}

	//set $per_page item as int number
	function get_data( $query = '' ) {
		$page_number = ( $this->get_pagenum() - 1 ) * $this->limit;
		if ( ! $query ) {
			$query = 'SELECT * FROM `' . $this->db->prefix . 'tn_channels` LIMIT ' . $this->limit . ' OFFSET ' . $page_number;
		} else {
			$query .= ' LIMIT ' . $this->limit . ' OFFSET ' . $page_number;
		}
		$result = $this->db->get_results( $query, ARRAY_A );

		return $result;
	}

	//get total items on different Queries
	function get_total( $query = '' ) {
		if ( ! $query ) {
			$query = 'SELECT * FROM `' . $this->db->prefix . 'tn_channels`';
		}
		$result = $this->db->get_results( $query, ARRAY_A );
		$result = count( $result );

		return $result;
	}

}