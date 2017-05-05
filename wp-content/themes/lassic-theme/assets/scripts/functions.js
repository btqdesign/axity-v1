/* ---------------------------------------------------------------------------
*	Last Word Add Tag Function
* --------------------------------------------------------------------------- */
	jQuery(document).ready(function($) {
		$(".cs-section-title h2").html(function(index, old) {
			return old.replace(/(\b\w+)$/, '<span>$1</span>');
		});
	});

/* ---------------------------------------------------------------------------
* Navigation Height Function
* --------------------------------------------------------------------------- */
	jQuery(document).ready(function($) {
	  "use strict";
		if ($('.logo,.navigation,.navigation ul > li > a').length) {
			var contentH = $('.main-navbar').height() - 30;
			$('.logo,.navigation,.navigation ul.navbar-nav > li > a,.search-sec .cs_searchbtn').height();
			$('.logo,.navigation,.navigation ul.navbar-nav > li > a,.search-sec .cs_searchbtn').css('min-height', contentH + 'px');
			$('.logo,.navigation,.navigation ul.navbar-nav > li > a,.search-sec .cs_searchbtn').css('line-height', contentH + 'px');
		}
	});


/* ---------------------------------------------------------------------------
 * Footer Back To Top Function
 * --------------------------------------------------------------------------- */
	jQuery(document).ready(function(){
		//Click event to scroll to top
		jQuery('#backtop').click(function(){
			jQuery('html, body').animate({scrollTop : 0},800);
			return false;
		});
		
	});

/* ---------------------------------------------------------------------------
	* nice scroll for theme
 	* --------------------------------------------------------------------------- */
	function cs_nicescroll(){
		'use strict';	
		var nice = jQuery("html").niceScroll({mousescrollstep: "50",scrollspeed: "100",}); 
	}


/* ---------------------------------------------------------------------------
*	Navigation SubMenu Function
* --------------------------------------------------------------------------- */
	jQuery(".sub-dropdown").parent("li").addClass("parentIcon");


/* ---------------------------------------------------------------------------
  * Textarea Focus Function's
  * --------------------------------------------------------------------------- */
	jQuery(document).ready(function($){
		"use strict";
		jQuery('input,textarea').focus(function(){
		   jQuery(this).data('placeholder',jQuery(this).attr('placeholder'))
		   jQuery(this).attr('placeholder','');
		});
		jQuery('input,textarea').blur(function(){
		   jQuery(this).attr('placeholder',jQuery(this).data('placeholder'));
		});
	});
/* ---------------------------------------------------------------------------
  * MailChimp Function's
  * --------------------------------------------------------------------------- */

	function cs_mailchimp_submit(theme_url,counter,admin_url){
		'use strict';
		$ = jQuery;
	   // $('#btn_newsletter_'+counter).hide();
		$('#process_'+counter).html('<div id="process_newsletter_'+counter+'"><i class="icon-refresh icon-spin"></i></div>');
		$.ajax({
			type:'POST', 
			url: admin_url,
			data:$('#mcform_'+counter).serialize()+'&action=cs_mailchimp', 
			success: function(response) {
				$('#mcform_'+counter).get(0).reset();
				$('#newsletter_mess_'+counter).fadeIn(600);
				$('#newsletter_mess_'+counter).html(response);
				$('#btn_newsletter_'+counter).fadeIn(600);
				$('#process_'+counter).html('');
			}
		});
	}


/* ---------------------------------------------------------------------------
	* skills Function
 	* --------------------------------------------------------------------------- */
	function cs_skill_bar(){
		
		"use strict";	 
		jQuery(document).ready(function($){
			jQuery('.skillbar').each(function($) {
				jQuery(this).waypoint(function(direction) {
					jQuery(this).find('.skillbar-bar').animate({
						width: jQuery(this).attr('data-percent')
					}, 2000);
				}, {
					offset: "100%",
					triggerOnce: true
				});
			});
		});
	}

/* ---------------------------------------------------------------------------
  * Remove Empty P Tag Function
  * --------------------------------------------------------------------------- */
	jQuery('p').each(function() {
		var jQuerythis = jQuery(this);
		if(jQuerythis.html().replace(/\s|&nbsp;/g, '').length == 0)
			jQuerythis.remove();
	});

/* ---------------------------------------------------------------------------
 *  Filterable Function
 * --------------------------------------------------------------------------- */
	function portfolio_mix(){
		'use strict';
		jQuery('#list').mixitup({ effects :["blur","fade"]});
		return false;
	}
