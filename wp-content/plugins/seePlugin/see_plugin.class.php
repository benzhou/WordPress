<?php

class see_plugin {

	/**
	 * When successfully used, this stores an key => value array of a request made to the SEE API
	 *
	 * @var boolean|array
	 */
	private static $updateRequest = false;

	private $debugging = false;

	/**
	 * An Array of information about the user that is currently logged in
	 *
	 * @var array
	 */
	private static $see_data      = array();

	public function __construct() {

		// Hardcoded config (for now)
		$this->algorithm           = "sha1";
		$this->api_request_timeout = 500;
		$this->debugging = get_option( TRITON_SEE_OPTION_NAME_DEBUG );
	}

	/**
	 * Called upon activation of the plugin, creates all necessary options
	 * using default values.
	 */
	public function tritonInstall() {
		add_option( TRITON_SEE_OPTION_NAME_URL,             "www.testsee.com" );
		add_option( TRITON_SEE_OPTION_NAME_PUBLIC_KEY,      "" );
		add_option( TRITON_SEE_OPTION_NAME_PRIVATE_KEY,     "" );
		add_option( TRITON_SEE_OPTION_NAME_TENANT_ID,       "see_123" );
		add_option( TRITON_SEE_OPTION_NAME_ACTIONS,         array() );
		add_option( TRITON_SEE_OPTION_NAME_POST_PAGE_VIEWS, array() );
		add_option( TRITON_SEE_OPTION_NAME_USE_SSL,         "1" );
		add_option( TRITON_SEE_OPTION_NAME_DEBUG,           "0" );
		add_option( TRITON_SEE_PLUGIN_VERSION_KEY,          TRITON_SEE_PLUGIN_VERSION_NUM );
	}

	/**
	 * Called upon deactivation of the plugin, cleans up by removing all
	 * stored options.
	 */
	public function tritonUninstall() {
		delete_option( TRITON_SEE_OPTION_NAME_URL );
		delete_option( TRITON_SEE_OPTION_NAME_PUBLIC_KEY );
		delete_option( TRITON_SEE_OPTION_NAME_PRIVATE_KEY );
		delete_option( TRITON_SEE_OPTION_NAME_TENANT_ID );
		delete_option( TRITON_SEE_OPTION_NAME_ACTIONS );
		delete_option( TRITON_SEE_OPTION_NAME_POST_PAGE_VIEWS );
		delete_option( TRITON_SEE_OPTION_NAME_USE_SSL );
		delete_option( TRITON_SEE_OPTION_NAME_DEBUG );
		delete_option( TRITON_SEE_PLUGIN_VERSION_KEY );
	}

	/**
	 * Gets the list of defined custom actions and registers handler functions
	 * for the specified wordpress action hooks.
	 */
	public function registerCustomActions()
	{
		$customActions = $this->getCustomActions();

		foreach( $customActions as $action ) {
			$wordpressAction = $action["wordpress_action"];
			$seeAction = $action["see_action"];
			$actionReadCategory = $action['action_read_category'];

			$callback = null;
			$handlerMethod = null;

			$methodName = "slug" . ucfirst( $seeAction );

			// error_log($methodName);

			if ( is_callable( array( $this, $methodName ) ) ) {
				$handlerMethod = array( $this, $methodName );
			} else {
				$self = $this;
				$handlerMethod = function () use ( &$self, $seeAction ) {
					$self->slugAction( $seeAction );
				};
			}

			switch( $action["wordpress_action"] ) {
				case "read_post":
					$wordpressAction = "loop_start";
					$actionReadCategory = ( isset( $action['action_read_category'] ) ) ? $action['action_read_category'] : -1;
					$self = $this;

					$callback = function ( $wpQuery ) use ( &$self, $actionReadCategory, $handlerMethod ) {
						if ( $wpQuery->post_count != 1 || count( $wpQuery->posts ) != 1 ) return;

						$psotID = null;
						foreach ( $wpQuery->posts as $post ) {
							if ( !is_single( $post->ID ) ) return;
							$postID = $post->ID;
						}

						if ( $actionReadCategory == 0 ) {
							call_user_func( $handlerMethod );
							return;
						}

						$categories = wp_get_post_categories( $postID );

						if ( in_array( $actionReadCategory, $categories ) ) {
							call_user_func( $handlerMethod );
							return;
						}
					};

					break;
				default:
					$callback = $handlerMethod;
					break;
			}

			add_action( $wordpressAction, $callback, 10 );

		}
	}

