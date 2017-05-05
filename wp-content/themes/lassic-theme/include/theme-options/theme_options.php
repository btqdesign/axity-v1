<?php
// Theme option function
if ( ! function_exists( 'cs_options_page' ) ) {
	function cs_options_page(){
		global $cs_theme_options,$options;
		$cs_theme_options=get_option('cs_theme_options');
	?>
		<div class="theme-wrap fullwidth">
			<div class="inner">
				<div class="outerwrapp-layer">
					<div class="loading_div">
						<i class="icon-circle-o-notch icon-spin"></i>
						<br>
						<?php esc_html_e('Saving changes...','lassic');?>
					</div>
					<div class="form-msg">
						<i class="icon-check-circle-o"></i>
						<div class="innermsg"></div>
					</div>
				</div>
				<div class="row">   
					<form id="frm" method="post">
						<?php 
							$theme_options_fields = new theme_options_fields();
							$return = $theme_options_fields->cs_fields($options);
						?>
						<div class="col1">
							<nav class="admin-navigtion">
								<div class="logo">
									<a href="#" class="logo1"><img src="<?php echo get_template_directory_uri()?>/include/assets/images/logo-themeoption.png" alt=""/></a>
									<a href="#" class="nav-button"><i class="icon-align-justify"></i></a>
								</div>
								<ul>
									<?php  echo force_balance_tags($return[1],true); ?>
								</ul>
							</nav>
						</div>
						<div class="col2">
							<?php  echo force_balance_tags($return[0],true); /* Settings */ ?>
						</div>
						<div class="clear"></div>
						<div class="footer">
							<input type="button" id="submit_btn" name="submit_btn" class="bottom_btn_save" value="Save All Settings" onclick="javascript:theme_option_save('<?php echo admin_url('admin-ajax.php')?>', '<?php echo get_template_directory_uri();?>');" />
							<input type="hidden" name="action" value="theme_option_save"  />
							<input class="bottom_btn_reset" name="reset" type="button" value="Reset All Options"  
							onclick="javascript:cs_rest_all_options('<?php echo esc_js(admin_url('admin-ajax.php'))?>', '<?php echo esc_js(get_template_directory_uri())?>');" />
						</div>
				  </form>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<!--wrap-->
		<script type="text/javascript">
			// Sub Menus Show/hide
			jQuery(document).ready(function($) {
				jQuery(".sub-menu").parent("li").addClass("parentIcon");
				$("a.nav-button").click(function() {
					$(".admin-navigtion").toggleClass("navigation-small");
				});
				
				$("a.nav-button").click(function() {
					$(".inner").toggleClass("shortnav");
				});
				
				$(".admin-navigtion > ul > li > a").click(function() {
					var a = $(this).next('ul')
					$(".admin-navigtion > ul > li > a").not($(this)).removeClass("changeicon")
					$(".admin-navigtion > ul > li ul").not(a) .slideUp();
					$(this).next('.sub-menu').slideToggle();
					$(this).toggleClass('changeicon');
				});
			});
			
			function show_hide(id){
				var link = id.replace('#', '');
				jQuery('.horizontal_tab').fadeOut(0);
				jQuery('#'+link).fadeIn(400);
			}
			
			function toggleDiv(id) { 
				jQuery('.col2').children().hide();
				jQuery(id).show();
				location.hash = id+"-show";
				var link = id.replace('#', '');
				jQuery('.categoryitems li').removeClass('active');
				jQuery(".menuheader.expandable") .removeClass('openheader');
				jQuery(".categoryitems").hide();
				jQuery("."+link).addClass('active');
				jQuery("."+link) .parent("ul").show().prev().addClass("openheader");
			}
			jQuery(document).ready(function() {
				jQuery(".categoryitems").hide();
				jQuery(".categoryitems:first").show();
				jQuery(".menuheader:first").addClass("openheader");
				jQuery(".menuheader").live('click', function(event) {
					if (jQuery(this).hasClass('openheader')){
						jQuery(".menuheader").removeClass("openheader");
						jQuery(this).next().slideUp(200);
						return false;
					}
					jQuery(".menuheader").removeClass("openheader");
					jQuery(this).addClass("openheader");
					jQuery(".categoryitems").slideUp(200);
					jQuery(this).next().slideDown(200); 
					return false;
				});
				
				var hash = window.location.hash.substring(1);
				var id = hash.split("-show")[0];
				if (id){
					jQuery('.col2').children().hide();
					jQuery("#"+id).show();
					jQuery('.categoryitems li').removeClass('active');
					jQuery(".menuheader.expandable") .removeClass('openheader');
					jQuery(".categoryitems").hide();
					jQuery("."+id).addClass('active');
					jQuery("."+id) .parent("ul").slideDown(300).prev().addClass("openheader");
				} 
			});
			jQuery(function($) {
				$( "#cs_launch_date" ).datepicker({
					defaultDate: "+1w",
					dateFormat: "dd/mm/yy",
					changeMonth: true,
					numberOfMonths: 1,
					onSelect: function( selectedDate ) {
						$( "#cs_launch_date" ).datepicker( "option", "minDate", selectedDate );
					}
				});
			});
		</script>
		<link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri())?>/include/assets/css/jquery_ui_datepicker.css">
		<link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri())?>/include/assets/css/jquery_ui_datepicker_theme.css">
	<?php
	}
}

