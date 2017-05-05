
		var counter = 0;
		function delete_this(id){
				jQuery('#'+id).remove();
				jQuery('#'+id+'_del').remove();
				count_widget--;
				if (count_widget < 1)jQuery("#add_page_builder_item").addClass("hasclass");
		}

		var Data = [
			{ "Class" : "column_100" , "title":"100" , "element" : ["class","gallery", "slider","blog","cause", "event", "team", "column", "accordions", "team", "client", "contact", "column", "divider", "message_box",'image', "image_frame", "map", "video", "quote", "dropcap", "pricetable", "tabs", "accordion", "prayer", "advance_search", "parallax","table","call_to_action","flex_column","clients","spacer","heading","testimonials","infobox","promobox","offerslider","audio","icons","contactus","tooltip","highlight","list","mesage","faq","counter","member","timetable","project"] },
			{ "Class" : "column_75" , "title" : "75" , "element" : ["class","gallery", "slider", "blog", "cause", "event", "team", "column", "accordions", "team", "client", "contact", "column", "divider", "message_box",'image', "image_frame", "map", "video", "quote", "dropcap", "pricetable", "tabs", "accordion", "advance_search", "prayer","table","flex_column","clients","spacer","heading","testimonials","infobox","promobox","offerslider","audio","icons","contactus","tooltip","highlight","list","mesage","faq","counter","member","timetable","project"] },
			{ "Class" : "column_67" , "title" : "67" , "element" : ["class","gallery", "slider", "blog", "cause", "event", "team", "column", "accordions", "team", "client", "contact", "column", "divider", "message_box",'image', "image_frame", "map", "video", "quote", "dropcap", "pricetable", "tabs", "accordion", "advance_search", "prayer", "pointtable","table","flex_column","clients","spacer","heading","testimonials","infobox","promobox","offerslider","audio","icons","contactus","tooltip","highlight","list","mesage","faq","counter","member","timetable","project"] },
			{ "Class" : "column_50" , "title" : "50" , "element" : ["class","gallery", "slider", "blog", "cause", "event", "team", "column", "accordions", "team", "client", "contact", "column", "divider", "message_box",'image', "image_frame", "map", "video", "quote", "dropcap", "pricetable","tabs", "accordion", "advance_search", "prayer","teams","services","table","flex_column","clients","spacer","heading","testimonials","infobox","promobox","offerslider","audio","icons","contactus","tooltip","highlight","list","mesage","faq","counter","member","timetable","project"] },
			{ "Class" : "column_33" , "title" : "33" , "element" : ["gallery", "slider", "cause", "event", "team", "column", "accordions", "message_box",'image', "fixtures", "map", "video", "quote", "dropcap","services", "tabs", "accordion","prayer", "pointtable", "teams","flex_column", "pricetable","spacer","heading","testimonials","infobox","promobox","audio","icons","contactus","tooltip","highlight","list","mesage","faq","counter","project"] },
			{ "Class" : "column_25" , "title" : "25" , "element" : ["gallery","column", "divider", "message_box",'image', "image_frame", "map", "video", "quote", "dropcap", "pricetable","pastor","services","counter","teams",'services',"flex_column","spacer","heading","testimonials","infobox","promobox","audio","icons","contactus","tooltip","highlight","list","mesage","faq","project"] },
		];
	
		function callme() {
			jQuery(".dragarea") .each(function(index){
				var lengthitem = jQuery(this) .find(".parentdelete") .size()
				jQuery(this) .find("input.textfld") .val(lengthitem);
			});
		};
		
		function callmeontime () {
			jQuery( ".dradginner" ).sortable({
				 connectWith: '.draginner',
				 revert:true,
				 handle:'.column-in',
				 cancel:'.draginner .poped-up,#confirmOverlay',
				 placeholder: "ui-state-highlight",
				 receive: function( event, ui ) {callme();}
			});
		}

		var DataElement = [{"ClassName" : "col_width_full" ,"element" :["gallery", "slider", "blog","spacer", "event", "contact", "parallax", "services", "project"]}];
		function decrement(id){
			var $ = jQuery;
			var parent,ColumnIndex,CurrentWidget,CurrentColumn,module;
			parent = $(id).parent('.column-in');
			parent = $(parent).parent('.column');
			CurrentColumn = parseInt($(parent).attr('data'));
			CurrentWidget = $(parent).attr('widget');
			ColumnIndex = parseInt($(parent).attr('data'));
			module = $(parent).attr('item').toString();
			for(i = ColumnIndex + 1; i < Data.length; i++){
				for(c = 0; c <= Data[i].element.length; c++){
					if(Data[i].element[c] == module ){
						$(parent).removeClass(Data[ColumnIndex].Class)
						$(parent).addClass(Data[i].Class)
						$(parent).find('.ClassTitle').text(Data[i].title);
						$(parent).find('.item').val(Data[i].title);
						$(parent).find('.columnClass').val(Data[i].Class)
						$(parent).attr('data',i);
						return false;
					}
				}
			}
		}

        function increment(id){
			var $ = jQuery;
            var parent,ColumnIndex,CurrentWidget,CurrentColumn,module;
			parent = $(id).parent('.column-in');
			parent = $(parent).parent('.column');
            CurrentColumn = parseInt($(parent).attr('data'));
            CurrentWidget = $(parent).attr('widget');
            ColumnIndex = parseInt($(parent).attr('data'));
            module = $(parent).attr('item').toString();
				if(ColumnIndex > 0){
				for(i = ColumnIndex - 1; i < Data.length; i--){//
					for(c = 0; c <= Data[i].element.length; c++){
						if(Data[i].element[c] == module ){
							$(parent).removeClass(Data[ColumnIndex].Class)
							$(parent).addClass(Data[i].Class)
							$(parent).find('.ClassTitle').text(Data[i].title);
							$(parent).find('.item').val(Data[i].title);
							$(parent).attr('data',i);
							return false;
						}
					}
				}
			}
        }
		
		function showValues() {
			var fields = jQuery( ".shortcode_element_class input" ).serializeArray();
			jQuery( "#results-shortocdee" ).empty();
			jQuery.each( fields, function( i, field ) {
				jQuery( "#results-shortocde" ).append( field.value + " " );
			});
		}	
		
		function formsToObj(){
			var forms = [];
			jQuery('.shortcode_element_class').each(function(i){
				forms[i] = {};
				jQuery(this).children('input,textarea,select').each(function(){
					forms[jQuery(this).attr('name')] = jQuery(this).val();
				});
			});
			jQuery("#results-shortocde").append( forms );
			//return forms;
		}

		function Shortcode_tab_insert_editor(element_name, id) {
				var $id = jQuery("#" + id),
				_this = jQuery(this),
				attributes = '',
				content = '',
				contentToEditor = '',
				template = $id.data('shortcode-template'),
				childTemplate = $id.data('shortcode-child-template'),
				tables = $id.find(".cs-wrapp-clone.cs-shortcode-wrapp");
			for (var i = 0; i < tables.length; i++) {
				var elems = jQuery(tables[i]).find('input, select, textarea').not('.cs-search-icon,.wp-picker-clear');
				attributes = jQuery.map(elems, function(el, index) {
					var $el = jQuery(el);
					console.log(el);
					if ($el.data('content-text') === 'cs-shortcode-textarea') {
						content = $el.val();
						return '';
					} else if ($el.data('check-box') === 'check-field') {
						if ($el.is(':checked')) {
							return $el.attr('name') + '="true"';
						} else {
							return '';
						}
					} else {
						if ( $el.attr('name') != undefined )
							
							var _name = $el.attr('name').replace('[]', '');
							_name = _name.replace('[1]', '');
							if ( $el.val() != '' && _name !='fontawesome_icon' && _name !='users'){
								return _name + '="' + $el.val() + '"';
							}
						}
				});
				attributes = attributes.join(' ').trim();
				if (childTemplate) {
					var a = jQuery(tables[i]).data('template');
					if (a){
					contentToEditor += a.replace('{{attributes}}', attributes);

					} else{
					contentToEditor += childTemplate.replace('{{attributes}}', attributes).replace('{{attributes}}', attributes).replace('{{content}}', content);
					}
				} else {
					contentToEditor += template.replace('{{attributes}}', attributes).replace('{{attributes}}', attributes).replace('{{content}}', content);
				}
			};
			if (childTemplate) {
				contentToEditor = template.replace('{{child_shortcode}}', contentToEditor);
			}
			window.send_to_editor(contentToEditor);
			jQuery('body').removeClass('cs-overflow')
			jQuery("#cs-pbwp-outerlay").remove();
			return false;
		}


		
		// custom fields function
		function cs_custom_fields_js(){
			var parentItem = jQuery( "#pb-formelements" );
                parentItem.sortable({
                    cancel : 'div div.poped-up,.pb-toggle',
                    handle: ".pbwp-legend",
                    placeholder: "ui-state-highlighter"
                });
                var c= 0;
                parentItem.on("click","img.pbwp-clone-field",function(e){
                    e.preventDefault();
                    var _this = jQuery(this),
                    b = _this.closest('div.pbwp-clone-field');
                    b.clone().insertAfter(b);
                   var a =  _this.parents('.pbwp-form-sub-fields') .find('input:radio');
                   a.each(function(index, el) {
                   		jQuery(this).val(index+1);
                   });

                });
                parentItem.on("click","img.pbwp-remove-field",function(e){
                    e.preventDefault();
                    var _this = jQuery(this),
                    b = _this.closest('.pbwp-form-sub-fields');
                    c = b.find('div.pbwp-clone-field').length;
                    if (c > 1){
                        _this.closest("div.pbwp-clone-field").remove()
                    }
                });
               parentItem.on("click",".pbwp-remove",function(e){
                    e.preventDefault();
                    var a = confirm("This will delete Item");
                    if (a) {
                        jQuery(this).parents(".pb-item-container").remove()
                        alertbox();
                    }
               })
                parentItem.on("click","a.pbwp-toggle",function(e){
                    e.preventDefault();
                    jQuery(this).parents(".pbwp-legend").next().slideToggle(300);
                });
		}
		
		 function alertbox() {
			var itemcount = jQuery("#pb-formelements .pb-item-container") .length;
			if (itemcount > 0) {
				jQuery("#pbwp-alert").hide();	
			}else{
				jQuery("#pbwp-alert") .show();
			}		
		}

		jQuery(document).ready(function ($) {
			$('#cs_dynamic_custom_post_type_meta_id .handlediv').remove();
			$('#cs_dynamic_custom_post_type_meta_id h3.hndle').remove();
		});
	
		 // Shortcode
		var cs_shortocde_selection = (function(val, admin_url, id) {
			
			SuccessLoader()
			jQuery("#" + id).parents('#cs-widgets-list').removeClass('wide-width');
			if (val != "") {
				var shortcode_counter = 1
				var action = "cs_pb_" + val;
				var newCustomerForm = "action=" + action + '&counter=' + shortcode_counter + '&shortcode_element=shortcode';
				jQuery.ajax({
					type: "POST",
					url: admin_url,
					data: newCustomerForm,
					success: function(data) {
						_fontnamesearch();
						removeoverlay(id, 'widgetitem');
						_createpop(data, "csmedia");
						jQuery('.bg_color').wpColorPicker(); 
						jQuery('div.cs-drag-slider').each(function() {
						var _this = jQuery(this);
							_this.slider({
								range:'min',
								step: _this.data('slider-step'),
								min: _this.data('slider-min'),
								max: _this.data('slider-max'),
								value: _this.data('slider-value'),
								slide: function (event, ui) {
									jQuery(this).parents('li.to-field').find('.cs-range-input').val(ui.value)
								}
							});
						});
					}
				});
			}

		})
				 
		function cs_shortcode_element_ajax_call(val, id, admin_url) {
			if (val != "") {
				var shortcode_counter = 1
				var action = val;
				var newCustomerForm = "shortcode_element=" + val + '&action=cs_shortcode_element_ajax_call';
				//jQuery('.shortcodeload').html("<img src='http://irfan/education/wp-content/themes/education-theme/include/assets/images/ajax_loading.gif' alt=''/>");
				jQuery.ajax({
					type: "POST",
					url: admin_url,
					data: newCustomerForm,
					success: function(data) {
						jQuery('.shortcodeload').html("");
						jQuery("#" + id).append(data);
						jQuery('.bg_color').wpColorPicker();
						//_commonshortcode(id);
						var a = jQuery("#"+id+" .cs-wrapp-clone.cs-shortcode-wrapp").not('.cs-disable-true').length;
						jQuery("#"+id).next('.hidden-object') .find('.fieldCounter').val(a);
						jQuery('div.cs-drag-slider').each(function() {
						var _this = jQuery(this);
							_this.slider({
								range:'min',
								step: _this.data('slider-step'),
								min: _this.data('slider-min'),
								max: _this.data('slider-max'),
								value: _this.data('slider-value'),
								slide: function (event, ui) {
									jQuery(this).parents('li.to-field').find('.cs-range-input').val(ui.value)
								}
							});
						});
					}
				});
			}
		}
		
		function cs_fontawsome_popup(admin_url,id) {
			var shortcode_counter = 1
			var newCustomerForm = 'action=cs_fontawsome_popup_load';
			jQuery.ajax({
				type: "POST",
				url: admin_url,
				data: newCustomerForm,
				success: function(data) {
				var _data = jQuery("#"+id).find(".sipInitialized").val();	
				jQuery("#"+id).append("<div id='cs-pbwp-outerlay'><div id='cs-widgets-list'>"+data+"</div></div>");
				if (_data) {
					jQuery("#cs-widgets-list").find("li[data-icon-title^="+_data+"]").addClass('active')
				}
				_fontnamesearch();
				}
			});
		}

			function removeoverlay(id, text) {
				jQuery("#cs-widgets-list .loader").remove();
				var _elem1 = "<div id='cs-pbwp-outerlay'></div>",
					_elem2 = "<div id='cs-widgets-list'></div>";
				$elem = jQuery("#" + id);
				jQuery("#cs-widgets-list").unwrap(_elem1);
				if (text == "append" || text == "filterdrag") {
					$elem.hide().unwrap(_elem2);
				}
				if (text == "widgetitem") {
					$elem.hide().unwrap(_elem2);
					jQuery("body").append("<div id='cs-pbwp-outerlay'><div id='cs-widgets-list'></div></div>");
					return false;

				}
				if (text == "ajax-drag") {
					jQuery("#cs-widgets-list").remove();
				}
				jQuery("body").removeClass("cs-overflow");
			}

			function _createpop(data, type) {

				var _structure = "<div id='cs-pbwp-outerlay'><div id='cs-widgets-list'></div></div>",
					$elem = jQuery('#cs-widgets-list');
				jQuery('body').addClass("cs-overflow");
				if (type == "csmedia") {
					$elem.append(data);
				}
				if (type == "filter") {
					jQuery('#' + data).wrap(_structure).delay(100).fadeIn(150);
					jQuery('#' + data).parent().addClass("wide-width");
				}
				if (type == "filterdrag") {
					jQuery('#' + data).wrap(_structure).delay(100).fadeIn(150);
				}

			}
			 // Page builder widget element search filterable

			function cs_page_composer_filterable(id) {
				var $container = jQuery("#page_element_container" + id),
					elclass = "cs-filter-item";
				$container.find('.element-item').addClass("cs-filter-item");
				jQuery("#filters" + id + " li").click(function(event) {
					var $selector = jQuery(this).attr('data-filter'),
						$elem = $container.find("." + $selector + "");
					jQuery("#filters" + id + " li").removeClass("active");
					jQuery(this).addClass("active");
					$container.find('.element-item').removeClass(elclass);
					if ($selector == "all") {
						$container.find('.element-item').addClass(elclass);
					} else {
						jQuery($elem).addClass(elclass);
					}
					event.preventDefault();
				});
				// Search By input
				jQuery("#quicksearch" + id).keyup(function() {
					var _val = jQuery(this).val(),
						$this = jQuery(this);
					$container.find('.element-item').addClass("cs-filter-item");
					jQuery("#filters" + id + " li").removeClass("active");
					var filter = jQuery(this).val(),
						count = 0;
					jQuery("#page_element_container" + id + " .element-item span").each(function() {
						if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
							jQuery(this).parents(".element-item").removeClass(elclass);
						} else {
							jQuery(this).parents(".element-item").addClass(elclass);
							count++;
						}
					});
				})
			}

			function _fontnamesearch() {
				jQuery(".cs-search-icon").bind('keyup',function() {
					var _val = jQuery(this).val(),
						count = 0;
					var $elem = jQuery(this).parents(".cs-custom-fonts").find('.webfonts-wrapper li')
					$elem.each(function() {
						if (jQuery(this).data('icon-title').search(new RegExp(_val, "i")) < 0) {
							jQuery(this).hide();
						} else {
							jQuery(this).show(100);
							count++;
						}
					});
				});
			}
			jQuery(document).ready(function($) {
			if (location.hash) {
				jQuery('a[href=' + location.hash + ']').tab('show');
			}
			jQuery(document.body).on("click", "a[data-toggle]", function(event) {
				location.hash = this.getAttribute("href");
			});
		jQuery(window).on('popstate', function() {
				var anchor = location.hash || jQuery("a[data-toggle=tab]").first().attr("href");
				jQuery('a[href=' + anchor + ']').tab('show');
			});
				jQuery('.webfonts-wrapper li').live('click', function() {
					var _this = jQuery(this),
					_mainParent = jQuery(this).parents('[id*="custom-field-fontawsome"]');
						_parents = _this.parents('.webfonts-wrapper'),
						elemClass = _this.hasClass('active'),
						iconTitle = _this.data('icon-title');
					jQuery(_parents).children('li').not(_this).removeClass('active');
					_this.toggleClass('active');
					if (_mainParent.hasClass("pbwp-form-rows")){
					if (!elemClass) {
						_mainParent.find('.sipInitialized').val(iconTitle);

					} else {
						_mainParent.find('.sipInitialized').val('');
					}
					jQuery("#cs-pbwp-outerlay").fadeOut(300,function(){jQuery("#cs-pbwp-outerlay").remove();})
					} else {
					if (!elemClass) {
						_this.parents('.cs-font-container').prev().find('.cs-search-icon-hidden').val(iconTitle);

					} else {
						_this.parents('.cs-font-container').prev().find('.cs-search-icon-hidden').val('')
					}
				}
				})
				_fontnamesearch();
				jQuery('div.cs-drag-slider').each(function() {
					var _this = jQuery(this);
					_this.slider({
						range: 'min',
						step: _this.data('slider-step'),
						min: _this.data('slider-min'),
						max: _this.data('slider-max'),
						value: _this.data('slider-value'),
						slide: function(event, ui) {
							jQuery(this).parents('li.to-field').find('.cs-range-input').val(ui.value)
						}
					});
				});
				var $a = jQuery('input[type="radio"].styled');
				for (var i = 0; i < $a.length; i++) {
					jQuery('<span class="radio"></span>').insertAfter($a[i]);
				}
			});

			function SuccessLoader() {
				jQuery("#cs-widgets-list div:first").hide();
				var loader = "<div class='loader'><i class='icon-spinner icon-spin'></i></div>"
				jQuery("#cs-widgets-list").append(loader)

			}