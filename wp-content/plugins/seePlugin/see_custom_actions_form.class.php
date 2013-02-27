<?php

class See_Custom_Actions_Form extends See_Form {

	private $_seePlugin = null;
	private $template_args = array();

	public function __construct( &$seePlugin ) {
		$this->_seePlugin = $seePlugin;
	}

	public function display() {
		$template_args = $this->template_args;

		require_once "see_actions_list_table.class.php";
		$actions_list_table = new See_Plugin_Actions_List_Table();
		if ( ! ( $customActions = $this->_seePlugin->getCustomActions() ) ) $customActions = array();
		$actions_list_table->prepare_items( $customActions );

		include "form.custom_actions.php";
	}

	public function process() {
		$action = $_REQUEST["action"];

		switch( $action ) {
			case "deleteAction":
				$this->processDeleteAction();
				break;
			case "addAction":
				$this->processAddAction();
				break;
		}
	}

	private function processAddAction() {
		if ( ! wp_verify_nonce( $_POST["_wpnonce"], "triton_add_custom_action" ) ) {
			$this->template_args["errors"][] = "Invalid form submission";
			return;
		}

		$event_type = $_POST["event_type"];
		$event_read_category = null;
		if ( $event_type == "read" ) {
			$wordpress_action = "read_post";
			$event_read_category = $_POST["event_type_category"];
		} else if ( $event_type == "list" ) {
			if ( ! empty( $_POST["event_type_custom"] ) ) {
				$wordpress_action = $_POST["event_type_custom"];
			} else {
				$wordpress_action = $_POST["event_type_list_selection"];
			}
		} else {
			$this->template_args["errors"][] = "Invalid trigger event specified";
		}

		$see_action = $_POST["event_see_action"];
		if ( empty( $see_action ) ) {
			$this->template_args["errors"][] = "You must select a SEE action";
		}

		if ( count( $this->template_args["errors"] ) == 0 ) {
			$customActions = $this->_seePlugin->getCustomActions();
			$customActions[] = array(
				"wordpress_action" => $wordpress_action,
				"see_action"       => $see_action,
				"action_read_category" => $event_read_category
			);
			$this->_seePlugin->updateCustomActions( $customActions );
			$this->template_args["alerts"][] = "New custom action successfully added";
		}		
	}

	private function processDeleteAction() {
		if ( !wp_verify_nonce( $_GET["_wpnonce"], "delete_custom_action" ) ) {
			$this->template_args["errors"][] = "Invalid form submission";
			return;
		}

		$customActions = $this->_seePlugin->getCustomActions();
		$deleteIndex = $_GET["item"];
		// We check the See action to prevent accidental deletions
		// resulting from page refreshes
		$deleteSeeAction = $_GET["itemSeeAction"];

		if ( $deleteIndex < 0 
			|| $deleteIndex >= count( $customActions ) 
			|| ! $customActions[ $deleteIndex ] 
			|| $customActions[ $deleteIndex ]["see_action"] != $_GET["itemSeeAction"] ) {
			$this->template_args["errors"][] = "Invalid form submission";
		} else {
			array_splice($customActions, $deleteIndex, 1);
			$this->_seePlugin->updateCustomActions( $customActions );
			$this->template_args["alerts"][] = "Custom action successfully deleted";
		}					
	}

	public function run() {
		if ( ! empty( $_REQUEST["action"] ) ) {
			$this->process();
		}

		$this->display();
	}

}