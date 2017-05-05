<?php
if(!function_exists('cs_activate_widget')){
	
	function cs_activate_widget(){
		
		$sidebars_widgets = get_option('sidebars_widgets');
	
	/********************** Footer Siderbar Setting Start **********************/
	
		 /* ---- Footer Contact Us --- */
		/*----------------------------*/
			$footer_contactinfo = array();
			$footer_contactinfo[1] = array(
				'title' => '',
				'image_url' => get_template_directory_uri().'/assets/images/footer-logo.png',
				'address' => 'DieSachbearbeiter Schönhauser 10435 Berlin, Germany',
				'phone' => '+44 123 456 789',
				'fax' => '0044 123 456 789',
				'email' => 'info@lassicarchitecture.com',
				'quick_link' => 'Mon-Sat 8.00-18.00',
			);						
			$footer_contactinfo['_multiwidget'] = '1';
			update_option('widget_contactinfo',$footer_contactinfo);
			$footer_contactinfo = get_option('widget_contactinfo');
			krsort($footer_contactinfo);
			foreach($footer_contactinfo as $key1=>$val1){
				$footer_contactinfo_key = $key1;
				if(is_int($footer_contactinfo_key)){
					break;
				}
			}
				
 		
		 /* ---- Footer Contact Form --- */
		/*----------------------------*/
			$footer_contact_form = array();
			$footer_contact_form[1] = array(
					"title" =>	'Contact Form',
					"contact_email" => '',
					"contact_succ_msg" => '',
					);					
			$footer_contact_form['_multiwidget'] = '1';
			update_option('widget_cs_contact_msg',$footer_contact_form);
			$footer_contact_form = get_option('widget_cs_contact_msg');
			krsort($footer_contact_form);
			foreach($footer_contact_form as $key1=>$val1){
				$footer_contact_form_key = $key1;
				if(is_int($footer_contact_form_key)){
					break;
				}
			} 
		
		 /* ---- Footer Twitter Widget --- */
		/*----------------------------*/
			
			$cs_twitter_widget = array();
			$cs_twitter_widget[1] = array(
					"title"		=>	'Twitter',
					"username" 	=>	"envato",
					"numoftweets" => "3",
					);						
			$cs_twitter_widget['_multiwidget'] = '1';
			update_option('widget_cs_twitter_widget',$cs_twitter_widget);
			$cs_twitter_widget = get_option('widget_cs_twitter_widget');
			krsort($cs_twitter_widget);
			foreach($cs_twitter_widget as $key1=>$val1){
				$cs_twitter_widget_key = $key1;
				if(is_int($cs_twitter_widget_key)){
					break;
				}
			}

		 /* ---- Default Sidebar Facebook widget setting --- */
		/*----------------------------------*/
	
			$facebook_module = array();
			$facebook_module[1] = array(
					"title"		=> 'Facebook',
					"pageurl" 	=> "https://www.facebook.com/envato",
					"showfaces" => "on",
					"showstream" => "",
					"likebox_height" => "265",
					"fb_bg_color" => "#ffffff",
					);						
			$facebook_module['_multiwidget'] = '1';
			update_option('widget_facebook_module',$facebook_module);
			$facebook_module = get_option('widget_facebook_module');
			krsort($facebook_module);
			foreach($facebook_module as $key1=>$val1) {
				$facebook_module_key = $key1;
				if(is_int($facebook_module_key)) {
					break;
				}
			}
		
		 /* ---- Default Sidebar Twitter widget setting ----- */
		/*-----------------------------------*/
			$cs_twitter_widget2 = array();
			$cs_twitter_widget2[2] = array(
					"title"		=>	'Twitter Feeds',
					"username" 	=>	"envato",
					"numoftweets" => "3",
					);						
			$cs_twitter_widget2['_multiwidget'] = '1';
			update_option('widget_cs_twitter_widget',$cs_twitter_widget2);
			$cs_twitter_widget2 = get_option('widget_cs_twitter_widget');
			krsort($cs_twitter_widget2);
			foreach($cs_twitter_widget2 as $key1=>$val1){
				$cs_twitter_widget2_key = $key1;
				if(is_int($cs_twitter_widget2_key)){
					break;
				}
			}
		

		
		 /* ---- Blog Sidebar Recent Posts --- */
		/*----------------------------*/
			$blog_recent_post = array();
			$blog_recent_post = get_option('widget_recentposts');
			$blog_recent_post[2] = array(
					"title"		=>	'Latest News',
					"select_category" 	=> 'blogs',
					"showcount" => '4',
					"thumb" => true
					);					
			$blog_recent_post['_multiwidget'] = '1';
			update_option('widget_recentposts',$blog_recent_post);
			$blog_recent_post = get_option('widget_recentposts');
			krsort($blog_recent_post);
			foreach($blog_recent_post as $key1=>$val1){
				$blog_recent_post_key = $key1;
				if(is_int($blog_recent_post_key)){
					break;
				}
			}
		
		 /* ---- Blog Sidebar Recent Posts Projects --- */
		/*----------------------------*/
			$blog_recent_postp = array();
			$blog_recent_postp = get_option('widget_recentpostsproj');
			$blog_recent_postp[2] = array(
					"title"		=>	'Latest News',
					"select_category" 	=> 'blogs',
					"showcount" => '4',
					"thumb" => true
					);					
			$blog_recent_postp['_multiwidget'] = '1';
			update_option('widget_recentpostsproj',$blog_recent_postp);
			$blog_recent_postp = get_option('widget_recentpostsproj');
			krsort($blog_recent_postp);
			foreach($blog_recent_postp as $key1=>$val1){
				$blog_recent_postp_key = $key1;
				if(is_int($blog_recent_postp_key)){
					break;
				}
			}
		
		 /* ---- Blog Sidebar Related Posts --- */
		/*----------------------------*/
			$blog_recent_postp = array();
			$blog_recent_postp = get_option('widget_relatedposts');
			$blog_recent_postp[2] = array(
					"title"		=>	'Latest News',
					"showcount" => '4',
					"thumb" => true
					);					
			$blog_recent_postp['_multiwidget'] = '1';
			update_option('widget_relatedposts',$blog_recent_postp);
			$blog_recent_postp = get_option('widget_relatedposts');
			krsort($blog_recent_postp);
			foreach($blog_recent_postp as $key1=>$val1){
				$blog_recent_postp_key = $key1;
				if(is_int($blog_recent_postp_key)){
					break;
				}
			}
		
		 /* ---- Blog Sidebar text widget ---- */
		/*---------------------------*/
			$text = array();
			//$text = get_option('widget_text');
			$text[1] = array(
				'title' => 'TEXT WIDGET',
				'text' => 'Bhat sneered vivaciously that thus are they poroise uncriti cal gosh and be to the that thus are much and vivaciously that thus are they poroise uncritical gosh and be to thvivaci ously that thus are they Bhat sneered vivaciously that thus are they.',
			);						
			$text['_multiwidget'] = '1';
			update_option('widget_text',$text);
			$text = get_option('widget_text');
			krsort($text);
			foreach($text as $key1=>$val1){
				$text_key = $key1;
				if(is_int($text_key)){
					break;
				}
			}
			
		 /* ------  Blog Sidebar Tags ----- */
		/*--------------------------------*/
			$blog_flickr = array();
	
			$blog_flickr[1] = array(
	
			"title"			=> 'Gallery',
			"username"		=> 'envato',
			"no_of_photos"	=> '12',
			);
	
			$blog_flickr['_multiwidget'] = '1';
			update_option('widget_cs_flickr',$blog_flickr);
			$blog_flickr = get_option('widget_cs_flickr');
			krsort($blog_flickr);
			foreach($blog_flickr as $key1=>$val1){
				$blog_flickr_key = $key1;
				if(is_int($blog_flickr_key)){
					break;
				}
			}
			
		/* ------  Blog Sidebar Cats ----- */
		/*--------------------------------*/
			$categories = array();
	
			$categories[1] = array(
	
			"title"		=>	'Quick Links',
			"dropdown" => '',
			"count" => '1',
			"hierarchical" => '',
			);
	
			$categories['_multiwidget'] = '1';
			update_option('widget_categories',$categories);
			$categories = get_option('widget_categories');
			krsort($categories);
			foreach($categories as $key1=>$val1){
				$categories_key = $key1;
				if(is_int($categories_key)){
					break;
				}
			}
			
		/* ------  Blog Sidebar Search ----- */
		/*--------------------------------*/
			$search = array();
	
			$search[1] = array(
	
			"title"		=>	'',
			
			);
	
			$search['_multiwidget'] = '1';
			update_option('widget_search',$search);
			$search = get_option('widget_search');
			krsort($search);
			foreach($search as $key1=>$val1){
				$search_key = $key1;
				if(is_int($search_key)){
					break;
				}
			}
			
		 /* ---- News Sidebar Latest Posts --- */
		/*----------------------------*/
			$news_recent_post = array();
			$news_recent_post = get_option('widget_recentposts');
			$news_recent_post[4] = array(
					"title"		=>	'Latest Posts',
					"select_category" 	=> 'news',
					"showcount" => '5',
					"thumb" => true
					);					
			$news_recent_post['_multiwidget'] = '1';
			update_option('widget_recentposts',$news_recent_post);
			$news_recent_post = get_option('widget_recentposts');
			krsort($news_recent_post);
			foreach($news_recent_post as $key1=>$val1){
				$news_recent_post_key = $key1;
				if(is_int($news_recent_post_key)){
					break;
				}
			} 

		 /* ---- News Sidebar Latest Posts --- */
		/*----------------------------*/
			$news_recent_post = array();
			$news_recent_post = get_option('widget_recentpostsproj');
			$news_recent_post[4] = array(
					"title"		=>	'Latest Posts',
					"select_category" 	=> 'news',
					"showcount" => '5',
					"thumb" => true
					);					
			$news_recent_post['_multiwidget'] = '1';
			update_option('widget_recentpostsproj',$news_recent_post);
			$news_recent_post = get_option('widget_recentpostsproj');
			krsort($news_recent_post);
			foreach($news_recent_post as $key1=>$val1){
				$news_recent_post_key = $key1;
				if(is_int($news_recent_post_key)){
					break;
				}
			} 

		 /* ---- News Sidebar Latest Posts --- */
		/*----------------------------*/
			$news_recent_post = array();
			$news_recent_post = get_option('widget_relatedposts');
			$news_recent_post[4] = array(
					"title"		=>	'Latest Posts',
					"showcount" => '5',
					"thumb" => true
					);					
			$news_recent_post['_multiwidget'] = '1';
			update_option('widget_relatedposts',$news_recent_post);
			$news_recent_post = get_option('widget_relatedposts');
			krsort($news_recent_post);
			foreach($news_recent_post as $key1=>$val1){
				$news_recent_post_key = $key1;
				if(is_int($news_recent_post_key)){
					break;
				}
			} 

		 /* ---- Contact Sidebar text widget ---- */
		/*---------------------------*/
			$text_contact = array();
			$text_contact = get_option('widget_text');
			$text_contact[3] = array(
				'title' => 'NEW YORK OFFICE',
				'text' => '<p>Lassic<br />Schonhauser Allee 167c<br />12345 Crickvilla<br />Newyork</p>
[cs_list cs_list_type="icon" cs_border="no"][list_item ="pho" ="null" cs_list_icon="icon-phone4"] Telephone: +49 30 47373795 [/list_item][list_item ="prin" ="null" cs_list_icon="icon-printer3"] Fax: +49 30 47373795 [/list_item][list_item ="env" ="null" cs_list_icon="icon-envelope3"] E-mail: info@peachclub.com [/list_item] [/cs_list]<br /><br />
[cs_button button_size="btn-lg" button_title="GET DIRECTIONS ON THE MAP" button_link="#" button_border="no" border_button_color="#2a313a" button_bg_color="#2a313a" button_color="#ffffff" ="null" button_icon_position="left" button_type="rectangle" button_target="_self"]',
			);						
			$text_contact['_multiwidget'] = '1';
			update_option('widget_text',$text_contact);
			$text_contact = get_option('widget_text');
			krsort($text_contact);
			foreach($text_contact as $key1=>$val1){
				$text_contact_key = $key1;
				if(is_int($text_contact_key)){
					break;
				}
			}
			
		/* ---- Contact Sidebar text widget ---- */
		/*---------------------------*/
			$text_contact = array();
			$text_contact = get_option('widget_text');
			$text_contact[4] = array(
				'title' => 'OFFICE TIMING',
				'text' => '[cs_list cs_list_type="icon" cs_border="no"][list_item ="clock" ="null" cs_list_icon="icon-clock7"] Monday : 0900-1700hrs [/list_item][list_item ="clock" ="null" cs_list_icon="icon-clock7"] Tuesday : 0900-1700hrs [/list_item][list_item ="clock" ="null" cs_list_icon="icon-clock7"] Wednesday : 0900-1700hrs [/list_item][list_item ="clock" ="null" cs_list_icon="icon-clock7"] Thursday : 0900-1700hrs [/list_item][list_item ="clock" ="null" cs_list_icon="icon-clock7"] Friday : 0900-1700hrs [/list_item][list_item ="clock" ="null" cs_list_icon="icon-clock7"] Saturday : 0900-1700hrs [/list_item][list_item ="clock" ="null" cs_list_icon="icon-clock7"] Sunday : Closed [/list_item] [/cs_list]',
			);						
			$text_contact1['_multiwidget'] = '1';
			update_option('widget_text',$text_contact);
			$text_contact = get_option('widget_text');
			krsort($text_contact);
			foreach($text_contact as $key1=>$val1){
				$text_contact_key1 = $key1;
				if(is_int($text_contact_key1)){
					break;
				}
			}
 
		/* ------  Practics Sidebar Cats ----- */
		/*--------------------------------*/
			$categories = array();
			$categories = get_option('widget_categories');
			$categories[2] = array(
	
			"title"		=>	'Practice Aeras',
			"dropdown" => '',
			"count" => '',
			"hierarchical" => '',
			);
	
			$categories['_multiwidget'] = '1';
			update_option('widget_categories',$categories);
			$categories = get_option('widget_categories');
			krsort($categories);
			foreach($categories as $key1=>$val1){
				$categories_key1 = $key1;
				if(is_int($categories_key1)){
					break;
				}
			}
			
				
		/* ----- Footer Sidebar Cats ----- */
		/*---------------------------------*/
			$categories = array();
			$categories = get_option('widget_categories');
			$categories[3] = array(
	
			"title"		=>	'Latest News',
			"dropdown" => '',
			"count" => '',
			"hierarchical" => '',
			);
	
			$categories['_multiwidget'] = '1';
			update_option('widget_categories',$categories);
			$categories = get_option('widget_categories');
			krsort($categories);
			foreach($categories as $key1=>$val1){
				$categories_key3 = $key1;
				if(is_int($categories_key3)){
					break;
				}
			}
			
		/* ---- Practice Sidebar text widget ---- */
		/*---------------------------*/
			$footer_about_us = array();
			$footer_about_us = get_option('widget_text');
			$footer_about_us[6] = array(
				'title' => 'About Us',
				'text' => '<p>Far strung contrary tiger uselessly one we religious assenting peculiar oh far compatible one terrier ahead ape well be to emu sweeping.</p>
[cs_list cs_list_type="icon" cs_border="no"][list_item cs_list_icon="icon-check" icon="icon-check"] Strung contrary tiger uselessly [/list_item][list_item cs_list_icon="icon-check" icon="icon-check"] Religious assenting peculiar [/list_item][list_item cs_list_icon="icon-check" icon="icon-check"] Far compatible one terrier ahead [/list_item][list_item cs_list_icon="icon-check" icon="icon-check"] Well be to emu sweeping. [/list_item] [/cs_list]',
			);						
			$footer_about_us['_multiwidget'] = '1';
			update_option('widget_text',$footer_about_us);
			$footer_about_us = get_option('widget_text');
			krsort($footer_about_us);
			foreach($footer_about_us as $key1=>$val1){
				$footer_about_us_key1 = $key1;
				if(is_int($footer_about_us_key1)){
					break;
				}
			}
	
	
	/* ----  Add widgets in sidebars  --- */
	/* ---------------------------------- */
		$sidebars_widgets['default_pages'] = array("search-$search_key","categories-$categories_key","facebook_module-$facebook_module_key","cs_flickr-$blog_flickr_key");
		$sidebars_widgets['blogs_sidebar'] = array("search-$search_key","categories-$categories_key","recentposts-$blog_recent_post_key","relatedposts-$blog_recent_post_key","recentpostsproj-$blog_recent_postp_key","cs_flickr-$blog_flickr_key","cs_twitter_widget-$cs_twitter_widget2_key");
		$sidebars_widgets['contact'] = array("text-$text_contact_key","text-$text_contact_key1");
 		$sidebars_widgets['footer-widget-1'] = array("contactinfo-$footer_contactinfo_key","cs_twitter_widget-$cs_twitter_widget_key","cs_contact_msg-$footer_contact_form_key");
		
		update_option('sidebars_widgets',$sidebars_widgets); //save widget informations
	
	}
}