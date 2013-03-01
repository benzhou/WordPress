<?php 
class DigitalIvy_Plugin {
	$diListShortCodeFound = false;

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
    	wp_register_script( 'dilist', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/diListApp.js', array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection","di"));
		// wp_enqueue_scripts( 'dicarrot', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/carrot/v_alpha/carrot.js', false, false);
  //   	wp_enqueue_scripts( 'dicache', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/cache/cache.js', false , false);
  //   	wp_enqueue_scripts( 'dijqueryeasing', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.easing.1.3.js', array("jquery") , false);
  //   	wp_enqueue_scripts( 'dijqueryelastislide', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.elastislide.js', array("jquery") , false);
  //   	wp_enqueue_scripts( 'direflection', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/reflection.js', false , false);
  //   	wp_enqueue_scripts( 'di', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/digitalIvy.js', array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection") , false);
  //   	wp_enqueue_scripts( 'dilist', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/diListApp.js', array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection","di"), false);
	
	}

	public function init_di_list($attrs){
		echo "Hello DI";
 		$diListShortCodeFound = true;

		

	}

	public function di_plugin_head(){
		if($diListShortCodeFound){
			wp_print_scripts('dicarrot');
			wp_print_scripts('dicache');
			wp_print_scripts('dijqueryeasing');
			wp_print_scripts('dijqueryelastislide');
			wp_print_scripts('di');
			wp_print_scripts('dilist');
    		
		}
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