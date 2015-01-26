<?php

class Ebay_app {

	var $app_ID;
	var $ebay_Cats;
	
	function __construct() {
		// get options
		$app_id=get_option( 'WD_ESP_APP_ID' );
		$this->setupEbayApp($app_id);
	}
	
	function format_style() {
		echo '<style>
#sidebar {
    min-height: 500px;
    width: 30%;
}
#right-column {
    min-height: 600px;
    width: 70%;
}
.float-left {
    float: left;
}
.float-right {
    float: right;
}
.clear { clear: both; height: 10px; }
.gallery-grid {
    margin: 0 auto;
}
.gallery-grid span {
    margin-right: 3px;
}
.gallery-grid .item-cell {
    display: block;
    height: 350px;
    margin-bottom: 20px;
    margin-left: 5.656% !important;
    position: relative;
    width: 194px !important;

	box-sizing: border-box;
    display: block;
    float: left;
}
.gallery-grid .item-cell.h-med {
    height: 310px;
}
.gallery-grid .item-cell.h-small {
    height: 295px;
}
.gallery-grid .item-cell.h-auto {
    height: auto;
}
.img-cell:before {
    bottom: 15px;
    box-shadow: 0 15px 6px rgba(0, 0, 0, 0.3);
    content: "";
    height: 10%;
    left: 5px;
    position: absolute;
    transform: rotate(-4deg);
    width: 40%;
    z-index: -2;
}
.gallery-grid .img-cell {
    border: 1px solid #ccc;
    box-shadow: 0 0 10px #ccc;
    height: 190px;
    width: 190px;

    border: 1px solid #ddd;
    border-radius: 3px;
    min-height: 80px !important;
    padding: 1px;
    position: relative;
    text-align: center;
    vertical-align: middle;

}
.gallery-grid .img-cell img {
    display: inline-block;
    max-height: 100%;
    max-width: 100%;
    vertical-align: middle;
}
.gallery-grid .item-cell .desc {
    padding-top: 15px;
}
.gallery-grid .item-cell .price {
    padding-top: 10px;
}
.gallery-grid .item-cell .tags, .gallery-grid .item-cell .msku {
    clear: both;
    padding-top: 4px;
}
.gallery-grid .item-cell .title {
    font-size: 0.92em;
    line-height: 1.3em;
}
.gallery-grid .item-cell .price .bin {
    padding-top: 2px;
}
.gallery-grid .item-cell .price .curr {
    font-size: 1em;
    line-height: 1.5em;
}
.gallery-grid .item-cell .tag {
    font-size: 0.76em;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.gallery-grid .item-cell .tag-s {
    font-size: 0.69em;
}
.gallery-grid .item-cell .tleft {
    font-size: 0.76em;
    line-height: 2em;
}
.wd-esc-pagination {
    clear: both;
}
.wd-esc-pagination ul {
    list-style: none;
}
.wd-esc-pagination li {
    display: inline;
	margin: 5px;
}
.wd-esc-pagination li.current {
    padding: 5px 10px;
}
.wd-esc-pagination li a {
    padding: 5px 10px;
	text-decoration: none;
}
@media (max-width: 1240px) {
	.gallery-grid {
	    left: -74px;
	    width: 804px;
	}
	.gallery-grid .item-cell {
	    margin-left: 74px !important;
	}
}
@media (max-width: 1020px) {
	.gallery-grid {
	    left: -43px;
	    padding: 0 10px;
	    width: 623px;
	}
	.gallery-grid .item-cell {
	    height: 295px;
	    margin-left: 43px !important;
	    width: 164px !important;
	}
	.gallery-grid .item-cell.h-med {
	    height: 295px;
	}
	.gallery-grid .item-cell.h-small {
	    height: 295px;
	}
	.gallery-grid .img-cell {
	    height: 160px;
	    width: 160px;
	}
}
</style>';
	}
	
	function show_gallery($seller='', $brand='', $per_page=12) {
	
		$posted=array(); 
		// if(!empty($_REQUEST['filter'])) 
		$posted=$_REQUEST;
		if(!empty($posted['brand'])) $brand=$posted['brand'];
		if(!empty($posted['rcat'])) $cat=$posted['rcat'];
		if(!empty($posted['category'])) $cat=$posted['category']; else $cat='';
		if(!empty($posted['size'])) $sizes=$posted['size']; else $sizes='';
		if(!empty($posted['price-range-min'])) $price_min=$posted['price-range-min']; else $price_min='';
		if(!empty($posted['price-range-max'])) $price_max=$posted['price-range-max']; else $price_max='';
		if(!empty($posted['rpage'])) $page =$posted['rpage']; else $page = 1;
		$app_per_page=get_option( 'WD_ESP_APP_PER_PAGE' );
		if(!empty($app_per_page)) $per_page=$app_per_page; else $per_page = 20;
		
		// var_dump($posted);
		
		$endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1'; 
		/*** SELLERS ****/
		$headers = array(
			'X-EBAY-SOA-SERVICE-NAME: FindingService',
		    'X-EBAY-SOA-OPERATION-NAME: findItemsAdvanced',
		    'X-EBAY-SOA-SERVICE-VERSION: 1.3.0',
		    'X-EBAY-SOA-REQUEST-DATA-FORMAT: XML',
		    'X-EBAY-SOA-GLOBAL-ID: EBAY-US',
		    'X-EBAY-SOA-SECURITY-APPNAME: '.$this->app_ID,
		    'Content-Type: text/xml;charset=utf-8'
		); 
		$xmlrequest = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<findItemsAdvanced xmlns=\"http://www.ebay.com/marketplace/search/v1/services\">
		  <storeName>$seller</storeName>
		  <itemFilter>
		    <name>Brand</name>
		    <value>$brand</value>
		  </itemFilter>
		  <itemFilter>
		    <name>Seller</name>
		    <value>$seller</value>
		  </itemFilter>";
			  
			// Add'l filters
			if($sizes) $xmlrequest .= "<itemFilter>
		    	<name>Size</name>
		    	<value>$sizes</value>
		  	</itemFilter>";
			if($price_min) $xmlrequest .= "<itemFilter>
		    	<name>MinPrice</name>
		    	<value>$price_min</value>
		  	</itemFilter>";
			if($price_max) $xmlrequest .= "<itemFilter>
		    	<name>MaxPrice</name>
		    	<value>$price_max</value>
		  	</itemFilter>";
			if($cat) $xmlrequest .= "<categoryId>$cat</categoryId>";
		
			// per page
			$xmlrequest .= "<paginationInput>
		    	<pageNumber>$page</pageNumber>
		    	<entriesPerPage>$per_page</entriesPerPage>
		  	</paginationInput>";
			//  ["paginationOutput"]=> object(SimpleXMLElement)#227 (4) { ["pageNumber"]=> string(1) "1" ["entriesPerPage"]=> string(2) "12" ["totalPages"]=> string(3) "156" ["totalEntries"]=> string(4) "1864" } ["itemSearchURL"]=> string(111) "http://www.ebay.com/sch/93427/i.html?_sasl=karmaloop&_saslop=1&_fss=1&LH_SpecificSeller=1&_ddo=1&_ipg=12&_pgn=1" }
 		
		  $xmlrequest .= "
		</findItemsAdvanced>";
		$session  = curl_init($endpoint);                 
		curl_setopt($session, CURLOPT_POST, true);           
		curl_setopt($session, CURLOPT_HTTPHEADER, $headers);   
		curl_setopt($session, CURLOPT_POSTFIELDS, $xmlrequest); 
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$responsexml = curl_exec($session);                    
		curl_close($session); 
		$xml = simplexml_load_string($responsexml);
		// echo 'count '.var_dump($xml);
		//  ["paginationOutput"]=> object(SimpleXMLElement)#227 (4) { ["pageNumber"]=> string(1) "1" ["entriesPerPage"]=> string(2) "12" ["totalPages"]=> string(3) "156" ["totalEntries"]=> string(4) "1864" } ["itemSearchURL"]=> string(111) "http://www.ebay.com/sch/93427/i.html?_sasl=karmaloop&_saslop=1&_fss=1&LH_SpecificSeller=1&_ddo=1&_ipg=12&_pgn=1" }
		// $xml->paginationOutput
		// $app_per_page=get_option( 'WD_ESP_APP_PER_PAGE' );
		$gallery = '';
		
		// pagination
		$pagination='';
		if($xml->paginationOutput->totalPages > 1) {
			$all = $xml->paginationOutput->totalPages;
			$cur = $xml->paginationOutput->pageNumber;
			$prev = ($cur>1?$cur-1:'');
			$prev2 = ($cur>2?$cur-2:'');
			$next = (($all-$cur)>1?$cur+1:'');
			$next2 = (($all-$cur)>2?$cur+2:'');
			$q=$_SERVER['QUERY_STRING']; // get page query 
			$app_page_id=get_option( 'WD_ESP_APP_PAGE' );
		
			$page_query = site_url('?page_id='.$app_page_id).'brand='.$brand.'&size='.$sizes.'&price_min='.$price_min.'&price_max='.$price_max.'&rcat='.$cat;
			
			$pagination = '<div class="wd-esc-pagination">';
				$pagination .= '<ul>'
					.($prev?'<li><a href="'.$page_query.'&rpage='.$prev.'"> &laquo; </a></li>':' &laquo; ')
					.($prev2?'<li><a href="'.$page_query.'&rpage='.$prev2.'">'.$prev2.'</a></li>':'')
					.($prev?'<li><a href="'.$page_query.'&rpage='.$prev.'">'.$prev.'</a></li>':'')
					.'<li class="current">'.$cur.'</li>'
					.($next?'<li><a href="'.$page_query.'&rpage='.$next.'">'.$next.'</a></li>':'')
					.($next2?'<li><a href="'.$page_query.'&rpage='.$next2.'">'.$next2.'</a></li>':'')
					.($next?'<li><a href="'.$page_query.'&rpage='.$next.'"> &raquo; </a></li>':' &raquo; ')
				.'</ul>';
			$pagination .= '</div>';
		}
		
		if($xml->searchResult->item) {
			$gallery .= '<div class="gallery-grid clr" id="result-set">';
			$gallery .= $pagination;
			$gallery .= '<h2>'.$seller.($brand?' &raquo; Brand: '.$brand:'').'</h2>';
			foreach($xml->searchResult->item as $item) {
			    $gallery .= '<div data-item-id="'.$item->itemId.'" class="span3 item-cell">
			        <div class="img-cell"><a class="vi-url" href="'.$item->viewItemURL.'" target="_blank"><i></i><img title="'.$item->title.'" alt="'.$item->title.'" src="'.$item->galleryURL.'"><span class="lens-item"></span></a></div>
			        <div class="desc"><a title="'.$item->title.'" class="vi-url" href="'.$item->viewItemURL.'" target="_blank"><span class="title">
							'.$item->title.'
			            </span></a>
			        </div>
			        <div class="price">
			            <span class="curr">'. number_format(floatval($item->sellingStatus->currentPrice), 2 ,'.',','). '</span>';
						if($item->buyItNowAvailable) { 
							$gallery .= '<span class="tag-s visible-desktop">Buy It Now</span>';
						} 
			            $gallery .= '<div class="fr tleft">';
						$datetime1 = new DateTime();
						$datetime1->add(new DateInterval($item->sellingStatus->timeLeft));
						$datetime2 = new DateTime("now");
						$interval = $datetime2->diff($datetime1);
						$gallery .=  ($interval?'Time Left: <br>'.$interval->format('%a days, %h hours, %i min, %s secs.'):''); 
			            $gallery .= '</div>
			        </div> 
			    </div>';
			} 
			$gallery .= $pagination;
			$gallery .= '</div>';
			echo $gallery;	
		} else {
			// var_dump($xml);
			$gallery .= '<div class="gallery-grid clr" id="result-set">';
			$gallery .= '<h3>No Results for '.$seller.($brand?' &raquo; Brand: '.$brand:'').'</h3>';
			$gallery .= '</div>';
			echo $gallery;
		}
		
	}
	
	function show_search_form() {
	
		// Get categories
		$categories = $this->ebay_Cats->Category; 

		// Build sizes
		$sizes = array(
			'xs'=>'X-Small',
			's'=>'Small',
			'm'=>'Medium',
			'l'=>'Large',
			'xl'=>'X-Large',
			'xxl'=>'XX-Large'
		); 
		
		// Build price ranges
		$price_ranges = array(
			'10'=>'10',
			'20'=>'20',
			'50'=>'50',
			'100'=>'100',
			'200'=>'200',
			'500'=>'500',
			'1000'=>'1000',
		); 
		
		// Get POST data
		$posted=array(); if(!empty($_POST['filter'])) $posted=$_POST;
		
		$search_form = '';
		
		$app_page_id=get_option( 'WD_ESP_APP_PAGE' );
		
		// Output
		$search_form .= '<form method="POST" action="?page_id='.$app_page_id.'">
			<br>
			<label>Brands</label>
			<br><br>
			<input type="text" name="brand" placeholder="Brands" '.(isset($posted['brand'])?' value="'.$posted['brand'].'" ':'').'/>
			<br><br>
			<select name="category">
				<option value="">Select Category</option>';
				if($categories) foreach($categories as $key => $cat) 
					$search_form .= '<option value="'.$cat->CategoryID.'" '.(isset($posted['category']) && $posted['category']==$cat->CategoryID?' selected="selected" ':'').'>'.$cat->CategoryName.'</option>';
			$search_form .= '</select>
			<br><br>
			<select name="size">
				<option value="">Select Size</option>';
				if($sizes) foreach($sizes as $key => $size) 
					$search_form .= '<option value="'.$key.'" '.(isset($posted['size']) && $posted['size']==$key?' selected="selected" ':'').'>'.$size.'</option>';
			$search_form .= '</select>
			<br><br>
			<select name="price-range-min">
				<option value="">Min Price</option>';
				if($price_ranges) foreach($price_ranges as $key => $price) 
					$search_form .= '<option value="'.$key.'" '.(isset($posted['price-range-min']) && $posted['price-range-min']==$key?' selected="selected" ':'').'>$'.$price.'</option>';
			$search_form .= '</select>
			<select name="price-range-max">
				<option value="">Max Price</option>';
				if($price_ranges) foreach($price_ranges as $key => $price)
					$search_form .= '<option value="'.$key.'" '.(isset($posted['price-range-max']) && $posted['price-range-max']==$key?' selected="selected" ':'').'>$'.$price.'</option>';
			$search_form .= '</select>
			<br><br>
			<input type="hidden" name="filter" value="1" />
			<input type="submit" name="send" value="Search" />
		</form>';
		
		echo $search_form;
	}
	
	function getCats($parent_cat=-1) {
		if(!$parent_cat) $parent_cat = -1; // doesn't work as a prop
		
		 // -1: top, 11450: Clothing, Shoes & Accessories, 1059: Men's Clothing, 
		$endpoint = 'http://open.api.ebay.com/Shopping?callname=GetCategoryInfo&appid='.$this->app_ID.'&version=675&siteid=0&CategoryID='.$parent_cat.'&IncludeSelector=ChildCategories';
    	$responsexml = '';
		if( ini_get('allow_url_fopen') ) {
			$responsexml = @file_get_contents($endpoint);
			if($responsexml) {
				$xml = simplexml_load_string($responsexml);
				// remove top from list
				unset($xml->CategoryArray->Category[0]); 
				return $xml->CategoryArray;
			}
			return;
		} else if(function_exists('curl_version')) {
			$curl = curl_init(); 
			if (is_resource($curl) === true) {
				$endpoint = 'http://open.api.ebay.com/shopping?'; 
				$headers = array(
					'X-EBAY-API-CALL-NAME: GetCategoryInfo',
				    'X-EBAY-API-VERSION: 521',
					'X-EBAY-API-REQUEST-ENCODING: XML',
				    'X-EBAY-API-SITE-ID: 0',
				    'X-EBAY-API-APP-ID: '.$this->app_ID,
		    		'Content-Type: text/xml;charset=utf-8'
				); 
				$xmlrequest = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<GetCategoryInfoRequest xmlns=\"urn:ebay:apis:eBLBaseComponents\">
				  	<CategoryID>".$parent_cat."</CategoryID>
					<IncludeSelector>ChildCategories</IncludeSelector>
				</GetCategoryInfoRequest>";
					   
				$session  = curl_init($endpoint);                 
				curl_setopt($session, CURLOPT_POST, true);           
				curl_setopt($session, CURLOPT_HTTPHEADER, $headers);   
				curl_setopt($session, CURLOPT_POSTFIELDS, $xmlrequest); 
				curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
				$responsexml = curl_exec($session);                    
				curl_close($session);  
		
 				// var_dump($responsexml);
				$xml = simplexml_load_string($responsexml);
				// remove top from list
				unset($xml->CategoryArray->Category[0]); 
				return $xml->CategoryArray;
			}
		} else {
			return;
		}
	}
	
	function setCats($parent='') {
		$this->ebay_Cats = $this->getCats($parent);
	}
	
	function setAppID($app_ID) { 
		$this->app_ID = $app_ID;
	}
	
	function setupEbayApp($app_ID=null) {
		if($app_ID==null)
			die("App ID is required.");
		$this->setAppID($app_ID);	
		$parent_cat=get_option('WD_ESP_APP_CAT');
		$this->setCats($parent_cat);
	}

}

$ebay_app = New Ebay_app();