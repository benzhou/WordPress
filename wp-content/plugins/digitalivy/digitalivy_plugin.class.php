<?php 
class DigitalIvy_Plugin {
	private $diListShortCodeFound = false;

	public function __construct() {

		
	}

	/**
	 * Called upon activation of the plugin, creates all necessary options
	 * using default values.
	 */
	public function tritonDIInstall() {
		add_option( TRITON_DI_OPTION_ORG_CODE,             "DEV-4SANBA" );
	}

	/**
	 * Called upon deactivation of the plugin, cleans up by removing all
	 * stored options.
	 */
	public function tritonDIUninstall() {
		delete_option( TRITON_DI_OPTION_ORG_CODE );
	}

	public function adminUI() {
		add_menu_page( "Triton DigitalIvy Plugin Settings",  "Triton DigitalIvy", "edit_plugins", "diPluginSettings", array( $this, "configForm" ));
		add_submenu_page( "diPluginSettings", "Triton DigitalIvy Plugin Genneal Settings", "General Settings", "edit_plugins", "diPluginSettings", array( $this, "configForm" ) );
	}

	public function init_di_plugin(){
		/* Register all DigitalIvy plugin needed script. */
        wp_register_script( 'dicarrot', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/carrot/v_alpha/carrot.js',array("jquery") );
    	wp_register_script( 'dicache', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/cache/cache.js', false);
    	wp_register_script( 'dijqueryeasing', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.easing.1.3.js', array("jquery"));
    	wp_register_script( 'dijqueryelastislide', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.elastislide.js', array("jquery"));
    	wp_register_script( 'direflection', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/reflection.js',false );
    	wp_register_script( 'di', plugins_url('digitalivy.js', __FILE__), array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection") );
    	wp_register_script( 'dilist', plugins_url('digitalivylist.js', __FILE__), array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection","di"));
   //	wp_enqueue_scripts( 'dicarrot', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/carrot/v_alpha/carrot.js', false, false);
  //   	wp_enqueue_scripts( 'dicache', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/cache/cache.js', false , false);
  //   	wp_enqueue_scripts( 'dijqueryeasing', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.easing.1.3.js', array("jquery") , false);
  //   	wp_enqueue_scripts( 'dijqueryelastislide', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.elastislide.js', array("jquery") , false);
  //   	wp_enqueue_scripts( 'direflection', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/reflection.js', false , false);
  //   	wp_enqueue_scripts( 'di', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/digitalIvy.js', array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection") , false);
  //   	wp_enqueue_scripts( 'dilist', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/diListApp.js', array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection","di"), false);


    	wp_register_style('dicss', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Content/css/listPagestyle.css');
    	wp_register_style('dilistcss', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Content/css/contest-list/style.css');
    	wp_register_style('diwordpress', plugins_url('diwordpress.css', __FILE__));
	}

	public function init_di_list($attrs){
		//echo "Hello DI";
    	// wp_enqueue_script( 'dicarrot', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/carrot/v_alpha/carrot.js', false );
    	// wp_enqueue_script( 'dicache', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/cache/cache.js', false );
    	// wp_enqueue_script( 'dijqueryeasing', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.easing.1.3.js', array("jquery") );
    	// wp_enqueue_script( 'dijqueryelastislide', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/jquery.elastislide.js', array("jquery") );
    	// wp_enqueue_script( 'direflection', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/reflection.js', false );
    	// wp_enqueue_script( 'di', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/digitalIvy.js', array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection") );
    	// wp_enqueue_script( 'dilist', '//dc4olesfnreg4.cloudfront.net/digitalivy/UGC_Client_Rollout_20130221.2/Scripts/digitalIvy/0_1/diListApp.js', array("jquery","dicarrot","dicache","dijqueryeasing","dijqueryelastislide","direflection","di"));
    	$this->diListShortCodeFound = true;

		echo '<div id="container-bg" class="list">
	            <div id="container-list">
	                <div id="container-list-header">
	                        <h1>Currently Active Contests</h1>
	                </div>
	                <div id="featured-contest-items">
	            	
	                </div>
	                <div id="container-contestitems">
	                    <div id="container-contestStates"></div>
	                    <div id="container-contestListItem"></div>
	                </div>
	            </div>
	        </div>
			';


	}

	public function di_plugin_footer(){
		echo "$diListShortCodeFound " . $this->diListShortCodeFound;
		//Only renders the script if the short code presented
		if($this->diListShortCodeFound == true){
			//Print out all style /scripts needed for the contest list.
			//TODO: this is definitely not the place I want to place them (I want to place them in the head instead of footer)
			wp_print_styles('dicss');
			wp_print_styles('dilistcss');
			wp_print_styles('diwordpress');
			wp_print_scripts('dicarrot');
			wp_print_scripts('dicache');
			wp_print_scripts('dijqueryeasing');
			wp_print_scripts('dijqueryelastislide');
			wp_print_scripts('di');
			wp_print_scripts('dilist');

			echo '<script type="text/javascript">
					digitalIvy.listApp.run({
	            	debug: false, 
		            labels: {
		                ListEnter: "Enter",
		                ListView: "View Contest",
		                sweepCurrentListEnd: "Ends in {0} days",
		                sweepCurrentListEndToday: "Ends today",
		                ugcCurrentSubmissionEnd: "Submissions end in {0} days",
		                ugcCurrentSubmissionEndToday: "Submissions end today",
		                ugcCurrentVotingEnd: "Voting ends in {0} days",
		                ugcCurrentVotingEndToday: "Voting ends today",
		                contestUpcoming: "This contest starts on {0}",
		                contestClosed: "This contest has closed",
		                stateC: "Current",
		                stateU: "Upcoming",
		                stateCl: "Closed",
		                searchPh: "Search for Contest",
		                featured: "Featured Contests",
		                EMPTYUPCOMINGLIST: "Currently, there are no upcoming contests scheduled. Please check back soon.",
		                EMPTYCURRENTLIST: "Currently, there are no active contests. Please check back soon.",
		                EMPTYEXPIREDLIST: "No contests have ended in the last 30 days.",
		                headers: {
		                    current: "Currently Active Contests",
		                    upcoming: "Upcoming Contests",
		                    closed: "Expired Contests"
		                }
		            }, 
		            filterId: "DEV-4SANBA",
		            contestListType: 0,
		            forceHrefToTopFrame: false,
		            disablePaging: false, // should be true or false
		            api: {
		                url: "http://dev4sanban.test.listenernetwork.net",
		                forceHttps: false,
		                methods: {
		                    getContestList: "/Contest/Home/GetContestList"
		                }
		            }
		        });
				</script>';
		}
	}

	public function di_plugin_head(){
		echo "$diListShortCodeFound:" . $diListShortCodeFound;
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