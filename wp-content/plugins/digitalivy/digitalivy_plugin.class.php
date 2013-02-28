<?php 
class DigitalIvy_Plugin {
	public function __construct() {

		
	}

	public function adminUI() {
		add_menu_page( "Triton DigitalIvy Plugin Settings",  "Triton DigitalIvy", "edit_plugins", "diPluginSettings", array( $this, "configForm" ));
		add_submenu_page( "diPluginSettings", "Triton DigitalIvy Plugin Genneal Settings", "General Settings", "edit_plugins", "diPluginSettings", array( $this, "configForm" ) );
	}

	public function init_di_list(){
		
	}

	protected function configForm() {
		$page = $_GET["page"];

		switch ($page) {
			case "diPluginSettings":
			default:
				
				break;
		}
	}
}

?>