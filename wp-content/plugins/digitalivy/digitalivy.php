<?php 
/*
Plugin Name: Triton DigitalIvy Contest
Plugin URI: http://www.tritondigital.com/publishers/audience-management/loyalty-social-engagement-gamification
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
// Get Class File
require_once "digitalivy_plugin.class.php";

// init see plugin
$diPlugin = new DigitalIvy_Plugin;
// Admin UI
add_action( "admin_menu" ,    array( $diPlugin, "adminUI" ) );

// Init hook so we can register any needed scripts
//add_action("init", array($diPlugin, 'init_di_plugin'));

//add_action("wp_head", array($diPlugin, "di_plugin_head"));

//Shortcode 
add_shortcode('digitalIvyList', array($diPlugin, 'init_di_list')); 

?>