	/**
	 * Get the meta data for see short code and return a link
	 *  @param array  $atts  auto-populated array of attributes from Wordpress
	 *  @return string
	 */
	public function shortcodeSEE( $atts ) {
		extract( shortcode_atts( array(
					"field"    => "username", // Required
					"name"     => "",         // Optional
					"property" => "value",    // Optional
					"default"  => "",         // Optional

				), $atts ) );

		$return = ''; // Holder variable for what we're returning

		// Make sure we have the latest and greatest User Information
		$this->populateSEEData();

		// Is what we're looking for a string? (Fields like displayName)
		if ( is_string( self::$see_data[ $field ] ) ) {
			$return = self::$see_data[ $field ];

			// Is what we're looking for an array? (Fields like Ladder, Achievement or Action)
		} else if ( is_array( self::$see_data[ $field ] ) && $name != "" && $property != "" ) {

				foreach ( self::$see_data[ $field ] as $obj ) {
					// Search the array for an item with the corresponding name that has the property we're looking up
					if ( $obj["name"] == $name && isset( $obj[ $property ] ) ) {
						$return = (string)$obj[ $property ];
					}
				}

			}

		// If we didn't get anything, use default
		return empty( $return ) ? $default : $return;
	}

	/**
	 * Use API to get information about the current user and store it into the $see_data static variable
	 *
	 * @return array   of SEE information about the current user
	 */
	private function getSeeData() {

		// Load $user information from Wordpress
		$user      = wp_get_current_user();
		$vid       = get_user_meta( $user->ID, 'tritonsee_vid', true );

		//error_log(var_dump($vid, 1));

		// Generate Payload to send to API
		$payload = array(
			"tenant_id" => get_option( TRITON_SEE_OPTION_NAME_TENANT_ID ),
		);

		// Sign payload with API Signature
		$payload = $this->signPayload( $payload );

		// Build URL
		$url  = get_option( TRITON_SEE_OPTION_NAME_USE_SSL ) == "1" ? "https://" : "http://";
		$url .= get_option( TRITON_SEE_OPTION_NAME_URL );
		$url .= "/api/2/user/" . $vid ."/get?" . http_build_query( $payload );

		$this->debug( "API URL: {$url}" );

		// Send to SEE, and capture the result
		$resp = wp_remote_get( $url );

		if ( is_wp_error( $resp ) ) {
			return array();
			// @todo We got an error, not sure what to do
			// Well one thing is for sure, we don't want to make 100 more requests if the first one fails...

		} else {

			$arr = json_decode( $resp['body'], true );

			// Base Information
			$userinfo = array(
				"username" => $arr["displayName"],
				"email"    => $arr["email"],
				"avatar"   => $arr["profile_pic"],
				"ladder"   => $arr["laddersv2"],
			);

			// Custom Reg Fields
			foreach ( $arr["custom_reg_fields"] as $custom_reg_field ) {
				$userinfo[ $custom_reg_field["type"] ] = $custom_reg_field["value"];
			}

			return $userinfo;

		}
	}

	/**
	 * Use API to get information about the current user and store it into the self::$see_data static var
	 *
	 * @param boolean $force If set to true, then it ignores the lifetime of the data it has already gotten
	 */
	private function populateSEEData( $force = false ) {

		$now      = gmmktime();
		$lifetime = 900; // Make sure the data isn't older than 15 minutes (60 * 15 = 900)
		$user     = wp_get_current_user();

		if ( ! isset( $user->ID ) || $user->ID == 0 ) { // We're not logged in wtih a valid user
			self::$see_data = array();

			// Only get information from API if we
			//  - have a valid email to use for the current user
			//  - or whatever information we have there is too old according to $lifetime
		} else if ( $force || empty( self::$see_data ) ) {

				self::$see_data = $this->getSeeData();

			}
	}

	/**
	 * Sourcecode shortcode handler
	 *
	 * @return string
	 */
	public function shortcodeSourceCode() {
		return $this->getCurrentUserMetaData( "ex_member_id" );
	}

	/**
	 * Get the meta data for the current user and return it
	 *
	 * @param string  $key The name of the key to return
	 * @return string
	 */
	private function getCurrentUserMetaData( $key ) {
		$currentUser = wp_get_current_user();
		if ( 0 != $currentUser->ID )
			return (string) get_user_meta( $currentUser->ID, $key, true );
	}

