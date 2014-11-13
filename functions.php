<?php 

/**
 * Returns the current wd_esp_framework version number
 * @return string
 */

function wd_esp_installed_version() {
	return get_option( WD_ESP_NAME );  
}


/**
 * Dactivation of WD_ESP_Framework
 * These routines handle deactivation of WD_BKNramework.  Deletes/Removes 
 * database Tables and Options. If the application current version is 
 * different than the previous version data will not be removed.  Instead 
 * the application will assume that an upgrade is in progress.
 */

function wd_esp_deactivate()
{
	global $wpdb, $wd_esp_options_all;
	delete_option( WD_ESP_NAME );
	delete_option( 'WD_ESP_APP_PAGE' );
	
}


/**
 * Activation of WD_ESP_framework
 * Handles activation of plugin.  Creates/Updates database Tables, and will
 * allow the plugin to create tables, and insert default data into the tables.
 * Options will be added only if they don't exist.
 */

function wd_esp_activate() {

	global $wd_esp_options_all;

	if ( wd_esp_installed_version() != WD_ESP_VERSION ) {
		add_option( WD_ESP_NAME, WD_ESP_VERSION );	
	}
}


/**
 * Checks if this plugin is an update from a previous version. This routine 
 * is used in 'wd_esp_framework.php' and is executed every time wordpress calls
 * the 'plugins_loaded' action.
 */

add_action('plugins_loaded', 'wd_esp_check_for_updates' ); 

function wd_esp_check_for_updates() {
	wd_esp_activate();
}

// wd_esp_header_icon
function wd_esp_header_icon() {
	$icon_url = plugins_url('images/wd-icon-header.png',  __FILE__ ); ?>
	<div id="icon-myplugin" class="icon32"><img src="<?php echo $icon_url; ?>"></div><?php
}


/**
 * Load scripts and styles
 */

function wd_esp_load_scripts_and_styles() { 
    // scripts    
    wp_register_script(
        'wd_bkn-bootstrap-js',
        plugins_url('js/bootstrap/bootstrap.min.js', __FILE__ ),
        false,
        '3.2.0',
        true
    );
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'wd_bkn-bootstrap-js' );
    // styles
    wp_register_style( 'wd_bkn-wp-bootstrap-css', plugins_url('css/bootstrap/wp-bootstrap.css', __FILE__ ), false, '3.2.0');
    wp_enqueue_style( 'wd_bkn-wp-bootstrap-css' );
    wp_register_style( 'wd-bkn-admin-ui-style-css', plugins_url('css/wd-bkn-admin-ui-style.css', __FILE__ ), false, '1.2.0');
    wp_enqueue_style( 'wd-bkn-admin-ui-style-css' );    
}

add_action( 'enqueue_scripts', 'wd_esp_load_scripts_and_styles' );


/**
 * Displays the main app function.
 */

function wd_esp_app() {
 
	/*** APP_PAGE ***/ 
	require_once('ebay-app.php');  
	$ebay_app->format_style();
	$app_seller=get_option( 'WD_ESP_APP_SELLER' );
	if($app_seller==null)
		die("eBay Seller or Shop name is required."); // karmaloop
	
	$ebay_app->show_gallery($app_seller); 
}


/**
 * Main function for filter
 *
 */

function wd_esp_the_content_filter($content) {	
  	$app_page_id=get_option( 'WD_ESP_APP_PAGE' );
	if( $GLOBALS['post']->ID == $app_page_id) 
		return wd_esp_app();
	return $content;
}

add_filter( 'the_content', 'wd_esp_the_content_filter' );

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

?>