// Background Count function
if ( ! function_exists( 'cs_bgcount' ) ) {
	 function cs_bgcount($name,$count) {
		for($i=0; $i<=$count; $i++){
			$pattern['option'.$i] = $name.$i;
		}
		return $pattern;
	 }
}
add_action('init','cs_theme_option');
if ( ! function_exists( 'cs_theme_option' ) ) {
	function cs_theme_option(){
		global $options,$header_colors,$cs_theme_options;
		$cs_theme_options=get_option('cs_theme_options');
		$on_off_option =  array("show" => "on","hide"=>"off"); 
		$navigation_style = array("left" => "left","center"=>"center","right"=>"right");
		$google_fonts =array('google_font_family_name'=>array('','',''),'google_font_family_url'=>array('','',''));
		$social_network =array('social_net_icon_path'=>array('','','','','',''),'social_net_awesome'=>array('icon-facebook7','icon-twitter6','icon-googleplus7','icon-pinterest4','icon-linkedin4','icon-behance2'),'social_net_url'=>array('https://www.facebook.com/','https://www.twitter.com/','https://plus.google.com/','https://www.pintrest.com/','https://www.linkedin.com/','https://www.behance.com'),'social_net_tooltip'=>array('Facebook','Twitter','Google Plus','Pintrest','Linkedin','Behance'),'social_font_awesome_color'=>array('#fff','#fff','#fff','#fff','#fff','#fff'));
		

		$sidebar =array('sidebar' => array('default_pages'=>'Default Pages','blogs_sidebar'=>'Blogs Sidebar','pages_sidebar'=>'Pages Sidebar','contact'=>'Contact'));
		$menus_locations = array_flip(get_nav_menu_locations());
		$breadcrumb_option = array("option1" => "option1","option2"=>"option2","option3"=>"option3");
		$deafult_sub_header = array('breadcrumbs_sub_header'=>'Breadcrumbs Sub Header','slider'=>'Revolution Slider','no_header'=>'No sub Header');
		$padding_sub_header = array('Default'=>'default','Custom'=>'custom');
		
		$member_fields =array('member_fields'=>array('Designation','Hobbies'),'member_field_values'=>array('Employee','Reading Books'));
		//Menus List
		$menu_option = get_registered_nav_menus();
		foreach($menu_option as $key=>$menu){
			$menu_location = $key;
			$menu_locations = get_nav_menu_locations();
			$menu_object = (isset($menu_locations[$menu_location]) ? wp_get_nav_menu_object($menu_locations[$menu_location]) : null);
			$menu_name[] = (isset($menu_object->name) ? $menu_object->name : '');
		}
		//Mailchimp List
		$mail_chimp_list[]='';
		if(isset($cs_theme_options['cs_mailchimp_key'])){
			$mailchimp_option = $cs_theme_options['cs_mailchimp_key'];
			if($mailchimp_option <> ''){
				$mc_list = cs_mailchimp_list($mailchimp_option);
				if($mc_list <> ''){
					if(isset($mc_list['data'])){
					foreach($mc_list['data'] as $list){
						$mail_chimp_list[$list['id']]=$list['name'];
					}
					}
				}
		 	}
		}	
		
		//google fonts array
		$g_fonts = cs_googlefont_list(); 

		$g_fonts_atts = cs_get_google_font_attribute();
		
		global $cs_theme_options;
		if (isset($cs_theme_options) and $cs_theme_options <> '') {
			if(isset($cs_theme_options['sidebar']) and count($cs_theme_options['sidebar'])>0){
				$cs_sidebar =array('sidebar'=>$cs_theme_options['sidebar']);
			}elseif(!isset($cs_theme_options['sidebar'])){
				$cs_sidebar = array('sidebar'=>array());
			}
		}else{
			$cs_sidebar=$sidebar;
		}
	 	// Set the Options Array
		$options = array();
		$header_colors= cs_header_setting();
		/* general setting options */
		$options[] = array(	
					"name" => "General",
					"fontawesome" => 'icon-gear',
					"type" => "heading",
					"options" => array(
						'tab-global-setting'=>'global',
						'tab-header-options'=>'Header',
						'tab-sub-header-options'=>'Sub Header',
						'tab-footer-options'=>'Footer',
						'tab-social-setting'=>'social icons',
						'tab-social-network'=>'social sharing',
						'member-fields'=>'Member Fields',
						'tab-custom-code'=>'custom code'
					) 
				);
		$options[] = array( 
					"name" => "color",
					"fontawesome" => 'icon-magic',
					"hint_text" => "",
					"type" => "heading",
					
					"options" => array(
						'tab-general-color'=>'general',
						'tab-header-color'=>'Header',
						'tab-footer-color'=>'Footer',
						'tab-heading-color'=>'headings',
					) 
				);
	$options[] = array( 
					"name" => "typography / fonts",
					"fontawesome" => 'icon-font',
					"desc" => "",
					"hint_text" => "",
					"type" => "heading",
					"options" => array(
						'tab-custom-font'=>'Custom Font',
						'tab-font-family'=>'font family',
						'tab-font-size'=>'font size',
					) 
				);					
	$options[] = array(	
					"name" => "sidebar",
					"fontawesome" => 'icon-columns',
					"id" => "tab-sidebar",
					"std" => "",
					"type" => "main-heading",
					"options" => ''
				);
	$options[] = array(	
					"name" => "SEO",
					"fontawesome" => 'icon-globe6',
					"id" => "tab-seo",
					"std" => "",
					"type" => "main-heading",
					"options" => ""
				);	
	$options[] = array( 
					"name" => "global",
					"id" => "tab-global-setting",
					"type" => "sub-heading"
				);
	$options[] = array( 
					"name" => "Layout",
					"desc" => "",
					"hint_text" => "Layout type",
					"id" =>   "cs_layout",
					"std" =>  "full_width",
					"options" => array(
						"boxed" => "boxed",
						"full_width"=>"full width"
					),
					"type" => "layout",
				);		
				
	$options[] = array( 
					"name" => "",
					"id" =>   "cs_horizontal_tab",
					"class" =>  "horizontal_tab",
					"type" => "horizontal_tab",
					"std" => "",
					"options" => array('Background'=>'background_tab','Pattern'=>'pattern_tab','Custom Image'=>'custom_image_tab')
				);

	$options[] = array( 
					"name" => "Background image",
					"desc" => "",
					"hint_text" => "Choose from Predefined Background images.",
					"id" =>   "cs_bg_image",
					"class" =>  "cs_background_",
					"path" => "background",
					"tab"=>"background_tab",
					"std" =>  "bg1",
					"type" => "layout_body",
					"display"=>"block",
					"options" => cs_bgcount('bg','10')
				);
				
	$options[] = array( "name" => "Background pattern",
						"desc" => "",
						"hint_text" => "Choose from Predefined Pattern images.",
						"id" =>   "cs_bg_image",
						"class" =>  "cs_background_",
						"path" => "patterns",
						"tab"=>"pattern_tab",
						"std" =>  "bg1",
						"type" => "layout_body",
						"display"=>"none",
						"options" => cs_bgcount('pattern','27') 					
					);
	$options[] = array( 
					"name" => "Custom image",
					"desc" => "",
					"hint_text" => "This option can be used only with Boxed Layout.",
					"id" =>   "cs_custom_bgimage",
					"std" =>  "",
					"tab"=>"custom_image_tab",
					"display"=>"none",
					"type" => "upload logo"
				);
	$options[] = array( "name" => "Background image position",
						"desc" => "",
						"hint_text" => "Choose image position for body background",
						"id" =>   "cs_bgimage_position",
						"std" =>  "Center Repeat",
						"type" => "select",
						"options" =>array(
							"option1" => "no-repeat center top",
							"option2"=>"repeat center top",
							"option3"=>"no-repeat center",
							"option4"=>"Repeat Center",
							"option5"=>"no-repeat left top",
							"option6"=>"repeat left top",
							"option7"=>"no-repeat fixed center",
							"option8"=>"no-repeat fixed center / cover"
						)
					);	
	$options[] = array( "name" => "Custom favicon",
						"desc" => "",
						"hint_text" => "Custom favicon for your site.",
						"id" =>   "cs_custom_favicon",
						"std" =>  get_template_directory_uri()."/assets/images/favicon.png",
						"type" => "upload logo"
					);

	$options[] = array( "name" => "Smooth Scroll",
						"desc" => "",
						"hint_text" => "Lightweight Script for Page Scrolling animation",
						"id" =>   "cs_smooth_scroll",
						"std" => "off",
						"type" => "checkbox",
						"options" => $on_off_option
					);
	
	$options[] = array( "name" => "RTL",
						"desc" => "",
						"hint_text" => "Turn RTL ON/OFF here for Right to Left languages like Arabic etc.",
						"id" =>   "cs_style_rtl",
						"std" => "off",
						"type" => "checkbox",
						"options" => $on_off_option
					);
					
	$options[] = array( "name" => "Responsive",
						"desc" => "",
						"hint_text" => "Set responsive design layout for mobile devices ON/OFF here",
						"id" =>   "cs_responsive",
						"std" => "off",
						"type" => "checkbox",
						"options" => $on_off_option
					);

	// end global setting tab					
	// Header top strip option end
	// Header options start
	$options[] = array( "name" => "header",
						"id" => "tab-header-options",
						"type" => "sub-heading"
					);
	$options[] = array( "name" => "Attention for Header Position!",
						"id" => "header_postion_attention",
						"std"=>" <strong>Relative Position:</strong> The element is positioned relative to its normal position. The header is positioned above the content. <br> <strong>Absolute Position:</strong> The element is positioned relative to its first positioned. The header is positioned on the content.",
						"type" => "announcement"
					);
					
	$options[] = array( "name" => "Logo",
						"desc" => "",
						"hint_text" => "Upload your custom logo in .png .jpg .gif formats only.",
						"id" =>   "cs_custom_logo",
						"std" => get_template_directory_uri()."/assets/images/logo.png",
						"type" => "upload logo"
					);
	$options[] = array( "name" => "Logo Height",
						"desc" => "",
						"hint_text" => "Set exact logo height otherwise logo will not display normally.",
						"id" => "cs_logo_height",
						"min" => '0',
						"max" => '150',
						"std" => "42",
						"type" => "range"
					);				
	$options[] = array( "name" => "logo width",
						"desc" => "",
						"hint_text" => "Set exact logo width otherwise logo will not display normally.",
						"id" => "cs_logo_width",
						"min" => '0',
						"max" => '250',
						"std" => "198",
						"type" => "range"
					);				
	
	$options[] = array( "name" => "Logo margin top and bottom",
						"desc" => "",
						"hint_text" => "Logo spacing/margin from top and bottom.",
						"id" => "cs_logo_margintb",
						"min" => '0',
						"max" => '200',
						"std" => "6",
						"type" => "range"
					);	
	$options[] = array( "name" => "Logo margin left and right",
						"desc" => "",
						"hint_text" => "Logo spacing/margin from left and right.",
						"id" => "cs_logo_marginlr",
						"min" => '0',
						"max" => '200',
						"std" => "0",
						"type" => "range"
					);										

 	/* header element settings*/
 	
	$options[] = array( "name" => "Header Elements",
						"id" => "tab-header-options",
						"std" => "Header Elements",
						"type" => "section",
						"options" => ""
					);	
	$options[] = array( "name" => "Main Search",
						"desc" => "",
						"hint_text" => "Set header search On/Off. Allow user to search site content.",
						"id" =>   "cs_search",
						"std" => "on",
						"type" => "checkbox",
						"options" => $on_off_option
					);

	$options[] = array( "name" => "WPML",
						"desc" => "",
						"hint_text" => "Set WordPress Multi Language switcher ON/OFF in header",
						"id" =>   "cs_wpml_switch",
						"std" => "off",
						"type" => "wpml",
						"options" => $on_off_option
					);
						
	$options[] = array( "name" => "Sticky Header On/Off",
						"desc" => "",
						"id" =>   "cs_sitcky_header_switch",
						"hint_text" => "If you enable this option , header will be fixed on top of your browser window.",
						"std" => "off",
						"type" => "checkbox",
						"options" => $on_off_option
					);
						
	$options[] = array( "name" => "Header Position Settings",
						"id" => "tab-header-options",
						"std" => "Header Position Settings",
						"type" => "section",
						"options" => ""
					);
	$options[] = array( "name" => "Select Header Position",
					"desc" => "Make header position fixed as Absolute or move it",
					"hint_text" => "Select header position as Absolute OR Relative",
					"id" =>   "cs_header_position",
					"std" => "relative",
					"type" => "select",
					"options" => array('absolute'=>'absolute','relative'=>'relative')
				);
	$options[] = array( "name" => "Header Background",
					"desc" => "",
					"hint_text" => "Header settings made here will be implemented on default pages.",
					"id" =>   "cs_headerbg_options",
					"std" => "Default Header Background",
					"type" => "default header background",
					"options" => array('none'=>'None','cs_rev_slider'=>'Revolution Slider','cs_bg_image_color'=>'Bg Image / bg Color')
			);				
 	$options[] = array( "name" => "Revolution Slider",
						"desc" => "",
						"hint_text" => "<p>Please select Revolution Slider if already included in package. Otherwise buy Sliders from <a href='http://codecanyon.net/' target='_blank'>Codecanyon</a>. But its optional</p>",
						"id" =>   "cs_headerbg_slider",
						"std" => "",
						"type" => "headerbg slider",
						"options" => ''
					);
	$options[] = array( "name" => "Background Image",
						"desc" => "",
						"hint_text" => "Upload your custom background image in .png .jpg .gif formats only.",
						"id" =>   "cs_headerbg_image",
						"std" =>  "",
						"type" => "upload"
					);
	$options[] = array( "name" => "Background Color",
						"desc" => "",
						"hint_text" => "set header background color.",
						"id" =>   "cs_headerbg_color",
						"std" => "",
						"type" => "color"
					);
	$options[] = array( "name" => "Header Top Strip",
						"id" => "tab-header-options",
						"std" => "Header Top Strip",
						"type" => "section",
						"options" => ""
					);	
					
	$options[] = array( "name" => "Header Strip",
						"desc" => "",
						"hint_text" => "Enable/Disable header top strip.",
						"id" =>   "cs_header_top_strip",
						"std" => "on",
						"type" => "checkbox",
						"options" => $on_off_option);				
	
	$options[] = array( "name" => "Social Icon",
						"desc" => "",
						"hint_text" => "Enable/Disable social icon. Add icons from General > social icon",
						"id" =>   "cs_socail_icon_switch",
						"std" =>  "on",
						"type" => "checkbox",
						"options" => $on_off_option);				

	$options[] = array( "name" => "WPML Switch",
						"desc" => "",
						"hint_text" => "Wpml enable/disable",
						"id" =>   "cs_wpml_switch",
						"std" =>  "on",
						"type" => "checkbox",
						"options" => $on_off_option);
						
	$options[] = array( "name" => "Short Text",
						"desc" => "",
						"hint_text" => "Write phone no, email or address for Header top strip",
						"id" =>   "cs_header_strip_tagline_text",
						"std" => '<p>
									<i class="icon-phone8"></i>
									Call us: +00 44 123 456 78910
								  </p>
								  <p>
									<i class="icon-paperplane3"></i>
									<a href="#">Call us: info@websitename.com</a>
								  </p>',
						"type" => "textarea");
	$options[] = array( "name" => "Header addsense",
						"desc" => "",
						"hint_text" => "Embed Image/Google addsense Code",
						"id" =>   "cs_header_banner_addsense",
						"std" => '',
						"type" => "textarea");
	
	/* sub header element settings*/
	$options[] = array( "name" => "sub header",
						"id" => "tab-sub-header-options",
						"type" => "sub-heading"
					);
	$options[] = array( "name" => "Announcement!",
						"id" =>   "sub_header_announcement",
						"std"=>   "Change this and that and try again. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum.",
						"type" => "announcement"
					);
					
	$options[] = array( "name" => "Default",
						"desc" => "",
						"hint_text" => "Sub Header settings made here will be implemented on all pages.",
						"id" =>   "cs_default_header",
						"std" =>  "Breadcrumbs Sub Header",
						"type" => "default header",
						"options" => $deafult_sub_header
					);
	$options[] = array( "name" => "Content Padding",
						"desc" => "",
						"hint_text" => "Choose default or custom padding for sub header content.",
						"id" =>   "subheader_padding_switch",
						"std" =>  "Default",
						"type" => "default padding",
						"options" => $padding_sub_header
					);
					
	$options[] = array( "name" => "Header Border Color",
						"desc" => "",
						"hint_text" => "",
						"id" =>   "cs_header_border_color",
						"std" =>  "",
						"type" => "color"
					);
					
	$options[] = array( "name" => "Revolution Slider",
						"desc" => "",
						"hint_text" => "<p>Please select Revolution Slider if already included in package. Otherwise buy Sliders from <a href='http://codecanyon.net/' target='_blank'>Codecanyon</a>. But its optional</p>",
						"id" =>   "cs_custom_slider",
						"std" => "",
						"type" => "slider code",
						"options" => ''
					);
	$options[] = array( "name" => "Padding Top",
						"desc" => "",
						"hint_text" => "Set custom padding for sub header content top area.",
						"id" => "cs_sh_paddingtop",
						"min" => '0',
						"max" => '200',
						"std" => "45",
						"type" => "range"
					);
	$options[] = array( "name" => "Padding Bottom",
						"desc" => "",
						"hint_text" => "Set custom padding for sub header content bottom area.",
						"id" => "cs_sh_paddingbottom",
						"min" => '0',
						"max" => '200',
						"std" => "45",
						"type" => "range"
					);					
	$options[] = array( "name" => "Content Text Align",
						"desc" => "",
						"hint_text" => "select the text Alignment for sub header content.",
						"id" =>   "cs_title_align",
						"std" => "left",
						"type" => "select",
						"options" => $navigation_style
					);
	$options[] = array( "name" => "Page Title",
						"desc" => "",
						"hint_text" => "Set page title ON/OFF in sub header",
						"id" => "cs_title_switch",
						"std" => "on",
						"type" => "checkbox"
					);
	
					
	$options[] = array( "name" => "Breadcrumbs",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_breadcrumbs_switch",
						"std" => "on",
						"type" => "checkbox"
					);
	
	$options[] = array( "name" => "Background Color",
						"desc" => "",
						"hint_text" => "",
						"id" =>   "cs_sub_header_bg_color",
						"std" =>  "#4c545a",
						"type" => "color"
					);	
	$options[] = array( "name" => "Text Color",
						"desc" => "",
						"hint_text" => "",
						"id" =>   "cs_sub_header_text_color",
						"std" =>  "#fff",
						"type" => "color"
					);	
	$options[] = array( "name" => "Border Color",
						"desc" => "",
						"hint_text" => "",
						"id" =>   "cs_sub_header_border_color",
						"std" =>  "#dddddd",
						"type" => "color"
					);			
	$options[] = array( "name" => "Background",
						"desc" => "",
						"hint_text" => "Background Image",
						"id" =>   "cs_background_img",
						"std" =>  "",
						"type" => "upload logo"
					);			

	$options[] = array( "name" => "Parallax",
						"desc" => "",
						"hint_text" => "",
						"id" =>   "cs_parallax_bg_switch",
						"std" =>  "on",
						"type" => "checkbox"
					);				
	
	// start footer options	
				
	$options[] = array( "name" => "footer options",
						"id" =>   "tab-footer-options",
						"type" => "sub-heading"
						);						
	$options[] = array( "name" => "Footer section",
						"desc" => "",
						"hint_text" => "enable/disable footer area",
						"id" => "cs_footer_switch",
						"std" => "on",
						"type" => "checkbox"
					);			
	$options[] = array( "name" => "Footer Widgets",
						"desc" => "",
						"hint_text" => "enable/disable footer widget area",
						"id" =>   "cs_footer_widget",
						"std" =>  "on",
						"type" => "checkbox"
					);					
	
		
	$options[] = array( "name" => "Social Icons",
						"desc" => "",
						"hint_text" => "enable/disable Social Icons",
						"id" =>   "cs_sub_footer_social_icons",
						"std" =>  "on",
						"type" => "checkbox");
									
	$options[] = array( "name" => "Footer Background Image",
						"desc" => "",
						"hint_text" => "Set custom Footer Background Image",
						"id" =>   "cs_footer_background_image",
						"std" => get_template_directory_uri().'/assets/images/bg-footer.png',
						"type" => "upload logo");					
	$options[] = array( "name" => "copyright text",
						"desc" => "",
						"hint_text" => "write your own copyright text",
						"id" =>   "cs_copy_right",
						"std" =>  "&copy; GooseClub - Children Kindergarten WordPress Theme by Chimp Studio",
						"type" => "textarea"
				 );
	
	// End footer tab setting
	/* general colors*/				
	$options[] = array( "name" => "general colors",
						"id" =>   "tab-general-color",
						"type" => "sub-heading"
						);	
	$options[] = array( "name" => "Theme Color",
						"desc" => "",
						"hint_text" => "Choose theme skin color",
						"id" =>   "cs_theme_color",
						"std" =>  "#f37735",
						"type" => "color"
					);
	$options[] = array( "name" => "Background Color",
						"desc" => "",
						"hint_text" => "Choose Body Background Color",
						"id" =>   "cs_bg_color",
						"std" =>  "#eff2f5",
						"type" => "color"
					);
					
	$options[] = array( "name" => "Body Text Color",
						"desc" => "",
						"hint_text" => "Choose text color",
						"id" =>   "cs_text_color",
						"std" =>  "#999",
						"type" => "color"
					);	
					
	// start top strip tab options
	$options[] = array( "name" => "header colors",
						"id" =>   "tab-header-color",
						"type" => "sub-heading"
						);	
	$options[] = array( "name" => "top strip colors",
						"id" =>   "tab-top-strip-color",
						"std" =>  "Top Strip",
						"type" => "section",
						"options" => ""
						);
	$options[] = array( "name" => "Background Color",
						"desc" => "",
						"hint_text" => "Change Top Strip background color",
						"id" =>   "cs_topstrip_bgcolor",
						"std" =>  "#4c545a",
						"type" => "color"
					);
					
	$options[] = array( "name" => "Text Color",
						"desc" => "",
						"hint_text" => "Change Top Strip text color",
						"id" =>   "cs_topstrip_text_color",
						"std" =>  "#fff",
						"type" => "color"
					);
					
	$options[] = array( "name" => "Link Color",
						"desc" => "",
						"hint_text" => "Change Top Strip link color",
						"id" =>   "cs_topstrip_link_color",
						"std" =>  "#fff",
						"type" => "color"
					);
					
						
	// end top stirp tab options
	// start header color tab options
	$options[] = array( "name" => "Header Colors",
						"id" =>   "tab-header-color",
						"std" =>  "Header Colors",
						"type" => "section",
						"options" => ""
						);
	$options[] = array( "name" => "Background Color",
						"desc" => "",
						"hint_text" => "Change Header background color",
						"id" =>   "cs_header_bgcolor",
						"std" =>  "",
						"type" => "color"
					);											
	$options[] = array( "name" => "Navigation Background Color",
						"desc" => "",
						"hint_text" => "Change Header Navigation Background color",
						"id" =>   "cs_nav_bgcolor",
						"std" =>  "#ffffff",
						"type" => "color"
					);
					
	$options[] = array( "name" => "Menu Link color",
						"desc" => "",
						"hint_text" => "Change Header Menu Link color",
						"id" =>   "cs_menu_color",
						"std" =>  "#4c545a",
						"type" => "color"
					);
					
	$options[] = array( "name" => "Menu Active Link color",
						"desc" => "",
						"hint_text" => "Change Header Menu Active Link color",
						"id" =>   "cs_menu_active_color",
						"std" =>  "#fd4e33",
						"type" => "color"
					);

	$options[] = array( "name" => "Submenu Background",
						"desc" => "",
						"hint_text" => "Change Submenu Background color",
						"id" =>   "cs_submenu_bgcolor",
						"std" =>  "#fff",
						"type" => "color",
					);
			
	$options[] = array( "name" => "Submenu Link Color ",
						"desc" => "",
						"hint_text" => "Change Submenu Link color",
						"id" => "cs_submenu_color",
						"std" => "#4c545a",
						"type" => "color"
					);
					
	$options[] = array( "name" => "Submenu Hover Link Color",
						"desc" => "",
						"hint_text" => "Change Submenu Hover Link color",
						"id" => "cs_submenu_hover_color",
						"std" => "#fff",
						"type" => "color"
					);
	
	
	
	/* footer colors*/				
	$options[] = array( "name" => "footer colors",
						"id" => "tab-footer-color",
						"type" => "sub-heading"
						);								
	$options[] = array( "name" => "Footer Background Color",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_footerbg_color",
						"std" => "#4c545a",
						"type" => "color"
					);
	
	$options[] = array( "name" => "Footer Title Color",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_title_color",
						"std" => "#fff",
						"type" => "color"
					);
					
	$options[] = array( "name" => "Footer Text Color",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_footer_text_color",
						"std" => "#fff",
						"type" => "color"
					);
					
	$options[] = array( "name" => "Footer Link Color",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_link_color",
						"std" => "#fff",
						"type" => "color"
					);
	
	
	$options[] = array( "name" => "Copyright Text",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_copyright_text_color",
						"std" => "#fff",
						"type" => "color"
					);
	
	/* heading colors*/				
	$options[] = array( "name" => "heading colors",
						"id" => "tab-heading-color",
						"type" => "sub-heading"
						);								
	$options[] = array( "name" => "heading h1",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_heading_h1_color",
						"std" => "#262626",
						"type" => "color"
					);
	
	$options[] = array( "name" => "heading h2",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_heading_h2_color",
						"std" => "#262626",
						"type" => "color"
					);
	
	$options[] = array( "name" => "heading h3",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_heading_h3_color",
						"std" => "#262626",
						"type" => "color"
					);
	
	$options[] = array( "name" => "heading h4",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_heading_h4_color",
						"std" => "#262626",
						"type" => "color"
					);
	
	$options[] = array( "name" => "heading h5",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_heading_h5_color",
						"std" => "#262626",
						"type" => "color"
					);
	
	$options[] = array( "name" => "heading h6",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_heading_h6_color",
						"std" => "#262626",
						"type" => "color"
					);
																																																				
	// end header color tab options	
	
	/* start custom font family */
	$options[] = array( "name" => "Custom Fonts",
						"id" => "tab-custom-font",
						"type" => "sub-heading"
						);
						
	$options[] = array( "name" => "Custom Font .woff",
						"desc" => "",
						"hint_text" => "Custom font for your site upload .woff format file.",
						"id" =>   "cs_custom_font_woff",
						"std" =>  "",
						"type" => "upload font"
					);
					
	$options[] = array( "name" => "Custom Font .ttf",
						"desc" => "",
						"hint_text" => "Custom font for your site upload .ttf format file.",
						"id" =>   "cs_custom_font_ttf",
						"std" =>  "",
						"type" => "upload font"
					);
					
	$options[] = array( "name" => "Custom Font .svg",
						"desc" => "",
						"hint_text" => "Custom font for your site upload .svg format file.",
						"id" =>   "cs_custom_font_svg",
						"std" =>  "",
						"type" => "upload font"
					);
					
	$options[] = array( "name" => "Custom Font .eot",
						"desc" => "",
						"hint_text" => "Custom font for your site upload .eot format file.",
						"id" =>   "cs_custom_font_eot",
						"std" =>  "",
						"type" => "upload font"
					);	
					
	/* start font family */
	$options[] = array( "name" => "font family",
						"id" => "tab-font-family",
						"type" => "sub-heading"
						);
	$options[] = array( "name" => "Content Font",
						"desc" => "",
						"hint_text" => "Set fonts for Body text",
						"id" =>   "cs_content_font",
						"std" => "Lato",
						"type" => "gfont_select",
						"options" => $g_fonts
					);
	$options[] = array( "name" => "Content Font Attribute",
						"desc" => "",
						"hint_text" => "Set Font Attribute",
						"id" =>   "cs_content_font_att",
						"std" => "Regular",
						"type" => "gfont_att_select",
						"options" => $g_fonts_atts
					);
	$options[] = array( "name" => "Main Menu Font",
						"desc" => "",
						"hint_text" => "Set font for main Menu. It will be applied to sub menu as well",
						"id" =>   "cs_mainmenu_font",
						"std" => "Lato",
						"type" => "gfont_select",
						"options" => $g_fonts
					);
	$options[] = array( "name" => "Main Menu Font Attribute",
						"desc" => "",
						"hint_text" => "Set Font Attribute",
						"id" =>   "cs_mainmenu_font_att",
						"std" => "Regular",
						"type" => "gfont_att_select",
						"options" => $g_fonts_atts
					);
	$options[] = array( "name" => "Headings Font",
						"desc" => "",
						"hint_text" => "Select font for Headings. It will apply on all posts and pages headings",
						"id" =>   "cs_heading_font",
						"std" => "Lato",
						"type" => "gfont_select",
						"options" => $g_fonts
					);
	$options[] = array( "name" => "Headings Font Attribute",
						"desc" => "",
						"hint_text" => "Set Font Attribute",
						"id" =>   "cs_heading_font_att",
						"std" => "Regular",
						"type" => "gfont_att_select",
						"options" => $g_fonts_atts
					);					
	$options[] = array( "name" => "Widget Headings Font",
						"desc" => "",
						"hint_text" => "Set font for Widget Headings",
						"id" =>   "cs_widget_heading_font",
						"std" => "Lato",
						"type" => "gfont_select",
						"options" => $g_fonts
					);
	$options[] = array( "name" => "Widget Headings Font Attribute",
						"desc" => "",
						"hint_text" => "Set Font Attribute",
						"id" =>   "cs_widget_heading_font_att",
						"std" => "Regular",
						"type" => "gfont_att_select",
						"options" => $g_fonts_atts
					);								
	 /* start font size */
	$options[] = array( "name" => "Font size",
						"id" => "tab-font-size",
						"type" => "sub-heading"
						);
	 
	$options[] = array( "name" => "Content",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_content_size",
						"min" => '6',
						"max" => '50',
						"std" => "14",
						"type" => "range"
					);
	$options[] = array( "name" => "Main Menu",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_mainmenu_size",
						"min" => '6',
						"max" => '50',
						"std" => "12",
						"type" => "range"
					);
					
	$options[] = array( "name" => "Section Title",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_section_title_size",
						"min" => '6',
						"max" => '50',
						"std" => "24",
						"type" => "range"
					);
					
	$options[] = array( "name" => "Heading 1",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_heading_1_size",
						"min" => '6',
						"max" => '60',
						"std" => "60",
						"type" => "range"
					);
	$options[] = array( "name" => "Heading 2",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_heading_2_size",
						"min" => '6',
						"max" => '50',
						"std" => "20",
						"type" => "range"
					);
	$options[] = array( "name" => "Heading 3",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_heading_3_size",
						"min" => '6',
						"max" => '50',
						"std" => "18",
						"type" => "range"
					);	
	$options[] = array( "name" => "Heading 4",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_heading_4_size",
						"min" => '6',
						"max" => '50',
						"std" => "16",
						"type" => "range"
					);
	$options[] = array( "name" => "Heading 5",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_heading_5_size",
						"min" => '6',
						"max" => '50',
						"std" => "14",
						"type" => "range"
					);
	$options[] = array( "name" => "Heading 6",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_heading_6_size",
						"min" => '6',
						"max" => '50',
						"std" => "12",
						"type" => "range"
					);
					
	$options[] = array( "name" => "Widget Heading",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_widget_heading_size",
						"min" => '6',
						"max" => '50',
						"std" => "15",
						"type" => "range"
					);		
																							
	/* social icons setting*/					
	$options[] = array( "name" => "social icons",
						"id" => "tab-social-setting",
						"type" => "sub-heading"
						);			
	$options[] = array( "name" => "Social Network",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_social_network",
						"std" => "",
						"type" => "networks",
						"options" => $social_network
					); 
	/* social icons end*/	
	/* social Network setting*/					
					
	$options[] = array( "name" => "social Sharing",
						"id" => "tab-social-network",
						"type" => "sub-heading"
						);
	$options[] = array( "name" => "Facebook",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_facebook_share",
						"std" => "on",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Twitter",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_twitter_share",
						"std" => "on",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Google Plus",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_google_plus_share",
						"std" => "on",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Pinterest",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_pintrest_share",
						"std" => "on",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Tumblr",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_tumblr_share",
						"std" => "on",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Dribbble",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_dribbble_share",
						"std" => "on",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Instagram",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_instagram_share",
						"std" => "on",
						"type" => "checkbox");
						
	$options[] = array( "name" => "StumbleUpon",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_stumbleupon_share",
						"std" => "on",
						"type" => "checkbox");
						
	$options[] = array( "name" => "youtube",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_youtube_share",
						"std" => "on",
						"type" => "checkbox");
	
	$options[] = array( "name" => "share more",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_share_share",
						"std" => "on",
						"type" => "checkbox");
	
	/* social network end*/
	
	
	
	/* custom code setting*/	
	$options[] = array( "name" => "custom code",
						"id" => "tab-custom-code",
						"type" => "sub-heading"
					);
	$options[] = array( "name" => "Custom Css",
						"desc" => "",
						"hint_text" => "write you custom css without style tag",
						"id" => "cs_custom_css",
						"std" => "",
						"type" => "textarea"
					);
						
	$options[] = array( "name" => "Custom JavaScript",
						"desc" => "",
						"hint_text" => "write you custom js without script tag",
						"id" => "cs_custom_js",
						"std" => "",
						"type" => "textarea"
					);
	
	//== Member Fields
	$options[] = array( "name" => "Member Fields",
						"id" => "member-fields",
						"type" => "sub-heading"
					);
	$options[] = array( "name" => "Member Fields",
						"desc" => "",
						"hint_text" => "",
						"id" => "cs_member_fields",
						"std" => "",
						"type" => "member_fields",
						"options" => $member_fields
					);
									
	/* sidebar tab */
	$options[] = array( "name" => "sidebar",
						"id" => "tab-sidebar",
						"type" => "sub-heading"
					);
	$options[] = array( "name" => "Sidebar",
						"desc" => "",
						"hint_text" => "Select a sidebar from the list already given. (Nine pre-made sidebars are given)",
						"id" => "cs_sidebar",
						"std" => $sidebar,
						"type" => "sidebar",
						"options" => $sidebar
					);
	
	$options[] = array( "name" => "post layout",
						"id" => "cs_non_metapost_layout",
						"std" => "single post layout",
						"type" => "section",
						"options" => ""
						);				
	$options[] = array( "name" => "Single Post Layout",
						"desc" => "",
						"hint_text" => "Use this option to set default layout. It will be applied to all posts",
						"id" =>   "cs_single_post_layout",
						"std" => "sidebar_right",
						"type" => "layout",
						"options" => array(
							"no_sidebar" => "full width",
							"sidebar_left"=>"sidebar left",
							"sidebar_right"=>"sidebar right"
							)
						);
					
	$options[] = array( "name" => "Single Layout Sidebar",
						"desc" => "",
						"hint_text" => "Select Single Post Layout of your choice for sidebar layout. You cannot select it for full width layout",
						"id" =>   "cs_single_layout_sidebar",
						"std" => "Blogs Sidebar",
						"type" => "select_sidebar",
						"options" => $cs_sidebar
					);
					
	$options[] = array( "name" => "default pages",
						"id" => "default_pages",
						"std" => "default pages",
						"type" => "section",
						"options" => ""
						);
	$options[] = array( "name" => "Default Pages Layout",
						"desc" => "",
						"hint_text" => "Set Sidebar for all pages like Search, Author Archive, Category Archive etc",
						"id" =>   "cs_default_page_layout",
						"std" => "sidebar_right",
						"type" => "layout",
						"options" => array(
							"no_sidebar" => "full width",
							"sidebar_left"=>"sidebar left",
							"sidebar_right"=>"sidebar right"
							)
						);					
	$options[] = array( "name" => "Sidebar",
						"desc" => "",
						"hint_text" => "Select pre-made sidebars for default pages on sidebar layout. Full width layout cannot have sidebars",
						"id" =>   "cs_default_layout_sidebar",
						"std" => "Blogs Sidebar",
						"type" => "select_sidebar",
						"options" => $cs_sidebar
					);	
	$options[] = array( "name" => "Excerpt",
						"desc" => "",
						"hint_text" => "Set excerpt length/limit from here. It controls text limit for post's content",
						"id" => "cs_excerpt_length",
						"std" => "255",
						"type" => "text"
					);		
	
	/* seo */
	$options[] = array( "name" => "SEO",
						"id" => "tab-seo",
						"type" => "sub-heading"
						);
		$options[] = array( "name" => "<b>Attention for External SEO Plugins!</b>",
						"id" => "header_postion_attention",
						"std"=>" <strong> If you are using any external SEO plugin, Turn OFF these options. </strong>",
						"type" => "announcement"
					);

	$options[] = array( "name" => "Built-in SEO fields",
						"desc" => "",
						"hint_text" => "Turn SEO options ON/OFF",
						"id" => "cs_builtin_seo_fields",
						"std" => "on",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Meta Description",
						"desc" => "",
						"hint_text" => "HTML attributes that explain the contents of web pages commonly used on search engine result pages (SERPs) for pages snippets",
						"id" => "cs_meta_description",
						"std" => "",
						"type" => "text"
					);
					
	$options[] = array( "name" => "Meta Keywords",
						"desc" => "",
						"hint_text" => "Attributes of meta tags, a list of comma-separated words included in the HTML of a Web page that describe the topic of that page",
						"id" => "cs_meta_keywords",
						"std" => "",
						"type" => "text"
					);
					
	$options[] = array( "name" => "Google Analytics",
						"desc" => "",
						"hint_text" => "Google Analytics is a service offered by Google that generates detailed statistics about a website's traffic, traffic sources, measures conversions and sales. Paste Google Analytics code here",
						"id" => "cs_google_analytics",
						"std" => "",
						"type" => "textarea"
					);
					
	/* maintenance mode*/				
	$options[] = array( "name" => "Maintenance Mode",
						"fontawesome" => 'icon-tasks',
						"id" => "tab-maintenace-mode",
						"std" => "",
						"type" => "main-heading",
						"options" => ""
						);	
	$options[] = array( "name" => "Maintenance Mode",
						"id" => "tab-maintenace-mode",
						"type" => "sub-heading"
						);
	$options[] = array( "name" => "Maintenace Page",
						"desc" => "",
						"hint_text" => "Users will see Maintenance page & logged in Admin will see normal site.",
						"id" => "cs_maintenance_page_switch",
						"std" => "off",
						"type" => "checkbox");
						
	$options[] = array( "name" => "Show Logo",
						"desc" => "",
						"hint_text" => "Show/Hide logo on Maintenance. Logo can be uploaded from General > Header in CS Theme options.",
						"id" => "cs_maintenance_logo_switch",
						"std" => "on",
						"type" => "checkbox");
	
	$options[] = array( "name" => "Background Image",
						"desc" => "",
						"hint_text" => "Upload your custom Background Image in .png .jpg .gif formats only.",
						"id" =>   "cs_uc_bg_image",
						"std" =>  "",
						"type" => "upload logo"
					);
										
	$options[] = array( "name" => "Maintenance Text",
						"desc" => "",
						"hint_text" => "Text for Maintenance page. Insert some basic HTML or use shortcodes here.",
						"id" => "cs_maintenance_text",
						"std" => '<div class="cons-text-wrapp">
									<h2>Sorry We are down for maintenance</h2>
									<p>Our website is under construction, we are working very hard to give you<br />the best experience with this one.</p>
								  </div>',
						"type" => "textarea"
					);
					
	$options[] = array( "name" => "Launch Date",
						"desc" => "",
						"hint_text" => "Estimated date for completion of site on Maintenance page.",
						"id" => "cs_launch_date",
						"std" => gmdate("dd/mm/yy"),
						"type" => "text"
					);
											
	/* api options tab*/
	$options[] = array( "name" => "api settings",
						"fontawesome" => 'icon-chain',
						"id" => "tab-api-options",
						"std" => "",
						"type" => "main-heading",
						"options" => ""
						);
	//Start Twitter Api	
	$options[] = array( "name" => "all api settings",
						"id" => "tab-api-options",
						"type" => "sub-heading"
						);
	$options[] = array( "name" => "Twitter",
						"id" => "Twitter",
						"std" => "Twitter",
						"type" => "section",
						"options" => ""
						);								
	$options[] = array( "name" => "Attention for API Settings!",
						"id" => "header_postion_attention",
						"std"=>"API Settings allows admin of the site to show their activity on site semi-automatically. Set your social account API once, it will be update your social activity automatically on your site.",
						"type" => "announcement"
					);

	$options[] = array( "name" => "Consumer Key",
						"desc" => "",
						"hint_text" => "",
						"id" =>   "cs_consumer_key",
						"std" => "",
						"type" => "text");
						
	$options[] = array( "name" => "Consumer Secret",
						"desc" => "",
						"hint_text" => "Insert consumer key. To get your account key, <a href='https://dev.twitter.com/' target='_blank'>Click Here </a>",
						"id" =>   "cs_consumer_secret",
						"std" => "",
						"type" => "text");
						
	$options[] = array( "name" => "Access Token",
						"desc" => "",
						"hint_text" => "Insert Twitter Access Token for permissions. When you create your Twitter App, you get this Token",
						"id" =>   "cs_access_token",
						"std" => "",
						"type" => "text");
						
	$options[] = array( "name" => "Access Token Secret",
						"desc" => "",
						"hint_text" => "Insert Twitter Access Token Secret here. When you create your Twitter App, you get this Token",
						"id" =>   "cs_access_token_secret",
						"std" => "",
						"type" => "text");
	//end Twitter Api

	//start mailChimp api
	$options[] = array( "name" => "MailChimp",
						"id" => "mailchimp",
						"std" => "MailChimp",
						"type" => "section",
						"options" => ""
						);	
	$options[] = array( "name" => "MailChimp Key",
						"desc" => "Enter a valid MailChimp API key here to get started. Once you've done that, you can use the MailChimp Widget from the Widgets menu. You will need to have at least MailChimp list set up before the using the widget. You can get your mailchimp activation key",
						"hint_text" => "Get your mailchimp key by <a href='https://login.mailchimp.com/' target='_blank'>Clicking Here </a>",
						"id" =>   "cs_mailchimp_key",
						"std" => "90f86a57314446ddbe87c57acc930ce8-us2",
						"type" => "text"
						);
						
	$options[] = array( "name" => "MailChimp List",
						"desc" => "",
						"hint_text" => "",
						"id" =>   "cs_mailchimp_list",
						"std" => "on",
						"type" => "mailchimp",
						"options" => $mail_chimp_list
					);
					
	$options[] = array( "name" => "Flickr API Setting",
						"id" => "flickr_api_setting",
						"std" => "Flickr API Setting",
						"type" => "section",
						"options" => ""
						);					
	$options[] = array( "name" => "Flickr key",
						"desc" => "",
						"hint_text" => "",
						"id" =>   "flickr_key",
						"std" => "",
						"type" => "text");
	$options[] = array( "name" => "Flickr secret",
						"desc" => "",
						"hint_text" => "",
						"id" =>   "flickr_secret",
						"std" => "",
						"type" => "text");
	
	/* Cause Plugin options tab*/
	if(class_exists('wp_causes'))
	{
		
				
		$options[] = array( "name" => "Cause settings",
							"fontawesome" => 'icon-chain-broken',
							"id" => "tab-cause-options",
							"std" => "",
							"type" => "main-heading",
							"options" => ""
							);
		$options[] = array( "name" => "Cause settings",
							"id" => "tab-cause-options",
							"type" => "sub-heading"
							);
		 $options[] = array( "name" => "User Profile Page",
							"desc" => "",
							"hint_text" => "Select page for user profile here",
							"id" =>   "cs_dashboard",
							"std" => "",
							"type" => "select_dashboard",
							"options" => ''
						);
		$options[] = array( "name" => "Allow Campaigns From Frontend",
							"desc" => "",
							"hint_text" => "Allow Non Admins to Create Campaigns From Frontend",
							"id" =>   "cs_cause_campaigns_allow",
							"std" => "on",
							"type" => "checkbox",
							"options" => $on_off_option
						);
		$options[] = array( "name" => "New Campaigns Status",
							"desc" => "",
							"hint_text" => "New Campaigns Visibility. You can set default status of user compaigns",
							"id" =>   "cs_campaigns_visibility",
							"std" =>  "publish",
							"type" => "select",
							"options" =>array(
								"publish" => "Publish",
								"private"=>"Private",
							)
						);	
		$options[] = array( "name" => "Campaigns Description",
						"desc" => "",
						"hint_text" => "It will display on User Campaigns Listing",
						"id" => "cs_compaigns_text",
						"std" => "Campaigns help you organize people to achieve a common goal. Follow these simple steps and start campaigning for what you care about Get people interested with a short description of what yo are trying to do.",
						"type" => "textarea"
					);
		$options[] = array( "name" => "Add New Campaigns Text",
						"desc" => "",
						"hint_text" => "It will display on Add Campaigns Page",
						"id" => "cs_add_compaigns_text",
						"std" => "An event happening at a certain time and location, such as a concert, lecture, or festival.",
						"type" => "textarea"
					);
		$options[] = array( "name" => "Campaigns Terms & Conditions",
						"desc" => "",
						"hint_text" => "write your own copyright text",
						"id" => "cs_compaigns_terms_text",
						"std" => "Asome decently militantly versus that a enormous less treacherous genially well upon until fishy audaciously where fabulously underneath toucan armadillo far toward illustratively flawlessly shark much a emoted hey tersely pointedly much that hey quetzal up trenchant abundant made alas wildebeest overate overhung during busily burst as jeez much because more added on some thrust out.",
						"type" => "textarea"
					);	
					
					
					//cs_compaigns_terms_text									
		$options[] = array( "name" => "Paypal Sandbox",
							"desc" => "",
							"hint_text" => "Paypal Sandbox On/Off",
							"id" =>   "cs_paypal_sandbox",
							"std" => "on",
							"type" => "checkbox",
							"options" => $on_off_option
						);	
		$options[] = array( "name" => "Donor Registeration",
							"desc" => "",
							"hint_text" => "User Registeration For Donation On/Off",
							"id" =>   "cs_donation_user_register",
							"std" => "on",
							"type" => "checkbox",
							"options" => $on_off_option
						);						
		$options[] = array( "name" => "Paypal Email",
							"desc" => "",
							"hint_text" => "",
							"id" =>   "paypal_email",
							"std" => "",
							"type" => "text");
		$ipn_url = wp_causes::plugin_url().'causes/ipn.php';
		$options[] = array( "name" => "Paypal Ipn URL",
							"desc" => $ipn_url,
							"hint_text" => "",
							"id" =>   "paypal_ipn_url",
							"std" => $ipn_url,
							"type" => "text");
		$options[] = array( "name" => "Paypal Payments",
							"desc" => "",
							"hint_text" => "",
							"id" =>   "paypal_payments",
							"std" => "10,15,20,50,100.500,1000",
							"type" => "text");
				$currency_array = array('U.S. Dollar'=>'USD','Australian Dollar'=>'AUD','Brazilian Real'=>'BRL','Canadian Dollar'=>'CAD','Czech Koruna'=>'CZK','Danish Krone'=>'DKK','Euro'=>'EUR','Hong Kong Dollar'=>'HKD','Hungarian Forint'=>'HUF','Israeli New Sheqel'=>'ILS','Japanese Yen'=>'JPY','Malaysian Ringgit'=>'MYR','Mexican Peso'=>'MXN','Norwegian Krone'=>'NOK','New Zealand Dollar'=>'NZD','Philippine Peso'=>'PHP','Polish Zloty'=>'PLN','Pound Sterling'=>'GBP','Singapore Dollar'=>'SGD','Swedish Krona'=>'SEK','Swiss Franc'=>'CHF','Taiwan New Dollar'=>'TWD','Thai Baht'=>'THB','Turkish Lira'=>'TRY');
		$options[] = array( "name" => "Currency",
							"desc" => "",
							"hint_text" => "Currency Code",
							"id" =>   "paypal_currency",
							"std" =>  "publish",
							"type" => "select",
							"options" =>$currency_array
						);	
		$options[] = array( "name" => "Currency Sign",
							"desc" => "",
							"hint_text" => "Use Currency Sign eg: &pound;,&yen;",
							"id" =>   "paypal_currency_sign",
							"std" => "$",
							"type" => "text");										
	}
	
	
	// import and export theme options tab
	$options[] = array( "name" => "import & export",
						"fontawesome" => 'icon-database',
						"id" => "tab-import-export-options",
						"std" => "",
						"type" => "main-heading",
						"options" => ""
					);	
	$options[] = array( "name" => "import & export",
						"id" => "tab-import-export-options",
						"type" => "sub-heading"
						);	
	$options[] = array( "name" => "Export",
						"desc" => "",
						"hint_text" => "If you want to make changes in your site or want to preserve your current settings, Export them code by saving this code with you. You can restore your settings by pasting this code in Import section below ",
						"id" => "cs_export_theme_options",
						"std" => "",
						"type" => "export"
					);	
				
	$options[] = array( "name" => "Import",
						"desc" => "Import theme options",
						"hint_text" => "To Import your settings, paste the code that you got in above area and saved it with you",
						"id" => "cs_import_theme_options",
						"std" => "",
						"type" => "import"
					);
					
	update_option('cs_theme_data',$options); 
	//update_option('cs_theme_options',$options); 					  
	}
}
// saving all the theme options start
/**
*
*
* Header Colors Setting
 */
 
function cs_header_setting(){
	global $header_colors;
	  $header_colors = array();
			  $header_colors['header_colors'] =array(
					  'header_2'=>array(
						  'color' =>array( 
							  	'cs_topstrip_bgcolor'   =>  '#4c545a',
							 	'cs_topstrip_text_color' =>  '#fff',
								'cs_topstrip_link_color'  =>  '#fff',
							 	'cs_header_bgcolor'   =>  '#ffffff',
							 	'cs_nav_bgcolor'    =>  '#ffffff',
							 	'cs_menu_color'    => '#4c545a',
							 	'cs_menu_active_color'  => '#fd4e33',
							 	'cs_submenu_bgcolor'  => '#ffffff',
							 	'cs_submenu_color'   => '#fff',
							 	'cs_submenu_hover_color' => '#fff',
						  ),
						  'logo' =>array(
							  'cs_logo_with'			=> 	'198',
							  'cs_logo_height'		=> 	'42',
							  'cs_logo_margintb' 		=> 	'6',
							  'cs_logo_marginlr' 		=> 	'0',
						  )
				  ),
			  );
			  	
			  return $header_colors;
}