	/**
	 * Function that the API calls when Triton SEE sends an update for a specific user.  This way future
	 * requests to SEE can include the Vistior ID
	 *
	 * @param int     $user_id Unique identifier of a WP User
	 * @param string  $vid     Unique identifier of a SEE User
	 */
	public function updateVid( $user_id = 0, $vid = "" ) {

		if ( ! empty( $user_id ) && ! empty( $vid ) ) {
			update_user_meta( $user_id, "tritonsee_vid", $vid );
		}
	}

	/**
	 * Things stack up in the 'tritonsee_api_queue' table for users.  At every page load we see if there is anything
	 * we need to do for the $user_id that is logged in by calling this function.  It returns any $slugs and $actions
	 * we need to perform, and it also clears out the queue.
	 *
	 * @param int     $user_id
	 */
	private function flushApiQueue( $user_id = 0 ) {

		$user_id = intval( $user_id );
		$slugs   = array();
		$actions = array();

		if ( $user_id > 0 ) { // Do we even have a user to work with?

			// Get Api queue for this USER
			$res = get_user_meta( $user_id, "tritonsee_api_queue", true );
			$res = json_decode( $res, true );

			//$this->debug( "flushApiQueue(".$user_id.") $res: " . print_r( $res, true ) );
			//error_log( "flushApiQueue(".$user_id.") $res: " . var_export($res) );

			if ( is_array( $res ) && count( $res ) > 0 ) {
				foreach ( $res as $row ) {

					if ( ! in_array( $row["slug"], $slugs ) ) { // Slugs are unique
						$slugs[] = $row["slug"];
					}

					if ( ! empty( $row["action"] ) ) { // Actions can be done multiple times
						$actions[] = $row["action"];
					}

				}
			}

			// Clear out all of the slugs and actions for this user
			delete_user_meta( $user_id, "tritonsee_api_queue" );

		}

		return array( $slugs, $actions );
	}

	/**
	 * Writes out JS for SEE Iplementation that goes in the <head> tag
	 *
	 * @todo Move the script tags out of the head function and put them into the
	 * template file
	 */
	public function head() {

		global $userdata;
		//error_log(var_export($userdata, true));

		$userdata  = (array)$userdata;
		$timestamp = time() - date( 'Z' );

		// SEE JavaSCript
		echo '<script type="text/javascript" src="http://see-static.tritondigital.net/js/see_jquery.min.js"></script>'."\n";

		//echo '<script type="text/javascript" id="see_script" src="http://' . get_option( "tritonsee_url" ) .'/widget.php?wt=notifications,actions,authentication,leaderboardv2,profilev2,achievements,activity,miniprofile&seesid=' . preg_replace( "/[^0-9]/", "", get_option( "tritonsee_tenant_id" ) ) . '&_=' . $timestamp . '></script>' . "\n";

		echo '<script type="text/javascript" src="http://see-d-www-03.tritondigital.net/widget/v2/see_100/see.js"></script>';

		// We're viewing this page, record slug if need-be
		$this->slugPageView();

		if ( ! empty( $userdata["ID"] ) ) {  // Is a user logged in?

			// Get any $slugs and $actions about this particular user
			list( $slugs, $actions ) = $this->flushApiQueue( $userdata["ID"] );

			//$this->debug( "Here are our slugs: " . print_r( $slugs, true ) );
			//error_log(var_export($slugs, 1));

			if ( ! empty( $slugs ) ) { // We have something to do,

				// generate API request
				$payload = array(
					"tenant_id"     => get_option( TRITON_SEE_OPTION_NAME_TENANT_ID ),
					"vid"           => get_user_meta( $userdata["ID"], "tritonsee_vid", true ),
					"thirdparty_id" => $userdata["ID"],
					"slugs"         => $slugs,
					"actions"       => $actions,
				);

				error_log(var_export($payload, 1));

				// Sign API request
				$payload = $this->signPayload( $payload );

				// Build URL for API request
				$url = get_option( TRITON_SEE_OPTION_NAME_USE_SSL ) ? 'https://' : 'http://';
				$url .= get_option( TRITON_SEE_OPTION_NAME_URL );
				$url .= '/api/2/user?' . http_build_query( $payload ); // Finish Query

				// Make API request
				echo '<script type="text/javascript" src="'. $url .'"></script>';

			}
		} else {
			echo '<script type="text/javascript">
					$jqSEE(document).ready(function() {
	  				if (typeof see_loggedIn !== "undefined" && see_loggedIn) {
						see_auth.logout();
					}});
				</script>';
		}

	}


