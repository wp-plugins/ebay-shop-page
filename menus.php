<?php


add_action('admin_menu', 'wd_esp_menu');

function wd_esp_menu() {
	$icon_url = plugins_url('images/wd-icon-menu.png', __FILE__);
	add_menu_page('', 'eBay Shop Page', 'edit_posts', WD_ESP_NAME, 'wd_esp_submenu_welcome', $icon_url) ;
	add_submenu_page(WD_ESP_NAME, 'Settings', 'Settings', 'edit_posts', WD_ESP_NAME, 'wd_esp_submenu_welcome');
}

function wd_esp_submenu_welcome() {
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
  	?>
  	<div class="wrap">
        <h2><?php wd_esp_header_icon(); ?>eBay Shop Page (<?php echo 'v '.WD_ESP_VERSION; ?>)</h2>
	</div>

<style>
	.wd-esp-settings label { width: 250px; display: inline-block; }
	.wd-esp-settings input, .wd-esp-settings select { width: 450px; display: inline-block; height: 40px; }
	.wd-esp-settings input.button { width: auto; }
	.wd-esp-content-half-page {
	    width: 49%;
	    float: left;
	    clear: right;
	}

</style>
	
	<?php
	
	// Handle POST
	if(!empty($_POST['WD_ESP_APP_SETTINGS_SEND'])) {
		// clean up
		delete_option( 'WD_ESP_APP_ID' );
		delete_option( 'WD_ESP_APP_PAGE' );
		delete_option( 'WD_ESP_APP_SELLER' );
		delete_option( 'WD_ESP_APP_CAT' );
		delete_option( 'WD_ESP_APP_PER_PAGE' );
 
		add_option( 'WD_ESP_APP_ID', $_POST['WD_ESP_APP_ID'] ); 
		add_option( 'WD_ESP_APP_PAGE', $_POST['WD_ESP_APP_PAGE_ID'] );
		add_option( 'WD_ESP_APP_SELLER', $_POST['WD_ESP_APP_SELLER'] );
		add_option( 'WD_ESP_APP_CAT', $_POST['WD_ESP_APP_CAT'] );
		add_option( 'WD_ESP_APP_PER_PAGE', $_POST['WD_ESP_APP_PER_PAGE'] );

			
	}
	
	
 	// get options
	$app_id=get_option( 'WD_ESP_APP_ID' );
	$app_page_id=get_option( 'WD_ESP_APP_PAGE' );
	$app_seller=get_option( 'WD_ESP_APP_SELLER' );
	$app_cat_parent=get_option( 'WD_ESP_APP_CAT' );
	$app_per_page=get_option( 'WD_ESP_APP_PER_PAGE' );

 	?>
		
		<form name="WD_ESP_APP_SETTINGS" method="post" class="wd-esp-settings wd-esp-content-half-page">
			<p>You will need an eBay App ID, which can be obtained by signing up for a developer account. </p><p>To obatain an App ID, go to <a href="http://go.developer.ebay.com/developers/ebay" target="_blank" >http://go.developer.ebay.com/developers/ebay</a> </p>
		
			<label>Enter App ID</label>
			<input type="text" name="WD_ESP_APP_ID" value="<?php echo $app_id;?>" placeholder="Enter App ID" /> 
			<br /><br />
			
			<label>Enter Seller Name</label>

			<input type="text" name="WD_ESP_APP_SELLER" value="<?php echo $app_seller;?>" placeholder="Enter Seller Name" /> 
			<br /><br />
		<?php
		$pages = get_pages();
		if($pages) {
		?>

			<label>Select a page to display the shop</label>

			<select name="WD_ESP_APP_PAGE_ID">
				<option value="">Select</option>
			<?php 
				foreach($pages as $page) {
					?>
					<option value="<?php echo $page->ID;?>" <?php echo ($app_page_id==$page->ID?' selected="selected"':'');?>><?php echo $page->post_title;?></option>
					<?php
				}
			?>
			</select>
			<br /><br />
			
<?php
if($app_id) {
	require_once('ebay-app.php');
	if(empty($ebay_app))
	$ebay_app = New Ebay_app($app_id);
	$cats=$ebay_app->getCats();
	// var_dump($cats);
	// echo ' $app_cat_parent '.$app_cat_parent;

?>
			<label>Select top eBay Category</label>
			<select name="WD_ESP_APP_CAT">
				<option value="">Select</option>
			<?php 
				foreach($cats->Category as $k => $cat) {
					?>
					<option value="<?php echo $cat->CategoryID;?>" <?php echo (!empty($app_cat_parent) && $app_cat_parent==$cat->CategoryID?' selected="selected"':'');?>><?php echo $cat->CategoryName;?></option>
					<?php
				}
			?>
			</select>
			<br /><br />
<?php } ?>

			<label>Number of items per page </label>

			<select name="WD_ESP_APP_PER_PAGE">
				<option value="">Select</option>
			 	<option value="10" <?php echo ($app_per_page==10?' selected="selected"':'');?> >10</option>
			 	<option value="25" <?php echo ($app_per_page==25?' selected="selected"':'');?> >25</option>
			 	<option value="50" <?php echo ($app_per_page==50?' selected="selected"':'');?> >50</option>
			 	<option value="75" <?php echo ($app_per_page==75?' selected="selected"':'');?> >75</option>
			 	<option value="100" <?php echo ($app_per_page==100?' selected="selected"':'');?> >100</option>
			</select>
			<br /><br />

			
			<?php } ?>
			<input type="hidden" name="WD_ESP_APP_SETTINGS_SEND" value="1" />
			<?php submit_button( 'Save') ?>
		</form>
		
		
		<div class="wd-esp-content-half-page">
			<h3>Please Make a Donation</h3>
<p>If you enjoy using this plugin, just make a contribution to future development.</p>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input name="cmd" type="hidden" value="_s-xclick" />
<input name="hosted_button_id" type="hidden" value="DNNDFLNJD8U7C" />
<input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" type="image" />
<img src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" alt="" width="1" height="1" border="0" /></form>
		</div>
	<?php
 
}

