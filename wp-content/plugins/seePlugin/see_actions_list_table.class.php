<?php

if ( !class_exists( "WP_List_Table" ) ) {
	require_once( ABSPATH . "wp-admin/includes/class-wp-list-table.php" );
}

class See_Plugin_Actions_List_Table extends WP_List_Table {
	function __construct() {
		parent::__construct( array(
			"singular" => "wp_list_text_link",
			"plural"   => "wp_list_test_links",
			"ajax"     => false
		) );
	}

	function extra_tablenav( $which ) {
		/*if ( $which == "top" ) {
		} else if ( $which == "bottom" ) {
		}*/
	}

	function get_bulk_actions() {
		return $actions = array(
			"delete" => "Delete"
		);
	}

	function get_columns() {
		return $columns = array(
			"cb"               => "<input type='checkbox' />",
			"wordpress_action" => "Wordpress Action",
			"see_action"       => "See Action"
		);
	}

	public function get_sortable_columns() {
		return $sortable = array(
			"wordpress_action" => array( "wordpress_action", true),
			"see_action"       => array( "see_action", true)
		);
	}

	/**
	 * Sorting function used for processing an array of custom action records
	 * before displaying them to the user.
	 */
	private function usort_reorder( $a, $b ) {
		$orderby = ( !empty( $_GET["orderby"] ) ) ? $_GET["orderby"] : "wordpress_action";
		$order = ( !empty( $_GET["order"] ) ) ? $_GET["order"] : "asc";
		$result = strcmp( $a[$orderby], $b[$orderby] );
		return ( $order === "asc" ) ? $result : -$result;
	}

	public function prepare_items( $items ) {
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);

		$per_page = 5;
		$current_page = $this->get_pagenum();
		$total_items = count( $items );
		$items = array_slice( $items, ( ( $current_page - 1) * $per_page ), $per_page );

		$this->set_pagination_args( array(
			"total_items" => $total_items,
			"per_page"    => $per_page
		));

		$this->items = array();

		foreach( $items as $key => $item ) {
			$item["ID"] = $key;
			if ( $item["wordpress_action"] == "read_post" ) {
				$item["wordpress_action"] = "User reads a post ";
				$read_category = $item["action_read_category"];
				if ( empty( $read_category ) ) {
					$item["wordpress_action"] .= "in <b>any category</b>";
				} else {
					$category_name = get_cat_name( $read_category );
					if ( !empty( $category_name ) ) {
						$item["wordpress_action"] .= "in the <b>" . $category_name . "</b> category";
					} else {
						$item["wordpress_action"] .= "in an unknown category<br /><i>(This action should be modified or deleted)</i>";
					}
				}
			}
			$this->items[] = $item;
		}

		usort( $this->items, array( &$this, 'usort_reorder' ) );
	}

	function column_cb( $item ) {
		return sprintf( "<input type='checkbox' name='action[]' value='%s' />", $item["ID"] );
	}

	function column_wordpress_action( $item ) {
		$actions = array(
			"edit"   => sprintf( '<a href="?page=%s&action=%s&item=%s">Edit</a>', $_REQUEST["page"], "editAction", $item["ID"] ),
			"delete" => sprintf( '<a href="?page=%s&action=%s&item=%s&itemSeeAction=%s&_wpnonce=%s">Delete</a>', $_REQUEST["page"], "deleteAction", $item["ID"], $item["see_action"], wp_create_nonce( "delete_custom_action" ) )
		);
		return sprintf( '%1$s %2$s', $item["wordpress_action"], $this->row_actions( $actions ) );
	}

	function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case "wordpress_action":
			case "see_action":
				return $item[ $column_name ];
			default:
				return "";
		}
	}
}