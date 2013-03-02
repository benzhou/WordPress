<?php 
/*
Plugin Name: Triton DigitalIvy Contest
Plugin URI: http://benzhouonline.com/wp-content/plugins/digitalivy
Description: DigitalIvy is cool
Version: 0.1
Author: Triton Digital -- Ben Zhou
Author URI: http://tritondigital.com/
License: GPL2
*/


/*  Copyright 2012  Triton Digital  (email : info@tritondigital.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( ! defined( "TRITON_DI_PLUGIN_VERSION_KEY" ) ) define( "TRITON_DI_PLUGIN_VERSION_KEY", "triton_di_plugin_version" );
if ( ! defined( "TRITON_DI_PLUGIN_VERSION_NUM" ) ) define( "TRITON_DI_PLUGIN_VERSION_NUM", "0.0.1" );
if ( ! defined( "TRITON_DI_OPTION_ORG_CODE" ) ) define( "TRITON_DI_OPTION_ORG_CODE", "triton_di_option_org_code" );
if ( ! defined( "TRITON_DI_OPTION_NAME_DEBUG" ) ) define( "TRITON_DI_OPTION_NAME_DEBUG", "triton_di_option_name_debug" );



// Get Class File
require_once "digitalivy_plugin.class.php";

// init see plugin
$diPlugin = new DigitalIvy_Plugin;
// Install DB
register_activation_hook( __FILE__, array( $diPlugin, "tritonInstall" ) );
register_deactivation_hook( __FILE__, array( $diPlugin, "tritonUninstall" ) );

// Admin UI
add_action( "admin_menu" ,    array( $diPlugin, "adminUI" ) );

// Init hook so we can register any needed scripts
add_action("init", array($diPlugin, 'init_di_plugin'));

//add_action("wp_head", array($diPlugin, "di_plugin_head"));
add_action("wp_footer", array($diPlugin, "di_plugin_footer"));
//Shortcode 
add_shortcode('digitalIvyList', array($diPlugin, 'init_di_list')); 

?>