function widget_wd_esp_widget($args) {
    extract($args);
	// load options
	$widget_title = get_option('wd_esp_widget_title');
	$widget_description = get_option('wd_esp_widget_description');
?>
        <?php echo $before_widget; ?>
            <?php echo $before_title
                . ($widget_title ? $widget_title : 'Search Filter' )
                . $after_title; 
			echo ($widget_description ? '<p>'.$widget_description.'</p>' : '');
			require_once('ebay-app.php');
			if(empty($ebay_app))
			// if(is_null($ebay_app))
			$ebay_app = New Ebay_app();  
			// $ebay_app->format_style();
            $ebay_app->show_search_form();
      		echo $after_widget; ?>
<?php
}

$params=$opts = array(
	'description' => '',
);
wp_register_sidebar_widget('widget_wd_esp','eBay Shop Search', 'widget_wd_esp_widget', $opts, $params);


/**
 * Sidebar Widget Control
 *
 */

wp_register_widget_control(
	'widget_wd_esp', 
	'widget_wd_esp',	 
	'wd_esp_widget_control' 
);

function wd_esp_widget_control($args=array(), $params=array()) {

	if (isset($_POST['widget-settings'])) {
		update_option('wd_esp_widget_title', $_POST['widget-title']);
		update_option('wd_esp_widget_description', $_POST['widget-description']);
	}

	// load options
	$widget_title = get_option('wd_esp_widget_title');
	$widget_description = get_option('wd_esp_widget_description');
	?>
	<p>
		<label>Widget title:</label>
		<input type="text" class="widefat" name="widget-title" value="<?php echo stripslashes($widget_title); ?>" />
	</p>
	<p>
		<label>Describe how to use: <em>(optional)</em></label>
		<textarea class="widefat" rows="5" name="widget-description"><?php echo stripslashes($widget_description); ?></textarea>
	</p>

	<input type="hidden" name="widget-settings" value="1" />
	<?php
}


?>