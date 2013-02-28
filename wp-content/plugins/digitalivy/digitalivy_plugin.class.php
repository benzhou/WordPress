<?php 
class DigitalIvy_Plugin {
	public function __construct() {

		
	}

	public function adminUI() {
		add_menu_page( "Triton DigitalIvy Plugin Settings",  "Triton DigitalIvy", "edit_plugins", "diPluginSettings", array( $this, "configForm" ));
		add_submenu_page( "diPluginSettings", "Triton DigitalIvy Plugin Genneal Settings", "General Settings", "edit_plugins", "diPluginSettings", array( $this, "configForm" ) );
	}

	public function init_di_list($attrs){
		echo "Hello DI";
    	wp_enqueue_script( 'dicarrot', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/carrot/v_alpha/carrot.js', false );
    	wp_enqueue_script( 'dicache', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/cache/cache.js', false );
    	wp_enqueue_script( 'dijqueryeasing', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.easing.1.3.js', array("jquery") );
    	wp_enqueue_script( 'dijqueryelastislide', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.elastislide.js', array("jquery") );
    	wp_enqueue_script( 'direflection', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/reflection.js', false );
    	wp_enqueue_script( 'di', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/digitalIvy.js', array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection") );
    	wp_enqueue_script( 'dilist', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/diListApp.js', array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection","di");
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