<?php 
class DigitalIvy {
	public function __construct() {

		
	}

	public function adminUI() {
		add_menu_page( "Triton DigitalIvy Plugin Settings",  "Triton DigitalIvy", "edit_plugins", "digitalIvyPluginSettings", array( $this, "configForm" ));
		add_submenu_page( "digitalIvyPluginSettings", "Triton DigitalIvy Plugin Settings", "General Settings", "edit_plugins", "digitalIvyPluginSettings", array( $this, "configForm" ) );
	}

	public function configForm() {
		$page = $_GET["page"];

		switch ($page) {
			case "digitalIvyPluginSettings":
			default:
				
				break;
		}
	}
}

?>