/* ---------------------------------------------------------------------------
 *  Owl Crousel Callback
 * --------------------------------------------------------------------------- */
	function cs_owncrowsel_callback(cs_class){
		jQuery('.'+cs_class).owlCarousel({
		nav: true,
		navText: [
		  "<i class=' icon-arrow-left9'></i>",
		  "<i class=' icon-arrow-right9'></i>"
		],
		responsive: {
		  0: {
			items: 1 // In this configuration 1 is enabled from 0px up to 479px screen size 
		  },
		  480: {
			items: 2, // from 480 to 677 
			nav: false // from 480 to max 
		  },
		  678: {
			items: 3, // from this breakpoint 678 to 959
			center: false // only within 678 and next - 959
		  },
		  960: {
			items: 3, // from this breakpoint 960 to 1199
			center: false,
			loop: false
		
		  },
		  1200: {
			items: 3,
		  }
		}
		
		});  
	}
/* ---------------------------------------------------------------------------
  * Responsive Video Function
  * --------------------------------------------------------------------------- */

	jQuery(document).ready(function($) {
		jQuery(".main-section").fitVids();
	});
	
	(function(e){"use strict";e.fn.fitVids=function(t){var n={customSelector:null,ignore:null};if(!document.getElementById("fit-vids-style")){var r=document.head||document.getElementsByTagName("head")[0];var i=".fluid-width-video-wrapper{width:100%;position:relative;padding:0;}.fluid-width-video-wrapper iframe,.fluid-width-video-wrapper object,.fluid-width-video-wrapper embed {position:absolute;top:0;left:0;width:100%;height:100%;}";var s=document.createElement("div");s.innerHTML='<p>x</p><style id="fit-vids-style">'+i+"</style>";r.appendChild(s.childNodes[1])}if(t){e.extend(n,t)}return this.each(function(){var t=['iframe[src*="player.vimeo.com"]','iframe[src*="youtube.com"]','iframe[src*="youtube-nocookie.com"]','iframe[src*="kickstarter.com"][src*="video.html"]',"object","embed"];if(n.customSelector){t.push(n.customSelector)}var r=".fitvidsignore";if(n.ignore){r=r+", "+n.ignore}var i=e(this).find(t.join(","));i=i.not("object object");i=i.not(r);i.each(function(){var t=e(this);if(t.parents(r).length>0){return}if(this.tagName.toLowerCase()==="embed"&&t.parent("object").length||t.parent(".fluid-width-video-wrapper").length){return}if(!t.css("height")&&!t.css("width")&&(isNaN(t.attr("height"))||isNaN(t.attr("width")))){t.attr("height",9);t.attr("width",16)}var n=this.tagName.toLowerCase()==="object"||t.attr("height")&&!isNaN(parseInt(t.attr("height"),10))?parseInt(t.attr("height"),10):t.height(),i=!isNaN(parseInt(t.attr("width"),10))?parseInt(t.attr("width"),10):t.width(),s=n/i;if(!t.attr("id")){var o="fitvid"+Math.floor(Math.random()*999999);t.attr("id",o)}t.wrap('<div class="fluid-width-video-wrapper"></div>').parent(".fluid-width-video-wrapper").css("padding-top",s*100+"%");t.removeAttr("height").removeAttr("width")})})}})(window.jQuery||window.Zepto)



/* ---------------------------------------------------------------------------
	*  Menu Toggle Function
* --------------------------------------------------------------------------- */
		
	jQuery(document).ready(function() {
		var windowWidth = jQuery(window).width();
		if(windowWidth >= 1000){
			jQuery(".navigation ul ul,.navigation ul").show();
		}else{
			jQuery(".navigation ul ul,.navigation ul").hide();
		}

		jQuery(".navigation ul ul") .parent('li') .addClass('parentIcon');
		jQuery(".navigation ul ul") .parent('li') .append( "<span class='responsive-btn'><i class='icon-arrow-right9'></i></span>" );

		jQuery('.cs-click-menu').on('click', function(e) {
			jQuery(this).next().toggle();
			jQuery(".navigation ul ul") .hide();
		});

		jQuery(".navigation .responsive-btn") .click(function(){
			if(jQuery(this).parent('li').hasClass('active')){
				jQuery('.navbar-nav li').removeClass('active');
				jQuery(this).parent('li').parent('ul').find('li>ul').hide();
				jQuery(this).siblings('ul').hide();
			}else{
				jQuery(this).parent('li').addClass('active');
				jQuery(this).parent('li').parent('ul').find('li>ul').hide();
				jQuery(this).siblings('ul').show();
			}
		});
	});