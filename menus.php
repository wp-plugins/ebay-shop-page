<?php


add_action('admin_menu', 'wd_esp_menu');

function wd_esp_menu() {
	$icon_url = plugins_url('images/wd-icon-menu.png', __FILE__);
	add_menu_page('', 'eBay Shop Page', 3, WD_ESP_NAME, 'wd_esp_submenu_welcome', $icon_url) ;
	add_submenu_page(WD_ESP_NAME, 'Settings', 'Settings', 3, WD_ESP_NAME, 'wd_esp_submenu_welcome');
}

function wd_esp_submenu_welcome() {
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
  	?>
  	<div class="wrap">
        <h2><?php wd_esp_header_icon(); ?>eBay Shop Page (<?php echo 'v '.WD_ESP_VERSION; ?>)</h2>
	</div>
	
	<?php
	
	// Handle POST
	if($_POST['WD_ESP_APP_SETTINGS_SEND']) {
		// clean up
		delete_option( 'WD_ESP_APP_ID' );
		delete_option( 'WD_ESP_APP_PAGE' );
		delete_option( 'WD_ESP_APP_SELLER' );
 
		add_option( 'WD_ESP_APP_ID', $_POST['WD_ESP_APP_ID'] ); 
		add_option( 'WD_ESP_APP_PAGE', $_POST['WD_ESP_APP_PAGE_ID'] );
		add_option( 'WD_ESP_APP_SELLER', $_POST['WD_ESP_APP_SELLER'] );
			
	}
	
	
 	// get options
	$app_id=get_option( 'WD_ESP_APP_ID' );
	$app_page_id=get_option( 'WD_ESP_APP_PAGE' );
	$app_seller=get_option( 'WD_ESP_APP_SELLER' );
 	?>
		
		<form name="WD_ESP_APP_SETTINGS" method="post">
		<br><br>
		<p>You will need an eBay App ID, which can be obtained by signing up for a developer account. </p><p>To obatain an App ID, go to <a href="http://go.developer.ebay.com/developers/ebay" target="_blank" >http://go.developer.ebay.com/developers/ebay</a> </p>
		
		<br><br>
		<label>Enter App ID</label>
		<br><br>
		<input type="text" name="WD_ESP_APP_ID" value="<?php echo $app_id;?>" placeholder="Enter App ID" /> 
		<br><br>
		<label>Enter Seller Name</label>
		<br><br>
		<input type="text" name="WD_ESP_APP_SELLER" value="<?php echo $app_seller;?>" placeholder="Enter Seller Name" /> 
		
		<?php
		$pages = get_pages();
		if($pages) {
		?>
		<br><br>
		<label>Select a page to display the shop</label>
		<br><br>
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
		<?php } ?>
		<input type="hidden" name="WD_ESP_APP_SETTINGS_SEND" value="1" />
		<?php submit_button( 'Save') ?>
		</form>
	<?php
 
}

function widget_wd_esp_widget($args) {
    extract($args);
?>
        <?php echo $before_widget; ?>
            <?php echo $before_title
                . 'Search Filter'
                . $after_title; 
			require_once('ebay-app.php');
			if(is_null($ebay_app))
			$ebay_app = New Ebay_app();  
			// $ebay_app->format_style();
            $ebay_app->show_search_form();
      		echo $after_widget; ?>
<?php
}
register_sidebar_widget('eBay Shop Search',
    'widget_wd_esp_widget');


?>