<?php
/*
Plugin Name: Triton SEE Bar
Plugin URI: http://www.tritondigital.com/publishers/audience-management/loyalty-social-engagement-gamification
Description: SEE is a robust engagement engine that integrates social interaction, rich game mechanics, incentives and analytics into any site to grow online traffic and increase engagement. The SEE Bar is the newest and quickest way to increase engagement, pageviews and user registrations by bringing leaderboards, activity feeds, and achievements to every page in less than five minutes without sacrifice precious ad space and content real-estate.
Version: 0.1
Author: Triton Digital
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

if ( ! defined( "TRITON_SEE_PLUGIN_VERSION_KEY" ) ) define( "TRITON_SEE_PLUGIN_VERSION_KEY", "triton_see_plugin_version" );
if ( ! defined( "TRITON_SEE_PLUGIN_VERSION_NUM" ) ) define( "TRITON_SEE_PLUGIN_VERSION_NUM", "1.0.0" );
if ( ! defined( "TRITON_SEE_OPTION_NAME_URL" ) ) define( "TRITON_SEE_OPTION_NAME_URL", "tritonsee_url" );
if ( ! defined( "TRITON_SEE_OPTION_NAME_ACTIONS" ) ) define( "TRITON_SEE_OPTION_NAME_ACTIONS", "tritonsee_actions" );
if ( ! defined( "TRITON_SEE_OPTION_NAME_TENANT_ID" ) ) define( "TRITON_SEE_OPTION_NAME_TENANT_ID", "tritonsee_tenant_id" );
if ( ! defined( "TRITON_SEE_OPTION_NAME_PUBLIC_KEY" ) ) define( "TRITON_SEE_OPTION_NAME_PUBLIC_KEY", "tritonsee_public_key" );
if ( ! defined( "TRITON_SEE_OPTION_NAME_PRIVATE_KEY" ) ) define( "TRITON_SEE_OPTION_NAME_PRIVATE_KEY", "tritonsee_private_key" );
if ( ! defined( "TRITON_SEE_OPTION_NAME_POST_PAGE_VIEWS" ) ) define( "TRITON_SEE_OPTION_NAME_POST_PAGE_VIEWS", "tritonsee_post_page_views" );
if ( ! defined( "TRITON_SEE_OPTION_NAME_USE_SSL" ) ) define( "TRITON_SEE_OPTION_NAME_USE_SSL", "tritonsee_use_ssl" );
if ( ! defined( "TRITON_SEE_OPTION_NAME_DEBUG" ) ) define( "TRITON_SEE_OPTION_NAME_DEBUG", "tritonsee_debug" );

// Get Class File
require_once "see_plugin.class.php";

// Get Widgets File
require_once "see-widgets.php";

// Get base form class
require_once "see_form.class.php";

// init see plugin
$seeObj = new see_plugin;
// this is currently one big class. ewe should make it many?

// Pre-Headers
add_action( "init",           array( $seeObj, "createCookiesFromGet" ) );
// not sure if this is needed? it was causing an error
//add_action("init",           array($seeObj, "callReferralAction"));

// Post-Headers
add_action( "wp_head",          array( $seeObj, "head" ) );
add_action( "shutdown",         array( $seeObj, "processUpdate" ), 99 );

// Specialized
add_action( "user_register",   array( $seeObj, "flagUserRegistered" ) );
add_action( "profile_update",  array( $seeObj, "flagUserUpdated" ) );

// Shortcodes
add_shortcode( "see",             array( $seeObj, "shortcodeSEE" ) );

// Admin UI
add_action( "admin_menu" ,    array( $seeObj, "adminUI" ) );

// Install DB
register_activation_hook( __FILE__, array( $seeObj, "tritonInstall" ) );
register_deactivation_hook( __FILE__, array( $seeObj, "tritonUninstall" ) );

// not sure if this is needed? it was causing an error
//add_action("plugins_loaded",       array($seeObj, "tritonUpdateCheck"));

// Slugs for API
// add_action( "wp_login",          array( $seeObj, "slugLogin" ), 10, 2 );
// add_action( "user_register",     array( $seeObj, "slugRegister" ) );
// add_action( "wp_insert_comment", array( $seeObj, "slugComment" ) );

if ( is_admin() == false ) {
    $seeObj->registerCustomActions();
}

// register see widgets
add_action(
    "widgets_init",
    function () {
        register_widget("SEEBar");
        register_widget("SEELeaderboard");
        register_widget("SEETopScore");
        register_widget("SEEScoreBoard");
        register_widget("SEEActivity");
        register_widget("SEENextlevel");
        register_widget("SEEProfile");
        register_widget("SEEAchievments");
        register_widget("SEECheckin");
    }
);

?>
