<?php 

class DIAdminGeneralForm{

	private $_diPlugin = null;
	private $template_args = array();
	
	public function __construct( &$diPlugin ) {
		$this->_diPlugin = $diPlugin;
	}

	public function Get(){
		//include dirname(plugin_basename(__FILE__)) . '/templates/DI_Admin_GeneralForm.php';
		include 'templates/DI_Admin_GeneralForm.php';
	}

	public function Post(){
		$action = $_POST["action"];
		$errors = array();

		if ( $action == "update" ) {
			if ( ! wp_verify_nonce( $_POST["_wpnonce"], "update_triton_di" ) ) {
				$errors[] = "Invalid form submission";
			}			

			if ( empty( $_POST["org_short_code"] ) ) {
				$errors[] = "Org Short Code is a required field";
			} else {
				//$_POST["org_short_code"] = trim( preg_replace( "/https?:\/\//", "", $_POST["see_url"] ) );
			}

			if ( empty( $_POST["di_feed_url"] ) ) {
				$errors[] = "Feed Url is a required field";
			} else {
				//$_POST["di_feed_url"] = trim( $_POST["di_feed_url"] );
			}


			if ( empty( $errors ) ) {
				// Save Config
				update_option( TRITON_DI_OPTION_ORG_CODE,             $_POST["org_short_code"] );
				update_option( TRITON_DI_OPTION_DATA_FEED_URL,       $_POST["di_feed_url"] );
				

				// Notify user
				$this->template_args["alerts"][] = "Settings successfully updated";
			}
		}

		$this->template_args["errors"] = $errors;
	}

	public function Run(){
		if ( ! empty( $_POST ) ) {
			$this->Post();
		}

		$this->Get();
	}
}

?>