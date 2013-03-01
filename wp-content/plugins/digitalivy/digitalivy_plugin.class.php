<?php 
class DigitalIvy_Plugin {
	public function __construct() {

		
	}

	public function adminUI() {
		add_menu_page( "Triton DigitalIvy Plugin Settings",  "Triton DigitalIvy", "edit_plugins", "diPluginSettings", array( $this, "configForm" ));
		add_submenu_page( "diPluginSettings", "Triton DigitalIvy Plugin Genneal Settings", "General Settings", "edit_plugins", "diPluginSettings", array( $this, "configForm" ) );
	}

	public function init_di_plugin(){
		/* Register all DigitalIvy plugin needed script. */
        wp_register_script( 'dicarrot', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/carrot/v_alpha/carrot.js' );
    	wp_register_script( 'dicache', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/cache/cache.js' );
    	wp_register_script( 'dijqueryeasing', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.easing.1.3.js');
    	wp_register_script( 'dijqueryelastislide', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.elastislide.js');
    	wp_register_script( 'direflection', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/reflection.js' );
    	wp_register_script( 'di', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/digitalIvy.js' );
		wp_register_script( 'dilist', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/diListApp.js');
   		// wp_register_script( 'dicarrot', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/carrot/v_alpha/carrot.js', false );
    	// wp_register_script( 'dicache', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/cache/cache.js', false );
    	// wp_register_script( 'dijqueryeasing', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.easing.1.3.js', array("jquery") );
    	// wp_register_script( 'dijqueryelastislide', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.elastislide.js', array("jquery") );
    	// wp_register_script( 'direflection', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/reflection.js', false );
    	// wp_register_script( 'di', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/digitalIvy.js', array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection") );
    	// wp_register_script( 'dilist', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/diListApp.js', array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection","di"));
	
	}

	public function init_di_list($attrs){
		echo "Hello DI";
 


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