	/**
	 * Function that returns the $payload array properly signed with the correct public_key and api_signature
	 * In the process it will remove any previous signatures.
	 *
	 * @param array   $payload Associative array of Data used to generate token in the first place
	 * @return array  $payload    Newly signed Payload
	 */
	private function signPayload( $payload = array() ) {

		$private_key = get_option( TRITON_SEE_OPTION_NAME_PRIVATE_KEY );
		$public_key  = get_option( TRITON_SEE_OPTION_NAME_PUBLIC_KEY );

		if ( empty( $private_key ) || empty( $public_key ) || empty( $this->algorithm ) ) {
			return false;

		} else {

			// Make sure we tag with the correct key & Timestamp
			$payload["public_key"]    = $public_key;
			$payload["api_timestamp"] = time() - (int)substr( date( "O" ), 0, 3 ) * 3600; // GMT

			// Add Signature
			$payload["api_signature"] = $this->generateSignature( $payload, $payload["api_timestamp"] );

			return $payload;

		}
	}

	/**
	 * Function that returns the correct API signature for the given $payload and $timestamp
	 *
	 * @param array   $payload   Associative array of Data used to generate token in the first place
	 * @param string  $timestamp UNIX Timestamp used to encrypt the request.
	 * @return string Correct API Signature
	 */
	private function generateSignature( $payload = array(), $timestamp = 0 ) {

		// Remove any previous signatures (just in case)
		if ( isset( $payload["api_signature"] ) ) {
			unset( $payload["api_signature"] );
		}

		// Sort $payload by key so can be properly validated by the handler
		ksort( $payload );

		return hash_hmac( $this->algorithm, http_build_query( $payload ) . $timestamp, get_option( TRITON_SEE_OPTION_NAME_PRIVATE_KEY ) );
	}

	/**
	 * This function is used by the user.php API to verify that the request variables are properly set.
	 * It checks for:
	 *
	 * $_GET['api_signature'] should be a 32 character string and
	 * $_GET['api_timestamp'] should an integer greater than zero
	 * $_GET['vid']           to be specificed
	 *
	 * @return bool True being a valid request
	 */
	public function checkAPIRequest() {

		try {
			// Is a timestamp getting passed along
			if ( empty( $_GET["api_timestamp"] ) || ! is_numeric( $_GET["api_timestamp"] ) ) {
				throw new Exception( "Invalid API Timestamp" );
			}

			// Is a unique identifier being supplied
			if ( empty( $_GET["vid"] ) && empty( $_GET["user_id"] ) ) {
				throw new Exception( "Invalid User Identifier" );
			}

			// Check if it is an old request
			if ( $this->api_request_timeout > 0 && ( abs( $_GET["api_timestamp"] - gmmktime() ) > $this->api_request_timeout ) ) {
				throw new Exception( "Request is expired (" . ( abs( $_GET["api_timestamp"] - gmmktime() ) ) ." seconds)" );
			}

			// Is the request properly signed
			if ( empty( $_GET["api_signature"] ) || $_GET["api_signature"] != $this->generateSignature( $_GET, $_GET["api_timestamp"] ) ) {
				throw new Exception( "Invalid API Signature" );
			}

		} catch ( Exception $e ) {
			echo $e->getMessage();
			return false;
		}

		return true;
	}

	/**
	 * Looks to see if a querystring referral has been sent over.  If it has, set it as
	 * a cookie so the refering user can get credit for the referral at regsitration
	 * time
	 */
	public function createCookiesFromGet() {
		if ( ! empty( $_GET["referralSource"] ) ) {
			// Set a cookie
			setcookie( "referralSource", (string) $_GET["referralSource"], time() + 3600 );
		}
	}


	/**
	 * Function that gets called when 'user_register' hook is fired within Wordpress.
	 * The function sends a request to SEE to update the user.  See then calls back to
	 * get the actual user data.
	 *
	 * @param int     $user_id
	 */
	public function userUpdate( $userId ) {

		$vid = get_user_meta( $userId, "tritonsee_vid", true );

		$payload = array(
			"tenant_id"     => get_option( TRITON_SEE_OPTION_NAME_TENANT_ID ),
		);

		$payload = $this->signPayload( $payload );

		$url  = get_option( TRITON_SEE_OPTION_NAME_USE_SSL ) == "1" ? "https://" : "http://";
		$url .= get_option( TRITON_SEE_OPTION_NAME_URL );
		$url .= "/api/2/user/". $vid ."/flag" . http_build_query( $payload );

		$seeResp = wp_remote_get( $url );

		// @todo handle response at all?
	}

