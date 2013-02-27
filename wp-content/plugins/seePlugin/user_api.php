<?php
/**  Copyright 2012  Triton Digital  (email : info@tritondigital.com)

    This script is an API for SEE to get information about a user.
    
    It accepts three request variables:
    
    1. user_id
        So we can look up the user information based off of this 
        unique identifier
    
    2. api_key 
        This key is used for securing the fact that SEE can access this
        install.  It will check see_config for a compatible key
    
    If a user is found, information is returned in JSON format.  If a user
    is not found, an error will be returned via JSON
    
    Things to consider: Other ways this script could be called could
    check whether or not the user simply exists, getting individual 
    fields for them, or all of the fields.
    
    @todo return proper data structure
    @todo import VID into meta!
*/

// Wordpress Info
$root_wp_path = str_replace('/wp-content/plugins/triton-see/user_api.php', '/', $_SERVER['SCRIPT_FILENAME']);
require_once( $root_wp_path . '/wp-load.php');

// Get Class File
require_once('see_plugin.class.php');
$seeObj = new see_plugin;

// Are we getting the right request variables?
if ($seeObj->checkAPIRequest()) {

  // Sanitize
	$user_id = intval($_GET['user_id']);
	$action  = empty($_GET['action']) ? '' : $_GET['action'];
	
  switch ($action) {
    
    case 'update_vid':
      $data = $seeObj->updateVid($user_id, $_GET['vid']);
      header('HTTP/1.1 200 OK');
      break;

    case 'fetch':
    default: 
      $data = $seeObj->getUserInfo($user_id);
      echo json_encode($data);
      break;

  }
  
	// Sanitize
  
  
    
} else {

  // Fail
#  header('HTTP/1.1 403 Forbidden');
  echo 'Error handling API Request';
  
}

?>