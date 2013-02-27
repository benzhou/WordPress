<?php

class See_Settings_Form extends See_Form {

	private $_seePlugin = null;
	private $template_args = array();
	
	public function __construct( &$seePlugin ) {
		$this->_seePlugin = $seePlugin;
	}

	public function display() {
		$template_args = $this->template_args;		
		include "form.config.php";
	}

	public function process() {
		$action = $_POST["action"];
		$errors = array();

		if ( $action == "update" ) {
			if ( ! wp_verify_nonce( $_POST["_wpnonce"], "update_triton_see" ) ) {
				$errors[] = "Invalid form submission";
			}			

			if ( empty( $_POST["see_url"] ) ) {
				$errors[] = "SEE URL is a required field";
			} else {
				$_POST["see_url"] = trim( preg_replace( "/https?:\/\//", "", $_POST["see_url"] ) );
			}

			if ( empty( $_POST["tenant_id"] ) ) {
				$errors[] = "Tenant ID is a required field";
			} else {
				$_POST["tenant_id"] = trim( $_POST["tenant_id"] );
			}

			if ( empty( $_POST["public_key"] ) ) {
				$errors[] = "Public API Key is a required field";
			} else {
				$_POST["public_key"] = trim( $_POST["public_key"] );
			}

			if ( empty( $_POST["private_key"] ) ) {
				$errors[] = "Private API Key is a required field";
			} else {
				$_POST["private_key"] = trim( $_POST["private_key"] );
			}

			$_POST["use_ssl"]      = ! empty( $_POST["use_ssl"] )      ? "1" : "0";
			$_POST["debug"]        = ! empty( $_POST["debug"] )        ? "1" : "0";

			if ( empty( $_POST["post_page_views"] ) ) {
				$_POST["post_page_views"] = array();
			} else {
				$arr = array();
				foreach ( $_POST["post_page_views"] as $action ) {
					if ( ! empty( $action ) ) { $arr[] = $action; }
				}
				$_POST["post_page_views"] = $arr;
				asort( $_POST["post_page_views"] );
			}

			if ( empty( $errors ) ) {
				// Save Config
				update_option( TRITON_SEE_OPTION_NAME_URL,             $_POST["see_url"] );
				update_option( TRITON_SEE_OPTION_NAME_TENANT_ID,       $_POST["tenant_id"] );
				update_option( TRITON_SEE_OPTION_NAME_PUBLIC_KEY,      $_POST["public_key"] );
				update_option( TRITON_SEE_OPTION_NAME_PRIVATE_KEY,     $_POST["private_key"] );
				update_option( TRITON_SEE_OPTION_NAME_POST_PAGE_VIEWS, $_POST["post_page_views"] );
				update_option( TRITON_SEE_OPTION_NAME_USE_SSL,         $_POST["use_ssl"] );
				update_option( TRITON_SEE_OPTION_NAME_DEBUG,           $_POST["debug"] );

				// Notify user
				$this->template_args["alerts"][] = "Settings successfully updated";
			}
		}

		$this->template_args["errors"] = $errors;
	}

	public function run() {
		if ( ! empty( $_POST ) ) {
			$this->process();
		}

		$this->display();
	}

}