	/**
	 * Call the update functions.  This should happen on the 'shutdown' action
	 *
	 * @return null
	 */
	public function processUpdate() {
		if ( self::$updateRequest !== false ) {
			switch ( self::$updateRequest["action"] ) {
				case "update":
					$this->userUpdate( self::$updateRequest["userId"] );
					break;
				case "register":
					$this->userRegister( self::$updateRequest["userId"] );
					break;
			}
		}
	}

	/**
	 * Records that a user has been updated and needs to have data synced
	 * Called by 'edit_user_profile_update' action in wordpress
	 *
	 * @param int     $userId The wordpress user id
	 * @return none
	 */
	public function flagUserUpdated( $userId ) {
		self::$updateRequest = array(
			"action" => "update",
			"userId" => $userId
		);
	}

	/**
	 * Records that a user has registed
	 * Called by 'user_register' action in wordpress
	 *
	 * @param int     $userId The wordpress user id
	 * @return none
	 */
	public function flagUserRegistered( $userId ) {
		self::$updateRequest = array(
			"action" => "register",
			"userId" => $userId
		);
	}

	/**
	 * This function accesses the Wordpress DB and returns the core and meta information in an array
	 *
	 * @param int     $user_id Unique identifier within Wordpress for the user
	 * @return array      Array of core and meta user info for redistribution
	 * @todo          Consider using built in WP functions get_userdata($id), but need
	 *              to find a way to have it accommodate a custom set of Meta Data
	 */
	public function getUserInfo( $user_id = 0 ) {

		$user_id = intval( $user_id ); // Sanitize

		// Get row of user data
		$user_data = get_userdata( $user_id );

		// Get META data too
		$meta_info = get_user_meta( $user_id );

		// Strip out the values to a string since they're returned as an array
		foreach ( $meta_info as $k => $values ) {
			if ( is_array( $values ) ) {
				$meta_info[ $k ] = implode( ' ', $values );
			}
		}

		$data = array(
			"displayName"  => $user_data->display_name,
			"password"     => "password", // $user_data->user_pass, // What should go here?
			"email"      => $user_data->user_email,
			"firstName"   => $meta_info["first_name"],
			"lastName"   => $meta_info["last_name"],
			"ex_member_id" => $meta_info["ex_member_id"],
			"custom_reg_fields" => $meta_info
		);

		if ( ! empty( $meta_info["referralSource"] ) ) {
			$data["referralSource"] = $meta_info["referralSource"];
		}

		if ( ! empty( $meta_info["avatar"] ) ) {
			// We want to get a small image here so lets do 100
			$avatarHTML = get_avatar( $user_id, 100 );
			preg_match( '/src="( .*? )"/i', $avatarHTML, $matches );
			$data["profile_pic"] = $matches[1];
		} elseif ( ! empty( $meta_info["oa_social_login_user_thumbnail"] ) ) {
			$data["profile_pic"] = $meta_info["oa_social_login_user_thumbnail"];
		}

		return $data;
	}

	/**
	 * Simple function that adds Triton SEE to the Admin Navigation
	 */
	public function adminUI() {
		add_menu_page( "Triton SEE Plugin Settings",  "Triton SEE", "edit_plugins", "seePluginSettings", array( $this, "configForm" ));
		add_submenu_page( "seePluginSettings", "Triton SEE Plugin Settings", "General Settings", "edit_plugins", "seePluginSettings", array( $this, "configForm" ) );
		add_submenu_page( "seePluginSettings", "Triton SEE Plugin Custom Actions", "Custom Actions", "edit_plugins", "seePluginCustomActions", array( $this, "configForm" ) );
	}

	/**
	 * Retrieves and returns an array of custom action bindings.
	 *
	 * @return array
	 */
	public function getCustomActions() {
		$customActions = get_option( TRITON_SEE_OPTION_NAME_ACTIONS );
		return $customActions;
	}

	/**
	 * Updates stored custom action bindings using the provided data.
	 *
	 * @param array   $customActions
	 * @return bool True on success, false on failure
	 */
	public function updateCustomActions( $customActions ) {
		return update_option( TRITON_SEE_OPTION_NAME_ACTIONS, $customActions );
	}

	/**
	 * Routes control to the form referenced by the 'page' query parameter.
	 */
	public function configForm() {
		$page = $_GET["page"];

		switch ($page) {
			case "seePluginCustomActions":
				require_once "see_custom_actions_form.class.php";
				$form = new See_Custom_Actions_Form( $this );
				$form->run();
				break;
			case "seePluginSettings":
			default:
				require_once "see_settings_form.class.php";
				$form = new See_Settings_Form( $this );
				$form->run();
				break;
		}
	}

