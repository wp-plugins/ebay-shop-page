<?php
/*
Plugin Name: eBay Shop Page
Plugin URI: http://ebay-shop.webdevstudio.net/
Description: Allows you to add a Shop page on your WordPress website to list products form a selected eBay seller or shop.
Version: 2.0
Author: Web Dev Studio
Author URI: http://webdevstudio.net/
License: GPLv2 or later
*/

/*  Copyright 2014  Web Dev Studio  (email : webdevsolutionsstudio@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/


/**** Sets up Plugin configuration and routing based on names of Plugin folder and files. ***/

// define Plugin constants
define( 'WD_ESP_VERSION', "2.0");			
define( 'WD_ESP_PURGE_DATA', '1' );		
define( 'WD_ESP_ADMIN_PATH', ABSPATH . 'wp-admin/');  
define( 'WD_ESP_FILE', basename(__FILE__) );
define( 'WD_ESP_FILE_PATH', __FILE__);
define( 'WD_ESP_NAME', basename(__FILE__, ".php") );
define( 'WD_ESP_DB_NAME', WD_ESP_NAME ."_DB" );
define( 'WD_ESP_PATH', str_replace( '\\', '/', trailingslashit(dirname(__FILE__)) ) );
define( 'WD_ESP_URL', plugins_url('', __FILE__) ); 

require_once( WD_ESP_PATH . 'functions.php' );
require_once( WD_ESP_PATH . 'menus.php' ); 

register_activation_hook(__FILE__,'WD_ESP_activate'); 
register_deactivation_hook( __FILE__, 'WD_ESP_deactivate' );  

?>