	private function debug( $str ) {
		echo $this->debugging ? "<pre>" . $str . "</pre>" : "";
	}

	/***************************************************************************
	 * SLUGS
	 * These functions are called as a result of wordpress action hooks being
	 * triggered which are mapped to see actions. Also listed below are the
	 * various support functions for adding or otherwise manipulating slugs for
	 * the current user.
	 **************************************************************************/

	/**
	 * Adds a key value pair to a command queue array stored in a given users
	 * meta data which will initiate api calls from the users client on the
	 * next page load.
	 *
	 * @param int     $user_id
	 * @param string  $slug
	 * @param string  $action
	 */
	private function addSlug( $user_id = 0, $slug = '', $action = '' ) {

		// Sanitize inputs
		$user_id = intval( $user_id );
		$slug = sanitize_text_field( $slug );
		$action = sanitize_text_field( $action );

		if ( $user_id > 0 && ! empty( $slug ) ) { // If there"s no slug, there"s nothing to do
			$current = get_user_meta( $user_id, "tritonsee_api_queue", true );
			//error_log( "Found this \$current for {$user_id}: " . print_r( $current, true ) );
			$current = json_decode( $current, true );
			$current[] = array( "slug" => $slug, "action" => $action );
			//error_log( "Saving this \$current for {$user_id}: " . print_r( $current, true ) );
			update_user_meta( $user_id, "tritonsee_api_queue", json_encode( $current ) );
		}
	}

	/**
	 * Simple function that other functions use to record a specific $action
	 *
	 * @param string  $action This represents the KEY to associate to a Triton SEE Action VALUE
	 */
	private function slugAction( $action = "" ) {

		global $userdata;


		$user_id = ( gettype( $userdata ) == "array" )? $userdata["ID"] : $userdata->ID;

		$actions = get_option( TRITON_SEE_OPTION_NAME_ACTIONS );


		foreach ($actions as $action) {
			//error_log(var_export($action['see_action'], 1));
			if ( ! empty( $action['see_action'] ) && ! empty( $user_id ) ) {
				$this->addSlug( $user_id, "do", $action['see_action'] );
			}
		}

		//$this->addSlug( $user_id, "do", 'level_up' );

		// if ( ! empty( $actions[ $action ] ) && ! empty( $user_id ) ) {
		// 	$this->addSlug( $user_id, "do", $actions[ $action ] );
		// }

	}


	/**
	 * Function that saves to the 'tritonsee_api_queue' table a slug for logging a user in.
	 *
	 * @param string  $user_name Not used, but this is the WP username of the active user
	 * @param WP      User $userObj   Used to get the unique identifier property
	 */
	public function slugLogin( $user_name = "", $userObj ) {
		if ( ! empty( $userObj->ID ) && $userObj->ID > 0 ) {
			$this->addSlug( $userObj->ID, "login" );
		}

		// Is there an Action for this?
		$this->slugAction( "login" );
	}

	/**
	 * Simple function that"s called by a Wordpress hook to record a slug for a user registering
	 */
	public function slugRegister() {
		$this->slugAction( "register" );
	}

	/**
	 * Simple function that"s called by a Wordpress hook to record a slug for a user commenting
	 */
	public function slugComment() {
		$this->slugAction( "comment" );
	}

	/**
	 * Simple function that"s called by a Wordpress hook to record a slug for a user viewing a page
	 */



	// this should be split up!!!
	public function slugPageView() {
		global $post;
		global $userdata;

		if ( ! empty( $userdata["ID"] ) ) {

			$avail_actions = get_option( TRITON_SEE_OPTION_NAME_POST_PAGE_VIEWS );
			//$custom_action = get_option( TRITON_SEE_OPTION_NAME_ACTIONS );

			//$custom_actions = $this->getCustomActions();
			//$action =
			$custom_action = get_post_meta( $post->ID, "_tritonsee_". $post->post_type ."_action", true );
			//$dump = get_post_meta( $post->ID);


			//error_log(var_export($dump, 1));

			if ( ! empty( $avail_actions ) && ! empty( $custom_action ) && in_array( $custom_action, $avail_actions ) ) {
				//error_log( "slugPageView() $custom_action : " . $custom_action );
				$this->addSlug( $userdata["ID"], "do", $custom_action );
			} else {
				error_log('pageview');
				$this->slugAction( "pageview" );
			}
		}
	}

}
