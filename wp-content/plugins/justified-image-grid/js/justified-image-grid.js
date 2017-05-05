/*
* Justified Image Grid - Aligns your images into a Flickr / Google+ style thumbnail-grid gallery
* Version 2.2.1
*
* Copyright (c) 2012-2014 Firsh, http://www.justifiedgrid.com/
*/
function loadJustifiedImageGrid($) {
	$.justifiedImageGrid = function(element, options){
		// set up default options 
		var defaults = {
				targetHeight:				190,
				heightDeviation:			40,
				aspectRatio:				'',
				disableCropping:			'',
				randomizeWidth:				'',
				margins:					4,
				animSpeed:					300,
				limit:						0,
				maxRows:					'',
				linkClass:					'',
				linkRel:					'auto',
				linkAttributeName:			'',
				linkAttributeValue:			'',
				linkAttribute:				'',
				linkTitleField:				'description',
				imgAltField:				'title',
				wrapText:					'no',
				readingDirection:			'ltr',
				loadMore:					'off',
				loadMoreText:				'Load more',
				loadMoreCountText:			'(*count* images remaining)',
				loadMoreAutoWidth:			'on',
				disableHover:				'no',
				downloadLink:				'no',
				lightboxLink:				'no',
				verticalCenterCaptions:		'off',
				customFonts:				'yes',
				captionHeight:				54,
				quality:					90,
				retinaReady:				'yes',
				retinaQuality:				'auto',
				minRetinaQuality:			30,
				maxRetinaDensity:			3,
				caption:					'fade',
				captionMatchWidth:			'no',
				captionMatchWidthForceNo:	false,
				titleField:					'title',
				captionField:				'description',
				lightbox:					'prettyphoto',
				lightboxInit:				'jigAddLightbox1',
				overlay:					'hovered',
				overlayIcon:				'off',
				bordersTotal:				0,
				innerBorder:				'always',
				innerBorderWidth:			0,
				innerBorderAnimate:			'width',
				middleBorder:				'always',
				middleBorderWidth:			0,
				middleBorderColor:			'white',
				outerBorder:				'always',
				outerBorderColor:			'black',
				specialFx:					'off',
				specialFxType:				'desaturate',
				specialFxOptions:			'',
				specialFxBlend:				1,
				incompleteLastRow:			'normal',
				errorChecking:				'yes',
				retryCount:					0,
				resizeCount:				0,
				errorChecked:				false,
				errorImages:				[],
				filters:					false,
				filterMultiple:				'no',
				filterStyle:				'buttons',
				L2filters:					false,
				L2FilterMultiple:			'no',
				L2FilterStyle:				'buttons',
				filterSmallestColor:		'#A3A3A3',
				filterSmallestSize:			11,
				filterLargestColor:			'#000000',
				filterLargestSize:			22,
				separatorCharacter:			' - ',
				cropZone:					'',
				instance:					1,
				element:					$(element),
				lastWindowWidth:			$(window).width()
			},
		plugin = this,
		s = $.extend({}, defaults, options),
		oldIE = !jQuery.support.opacity,
		Chrome = !!window.chrome && !/opera|opr/i.test(navigator.userAgent);
		this.s = s;

		// base setup of settings, mouse interaction, images load event handler 
		plugin.init = function(){
			s.minHeight = s.targetHeight-s.heightDeviation;
			s.maxHeight = s.targetHeight+s.heightDeviation;
			s.defaultHeightRatio = s.targetHeight/s.maxHeight;
			s.originalLimit = s.limit;
			s.hiddenOpacity = !oldIE ? 0 : 0.01;
			if(s.lightbox !== "no" && s.lightbox !== "links-off" && s.lightbox !== "new_tab"){
				s.hiddenLinkClass = (s.linkClass !== '' ? 'class="jig-link jig-hiddenLink '+s.linkClass+'" ' : 'class="jig-link jig-hiddenLink" ');
				s.linkClass = (s.linkClass !== '' ? 'class="jig-link '+s.linkClass+'" ' : 'class="jig-link" ');
				
				switch(s.linkRel){
					case '':
						s.linkRel = "";
					break;
					default:
						switch(s.lightbox){
							case 'prettyphoto':
							s.linkRel = 'rel="'+s.linkRel.replace('auto','prettyPhoto['+s.instance+']')+'" ';
							break;
							case 'colorbox':
							s.linkRel = 'rel="'+s.linkRel.replace('auto','colorBox['+s.instance+']')+'" ';
							break;
							default:
							s.linkRel = 'rel="'+s.linkRel.replace('auto','gallery['+s.instance+']')+'" ';
							break;
						}
					break;
				}
			}else{
				s.linkClass = "";
				s.hiddenLinkClass = "";
				s.linkRel = "";
			}

			if(s.linkAttributeName !== ''){
				if(s.linkAttributeValue !== ''){
					s.linkAttribute = ' '+s.linkAttributeName+'="'+s.linkAttributeValue+'"';
				}else{
					s.linkAttribute = ' '+s.linkAttributeName+'=""';
				}
			}
			s.allItems = s.selectedItems = s.items.slice(); // Store the original value of All items because s.items will be modified

			if(s.aspectRatio){
				s.maxWidth = Math.floor(s.maxHeight*parseFloat(s.aspectRatio));
				if(s.incompleteLastRow == 'normal'){
					s.incompleteLastRow = 'match';
					if(s.loadMore !== 'off'){
						s.incompleteLastRow = 'flexible-match';
					}
				}
			}
			if(s.retinaReady == 'yes'){
				if(typeof window.devicePixelRatio === 'undefined' || window.devicePixelRatio == 1){
					s.retinaReady = 'no';
					s.devicePixelRatio = 1;
				}else{
					if(window.devicePixelRatio > s.maxRetinaDensity){
						s.devicePixelRatio = s.maxRetinaDensity;
					}else{
						s.devicePixelRatio = window.devicePixelRatio;
					}
				}
			}

			if(s.retinaReady == 'yes'){
				if(s.retinaQuality == 'auto'){
					s.quality = Math.ceil(s.quality/s.devicePixelRatio);
					s.quality = (s.quality > s.minRetinaQuality ? s.quality : s.minRetinaQuality);
				}else{
					s.quality = s.retinaQuality;
				}
			}


			if(s.disableCropping == 'yes'){
				s.minHeight = 50;
			}


			if(s.specialFx == 'captions' && s.caption == 'below'){
				s.specialFx = 'off';
			}

			if(s.customFonts == 'yes' && s.caption !== "off" && (s.caption == 'below' || s.verticalCenterCaptions !== 'off' || s.specialFx == 'captions')){
				var fontCheck = $('body').find('.jig-fontCheck');
				if(fontCheck.length === 0){
					$('body').append('<div class="jig-fontCheck">!"\'\\#$%&amp;()*+,-./0123456789:;&lt;=&gt;?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_abcdefghijklmnopqrstuvwxyz{|}~ A quick brown fox jumps over the lazy dog.</div>');
					fontCheck = $('body').find('.jig-fontCheck');

					var fontCheckBaseWidth = fontCheck.width(),
						fontChecksCounter = 0,
						fontCheckInterval = setInterval(function(){
							fontChecksCounter++;
							var fontCheckCurrentWidth = fontCheck.width();
							if(fontCheckBaseWidth == fontCheckCurrentWidth || fontCheckCurrentWidth === 0){
								// no change...
								if(fontChecksCounter > 50){
									fontCheck.remove(); // Important to remove it here
									clearInterval(fontCheckInterval);
								}
								return;
							}

							$('body').trigger('jig-fontLoaded');
							fontCheck.remove();
							clearInterval(fontCheckInterval);
							return;
						},100);
				}
			}

			// Filtering features (layout mostly)
			if(s.filters || s.L2filters){
				s.filteredImages = [];
				if(s.filters){
					s.filterMultiple = s.filterMultiple || 'no';
					s.filterStyle = s.filterStyle || 'buttons';
					var filterElementType, filterSlug;
					if(s.filterStyle == "buttons"){
						s.filterType = 'Button';
						filterElementType = 'div';
					}else{
						s.filterType = 'Tag';
						filterElementType = 'span';
					}
					
					// Creating the filter buttons or tags interface
					var filterButtons = '<div id="jig'+s.instance+'-filter'+s.filterType+'s" class="jig-filter'+s.filterType+'s '+(s.filterMultiple !== 'no' ? 'jig-filterMultiple ' : 'jig-filterSingle ')+'jig-clearfix" data-filter-level="1">';
					for(filterSlug in s.filters){
						if (ownProp(s.filters,filterSlug)){
							// p isn't inherited, do stuff with obj[p]
							s.filteredImages[filterSlug] = [];
							filterButtons += '<'+filterElementType+' class="jig-filter'+s.filterType+'" data-filter-slug="'+filterSlug+'">'+s.filters[filterSlug]+'</'+filterElementType+'>';
						}
					}

					filterButtons += '</div>';
					s.element.before(filterButtons); // Adding it above the grid

					if(typeof s.filteredImages['all-items-nofilter'] !== 'undefined'){
						s.filteredImages['all-items-nofilter'] = s.selectedItems.slice();
					}

					for(var i = 0, j = s.selectedItems.length; i < j; i += 1){
						if(s.selectedItems[i].filters){
							for(var k = 0, l = s.selectedItems[i].filters.length; k < l; k += 1){
								// If something is undefined here then PHP filters the actually needed filters and if an image is tagged with a tag which is not really needed, it doesn't exist
								if(typeof s.filteredImages[s.selectedItems[i].filters[k][0]] !== 'undefined'){
									s.filteredImages[s.selectedItems[i].filters[k][0]].push(s.selectedItems[i]); // add to an array like s.filteredImages['blue']
								}
							}
						}
					}

					var allButton = $('#jig'+s.instance+'-filter'+s.filterType+'s .jig-filter'+s.filterType+'[data-filter-slug="all-items-nofilter"]');
					if(allButton.length > 0){
						if($('#jig'+s.instance+'-filter'+s.filterType+'s .jig-filter'+s.filterType).first().attr('data-filter-slug') !== allButton.attr('data-filter-slug')){
							allButton.parent().prepend(allButton); // Force all button to be first, even if a slug makes another term alphabetically first in the array
						}
					}

					if(s.filterStyle == "tags"){
						for(filterSlug in s.filters){
							if(ownProp(s.filters,filterSlug)) {
								$('#jig'+s.instance+'-filter'+s.filterType+'s .jig-filterTag[data-filter-slug="'+filterSlug+'"]').attr('rel',s.filteredImages[filterSlug].length);
							}
						}

						$.fn.tagcloud.defaults = {
							size: {start: s.filterSmallestSize, end: s.filterLargestSize, unit: 'px'},
							color: {start: s.filterSmallestColor, end: s.filterLargestColor}
						};
						if(allButton.length > 0){
							allButton.css({'font-size':s.filterLargestSize,'color':s.filterLargestColor});
							$('#jig'+s.instance+'-filter'+s.filterType+'s .jig-filter'+s.filterType+':gt(0)').tagcloud(); // don't auto-tagcloud the All button
						}else{
							$('#jig'+s.instance+'-filter'+s.filterType+'s .jig-filter'+s.filterType).tagcloud();
						}
					}



					// Click handling for each filter button or tag, except the one that is currently selected
					$('#jig'+s.instance+'-filter'+s.filterType+'s').on('click', '.jig-filter'+s.filterType, doTheFilter);

				}
				if(s.L2filters){
					s.L2filterMultiple = s.L2filterMultiple || 'no';
					s.L2filterStyle = s.L2filterStyle || 'buttons';
					var L2filterElementType, L2filterSlug;
					if(s.L2filterStyle == "buttons"){
						s.L2filterType = 'Button';
						L2filterElementType = 'div';
					}else{
						s.L2filterType = 'Tag';
						L2filterElementType = 'span';
					}
					s.L2filteredImages = [];
					// Creating the filter buttons or tags interface
					var L2filterButtons = '<div id="jig'+s.instance+'-L2filter'+s.L2filterType+'s" class="jig-filter'+s.L2filterType+'s '+(s.L2filterMultiple !== 'no' ? 'jig-filterMultiple ' : 'jig-filterSingle ')+'jig-clearfix" data-filter-level="2">';
					for(L2filterSlug in s.L2filters){
						if (ownProp(s.L2filters,L2filterSlug)) {
							// p isn't inherited, do stuff with obj[p]
							s.L2filteredImages[L2filterSlug] = [];
							L2filterButtons += '<'+L2filterElementType+' class="jig-filter'+s.L2filterType+'" data-filter-slug="'+L2filterSlug+'">'+s.L2filters[L2filterSlug]+'</'+L2filterElementType+'>';
						}
					}

					L2filterButtons += '</div>';
					s.element.before(L2filterButtons); // Adding it above the grid

					if(typeof s.L2filteredImages['all-items-nofilter'] !== 'undefined'){
						s.L2filteredImages['all-items-nofilter'] = s.selectedItems.slice();
					}

					for(var ii = 0, jj = s.selectedItems.length; ii < jj; ii += 1){
						if(s.selectedItems[ii].L2filters){
							for(var kk = 0, ll = s.selectedItems[ii].L2filters.length; kk < ll; kk += 1){
								// If something is undefined here then PHP filters the actually needed filters and if an image is tagged with a tag which is not really needed, it doesn't exist
								if(typeof s.L2filteredImages[s.selectedItems[ii].L2filters[kk][0]] !== 'undefined'){
									s.L2filteredImages[s.selectedItems[ii].L2filters[kk][0]].push(s.selectedItems[ii]); // add to an array like s.L2filteredImages['blue']
								}
							}
						}
					}

					var L2allButton = $('#jig'+s.instance+'-L2filter'+s.L2filterType+'s .jig-filter'+s.L2filterType+'[data-filter-slug="all-items-nofilter"]');
					if(L2allButton.length > 0){
						if($('#jig'+s.instance+'-L2filter'+s.L2filterType+'s .jig-filter'+s.L2filterType).first().attr('data-filter-slug') !== L2allButton.attr('data-filter-slug')){
							L2allButton.parent().prepend(L2allButton); // Force all button to be first, even if a slug makes another term alphabetically first in the array
						}
					}

					if(s.L2filterStyle == "tags"){
						for(L2filterSlug in s.L2filters){
							if(ownProp(s.L2filters,L2filterSlug)) {
								$('#jig'+s.instance+'-L2filter'+s.L2filterType+'s .jig-filterTag[data-filter-slug="'+L2filterSlug+'"]').attr('rel',s.L2filteredImages[L2filterSlug].length);
							}
						}

						$.fn.tagcloud.defaults = {
							size: {start: s.filterSmallestSize, end: s.filterLargestSize, unit: 'px'},
							color: {start: s.filterSmallestColor, end: s.filterLargestColor}
						};
						
						if(L2allButton.length > 0){
							L2allButton.css({'font-size':s.filterLargestSize,'color':s.filterLargestColor});
							$('#jig'+s.instance+'-L2filter'+s.L2filterType+'s .jig-filter'+s.L2filterType+':gt(0)').tagcloud(); // don't auto-tagcloud the All button
						}else{
							$('#jig'+s.instance+'-L2filter'+s.L2filterType+'s .jig-filter'+s.L2filterType).tagcloud();
						}

					}



					// Click handling for each filter button or tag, except the one that is currently selected
					$('#jig'+s.instance+'-L2filter'+s.L2filterType+'s').on('click', '.jig-filter'+s.L2filterType, doTheFilter);

				}

				// Start the filtering by activating the first filter button (automatic)
				doTheFilter();
			}else{
				plugin.createGallery(); // calls the gallery creation function
			}

			// mouseenter and mouseleave functions
			var emptyFunc = function(){return false;},
				overlayMouseEnter = emptyFunc,
				overlayMouseLeave = emptyFunc,
				specialFxMouseEnter = emptyFunc,
				specialFxMouseLeave = emptyFunc,
				captionMouseEnter = emptyFunc,
				captionMouseLeave = emptyFunc,
				innerBorderMouseEnter = emptyFunc,
				innerBorderMouseLeave = emptyFunc,
				middleBorderMouseEnter = emptyFunc,
				middleBorderMouseLeave = emptyFunc,
				outerBorderMouseEnter = emptyFunc,
				outerBorderMouseLeave = emptyFunc,
			animateOpacity = function(el, findVal, eType, animDirection){
				if(s.specialFx !== 'captions' || findVal !== ".jig-caption-wrapper .jig-caption"){
					el.find(findVal).hoverFlow(eType, {'opacity': animDirection}, s.animSpeed);
				}else{
					var foundElements = el.find(findVal),
						captionObjects = getObjectsForCaptionSpecialEffect(foundElements,true);
					foundElements.hoverFlow(eType, {'opacity': animDirection}, {duration: s.animSpeed,
						progress:function(){
							if($(this).closest('.jig-cw-role-real').length !== 0){
								alignCaptionSpecialEffect(captionObjects);
							}
						}
					});
				}
			},

			animateHeight = function(el, findVal, eType, animDirection){
				if(s.specialFx !== 'captions'){
					el.find(findVal).hoverFlow(eType, {'height': animDirection}, s.animSpeed);
				}else{
					var foundElements = el.find(findVal),
						captionObjects = getObjectsForCaptionSpecialEffect(foundElements);
					foundElements.hoverFlow(eType, {'height': animDirection}, {duration: s.animSpeed,
						progress:function(){
							if($(this).closest('.jig-cw-role-real').length !== 0){
								alignCaptionSpecialEffect(captionObjects);
							}
						}
					});
				}
			},
			animateHeightFromCenter = function(el, findVal, eType, animDirection){
				if(s.specialFx !== 'captions'){
					el.find(findVal).hoverFlow(eType, {'height': animDirection }, {duration: s.animSpeed, progress:function(){
							var captionWrapper = el.find('.jig-caption-wrapper'),
								overflowElement = el.parent();
							captionWrapper.css('top',Math.round(overflowElement.height()/2-captionWrapper.height()/2));
					}});
				}else{
					var foundElements = el.find(findVal),
						captionObjects = getObjectsForCaptionSpecialEffect(foundElements),
						captionObject = foundElements.closest('.jig-caption');
					el.find(findVal).hoverFlow(eType, {'height': animDirection }, {duration: s.animSpeed,
						progress:function(){
							var captionWrapper = el.find('.jig-caption-wrapper'),
								overflowElement = el.parent();
							captionWrapper.css('top',Math.round(overflowElement.height()/2-captionObject.height()/2));
							if($(this).closest('.jig-cw-role-real').length !== 0){
								alignCaptionSpecialEffect(captionObjects);
							}
						},
						complete:function(){
							if((s.caption == 'slide' || s.caption == 'reverse-slide') && animDirection == 'hide'){
								el.find('.jig-cw-role-effect').height(0);
							}
						}
					});
				}
			},
			animateSpecialFx = function(el, findVal, eType, opacity){
				el.find(findVal).hoverFlow(eType, {'opacity': opacity}, s.animSpeed);
			},
			animateBorder = function(el, findVal, eType, borderWidth){
				el.find(findVal).hoverFlow(eType, {'borderLeftWidth': s.innerBorderWidth+'px', 'borderTopWidth': s.innerBorderWidth+'px', 'borderRightWidth': s.innerBorderWidth+'px', 'borderBottomWidth': s.innerBorderWidth+'px'}, s.animSpeed);
			};


			// overlay animation controls
			switch(s.overlay){
				case 'hovered':
					if(s.overlayIcon === "on"){
						overlayMouseEnter = function(el){
							animateOpacity(el, "div.jig-overlay-wrapper, div.jig-overlay-icon-wrapper", 'mouseenter', 'show');
						};
						overlayMouseLeave = function(el){
							animateOpacity(el, "div.jig-overlay-wrapper, div.jig-overlay-icon-wrapper", 'mouseleave', 'hide');
						};
					}else{
						overlayMouseEnter = function(el){
							animateOpacity(el, "div.jig-overlay-wrapper", 'mouseenter', 'show');
						};
						overlayMouseLeave = function(el){
							animateOpacity(el, "div.jig-overlay-wrapper", 'mouseleave', 'hide');
						};
					}
				break;
				case 'others':
					if(s.overlayIcon === "on"){
						overlayMouseEnter = function(el){
							animateOpacity(el, "div.jig-overlay-wrapper, div.jig-overlay-icon-wrapper", 'mouseenter', 'hide');
						};
						overlayMouseLeave = function(el){
							animateOpacity(el, "div.jig-overlay-wrapper, div.jig-overlay-icon-wrapper", 'mouseleave', 'show');
						};
					}else{
						overlayMouseEnter = function(el){
							animateOpacity(el, "div.jig-overlay-wrapper", 'mouseenter', 'hide');
						};
						overlayMouseLeave = function(el){
							animateOpacity(el, "div.jig-overlay-wrapper", 'mouseleave', 'show');
						};
					}
				break;
				default:
				break;
			}

			// border animation controls
			switch(s.innerBorder){
				case 'hovered':
					switch(s.innerBorderAnimate){
						case "width":
							innerBorderMouseEnter = function(el){
								animateBorder(el, "div.jig-border", 'mouseenter', s.innerBorderWidth+'px');
							};
							innerBorderMouseLeave = function(el){
								animateBorder(el, "div.jig-border", 'mouseleave', 0);
							};
						break;
						case "opacity":
							innerBorderMouseEnter = function(el){
								animateOpacity(el, "div.jig-border", 'mouseenter', 'show');
							};
							innerBorderMouseLeave = function(el){
								animateOpacity(el, "div.jig-border", 'mouseleave', 'hide');
							};
						break;
						case "off":
							innerBorderMouseEnter = function(el){
								$(el).find("div.jig-border").show();
							};
							innerBorderMouseLeave = function(el){
								$(el).find("div.jig-border").hide();
							};
						break;
						default:
						break;
					}

				break;
				case 'others':
					switch(s.innerBorderAnimate){
						case "width":
							innerBorderMouseEnter = function(el){
								animateBorder(el, "div.jig-border", 'mouseenter', 0);
							};
							innerBorderMouseLeave = function(el){
								animateBorder(el, "div.jig-border", 'mouseleave', s.innerBorderWidth+'px');
							};
						break;
						case "opacity":
							innerBorderMouseEnter = function(el){
								animateOpacity(el, "div.jig-border", 'mouseenter', 'hide');
							};
							innerBorderMouseLeave = function(el){
								animateOpacity(el, "div.jig-border", 'mouseleave', 'show');
							};
						break;
						case "off":
							innerBorderMouseEnter = function(el){
								$(el).find("div.jig-border").hide();
							};
							innerBorderMouseLeave = function(el){
								$(el).find("div.jig-border").show();
							};
						break;
						default:
						break;
					}
				break;
				default:
				break;
			}

			switch(s.middleBorder){
				case 'hovered':
					middleBorderMouseEnter = function(el){
						$(el).parent().parent("div.jig-imageContainer").css('background',s.middleBorderColor);
					};
					middleBorderMouseLeave = function(el){
						$(el).parent().parent("div.jig-imageContainer").css('background','transparent');
					};
				break;
				case 'others':
					middleBorderMouseEnter = function(el){
						$(el).parent().parent("div.jig-imageContainer").css('background','transparent');
					};
					middleBorderMouseLeave = function(el){
						$(el).parent().parent("div.jig-imageContainer").css('background',s.middleBorderColor);
					};
				break;
				default:
				break;
			}

			switch(s.outerBorder){
				case 'hovered':
					outerBorderMouseEnter = function(el){
						$(el).parent().parent("div.jig-imageContainer").css('border-color',s.outerBorderColor);
					};
					outerBorderMouseLeave = function(el){
						$(el).parent().parent("div.jig-imageContainer").css('border-color','transparent');
					};
				break;
				case 'others':
					outerBorderMouseEnter = function(el){
						$(el).parent().parent("div.jig-imageContainer").css('border-color','transparent');
					};
					outerBorderMouseLeave = function(el){
						$(el).parent().parent("div.jig-imageContainer").css('border-color',s.outerBorderColor);
					};
				break;
				default:
				break;
			}

			// specialfx animation controls
			if(!oldIE){
				switch(s.specialFx){
					case 'others':
						specialFxMouseEnter = function(el){
							animateSpecialFx(el, ".jig-pixastic", 'mouseenter', 0.01);
						};
						specialFxMouseLeave = function(el){
							animateSpecialFx(el, ".jig-pixastic", 'mouseleave', s.specialFxBlend);
						};
					break;
					case 'hovered':
						specialFxMouseEnter = function(el){
							animateSpecialFx(el, ".jig-pixastic", 'mouseenter', s.specialFxBlend);
						};
						specialFxMouseLeave = function(el){
							animateSpecialFx(el, ".jig-pixastic", 'mouseleave', 0.01);
						};
					break;
					default:
					break;
				}
			}else{ // for some reason old IE doesn't like the grayscale filter and opacity 0 at the same time
				switch(s.specialFx){
					case 'others':
						specialFxMouseEnter = function(el){
							//fxOpacityOutIE(el, ".jig-pixastic", 'mouseenter');
							animateSpecialFx(el, ".jig-pixastic", 'mouseleave', 0.01);

						};
						specialFxMouseLeave = function(el){
							animateSpecialFx(el, ".jig-pixastic", 'mouseleave', s.specialFxBlend);
						};
					break;
					case 'hovered':
						specialFxMouseEnter = function(el){
							animateSpecialFx(el, ".jig-pixastic", 'mouseenter', s.specialFxBlend);
						};
						specialFxMouseLeave = function(el){
							animateSpecialFx(el, ".jig-pixastic", 'mouseleave', 0.01);
							//fxOpacityOutIE(el, ".jig-pixastic", 'mouseleave');
						};
					break;
					default:
					break;
				}
			}

			// caption animation controls
			switch(s.caption){
				case 'fade':
					captionMouseEnter = function(el){
						animateOpacity(el, ".jig-caption-wrapper .jig-caption", 'mouseenter', 'show');
					};
					captionMouseLeave = function(el){
						animateOpacity(el, ".jig-caption-wrapper .jig-caption", 'mouseleave', 'hide');
					};
				break;
				case 'reverse-fade':
					captionMouseEnter = function(el){
						animateOpacity(el, ".jig-caption-wrapper .jig-caption", 'mouseenter', 'hide');
					};
					captionMouseLeave = function(el){
						animateOpacity(el, ".jig-caption-wrapper .jig-caption", 'mouseleave', 'show');
					};
				break;
				case 'slide':
					if(!oldIE){
						if(s.verticalCenterCaptions == 'off' || s.verticalCenterCaptions == 'simple'){
							captionMouseEnter = function(el){
								animateHeight(el, ".jig-caption-wrapper .jig-caption", 'mouseenter', 'show');
							};
							captionMouseLeave = function(el){
								animateHeight(el, ".jig-caption-wrapper .jig-caption", 'mouseleave', 'hide');
							};
						}else{
							captionMouseEnter = function(el){
								animateHeightFromCenter(el, ".jig-caption-wrapper .jig-caption", 'mouseenter', 'show');
							};
							captionMouseLeave = function(el){
								animateHeightFromCenter(el, ".jig-caption-wrapper .jig-caption", 'mouseleave', 'hide');
							};
						}
					}else{
						captionMouseEnter = function(el){
							animateOpacity(el, ".jig-caption-wrapper .jig-caption", 'mouseenter', 'show');
						};
						captionMouseLeave = function(el){
							animateOpacity(el, ".jig-caption-wrapper .jig-caption", 'mouseleave', 'hide');
						};
					}
				break;
				case 'reverse-slide':
					if(!oldIE){
						if(s.verticalCenterCaptions == 'off' || s.verticalCenterCaptions == 'simple'){
							captionMouseEnter = function(el){
								animateHeight(el, ".jig-caption-wrapper .jig-caption", 'mouseenter', 'hide');
							};
							captionMouseLeave = function(el){
								animateHeight(el, ".jig-caption-wrapper .jig-caption", 'mouseleave', 'show');
							};
						}else{
							captionMouseEnter = function(el){
								animateHeightFromCenter(el, ".jig-caption-wrapper .jig-caption", 'mouseenter', 'hide');
							};
							captionMouseLeave = function(el){
								animateHeightFromCenter(el, ".jig-caption-wrapper .jig-caption", 'mouseleave', 'show');
							};
						}
					}else{
						captionMouseEnter = function(el){
							animateOpacity(el, ".jig-caption-wrapper .jig-caption", 'mouseenter', 'hide');
						};
						captionMouseLeave = function(el){
							animateOpacity(el, ".jig-caption-wrapper .jig-caption", 'mouseleave', 'show');
						};
					}
				break;
				case 'mixed':
					if(s.verticalCenterCaptions == 'off' || s.verticalCenterCaptions == 'simple'){
						captionMouseEnter = function(el){
							animateHeight(el, ".jig-caption-wrapper .jig-caption-description-wrapper", 'mouseenter', 'show');
						};
						captionMouseLeave = function(el){
							animateHeight(el, ".jig-caption-wrapper .jig-caption-description-wrapper", 'mouseleave', 'hide');
						};
					}else{
						captionMouseEnter = function(el){
							animateHeightFromCenter(el, ".jig-caption-wrapper .jig-caption-description-wrapper", 'mouseenter', 'show');
						};
						captionMouseLeave = function(el){
							animateHeightFromCenter(el, ".jig-caption-wrapper .jig-caption-description-wrapper", 'mouseleave', 'hide');
						};
					}
				break;
				case 'reverse-mixed':
					if(s.verticalCenterCaptions == 'off' || s.verticalCenterCaptions == 'simple'){
						captionMouseEnter = function(el){
							animateHeight(el, ".jig-caption-wrapper .jig-caption-description-wrapper", 'mouseenter', 'hide');
						};
						captionMouseLeave = function(el){
							animateHeight(el, ".jig-caption-wrapper .jig-caption-description-wrapper", 'mouseleave', 'show');
						};
					}else{
						captionMouseEnter = function(el){
							animateHeightFromCenter(el, ".jig-caption-wrapper .jig-caption-description-wrapper", 'mouseenter', 'hide');
						};
						captionMouseLeave = function(el){
							animateHeightFromCenter(el, ".jig-caption-wrapper .jig-caption-description-wrapper", 'mouseleave', 'show');
						};
					}
				break;
				default:
				break;
			}

			// calls the animation functions on mouse interaction, also removes and readds title to avoid ugly tooltips
			if(s.disableHover !== 'yes'){
				s.element.on("mouseenter mouseleave", "a", function(event){
					var $this = $(this);
					if($this.css('display') !== 'none'){
						event.stopImmediatePropagation();
						if(event.type === "mouseenter"){
							overlayMouseEnter($this);
							specialFxMouseEnter($this);
							captionMouseEnter($this);
							innerBorderMouseEnter($this);
							middleBorderMouseEnter($this);
							outerBorderMouseEnter($this);
							$this.data('title',$this.attr('title'));
							$this.removeAttr('title');
						}else{
							overlayMouseLeave($this);
							specialFxMouseLeave($this);
							captionMouseLeave($this);
							innerBorderMouseLeave($this);
							middleBorderMouseLeave($this);
							outerBorderMouseLeave($this);
							$this.attr('title',$this.data('title'));
						}
					}
				});
				// re-adds title upon mousedown (for lightbox scripts)
				s.element.on("mousedown", "a", function(event){
					$(this).attr('title',$(this).data('title'));
				});
			}

			$('body').one('jig-fontLoaded',alignAllCaptions);

			if(s.loadMore !== 'off'){
				s.loadMoreCounter = 0;
				if(s.lightbox == 'carousel'){
					s.element.on('click',function(event){
						if($(event.target).hasClass('justified-image-grid')){
							event.stopPropagation();
							event.preventDefault();
							return;
						}
					});
				}
			}
			if(s.lightbox == 'prettyphoto' && typeof jigOtherPrettyPhotoIsPresent !== 'undefined'){
				$(document).ready(function(){
					setTimeout(function(){
						// This ensures that prettyPhoto is called with the settings values from JIG
						// if another prettyPhoto is in the page, assuming that there is a script calling it
						window[s.lightboxInit]();
					}, 10); // some tiny delay so other document ready calls execute, this can override them
				});
			}
		}; // end of init

		// builds/rebuilds the gallery, calls functions that create the rows and the adds/updates all the image elements
		plugin.createGallery = function(mode){

			if($.JIGminVersion('1.7') === false){
				JIGminVersion('1.7',true);
				return false;
			}
			var previousWidth = s.element.width(),
				currentWindowWidth = $(window).width(),
				extraWidth;
			if(s.fixedWidth === undefined){ // If there is no fixed width
				s.element.css('width', "").css('width', s.element.width());
				// If the width would be 0 and there is an fallbackWidth
				if(s.element.width() === 0){
					if(s.fallbackWidth === undefined){
						// defaults to previous width as window didn't change
						if(previousWidth !== 0 && currentWindowWidth == s.lastWindowWidth){
							s.element.css('width', previousWidth+'px');
						}else{ // Figures out the width even if the container is initially invisible
							extraWidth = 0; // Margins and paddings need to be accounted for
							s.element.parents().each(function(parentIndex,oneParent){ // Each parent up the tree
								var $oneParent = $(oneParent),
									$oneParentParent = $(oneParent).parent(),
									oneParentWidth = $oneParent.width();

								if($oneParent.is(':visible') && oneParentWidth !== 0){ // At the first parent that is visible AND width a width, stop and use its width
									s.element.css('width', (oneParentWidth-extraWidth)+'px'); // Set the found parent's width minus any extra
									// The following is necessary to detect scrollbar appear/disappear - changes window width that could change the tabs
									// Checks if this handler has been added to this common parent
									if(typeof $oneParentParent.data('jigAddedTabsHandler') == 'undefined'){
										$oneParentParent.on("click", function(event){ // Click handler on the nearby elements all subelements (supposedly tab heads)
											if(currentWindowWidth !== $(window).width()){ // s.lastWindowWidth would be a proper value but to extend it over for the other instances, need to use the currentWindowWidth that was added at the same time as the handler
												$oneParentParent.find('.justified-image-grid').each(function(){ // Each JIG instance nearby
													var $this = $(this);
													if($this.is(':visible')){ // Only if this is the currently visible
														$this.data('justifiedImageGrid').createGallery('resize'); // Resize this very instance only
													}
												});
											}
											return;
										});
										$oneParentParent.data('jigAddedTabsHandler',true);
									}
									return false;
								}else{
									extraWidth += $oneParent.outerWidth(true)-$oneParent.width(); // since margins and paddings etc are calculated even for invisible elements, it's possible to add them together to remove that amount from the result, giving a good guess - but this is not needed to include the visible element's extra values
								}
							});
						}
					}else{
						s.element.css('width', s.fallbackWidth+'px');
					}
				}else if(s.element.width() > 10000){
					s.element.css('width', currentWindowWidth);
					$(window).on('load', function(){
						plugin.createGallery('resize');
					});
				}
			}else{ // If there is fixed width
				s.element.css('width', "").css('width', s.fixedWidth);
			}


			var newAreaWidth = s.element.width();
			if(newAreaWidth < 9){

				if(s.retryCount > 10){
					s.element.find('.jig-clearfix').html('The element is invisible or too thin, upon page loading. In case of using tabs, try a Custom width in the General settings and you may also use a fixed width. (Justified Image Grid)');
					return;
				}
				s.retryBuilding = setTimeout(function(){
					s.retryCount++;
					plugin.createGallery();
				}, 100);
				return;
			}else{
				if(s.retryBuilding){
					clearTimeout(s.retryBuilding);
				}
				s.element.find('.jig-clearfix').empty();
			}
			s.justResized = false;
			if(mode === 'resize'){
				if(s.areaWidth && s.areaWidth == newAreaWidth){
					return;
				}else{
					s.justResized = true;
				}
			}

			s.element.find('.jig-overflow a *:not(div)').off();
			s.areaWidth = newAreaWidth;
			s.row = [];
			s.fullWidth = s.extra = 0;
			s.rows = [];
			s.unshifts = [];
			s.items = s.selectedItems.slice(); // refreshing from the selected items (all items or filtered items)
			if(s.errorChecked === true && s.justResized === false){
				for(var p in s.items){
					if (ownProp(s.items,p)) {
						// p isn't inherited, do stuff with obj[p]
						if($.inArray((s.items[p].photon === undefined ? (s.items[p].thumbUrl === undefined ? s.items[p].url : s.items[p].thumbUrl) : s.items[p].photon), s.errorImages) != -1){
							// p numerical index
							// s.items[p] the object itself
							var removedItem = s.items[p];
							s.items.splice(p,1);
							if(s.filters){
								var slug = $('#jig'+s.instance+'-filterButtons .jig-filterButtonSelected').attr('data-filter-slug');
								s.filteredImages[slug] = s.items.slice();
								for(var i = 1, j = s.filters.length; i < j; i += 1){ // for each filter
									for(var k = 0, l = s.filteredImages[s.filters[i][0]].length; k < l; k += 1){ // for each image in a filter
										var inArrayIndex = $.inArray(removedItem, s.filteredImages[s.filters[i][0]]);
										if(inArrayIndex != -1){
											s.filteredImages[s.filters[i][0]].splice(inArrayIndex,1);
										}
									}
								}
							}
						}
					}
				}
				s.selectedItems = s.items.slice(); // removing erroneous images from the selected items (it's only useful without filtering, as filtering recreates the selected items from all items every time, anyway)

				s.errorImages = [];
				if(s.selectedItems.length === 0){
					s.timThumbError = '<p style="background-color: red;background-color: rgba(255, 0, 0, 0.5);color: white;font-weight: bold;padding: 10px;">All of the images have failed to load.</p> <div style="background-color: black;background-color: rgba(0, 0, 0, 0.5);color: #D6D6D6;padding: 10px;">'+"<p>This is most likely a TimThumb permissions error.</p><p>Go to the Justified Image Grid settings, TimThumb & CDN tab. Click check permissions then click 0755 or 0777 to see if that works (or do it manually via FTP, on the files and folders it lists there in case chmod fails). You can disable TimThumb with the 'Use TimThumb' setting and the option 'No'.</p><p>Also read the troubleshooting guide in the documentation on what else to do, especially if you are using "+'<a href="http://support.hostgator.com/articles/pre-sales-policies/secfilterengine-and-secfilterscanpost" target="_blank">Hostgator</a>'+"!</p><p>If you are using a Better WP Security plug-in go to the Better WP Security settings, System Tweaks, Filter Suspicious Query Strings: Disable</p><p>Tip: Install the official WP plugin 'Jetpack' by Automattic and enable 'Photon'. Jetpack enables you to connect your blog to a WordPress.com account to use the powerful features normally only available to WordPress.com users. It's an excellent TimThumb alternative and will make your images load faster. Note that you won't be able to use special effects due to cross-domain security limitations. "+'Read more at: <a href="http://jetpack.me/" target="_blank" rel="external nofollow">jetpack.me</a></p></div>';
					s.element.html(s.timThumbError);
					return;
				}

				s.errorChecked = false;

			}

			if(s.maxRows === '' || s.maxRows === 0){
				s.maxRows = 1000;
			}
			s.rowcount = 0;
			s.imagesShown = 0;
			if(s.limit === 0){
				s.whileUntil = 0;
			}else if (s.limit < s.selectedItems.length){
				s.whileUntil = s.items.length-s.limit;
			}else{
				s.whileUntil = 0;
			}

			// calculates dimensions and everything else for all the image elements, builds the rows
			// until the image source is depleted or the rows reach maximum set, whichever occurs first
			while(s.items.length > s.whileUntil && s.rowcount < s.maxRows){
				var row = buildImageRow();
				if(row !== false){
					s.rows.push(row);
					s.rowcount++;
				}
			}
			// keeps track of images that should be loaded
			s.imagesShown += s.selectedItems.length - s.items.length;

			// removes leftover images
			s.element.find('.jig-imageContainer:gt('+(s.imagesShown-1)+')').remove();

			// keeps track of images that are actually added
			s.imagesAlreadyAdded = s.element.find(".jig-imageContainer").length;

			// goes through every image of every row
			var imageCount = 0;
			var item;
			for(var r = 0, t = s.rows.length; r < t; r += 1){
				for(var u = 0, v = s.rows[r].length; u < v; u += 1){
					imageCount++;
					item = s.rows[r][u];
					if(item.container && imageCount <= s.imagesAlreadyAdded){
						// updates image elements that already exist
						updateImageElement(item, s.rows[r].length, u);
					}else{
						// adds image elements not yet created
						createImageElement(item, s.rows[r].length, u);
					}
				}
			}
			if(s.lightbox !== "links-off" && s.lightbox !== "no" && s.lightbox !== "new_tab" && s.linkrel !== ''){
				while(s.unshifts.length > 0){
					item = s.unshifts.shift();
					if(item.linkContainer === undefined){
						buildHiddenLink(item);
					}else{
						s.currentHiddenLink = item.linkContainer;
					}
				}
				while(s.items.length > 0){
					item = s.items.shift();
					if(item.linkContainer === undefined){
						buildHiddenLink(item);
					}else{
						s.currentHiddenLink = item.linkContainer;
					}
				}
				s.currentHiddenLink = undefined;
			}

			// This is needed to quickly recheck width for fluid layouts
			if(s.fixedWidth === undefined){ // If there is no fixed width
				s.element.css('width', "").css('width', s.element.width());
				if(s.element.width() === 0){
					s.element.css('width', s.areaWidth+'px'); // Don't change anything if it's invisible otherwise
				}
			}

			$("img", s.element).not('.jig-hiddenImg').on('load', function(){
				if(this.complete || (this.naturalWidth !== undefined && this.naturalWidth !== 0) || (this.readyState !== undefined && (this.readyState === 'complete' || this.readyState === 4)) || oldIE){
					var a = $(this).closest("a"),
					o = $(this).closest(".jig-overflow");

					if(a.length !== 0 && a.hasClass('jig-loaded') !== true){
						a.addClass('jig-loaded');
						if(o.css('opacity') === '0'){
							o.animate({opacity:1}, s.animSpeed);
						}
						if(s.specialFx != "off"){
							var imgClone = $(this).clone().addClass("jig-pixastic").insertAfter($(this));
							imgClone.on('load', imgCloneOnLoad).each(function(){
								if(this.complete || (this.naturalWidth !== undefined && this.naturalWidth !== 0) || (this.readyState !== undefined && (this.readyState === 'complete' || this.readyState === 4))){
									$(this).trigger("load");
								}
							});
						}
					}
					$(this).off("load");
				}else{
					if(s.errorChecking == 'yes'){
						var match = /(?:\?src=)(.*)(?:&h=)|^(https?:\/\/.+?wp\.com.*)$/g.exec($(this).attr('src'));
						if(match !== null){
							if(match[1] !== undefined){
								s.errorImages.push(match[1]);
							}else if(match[2] !== undefined){
								s.errorImages.push(match[2]);
							}
						}else{
							s.errorImages.push($(this).attr('src'));
						}
						$(this).closest('.jig-imageContainer').addClass('jig-unloadable');
						checkLoadResults();
					}
				}
			}).on('error', function(){
				if(s.errorChecking == 'yes'){
					var match = /(?:\?src=)(.*)(?:&h=)|^(https?:\/\/.+?wp\.com.*)$/g.exec($(this).attr('src'));
					if(match !== null){
						if(match[1] !== undefined){
							s.errorImages.push(match[1]);
						}else if(match[2] !== undefined){
							s.errorImages.push(match[2]);
						}
					}else{
						s.errorImages.push($(this).attr('src'));
					}
					$(this).closest('.jig-imageContainer').addClass('jig-unloadable');
					checkLoadResults();
				}
			}).each(function(){
				if(this.complete || (this.naturalWidth !== undefined && this.naturalWidth !== 0) || (this.readyState !== undefined && (this.readyState === 'complete' || this.readyState === 4))){
					$(this).trigger("load");
				}
			});
			if(mode !== 'resize'){
			// removes clickability and hand cursor when links are turned off
			// registers lightbox scripts
				switch(s.lightbox){
					case 'prettyphoto':
					case 'colorbox':
					case 'magnific':
					case 'photoswipe':
					case 'foobox':
					case 'custom':
						window[s.lightboxInit]();
					break;
					case 'links-off':
						s.element.find("a").css("cursor","default");
						s.element.on("click", "a", function(event){
							event.preventDefault();
							return;
						});
					break;
					case 'socialgallery':
						try {
							socialGalleryBind();
						} catch(e) {
							// handle an exception here if function doesn't exist or throws an exception
						}
					break;
					default:
					break;
				}
			}else{
				if(s.lightbox == 'prettyphoto' && typeof jigReCallPrettyPhotoAfterPossibleResize !== 'undefined'){
					setTimeout(function(){
						// This ensures that prettyPhoto is called with the settings values from JIG
						// if another prettyPhoto is in the page, assuming that there is a script calling it
						window[s.lightboxInit]();
						jigReCallPrettyPhotoAfterPossibleResize = undefined;
					}, 10); // some tiny delay so other document ready calls execute, this can override them
				}else{
					if(s.visibleImageCount != $('.jig-imageContainer',s.element).length){
						// changed amount of visible images, needs lightbox reinit
						initLightbox();
					}
				}
			}
			if(s.loadMore == 'click' || s.loadMore == 'scroll' || s.loadMore == 'hybrid' || s.loadMore == 'once'){
				s.loadMoreButton = s.element.find(".jig-loadMoreButton");
				var remainingCount = s.selectedItems.length-s.limit,
					preciseRemainingCount = s.element.find('.jig-hiddenLink').length;
				if(s.loadMoreButton.length == 1){ // When there is already a Load more button
					if(remainingCount > 0){
						s.loadMoreButton.find('.jig-loadMoreButton-count').text(preciseRemainingCount);
						if(s.loadMoreAutoWidth === 'yes'){
							s.loadMoreButton.css('width',s.loadMoreButton.find('.jig-loadMoreButton-inner').width());
						}
					} else {
						s.loadMoreButton.remove();
					}
				}else{ // When there is no Load more button yet
					if(remainingCount > 0){ // When there are remaining images
						var countText = '';
						if(s.loadMoreCountText !== '' && s.loadMoreCountText !== 'none'){
							countText = s.loadMoreCountText;
							countText = countText.replace('*count*','<span class="jig-loadMoreButton-count">'+preciseRemainingCount+'</span>');
						}
						s.loadMoreButton = $('<div class="jig-loadMoreButton"><span class="jig-loadMoreButton-inner">'+s.loadMoreText+(countText !== '' ? '<br />'+countText : '')+'</span></div>');

						s.element.on("click", ".jig-loadMoreButton", loadMore);
						s.element.find(".jig-clearfix").after(s.loadMoreButton);
						if(s.loadMoreAutoWidth === 'yes'){
							s.loadMoreButton.css('width',s.loadMoreButton.find('.jig-loadMoreButton-inner').width());
						}
					}
				}
				if(s.loadMore == 'scroll' || s.loadMore == 'hybrid'){
					$(window).scroll(function(){
						if(s.loadMore == 'scroll' || (s.loadMore == 'hybrid' && s.loadMoreCounter > 0)){
							var remainingCount = s.selectedItems.length-s.limit;
							if(remainingCount > 0){
								var elementOffset = s.element.find(".jig-loadMoreButton").offset();
								if ($(window).scrollTop() >= elementOffset.top - $(window).height()){
									loadMore();
								}
							}
						}
					});
				}
			}
			if(s.wrapText !== 'no'){
				var lastForWrap = s.element.find('.jig-imageContainer:last');
				if(s.element.find('.jig-flowSpacer').length === 0){
					s.element.find('.jig-imageContainer:last').after('<div class="jig-flowSpacer"></div>').next().css({'width':'1px','height':lastForWrap.height()+3,'float':(s.readingDirection == "ltr" ? 'left' : 'right')});
				}
				var flowSpacer = s.element.find('.jig-flowSpacer');
				setTimeout(function(){
					if(s.readingDirection == "ltr"){
						if(s.element.offset().left == flowSpacer.offset().left){
							flowSpacer.css('margin-right',0);
						}else{
							flowSpacer.css('margin-right',s.margins*2);
						}
					}else{
						if(s.element.offset().left == flowSpacer.offset().left){
							flowSpacer.css('margin-left',0);
						}else{
							flowSpacer.css('margin-left',s.margins*2);
						}
					}

				},1);
			}

			if(s.customFonts == 'yes' && s.caption !== "off" && (s.caption == 'below' || s.verticalCenterCaptions !== 'off' || s.specialFx == 'captions')){
				alignAllCaptions(true);
			}

			// recalculates everything if the available width has been clipped due to the scrollbar that just appeared
			s.prevInstanceID = '#jig'+(s.instance-1);

			if($(window).width() != currentWindowWidth || s.areaWidth != s.element.width()){
				s.resizeCount++;
				if(s.resizeCount > 1){
					s.element.css('min-height',s.element.height());
				}
				// calling CG due to changed window / JIG dimensions
				plugin.createGallery('resize');

				if(s.instance > 1 && $(s.prevInstanceID).length !== 0){
					// calling previous instance's CG due to changed window dimensions
					$(s.prevInstanceID).data('justifiedImageGrid').createGallery('resize');
				}
				s.element.css('min-height','');
				return;

			}

			if(s.instance > 1 && $(s.prevInstanceID).length !== 0){
				s.prevInstanceData = $(s.prevInstanceID).data('justifiedImageGrid');
				s.prevInstanceData.s.element.css('width', "").css('width', s.prevInstanceData.s.element.width());
				if(s.prevInstanceData.s.areaWidth != s.prevInstanceData.s.element.width()){
					// calling previous instance's CG due to the fact that the previous instance is supposedly changed
					s.prevInstanceData.createGallery('resize');
				}
			}
			s.resizeCount = 0;
			s.lastWindowWidth = currentWindowWidth;
			s.visibleImageCount = $('.jig-imageContainer',s.element).length;
		}; // end  of createGallery

		// Aligns all captions, for vertical align feature, caption special effects and caption truncation for "below" style. Called once after the gallery is created (or resized) AND when custom fonts are loaded.
		var alignAllCaptions = function(instantAlign){
			// When explicitly set to true, it's part of the createGallery function, a sort of post processing
			if(instantAlign !== true){
				// this is called by custom fonts being loaded, instantAlign won't be a boolean at all
				if(s.specialFx == 'captions'){
					instantAlign = true;
				}else{
					instantAlign = false;
				}
			}
			
			s.element.find('.jig-imageContainer').each(function(index,imageContainer){
				var $imageContainer = $(imageContainer),
					captionWrappers;

				if(s.verticalCenterCaptions !== 'off'){

					captionWrappers = $imageContainer.find('.jig-caption-wrapper');
					var roleReal = captionWrappers.filter('.jig-cw-role-real'),
						roleRealHeight = roleReal.height(),
						captionObject = roleReal.find('.jig-caption'),
						captionDescriptionWrapper = (s.caption == 'mixed' || s.caption == 'reverse-mixed') ? roleReal.find('.jig-caption-description-wrapper') : false;

					if(!((s.caption == 'slide' || s.caption == 'reverse-slide') && captionObject.is(':animated'))){
						if(!((s.caption == 'mixed' || s.caption == 'reverse-mixed') && captionDescriptionWrapper.is(':animated'))){
							// the caption is not animated at the moment
							if(roleRealHeight === 0){
								captionObject.css({'opacity':0,'display':'block'});
								roleRealHeight = roleReal.height();
								captionObject.removeAttr('style');
							}
							if(instantAlign === true){
								captionWrappers.css({'bottom':'auto',
									'top':Math.round(roleReal.closest('.jig-imageContainer').height()/2-roleRealHeight/2)
									});

							}else{
								captionWrappers.css({'bottom':'auto'}).animate({
									'top':Math.round(roleReal.closest('.jig-imageContainer').height()/2-roleRealHeight/2)
									},s.animSpeed);
							}
						}else{
							// the caption is mixed or reverse-mixed AND animated at the moment
							captionDescriptionWrapper.promise().done(function(){
								roleRealHeight = roleReal.height();
								var captionDescriptionWrapperHeight = captionDescriptionWrapper.height();
								if(s.caption == 'mixed' && captionDescriptionWrapperHeight !== 0){
									captionDescriptionWrapper.css({'display':'none'});
									roleRealHeight = roleReal.height();
									captionDescriptionWrapper.css({'display':'block'});
								}else if(s.caption == 'reverse-mixed' && captionDescriptionWrapperHeight === 0){
									captionDescriptionWrapper.css({'display':'block'});
									roleRealHeight = roleReal.height();
									captionDescriptionWrapper.css({'display':'none'});
								}
								captionWrappers.css({'bottom':'auto'}).animate({
									'top':Math.round(roleReal.closest('.jig-imageContainer').height()/2-roleRealHeight/2)
									},s.animSpeed);
							});
						}
					}else{
						// the caption is slide or reverse-slide AND animated at the moment
						captionObject.promise().done(function(){
							roleRealHeight = roleReal.height();
							if(roleRealHeight === 0){
								captionObject.css({'opacity':0,'display':'block'});
								roleRealHeight = roleReal.height();
								captionObject.removeAttr('style');
							}
							captionWrappers.css({'bottom':'auto'}).animate({
								'top':Math.round(roleReal.closest('.jig-imageContainer').height()/2-roleRealHeight/2)
								},s.animSpeed);
						});
					}
				}

				if(s.caption == 'below'){
					truncateCaptions($imageContainer,true);
				}

				if(s.specialFx == 'captions'){
					// Chrome subpixel thing
					if(Chrome && s.captionMatchWidth !== 'no'){
						$imageContainer.find('.jig-caption-title').each(function(){
							if($(this).position().left % 1 !== 0){
								var $this = $(this);
								$this.css({width:$this.width()+1});
							}
						});
					}
					captionWrappers = captionWrappers ? captionWrappers : $imageContainer.find('.jig-caption-wrapper');
					alignCaptionSpecialEffect(getObjectsForCaptionSpecialEffect(captionWrappers));
				}


			});
					
		};

		var initLightbox = function(){
			switch(s.lightbox){
				case 'prettyphoto':
				case 'colorbox':
				case 'magnific':
				case 'photoswipe':
				case 'foobox':
					window[s.lightboxInit]();
				break;
				case 'links-off':
					s.element.find("a").css("cursor","default");
					s.element.on("click", "a", function(event){
						event.preventDefault();
						return;
					});
				break;
				case 'socialgallery':
					try {
						socialGalleryBind();
					} catch(e) {
						// handle an exception here if function doesn't exist or throws an exception
					}
				break;
				default:
				break;
			}
		};
		var doTheFilter = function(event){
			var $this = $(this),
				slug = $this.attr('data-filter-slug'),
				okToCreate = false,
				filterLevel,
				m, n, o, p; // loop variables pre defined for re-use
			// If the function is called manually (not via the click) then it' should select the first filter button
			if(typeof event === 'undefined'){
				if(s.filters !== false){
					$this = $('#jig'+s.instance+'-filter'+s.filterType+'s .jig-filter'+s.filterType).first();
					slug = $this.attr('data-filter-slug');
				}else{
					$this = $('#jig'+s.instance+'-L2filter'+s.L2filterType+'s .jig-filter'+s.L2filterType).first();
					slug = $this.attr('data-filter-slug');

				}
			}
			filterLevel = $this.parent().attr('data-filter-level');
			if(filterLevel == 1){ // If a level 1 filter button is clicked
				if(s.filterMultiple == 'no' || slug == 'all-items-nofilter'){
					if($this.hasClass('jig-filter'+s.filterType+'Selected') === false){
						$this.siblings('.jig-filter'+s.filterType+'Selected').removeClass('jig-filter'+s.filterType+'Selected');
						$this.addClass('jig-filter'+s.filterType+'Selected');
						s.selectedItems = s.filteredImages[slug];
						okToCreate = true;
					}
				}else{
					var selectedCount = $this.parent().find('.jig-filter'+s.filterType+'Selected').length;
					if(selectedCount == 1 && $this.siblings('.jig-filter'+s.filterType+'Selected').attr('data-filter-slug') == 'all-items-nofilter'){
						$this.siblings('.jig-filter'+s.filterType+'Selected').removeClass('jig-filter'+s.filterType+'Selected');
					}
					if($this.hasClass('jig-filter'+s.filterType+'Selected') === false){
						$this.addClass('jig-filter'+s.filterType+'Selected');
						selectedCount++;
					}else{
						if(selectedCount == 1){
							if($('#jig'+s.instance+'-filter'+s.filterType+'s .jig-filter'+s.filterType).first().attr('data-filter-slug') == 'all-items-nofilter'){
								$('#jig'+s.instance+'-filter'+s.filterType+'s .jig-filter'+s.filterType).first().click().addClass('jig-filter'+s.filterType+'Selected');
							}else{
								return;
							}
						}
						$this.removeClass('jig-filter'+s.filterType+'Selected');
						selectedCount--;
					}

					var filterCombination = [];
					$this.parent().find('.jig-filter'+s.filterType+'Selected').each(function(){
						filterCombination.push($(this).attr('data-filter-slug'));
					});

					slug = filterCombination.toString();

					if(typeof s.filteredImages[slug] === 'undefined'){
						s.filteredImages[slug] = [];
						var filterMatches = 0;
						n = s.allItems.length;
						for(m = 0; m < n; m += 1){
							if(s.allItems[m].filters){
								filterMatches = 0;
								// for each filter of the image
								p = s.allItems[m].filters.length;
								for(o = 0; o < p; o += 1){
									if(s.filterMultiple == 'or'){ // OR: expanding, union
										// checks if the slug is in the array of the combination
										// also avoids double images
										if($.inArray(s.allItems[m].filters[o][0],filterCombination) > -1 && $.inArray(s.allItems[m],s.filteredImages[slug]) == -1){
											// push the image in the results array that is used to display the gallery
											s.filteredImages[slug].push(s.allItems[m]);
										}
									}else{ // AND: narrowing, intersect
										// checks if the slug is in the array of the combination
										if($.inArray(s.allItems[m].filters[o][0],filterCombination) > -1){
											filterMatches++;
										}
										// if the image has all of the selected filters
										// push the image in the results array that is used to display the gallery
										// also avoids double images
										if(filterMatches == filterCombination.length && $.inArray(s.allItems[m],s.filteredImages[slug]) == -1){
											s.filteredImages[slug].push(s.allItems[m]);
										}
									}
								}
							}
						}
					}
					s.selectedItems = s.filteredImages[slug];
					okToCreate = true;

					/*
					s.element.find('.jig-imageContainer, .jig-hiddenLink').remove();
					for(var v = 0, x = s.selectedItems.length; v < x; v += 1){ // get rid of references to hidden links
						s.selectedItems[v].linkContainer = undefined;
					}

					plugin.createGallery('filter');
					*/
				}
				if(okToCreate && s.L2filters){
					s.L1selectedItems = s.selectedItems;
				}
			}
			// If Level 2 filters are there (not necessarily clicked yet)
			if(s.L2filters){
				// If the function is called manually (not via the click) then it' should select the first filter button
				if(typeof event === 'undefined'){
					$this = $('#jig'+s.instance+'-L2filter'+s.L2filterType+'s .jig-filter'+s.L2filterType).first();
					filterLevel = $this.parent().attr('data-filter-level');
				}
				// If a level 2 filtering is not just there but something from level 2was actually clicked
				if(filterLevel == 2){
					// manage selecting/deselecting the filter buttons or tags according to single or multiple filters
					// generate the l2 filtercombination and the l2 slug
					// not a single thing more

					if(s.L2filterMultiple == 'no' || slug == 'all-items-nofilter'){
						$this.siblings('.jig-filter'+s.L2filterType+'Selected').removeClass('jig-filter'+s.L2filterType+'Selected');
						$this.addClass('jig-filter'+s.L2filterType+'Selected');
					}else{
						var L2selectedCount = $this.parent().find('.jig-filter'+s.L2filterType+'Selected').length;
						if(L2selectedCount == 1 && $this.siblings('.jig-filter'+s.L2filterType+'Selected').attr('data-filter-slug') == 'all-items-nofilter'){
							$this.siblings('.jig-filter'+s.L2filterType+'Selected').removeClass('jig-filter'+s.L2filterType+'Selected');
						}
						if($this.hasClass('jig-filter'+s.L2filterType+'Selected') === false){
							$this.addClass('jig-filter'+s.L2filterType+'Selected');
							L2selectedCount++;
						}else{
							if(L2selectedCount == 1){
								if($('#jig'+s.instance+'-L2filter'+s.L2filterType+'s .jig-filter'+s.L2filterType).first().attr('data-filter-slug') == 'all-items-nofilter'){
									$('#jig'+s.instance+'-L2filter'+s.L2filterType+'s .jig-filter'+s.L2filterType).first().click().addClass('jig-filter'+s.L2filterType+'Selected');
								}else{
									return;
								}
							}
							$this.removeClass('jig-filter'+s.L2filterType+'Selected');
							L2selectedCount--;
						}
					}
				}


				// create a filter combination of level 2 selected filters
				var L2filterCombination = [];
				$('#jig'+s.instance+'-L2filter'+s.L2filterType+'s .jig-filter'+s.L2filterType+'Selected').each(function(){
					L2filterCombination.push($(this).attr('data-filter-slug'));
				});

				// create a slug combination of level 1 AND level 2 selected filter slugs

				// Get the (combined) slug of level 1 
				slug = [];
				if(s.filters){
					$('#jig'+s.instance+'-filter'+s.filterType+'s .jig-filter'+s.filterType+'Selected').each(function(){
						slug.push($(this).attr('data-filter-slug'));
					});
				}
				var slugCombination = slug.concat(L2filterCombination).toString();


				// s.L1selectedItems should be used instead of the s.allItems
				// if level 1 filters are false then make s.L1selectedItems the s.allItems manually
				if(s.filters === false){
					s.L1selectedItems = s.allItems;
				}

				// do the filtering for level 2, if that slug combination is not yet cached already
				if(typeof s.filteredImages[slugCombination] === 'undefined'){
					if(L2filterCombination.length == 1 && L2filterCombination[0] == 'all-items-nofilter'){
						s.filteredImages[slugCombination] = s.L1selectedItems;
						s.selectedItems = s.filteredImages[slugCombination];
					}else{
						s.filteredImages[slugCombination] = [];
						var L2filterMatches = 0;
						n = s.L1selectedItems.length;
						for(m = 0; m < n; m += 1){
							if(s.L1selectedItems[m].L2filters){
								L2filterMatches = 0;
								// for each filter of the image
								p = s.L1selectedItems[m].L2filters.length;
								for(o = 0; o < p; o += 1){
									if(s.L2filterMultiple == 'or'){ // OR: expanding, union
										// checks if the slugCombination is in the array of the combination
										// also avoids double images
										if($.inArray(s.L1selectedItems[m].L2filters[o][0],L2filterCombination) > -1 && $.inArray(s.L1selectedItems[m],s.filteredImages[slugCombination]) == -1){
											// push the image in the results array that is used to display the gallery
											s.filteredImages[slugCombination].push(s.L1selectedItems[m]);
										}
									}else{ // AND: narrowing, intersect
										// checks if the slugCombination is in the array of the combination
										if($.inArray(s.L1selectedItems[m].L2filters[o][0],L2filterCombination) > -1){
											L2filterMatches++;
										}
										// if the image has all of the selected filters
										// push the image in the results array that is used to display the gallery
										// also avoids double images
										if(L2filterMatches == L2filterCombination.length && $.inArray(s.L1selectedItems[m],s.filteredImages[slugCombination]) == -1){
											s.filteredImages[slugCombination].push(s.L1selectedItems[m]);
										}
									}
								}
							}
						}
					}
				}

				// leave the selected items in the filtered state
				s.selectedItems = s.filteredImages[slugCombination];
				// then indicate that it's ok to create the gallery
				okToCreate = true;
			}
			if(okToCreate){
				s.element.find('.jig-imageContainer, .jig-hiddenLink').remove();
				for(var t = 0, u = s.selectedItems.length; t < u; t += 1){ // get rid of references to hidden links
					s.selectedItems[t].linkContainer = undefined;
				}
				plugin.createGallery('filter');
			}
		};  // end  of doTheFilter

		// builds the rows of images
		// takes the overall average aspect ratio of the row into consideration,
		// to decide whether to shrink or enlarge the images when row height deviation is enabled
		// when it's not enabled (fixed row height), or it can't fit the images into the row
		// by enlarging or shrinking while maintaining aspect ratio,
		// then it'll just crop off left and right sides of the images, keeping them at the target height
		var buildImageRow = function(){
			s.row = [];
			s.validRow = true;
			s.fullWidth = 0;
			s.extra = 0;

			// builds a row to see how wide it would be when the last image pokes out of the row
			while(s.items.length > s.whileUntil && s.extra < s.areaWidth){
				var item = s.items.shift();
				item.newHeight = item.newWidth = item.containerHeight = item.containerWidth = item.marLeft = undefined;
				item.ratio = item.width/s.maxHeight;
				s.row.push(item);
				s.fullWidth += Math.round(item.width*s.defaultHeightRatio) + s.margins + s.bordersTotal;
				s.extra = s.fullWidth - s.margins;
			}
			// s.extra is the extra pixels the last image uses after the available width
			s.extra -= s.areaWidth;
			// if the line is too long, make images smaller/larger(by popping one)
			if((s.row.length > 0 && s.extra > 0) || (s.rows.length === 0 && s.items.length > s.whileUntil)){
				var orientation = "landscape";
				for(var i = 0, j = s.row.length; i < j; i += 1){
					if(s.row[i].ratio < 1){
						orientation = "portrait";
						break;
					}
				}
				if(orientation == "landscape"){ // if they are only landscape
					tryShrink(); // tries to shrink
				}else{ // if they have a portrait
					if(s.disableCropping !== 'yes'){
						tryGrow(); // tries to enlarge 
					}else{
						tryShrink();
					}
				}
			}else{ // rare case when all images fit in (and/or under) the row with the default height (commonly the last row)
				if(s.rows.length > 0 && s.items.length == s.whileUntil){ // this is the last row because no more images left
					switch(s.incompleteLastRow){
						case 'flexible':
							tryGrow('flexible');
						break;
						case 'flexible-center':
							tryGrow('flexible-center');
						break;
						case 'hide':
							tryGrow('hide');
						break;
						case 'flexible-match':
							if(s.loadMore !== 'off' && s.selectedItems.length-s.limit <= 0){
								matchRow();
							}else{
								tryGrow('flexible');
							}
						break;
						case 'flexible-match-center':
							if(s.loadMore !== 'off' && s.selectedItems.length-s.limit <= 0){
								matchRow('center');
							}else{
								tryGrow('flexible-center');
							}
						break;
						case 'match':
							matchRow();
						break;
						case 'match-center':
							matchRow('center');
						break;
						case 'center':
							tryGrow('center');
						break;
						default:
							tryGrow('lastRow');
						break;
					}
					if(s.validRow === true){
						return s.row;
					}else{
						//if it is the only row (first one do something to make the row visible)
						s.imagesShown -= s.row.length;
						s.unshifts = s.row;
						s.whileUntil += s.unshifts.length;
						return false;
					}
				}else{
					if(s.incompleteLastRow !== 'center'){
						tryGrow('lastRow');
					}else{
						tryGrow('center');
					}
				}
			}
			return s.row;
		}; // end of buildImageRow

		// tries to match the last row to the previous one if the images have the same aspect ratio
		// called by the switch in buildImageRow function
		var matchRow = function(mode){
			var prevRowHeight;
			var lastRowID = 0;
			for(var k = 0, l = s.rows.length; k < l; k += 1){
				lastRowID = k;
			}
			if(s.rows[lastRowID] === undefined || s.rows[lastRowID][0] === undefined ){
				tryGrow('lastRow');
				return;
			}
			prevRowHeight = s.rows[lastRowID][0].containerHeight ? s.rows[lastRowID][0].containerHeight : s.rows[lastRowID][0].newHeight; // this doesn't change over the previous row
			s.marginsTotal = ((s.row.length-1)*s.margins)+s.row.length*s.bordersTotal;
			s.rowlen = 0;
			for(var m = 0, n = s.row.length; m < n; m += 1){
				if(s.rows[lastRowID][m] !== undefined && s.row[m].width == s.rows[lastRowID][m].width){ // if the source picture is same kind, treat it the same way
					s.row[m].newHeight = s.rows[lastRowID][m].newHeight;
					s.row[m].containerHeight = s.rows[lastRowID][m].containerHeight;
					s.row[m].newWidth = s.rows[lastRowID][m].newWidth;
					s.row[m].containerWidth = s.rows[lastRowID][m].containerWidth;
					s.row[m].marLeft = s.rows[lastRowID][m].marLeft;
				}else{
					s.row[m].newHeight = prevRowHeight;
					s.row[m].newWidth = Math.round(s.row[m].newHeight*s.row[m].ratio);
				}
				s.rowlen += s.row[m].newWidth;
			}
			if(prevRowHeight > s.targetHeight){
				s.remaining = s.rowlen+s.marginsTotal-s.areaWidth;
				if(s.remaining > 0){
					finalize();
				}
			}
			if(typeof mode !== 'undefined' && s.rowlen+s.marginsTotal < s.areaWidth){
				s.row[0].spaceLeft = Math.floor((s.areaWidth-s.rowlen-s.marginsTotal)*0.5);
			}
		};

		// tries to build the row by shrinking the images
		// failure happens when it can only do that by going below the minimum height
		// then it'll skip to the enlarge function
		var tryShrink = function(){
			var doFinalize = true;
			s.marginsTotal = ((s.row.length-1)*s.margins)+s.row.length*s.bordersTotal;
			s.rowlen = 0;
			s.heights = [];
			for(var i = 0, j = s.row.length; i < j; i += 1){
				var targetWidth = Math.round(s.row[i].width*s.defaultHeightRatio),
				shrinkby = Math.round(((targetWidth+s.marginsTotal/s.row.length)/s.fullWidth)*s.extra);
				s.row[i].newWidth = (targetWidth-shrinkby);
				s.heights[i] = s.row[i].newWidth/s.row[i].ratio;
				if(s.heights[i] < s.minHeight){
					tryGrow();
					return;
				}
				if(s.heights[i] > s.maxHeight){
					s.row[i].newHeight = s.targetHeight;
					s.row[i].newWidth = Math.round(s.row[i].newHeight*s.row[i].ratio);
					doFinalize = false;
					continue;
				}
				s.row[i].newHeight = s.heights[i];
				s.rowlen += s.row[i].newWidth;
			}
			// there can be a few pixels that remain due to rounding, and they need to be taken care of later
			if(doFinalize){
				s.remaining = s.rowlen+s.marginsTotal-s.areaWidth;
				finalize();
			}
			return;
		}; // end of tryShrink

		// tries to build the row by enlarging the images (and moving the last one to the next row)
		// it fails when the images go above the maximum height
		// upon failure it'll give up enlarging or shrinking and will just crop (gets back the last image)
		var tryGrow = function(incompleteLastRow){
			var doFinalize = true;
			if(s.row.length != 1 && incompleteLastRow === undefined){
				var leftover = s.row.pop();
				s.fullWidth -= Math.round(leftover.width*s.defaultHeightRatio) + s.margins + s.bordersTotal;
				s.items.unshift(leftover);
				s.extra = s.fullWidth - s.margins;
				s.extra -= s.areaWidth;
				var removed = true;
			}
			s.marginsTotal = ((s.row.length-1)*s.margins)+s.row.length*s.bordersTotal;
			s.rowlen = 0;
			s.heights = [];

			for(var i = 0, j = s.row.length; i < j; i += 1){
				var targetWidth = Math.round(s.row[i].width*s.defaultHeightRatio);
				var growby = Math.round(((targetWidth+s.marginsTotal/s.row.length)/ s.fullWidth)*s.extra);
				s.row[i].newWidth = (targetWidth-growby);
				s.heights[i] = s.row[i].newWidth/s.row[i].ratio;
				if(s.heights[i] > s.maxHeight){
					if(incompleteLastRow === undefined){
						var item = s.items.shift();
						s.row.push(item);
						s.fullWidth += Math.round(item.width*s.defaultHeightRatio) + s.margins + s.bordersTotal;
						s.extra = s.fullWidth - s.margins;
						s.extra -= s.areaWidth;
						doCrop();
						return;
					}else{
						if(s.loadMore !== 'off' && s.selectedItems.length-s.limit <= 0){
							if(incompleteLastRow == 'flexible'){
								incompleteLastRow = 'lastRow';
							}else if(incompleteLastRow == 'flexible-center'){
								incompleteLastRow = 'center';
							}
						}
						if(incompleteLastRow == 'lastRow'){
							for(var k = 0, l = s.row.length; k < l; k += 1){
								s.row[k].newHeight = s.targetHeight;
								s.row[k].newWidth = Math.round(s.row[k].newHeight*s.row[k].ratio);
							}
							return;
						}else if(incompleteLastRow == 'center'){ // similar to the normal mode
							var temporaryRowlen = 0;
							for(var kk = 0, ll = s.row.length; kk < ll; kk += 1){
								s.row[kk].newHeight = s.targetHeight;
								s.row[kk].newWidth = Math.round(s.row[kk].newHeight*s.row[kk].ratio);
								temporaryRowlen += s.row[kk].newWidth;
							}

							s.row[0].spaceLeft = Math.floor((s.areaWidth-temporaryRowlen-s.marginsTotal)*0.5);

							return;
						}else{
							s.validRow = false;
							s.row[i].newWidth = undefined;
							doFinalize = false;
						}
					}
				}else if(s.heights[i] < s.minHeight && incompleteLastRow === undefined){ // it'll need to default to cropping after all, if it's fixed height
				doCrop();
				return;
				}
				if(s.row[i].newWidth !== undefined){
					s.row[i].newHeight = s.heights[i];
					s.rowlen += s.row[i].newWidth;
				}
			}
			if(doFinalize){
				s.remaining = s.rowlen+s.marginsTotal-s.areaWidth;
				finalize();
			}
			return;
		}; // end of tryGrow

		// this makes the rows perfect by cropping or adding pixels whenever needed and possible
		// it makes sure every row is truly justified even if it means cropping a few pixels off the bottom of some images
		// that is because it'll re-shrink or re-enlarge images to make the +- pixels happen, then they won't have the same height anymore
		// it'll distribute / take away pixels by taking into consideration the relative size of each image to the row
		var finalize = function(){
			if(s.remaining !== 0){
				if(s.remaining > 0){ // if positive, then an excess of pixels need to be removed (shrink images)
					while(s.remaining > 0){
						for(var i = 0, j = s.row.length; i < j; i += 1){
							s.row[i].newWidth--;
							s.row[i].newHeight = s.heights[i] = s.row[i].newWidth/s.row[i].ratio;
							s.remaining--;
							if(s.remaining === 0) {
								break;
							}
						}
					}
				}else{ // if negative, the row needs more pixels (enlarge images)
					while(s.remaining < 0){
						for(var k = 0, l = s.row.length; k < l; k += 1){
							s.row[k].newWidth++;
							s.row[k].newHeight = s.heights[k] = s.row[k].newWidth/s.row[k].ratio;
							s.remaining++;
							if(s.remaining === 0) {
								break;
							}
						}
					}
				}
			}
			// finds the smallest (safe) height and matches all the other images to that height by cropping
			var safeMinimumHeight = Math.floor(Math.min.apply(null, s.heights));
			for(var m = 0, n = s.row.length; m < n; m += 1){
				s.row[m].containerHeight = safeMinimumHeight;
				s.row[m].newHeight = Math.round(s.row[m].newHeight);
			}
		}; // end of finalize

		// does the croppnig by reducing the image container's width and by setting a left border
		// cropping happens often if the row height is fixed
		var doCrop = function(){
			var crop = getCrop();
			for(var i = 0, j = s.row.length; i < j; i += 1){
				var unWanted = crop[i];
				var item = s.row[i];
				item.marLeft = Math.round(unWanted/2);
				item.containerWidth = item.newWidth-unWanted;
			}
			return;
		}; // end of doCrop

		// calculates the actual pixels to crop by and distributes them over all the images
		// taking into consideration their relative size to the row		
		var getCrop = function(){
			var crop = [];
			var cropTotal = 0;
			s.marginsTotal = ((s.row.length-1)*s.margins)+s.row.length*s.bordersTotal;
			for(var i = 0, j = s.row.length; i < j; i += 1){
				var item = s.row[i],
				targetWidth = Math.round(s.row[i].width*s.defaultHeightRatio);
				item.newHeight = s.targetHeight;
				item.newWidth = targetWidth;
				crop[i] = Math.round(((targetWidth+s.marginsTotal/s.row.length)/ s.fullWidth)* s.extra);
				cropTotal += crop[i];
			}

			// similar to finalize after shrink/grow, there can be a few  +- pixels that remain due to rounding
			var cropRemain = s.extra - cropTotal;
			if(cropRemain !== 0){
				if(cropRemain > 0){
					while(cropRemain > 0){
						for(var k = 0, l = crop.length; k < l; k += 1){
							// add pixels
							crop[k]++;
							cropRemain--;
							if(cropRemain === 0){
								break;
							}
						}
					}
				}else{
					while(cropRemain < 0){
						for(var m = 0, n = crop.length; m < n; m += 1){
							// remove pixels
							crop[m]--;
							cropRemain++;
							if(cropRemain === 0){
								break;
							}
						}
					}
				}
			}
			return crop;
		}; // end of getCrop

		// creates the actual container element that holds the link, image, captions, overlay, and all the wrapper divs
		// used by createGallery
		var createImageElement = function(item, rowlength, id){
			if(isNaN(item.newWidth) || !item.width){
				return false;
			}
			if(item.linkContainer !== undefined){
				$(item.linkContainer).remove();
				item.linkContainer = undefined;
			}
			if(typeof item[s.linkTitleField] === 'undefined'){
				item[s.linkTitleField] = '';
			}
			if(typeof item[s.imgAltField] === 'undefined'){
				item[s.imgAltField] = '';
			}
			if(typeof item[s.titleField] === 'undefined'){
				item[s.titleField] = '';
			}
			if(typeof item[s.captionField] === 'undefined'){
				item[s.captionField] = '';
			}
			if(typeof item['gallery'] === 'undefined'){
				item['gallery'] = '';
			}



			var extraClass = '';
			if(!item.carousel_data){
				item.carousel_data = '';
			}else{
				item.carousel_data = ' '+item.carousel_data+' ';
				extraClass = ' tiled-gallery-item';
			}
			if(item.extra_class){
				extraClass += ' '+item.extra_class;
			}
			item.off = '';
			item.url = !item.link ? item.url : item.url;
			var imageContainer = $('<div class="jig-imageContainer'+extraClass+'"/>'),
			overflow = $('<div class="jig-overflow"/>'),
			href = item.url,
			target = item.link_target ? item.link_target : '_self',
			linkClass = s.linkClass,
			linkRel = s.linkRel,
			titleFragment = '',
			altFragment = '',
			downloadLink,
			flickrLink,
			instagramLink;

			if(item.gallery !== ''){
				imageContainer.append(item.gallery['html']);
				linkRel = ' rel="'+item.gallery['rel']+'" ';
				imageContainer.addClass(item.gallery['lightbox_class']);
				imageContainer.attr('id',item.gallery['id']);
			}

			if(item.link){
				href = item.link;
				if(target !== 'video' && target !== 'videoplayer'){ // video here means video / iframe / another picture in the lightbox!
					if(target !== 'foobox'){
						linkClass = 'target="'+target+'" '+linkClass.replace('jig-link','jig-customLink');
					}else{
						linkClass = 'target="'+target+'" '+linkClass.replace('jig-link','');
					}
					linkRel = item.link_rel ? 'rel="'+item.link_rel+'"' : "";
				}else if(s.lightbox == 'magnific'){
					if(linkClass.indexOf('mfp-') == -1){
						linkClass = linkClass.replace('jig-link', 'jig-link mfp-iframe');
					}
				}
			}

			try{
				href = decodeURIComponent((href+'').replace(/\+/g, '%20'));
			}catch(exception){}

			titleFragment = item[s.linkTitleField];
			altFragment = item[s.imgAltField];

			if(item.download){
				if(s.downloadLink == 'yes'){
					downloadLink = (titleFragment.length !== 0 ? s.separatorCharacter : '')+item.download;
					titleFragment += downloadLink;
				}else if(s.downloadLink == 'alt'){
					downloadLink = (altFragment.length !== 0 ? s.separatorCharacter : '')+item.download;
					altFragment += downloadLink;
				}
			}
			if(item.lightbox_link){
				if(s.lightboxLink == 'yes'){
					lightboxLink = (titleFragment.length !== 0 ? s.separatorCharacter : '')+item.lightbox_link;
					titleFragment += lightboxLink;
				}else if(s.lightboxLink == 'alt'){
					lightboxLink = (altFragment.length !== 0 ? s.separatorCharacter : '')+item.lightbox_link;
					altFragment += lightboxLink;
				}
			}
			if(titleFragment !== ''){
				titleFragment = 'title="'+titleFragment+'" ';
			}
			if(altFragment !== ''){
				altFragment = 'alt="'+altFragment+'" ';
			}
			if(s.lightbox == 'new_tab'){
				linkClass = 'target="_blank" ';
			}
			var link = $('<a ' + linkClass + linkRel + s.linkAttribute + titleFragment + 'href="' + (s.lightbox != "links-off" ? href : "#") + '"/>'),
			img = $("<img "+altFragment+item.carousel_data+"/>");
			if(item.carousel_data){
				if(item.download){
					if(s.downloadLink == 'yes'){
						downloadLink = (img.attr('data-image-title').length !== 0 ? s.separatorCharacter : '')+$('<textarea />').html(item.download).val();
						img.attr('data-image-title', img.attr('data-image-title')+downloadLink);
					}else if(s.downloadLink == 'alt'){
						downloadLink = (img.attr('data-image-description').length !== 0 ? '<br />' : '')+$('<textarea />').html(item.download).val();
						img.attr('data-image-description', img.attr('data-image-description')+downloadLink);
					}
				}
			}
			if(typeof item.geo !== 'undefined'){
				img.attr('data-geo', item.geo);
			}

			overflow.css('opacity',0);
			if(id==rowlength-1){
				imageContainer.addClass('jig-last');
			}
			overflow.css("width", (item.containerWidth ? item.containerWidth : item.newWidth) + "px");
			overflow.css("height", (item.containerHeight ? item.containerHeight : item.newHeight) + "px");
			if(!item.photon){
				var itemurl = !item.thumbUrl ? item.url : item.thumbUrl,
					timthumbHeight = s.maxHeight,
					timthumbWidth, match, ext = '';

				match = /.*\.(jpe?g|gif|bmp|webp)/im.exec(itemurl);
				if (match !== null) {
					ext = "&f=."+match[1];
				}

				if(!s.aspectRatio && !s.randomizeWidth){
					if(s.retinaReady == 'yes'){
						timthumbHeight = Math.floor(timthumbHeight*s.devicePixelRatio);
						timthumbWidth = Math.floor(item.width*s.devicePixelRatio);
						img.attr("src", s.timthumb + "?src=" + itemurl + "&h=" + timthumbHeight + "&w=" + timthumbWidth + "&q=" + s.quality + s.cropZone + ext );
					}else{
						img.attr("src", s.timthumb + "?src=" + itemurl + "&h=" + timthumbHeight + "&q=" + s.quality + s.cropZone + ext );
					}
				}else{
					timthumbWidth = s.maxWidth;
					if(s.randomizeWidth !== ''){
						timthumbWidth = item.width;
					}
					if(s.retinaReady == 'yes'){
						timthumbHeight = Math.floor(timthumbHeight*s.devicePixelRatio);
						timthumbWidth = Math.floor(timthumbWidth*s.devicePixelRatio);
					}
					img.attr("src", s.timthumb + "?src=" + itemurl + "&h=" + timthumbHeight + "&w=" + timthumbWidth + "&q=" + s.quality + s.cropZone + ext );
				}
			}else{
				img.attr("src", decodeURIComponent((item.photon+'').replace(/\+/g, '%20')));
			}
			img.attr('width',item.newWidth).css("width", item.newWidth + "px");
			img.attr('height',item.newHeight).css("height", item.newHeight + "px");
			if(item.marLeft){
				img.css("margin-left", -item.marLeft + "px");
			}

			if((s.incompleteLastRow == 'center' || s.incompleteLastRow == 'flexible-center' || s.incompleteLastRow == 'flexible-match-center' || s.incompleteLastRow == 'match-center') && item.spaceLeft && item.spaceLeft > 0){

				imageContainer.css((s.readingDirection == "ltr" ? "margin-left" : "margin-right"), item.spaceLeft + "px");
				item.spaceLeft = 0;
			}

			img.css("margin-top", 0);
			link.append(img);
			if(s.overlay !== "off"){
				link.append('<div class="jig-overlay-wrapper"><div class="jig-overlay"></div></div>'+(s.overlayIcon === "on" ? '<div class="jig-overlay-icon-wrapper"><div class="jig-overlay-icon"></div></div>' : ''));
			}
			if(s.bordersTotal !== 0 || s.innerBorderWidth !== 0){
				link.append('<div class="jig-border"></div>');
			}
			if(s.caption !== "off"){
				if(item.gallery !== ''){
					item[s.titleField] = (typeof item.gallery[s.titleField] !== 'undefined' && item.gallery[s.titleField] !== '' && item.gallery[s.titleField] !== ' ') ? item.gallery[s.titleField] : '';
					item[s.captionField] = (typeof item.gallery[s.captionField] !== 'undefined' && item.gallery[s.captionField] !== '' && item.gallery[s.captionField] !== ' ') ? item.gallery[s.captionField] : '';
				}
				var captionContent = '';
				if(item[s.titleField] !== '' && item[s.titleField] !== ' '){
					captionContent += '<div class="jig-caption-title">'+strip_tags(item[s.titleField],'<br><br/><i><b><strong><italic><font><span>')+'</div>';
				}
				if(item[s.captionField] !== '' && item[s.captionField] !== ' '){
					captionContent += '<div class="jig-caption-description-wrapper"><div class="jig-caption-description'+(captionContent !== '' ? '' : ' jig-alone')+'">'+strip_tags(item[s.captionField],'<br><br/><i><b><strong><italic><font><span>')+'</div></div>';
				}
				if(captionContent !== ''){
					captionContent = '<div class="jig-caption-wrapper jig-cw-role-real"><div class="jig-caption">'+captionContent+'</div></div>';

					if(s.caption !== 'below'){
						link.append(captionContent);
					}else if(s.middleBorderWidth !== 0 && s.innerBorder == 'always' && s.middleBorder !== 'always'){
						imageContainer.append($(captionContent).width(overflow.width()-2*parseFloat(s.innerBorderWidth)));
					}else{
						imageContainer.append($(captionContent).css({'width':overflow.css("width")}));
					}
				}else if(s.caption == 'below'){
					captionContent = '<div class="jig-caption-wrapper"></div>';
					if(s.middleBorderWidth !== 0 && s.innerBorder == 'always' && s.middleBorder !== 'always'){
						imageContainer.append($(captionContent).width(overflow.width()-2*parseFloat(s.innerBorderWidth)));
					}else{
						imageContainer.append($(captionContent).css({'width':overflow.css("width")}));
					}
				}
			}

			
			overflow.append(link);
			if(item.carousel_data && item['caption']){
				overflow.append('<div class="tiled-gallery-caption">'+strip_tags(item['caption'])+'</div>');
			}
			imageContainer.prepend(overflow);

			s.element.find(".jig-clearfix").before(imageContainer);

			if(s.caption !== "off"){
				if(s.caption == 'below'){
					truncateCaptions(imageContainer);
				}else{
					if(s.specialFx == 'captions'){
						var roleEffect,
							roleReal = imageContainer.find('.jig-cw-role-real');

						if(s.captionMatchWidth == 'no' || s.captionMatchWidthForceNo){
							roleEffect = $(roleReal[0].cloneNode(false));
						}else{
							roleEffect = roleReal.clone();
						}
						roleEffect.removeClass('jig-cw-role-real').addClass('jig-cw-role-effect');
						if(s.overlay !== 'off'){
							imageContainer.find('.jig-overlay-wrapper').before(roleEffect);
						}else{
							roleReal.before(roleEffect);
						}
					}
				}
			}

			item.container = imageContainer;
			item.overflow = overflow;
			item.img = img;
			item.linkElement = link;
			return imageContainer;
		}; // end of createImageElement

		// updates an existing image container element with the newly calculated dimensions and margin data
		// used by createGallery
		// checks for pixastic neighbour
		var updateImageElement = function(item, rowlength, id){
			if(id==rowlength-1){
				item.container.addClass('jig-last');
			}else{
				item.container.removeClass('jig-last');
			}

			var overflow = item.overflow,
				img = item.img;
			overflow.css("width", (item.containerWidth ? item.containerWidth : item.newWidth) + "px");
			overflow.css("height", (item.containerHeight ? item.containerHeight : item.newHeight) + "px");
			img.attr('width',item.newWidth).css("width", item.newWidth + "px");
			img.attr('height',item.newHeight).css("height", item.newHeight + "px");
			if(item.marLeft){
				img.css("margin-left", -item.marLeft + "px");
			}else{
				img.css("margin-left","");
			}
			if(s.incompleteLastRow == 'center' || s.incompleteLastRow == 'flexible-center' || s.incompleteLastRow == 'flexible-match-center' || s.incompleteLastRow == 'match-center'){
				if(item.spaceLeft && item.spaceLeft > 0){
					item.container.css((s.readingDirection == "ltr" ? "margin-left" : "margin-right"), item.spaceLeft + "px");
					item.spaceLeft = 0;
				}else{
					item.container.css((s.readingDirection == "ltr" ? "margin-left" : "margin-right"), "");
				}
			}
			if(s.specialFx != "off"){
				var neighbour = img.siblings('.jig-pixastic');
				if(neighbour.length === 0 && s.specialFx == 'captions'){
					neighbour = img.siblings('.jig-cw-role-effect').find('.jig-pixastic');
				}
				checkForPixastic(neighbour, img, item.container);
			}




			if(s.caption == 'below'){
				if(s.middleBorderWidth !== 0 && s.innerBorder == 'always' && s.middleBorder !== 'always'){
					item.container.find('.jig-caption-wrapper').width(overflow.width()-2*parseFloat(s.innerBorderWidth));
				}else{
					item.container.find('.jig-caption-wrapper').css({'width':overflow.css("width")});
				}
				truncateCaptions(item.container,true);
			}


			if(s.caption !== "off" && s.verticalCenterCaptions !== 'off'){
				var clonedCaption = item.container.find('.jig-cw-role-real').clone();
				clonedCaption.find('.jig-caption').css('display','block');
				clonedCaption.appendTo(item.linkElement).css({'bottom':'auto','opacity':0.01}).css('top',Math.round((item.containerHeight ? item.containerHeight : item.newHeight)/2-clonedCaption.height()/2));
				var finalTopDistance = clonedCaption.css('top');
				clonedCaption.remove();
				item.container.find('.jig-caption-wrapper').css({'bottom':'auto'}).animate({'top':finalTopDistance},s.animSpeed);
			}

		}; // end of updateImageElement

		// create dummy item link for the lightboxes if hidden images are to be added too
		// used by createGallery
		var buildHiddenLink = function(item,base){
				
			if(typeof item[s.linkTitleField] === 'undefined'){
				item[s.linkTitleField] = '';
			}
			if(typeof item[s.imgAltField] === 'undefined'){
				item[s.imgAltField] = '';
			}
			if(typeof item[s.titleField] === 'undefined'){
				item[s.titleField] = '';
			}
			if(typeof item[s.captionField] === 'undefined'){
				item[s.captionField] = '';
			}

			item.off = '';

			var extraClass = '';
			if(!item.carousel_data){
				item.carousel_data = '';
			}else{
				item.carousel_data = ' '+item.carousel_data+' ';
				extraClass = " tiled-gallery-item";
			}
			if(item.extra_class){
				extraClass += " "+item.extra_class;
			}
			var href = item.url,
				titleFragment = '',
				altFragment = '',
				linkClass = s.hiddenLinkClass,
				target = item.link_target ? item.link_target : '_self',
				linkRel = s.linkRel,
				downloadLink,
				flickrLink,
				instagramLink;

			if(extraClass !== ''){
				linkClass = linkClass.replace('jig-hiddenLink"', 'jig-hiddenLink'+extraClass+'"');
			}

			if(item.link){
				href = item.link;
				if(target !== 'video' && target !== 'videoplayer'){ // video here means video / iframe / another picture in the lightbox!
					if(target !== 'foobox'){
						linkClass = 'target="'+target+'" '+linkClass.replace('jig-link','jig-customLink jig-hiddenLink');
					}else{
						linkClass = 'target="'+target+'" '+linkClass.replace('jig-link','jig-hiddenLink');
					}
					linkRel = item.link_rel ? 'rel="'+item.link_rel+'"' : "";
				}else if(s.lightbox == 'magnific'){
					if(linkClass.indexOf('mfp-') == -1){
						linkClass = linkClass.replace('jig-link', 'jig-link mfp-iframe');
					}
				}
			}

			try{
				href = decodeURIComponent((href+'').replace(/\+/g, '%20'));
			}catch(exception){}

			titleFragment = item[s.linkTitleField];
			altFragment = item[s.imgAltField];

			if(item.download){
				if(s.downloadLink == 'yes'){
					downloadLink = (titleFragment.length !== 0 ? s.separatorCharacter : '')+item.download;
					titleFragment += downloadLink;
				}else if(s.downloadLink == 'alt'){
					downloadLink = (altFragment.length !== 0 ? s.separatorCharacter : '')+item.download;
					altFragment += downloadLink;
				}
			}
			if(item.lightbox_link){
				if(s.lightboxLink == 'yes'){
					lightboxLink = (titleFragment.length !== 0 ? s.separatorCharacter : '')+item.lightbox_link;
					titleFragment += lightboxLink;
				}else if(s.lightboxLink == 'alt'){
					lightboxLink = (altFragment.length !== 0 ? s.separatorCharacter : '')+item.lightbox_link;
					altFragment += lightboxLink;
				}
			}

			if(titleFragment !== ''){
				titleFragment = 'title="'+titleFragment+'" ';
			}
			if(altFragment !== ''){
				altFragment = 'alt="'+altFragment+'" ';
			}

			var link = $('<a ' + linkClass + linkRel + s.linkAttribute + titleFragment + 'href="' + (s.lightbox != "links-off" ? href : "#") + '"/>'),
			img = $('<img class="jig-hiddenImg" '+altFragment+item.carousel_data+'/>');
			if(item.carousel_data){
				if(item.download){
					if(s.downloadLink == 'yes'){
						downloadLink = (img.attr('data-image-title').length !== 0 ? s.separatorCharacter : '')+$('<textarea />').html(item.download).val();
						img.attr('data-image-title', img.attr('data-image-title')+downloadLink);
					}else if(s.downloadLink == 'alt'){
						downloadLink = (img.attr('data-image-description').length !== 0 ? '<br />' : '')+$('<textarea />').html(item.download).val();
						img.attr('data-image-description', img.attr('data-image-description')+downloadLink);
					}
				}
			}

			img.attr("src", "data:image/gif;base64,R0lGODlhAQABAPABAP///wAAACH5BAEKAAAALAAAAAABAAEAAAICRAEAOw%3D%3D");
			link.append(img);
			item.linkContainer = link;
			if(s.currentHiddenLink !== undefined){
				$(s.currentHiddenLink).after(link);
			}else{
				s.element.find(".jig-clearfix").after(link);
			}
			s.currentHiddenLink = link;
			return;
		}; // end of buildHiddenLink

		// function that stops all special effects from being created or removes all existing special effects
		// it's necessary because it could be queued at the time of check (when the original image isn't loaded yet)
		// as the window can be resized at any time, the spoecialfx processes will need to start over with the new dimensions
		// so it'll wait for it then get rid of the specialfx neighbour and will replace it with a new one
		var checkForPixastic = function(neighbour, img, imageContainer){
			img.off('load');
			if(neighbour.length !== 0){
				neighbour.off('load').remove();
			}

			var imgClone = img.clone().addClass("jig-pixastic").insertAfter(img);

			imgClone.on('load', imgCloneOnLoad).each(function(){
				if(this.complete || (this.naturalWidth !== undefined && this.naturalWidth !== 0)){
					$(this).trigger("load");
				}
			});
		}; // end of checkForPixastic

		// Callback function when the image is cloned and to be processed by pixastic
		var imgCloneOnLoad = function(){
			if($(this).hasClass("jig-specialfx-complete") !== true){
				var par = $(this).parent();
				$(this).stop().css("display","block").css("opacity",1);
				if(s.specialFxOptions === ''){
					switch(s.specialFxType){
						case 'desaturate':
							Pixastic.process(this, "desaturate", {retinaReady:s.retinaReady, devicePixelRatio:s.devicePixelRatio, average:false}, pixasticDone);
						break;
						case 'blur':
							Pixastic.process(this, "blurfast", {retinaReady:s.retinaReady, devicePixelRatio:s.devicePixelRatio, amount:0.5}, pixasticDone);
						break;
						case 'glow':
							Pixastic.process(this, "glow", {retinaReady:s.retinaReady, devicePixelRatio:s.devicePixelRatio, amount:0.3,radius:0.2}, pixasticDone);
						break;
						case 'sepia':
							Pixastic.process(this, "sepia", {retinaReady:s.retinaReady, devicePixelRatio:s.devicePixelRatio}, pixasticDone);
						break;
						case 'laplace_dark':
							Pixastic.process(this, "laplace", {retinaReady:s.retinaReady, devicePixelRatio:s.devicePixelRatio, edgeStrength:2,invert:false,greyLevel:0}, pixasticDone);
						break;
						case 'laplace_light':
							Pixastic.process(this, "laplace", {retinaReady:s.retinaReady, devicePixelRatio:s.devicePixelRatio, edgeStrength:2,invert:true,greyLevel:0}, pixasticDone);
						break;
						default:
						break;
					}
				}else{
					var specialOption = pixasticOptionsObject(s.specialFxOptions, pixasticDone);
					switch(s.specialFxType){
						case 'desaturate':
							Pixastic.process(this, "desaturate", specialOption, pixasticDone);
						break;
						case 'blur':
							Pixastic.process(this, "blurfast", specialOption, pixasticDone);
						break;
						case 'glow':
							Pixastic.process(this, "glow", specialOption, pixasticDone);
						break;
						case 'sepia':
							Pixastic.process(this, "sepia", pixasticDone);
						break;
						case 'laplace_dark':
							Pixastic.process(this, "laplace", specialOption, pixasticDone);
						break;
						case 'laplace_light':
							Pixastic.process(this, "laplace", specialOption, pixasticDone);
						break;
						default:
						break;
					}
				}
				// Anything that has to do with the pixastic element is in the callback, pixasticDone
			}
			$(this).off("load");
		};

		// passed as callback for pixastic's process function, treats the result element
		var pixasticDone = function(pixasticElement,img){
			if(pixasticElement === false){
				// If not supported, clean up the clone of the image pixastic left
				var $img = $(img);
				$img.siblings('.jig-cw-role-effect').remove();
				$img.remove();
				return;
			}
			pixasticElement = $(pixasticElement);

			if(s.specialFx === "hovered"){
				pixasticElement.css("opacity",s.hiddenOpacity);
			}else {
				pixasticElement.css("opacity",s.specialFxBlend);
			}
			pixasticElement.addClass("jig-specialfx-complete");

			if(s.specialFx == 'captions'){
				var roleEffect = pixasticElement.closest('.jig-imageContainer').find('.jig-cw-role-effect');

				if(s.captionMatchWidth == 'no'){
					pixasticElement.appendTo(roleEffect);
				}else{
					try {
						var jpegUrl = pixasticElement[0].toDataURL("image/jpeg"),
							backgroundSize = pixasticElement.css('width')+" "+pixasticElement.css('height');
						pixasticElement.remove();

						roleEffect.find(".jig-caption-title").css({
							'background-image':'url('+jpegUrl+')',
							'background-size': backgroundSize

						});

						roleEffect.find(".jig-caption-description").css({
							'background-image':'url('+jpegUrl+')',
							'background-size': backgroundSize
						});
					} catch(e) {
						// It's not possible to copy the canvas data to caption element background
						// as there is no canvas. But since the effect is created by other means
						// use it the same way as s.captionMatchWidth = 'no'
						s.captionMatchWidthForceNo = true;
						var imageContainer = pixasticElement.closest('.jig-imageContainer');
						imageContainer.find('.jig-cw-role-effect .jig-caption').remove();
						imageContainer.find('.jig-caption-title').css({'display':'block'});
						pixasticElement.appendTo(roleEffect);
					}
				}

				// All align is handled by this
				alignCaptionSpecialEffect(getObjectsForCaptionSpecialEffect(roleEffect));
			}
		};

		// get objects at mouse event for alignCaptionSpecialEffect
		// saves some performance as it's not needed to run at every animationprogress
		var getObjectsForCaptionSpecialEffect = function(foundElements,fadeZeroHeight){

			var captionObjects = {};
			//captionObjects.roleEffect = foundElements.filter('.jig-cw-role-effect');
			captionObjects.roleEffect = foundElements.eq(0).closest('.jig-caption-wrapper');
			if(captionObjects.roleEffect.length === 0){
				return false;
			}

			if(s.captionMatchWidth == 'no' || s.captionMatchWidthForceNo){
				if(!captionObjects.roleEffect.hasClass('jig-cw-role-effect')){
					captionObjects.roleReal = captionObjects.roleEffect;
					captionObjects.roleEffect = captionObjects.roleEffect.siblings('.jig-cw-role-effect');
				}else{
					captionObjects.roleReal = captionObjects.roleEffect.siblings('.jig-cw-role-real');
				}

				if(fadeZeroHeight){
					captionObjects.fadeZeroHeight = fadeZeroHeight;
				}
				captionObjects.captionElement = captionObjects.roleReal.find(".jig-caption");
			}else{
				captionObjects.roleEffect = captionObjects.roleEffect.filter('.jig-cw-role-effect');
				if(!captionObjects.roleEffect.hasClass('jig-cw-role-effect')){
					return false;
				}
				captionObjects.img = captionObjects.roleEffect.siblings('img');
				captionObjects.captionTitle = captionObjects.roleEffect.find(".jig-caption-title");
				captionObjects.captionDescription = captionObjects.roleEffect.find(".jig-caption-description");
			}


			return captionObjects;
		};

		// adjust the background position for the special effect background of captions per animation progress
		var alignCaptionSpecialEffect = function(captionObjects){
			if(captionObjects === false){
				return false;
			}
			if(s.captionMatchWidth == 'no' || s.captionMatchWidthForceNo){
				var	roleRealPosition = captionObjects.roleReal.position(),
					roleRealHeight = captionObjects.roleReal.height();
				if(roleRealHeight !== 0){
					captionObjects.roleEffect.css({
						'height':captionObjects.captionElement.height()+'px'
					}).find(".jig-pixastic").css({
						'top':(-roleRealPosition.top)+'px'
					});
					if(captionObjects.fadeZeroHeight){
						captionObjects.roleEffect.css({
							'opacity' : captionObjects.captionElement.css('opacity')
						});
					}
				}else{
					captionObjects.roleEffect.find(".jig-pixastic").css({
						'top':(-roleRealPosition.top)+'px'
					});
				}


			}else{
				var roleEffectPosition = captionObjects.roleEffect.position(),
					titlePosition = captionObjects.captionTitle.position(),
					descriptionPosition = captionObjects.captionDescription.position(),
					imageMargin = parseInt(captionObjects.img.css('margin-left'), 10);
				if(titlePosition){
					captionObjects.captionTitle.css({
						'background-position':imageMargin-titlePosition.left+'px '+(-roleEffectPosition.top)+'px'
					});
				}
				if(descriptionPosition){
					captionObjects.captionDescription.css({
						'background-position':imageMargin+'px '+(-descriptionPosition.top-roleEffectPosition.top)+'px'
					});
				}
			}
		};

		// Truncates captions with dotdotdot plugin, used by the "below" caption style
		var truncateCaptions = function(imageContainer,refreshing){
			var captionWrapper = imageContainer.find(".jig-caption-wrapper"),
				captionTitle = captionWrapper.find(".jig-caption-title"),
				captionDescription = captionWrapper.find(".jig-caption-description"),
				comparisonHeight = s.captionHeight,
				lastCharacter = {'remove': [' ', '-', ',', ';', '.', '!', '?'], 'noEllipsis': []};

			if(refreshing){
				if(captionTitle.length !== 0 && captionTitle.triggerHandler("isTruncated.dot")){
					captionTitle.trigger("destroy.dot");
				}
				if(captionDescription.length !== 0 && captionDescription.triggerHandler("isTruncated.dot")){
					captionDescription.trigger("destroy.dot");
				}
			}
			// If there is a caption title taller than the allocated space
			if(captionTitle.length !== 0){
				if(captionTitle.outerHeight() > s.captionHeight){
					captionTitle.dotdotdot({
						'height': s.captionHeight-(captionTitle.outerHeight()-captionTitle.height()),
						'lastCharacter': lastCharacter
					}); // Truncate it with dotdotdot
				}
				comparisonHeight -= captionTitle.outerHeight();
			} // If it's not taller there is no title then it's okay

			// If there is caption description and is taller than the available height including the negative margin
			if(captionDescription.length !== 0 && captionDescription.outerHeight(true) > comparisonHeight){
				captionDescription.dotdotdot({
					// If there was a title already calculate the height available without the title
					'height': comparisonHeight-(captionDescription.outerHeight()-captionDescription.height()),
					'lastCharacter': lastCharacter
				}); // Truncate it with dotdotdot
			} // If it's not taller there is no description then it's okay

		};


		// checks if all images have been loaded, restarts when an error image is encountered
		s.errorChecked = false;
		var checkLoadResults = function(){
			if($('.jig-unloadable').length > 0){
				s.errorChecked = true;
				s.element.find('.jig-unloadable').remove();
				plugin.createGallery('errorCheck');
			}
			/*
			if(($('.jig-loaded').length === s.imagesShown || $('.jig-loaded').length+s.loadError === s.imagesShown)){
				if(s.loadError !== 0){
					s.loadSuccess = 0;
					s.loadError = 0;
					s.errorChecked = true;
					s.element.find('.jig-unloadable').remove();
					plugin.createGallery('errorCheck');
				}else{
					s.loadSuccess = 0;
					s.loadError = 0;
				}
			}
			*/
		};

		// creates an object for pixastic
		function pixasticOptionsObject(o){
			var finalObject = {};
			for(var i = 0, j = o.length; i < j; i += 1){
				var pairs = o[i].split(":");
				finalObject[pairs[0]] = pairs[1];
			}
			finalObject['retinaReady'] = s.retinaReady;
			finalObject['devicePixelRatio'] = s.devicePixelRatio;
			return finalObject;
		}

		// IE compatible hasOwnProperty
		function ownProp(o, prop){
			if ('hasOwnProperty' in o) {
				return o.hasOwnProperty(prop);
			} else {
				return Object.prototype.hasOwnProperty.call(o, prop);
			}
		}

		function strip_tags(input, allowed) {
			input = htmlspecialchars_decode(input);
			if(s.caption == 'below'){
				return input; // Allowing links or anything when the caption is below the image (still need to decode)
			}
			allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
			var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
				commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
			return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
				return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
			});
		}
		function htmlspecialchars_decode(string, quote_style) {
			if(string === undefined){
				return '';
			}
			var optTemp = 0,
				noquotes = false;
			if (typeof quote_style === 'undefined') {
				quote_style = 2;
			}
			string = string.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
			var OPTS = {
				'ENT_NOQUOTES': 0,
				'ENT_HTML_QUOTE_SINGLE': 1,
				'ENT_HTML_QUOTE_DOUBLE': 2,
				'ENT_COMPAT': 2,
				'ENT_QUOTES': 3,
				'ENT_IGNORE': 4
			};
			if (quote_style === 0) {
				noquotes = true;
			}
			if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
				quote_style = [].concat(quote_style);
				for (var i = 0, j = quote_style.length; i < j; i += 1) {
					// Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
					if (OPTS[quote_style[i]] === 0) {
						noquotes = true;
					} else if (OPTS[quote_style[i]]) {
						optTemp = optTemp | OPTS[quote_style[i]];
					}
				}
				quote_style = optTemp;
			}
			if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
				string = string.replace(/&#0*39;/g, "'"); // PHP doesn't currently escape if more than one 0, but it should
				// string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP
			}
			if (!noquotes) {
				string = string.replace(/&quot;/g, '"');
			}
			// Put this in last place to avoid escape being double-decoded
			string = string.replace(/&amp;/g, '&');

			return string;
		}


		// loads more images into the grid, on demand
		function loadMore(event){
			if(typeof event !== 'undefined'){
				event.stopPropagation();
			}
			var loaded = s.element.find('.jig-overflow > a.jig-loaded').length,
				toBeLoaded = s.element.find('.jig-imageContainer .jig-overflow > a').length;
				// Some tolerance of unloadable images when error checking is off
				if(s.errorChecking == 'no'){
					toBeLoaded -= 5;
				}
				if(typeof s.loadCheckTimeout !== 'undefined'){
					clearTimeout(s.loadCheckTimeout);
				}
				if(loaded < toBeLoaded){
					s.loadCheckTimeout = setTimeout(function(){
						loadMore();
						return;
					},500);
					s.loadMoreButton.animate({opacity: 0.5}, 300);
				}else{
					s.loadMoreButton.animate({opacity: 1}, 300);

					s.limit += s.originalLimit;
					s.loadMoreCounter++;
					if(s.loadMore == 'once'){
						s.limit = s.allItems.length;
					}
					plugin.createGallery();
				}
		}

		plugin.init();

	}; // end of 'class'

	// sets up the plugin to be used conveniently and makes later access possible
	$.fn.justifiedImageGrid = function(options){
		return this.each(function(){
			if(undefined === $(this).data('justifiedImageGrid')){
				var plugin = new $.justifiedImageGrid(this, options);
				$(this).data('justifiedImageGrid', plugin);
			}
		});
	};


	/*
	* hoverFlow - A Solution to Animation Queue Buildup in jQuery
	* Version 1.00
	*
	* Copyright (c) 2009 Ralf Stoltze, http://www.2meter3.de/code/hoverFlow/
	* Dual-licensed under the MIT and GPL licenses.
	* http://www.opensource.org/licenses/mit-license.php
	* http://www.gnu.org/licenses/gpl.html
	*/

	$.fn.hoverFlow = function(type, prop, speed, easing, callback) {
		//easing = 'linear';
		// only allow hover events
		if ($.inArray(type, ['mouseover', 'mouseenter', 'mouseout', 'mouseleave']) == -1) {
			return this;
		}

		// build animation options object from arguments
		// based on internal speed function from jQuery core
		var opt = typeof speed === 'object' ? speed : {
			complete: callback || !callback && easing || $.isFunction(speed) && speed,
			duration: speed,
			easing: callback && easing || easing && !$.isFunction(easing) && easing
		};

		// run immediately
		opt.queue = false;

		// wrap original callback and add dequeue
		var origCallback = opt.complete;
		opt.complete = function() {
			// execute next function in queue
			$(this).dequeue();
			// execute original callback
			if ($.isFunction(origCallback)) {
				origCallback.call(this);
			}
		};

		// keep the chain intact
		return this.each(function() {
			var $this = $(this);

			// set flag when mouse is over element
			if (type == 'mouseover' || type == 'mouseenter') {
				$this.data('jQuery.hoverFlow', true);
			} else {
				$this.removeData('jQuery.hoverFlow');
			}

			// enqueue function
			$this.queue(function() {
				// check mouse position at runtime
				var condition = (type == 'mouseover' || type == 'mouseenter') ?
					// read: true if mouse is over element
					$this.data('jQuery.hoverFlow') !== undefined :
					// read: true if mouse is _not_ over element
					$this.data('jQuery.hoverFlow') === undefined;

				// only execute animation if condition is met, which is:
				// - only run mouseover animation if mouse _is_ currently over the element
				// - only run mouseout animation if the mouse is currently _not_ over the element
				if(condition) {
					$this.animate(prop, opt);
				// else, clear queue, since there's nothing more to do
			} else {
				$this.queue([]);
			}
		});

		});
	};


	/*!
	 * jquery.tagcloud.js
	 * A Simple Tag Cloud Plugin for JQuery
	 *
	 * https://github.com/addywaddy/jquery.tagcloud.js
	 * created by Adam Groves
	 * LICENSE: https://github.com/addywaddy/jquery.tagcloud.js/blob/master/LICENSE
	 */
	
	(function($) {

		/*global jQuery*/
		"use strict";
		
		var compareWeights = function(a, b)
		{
			return a - b;
		};
		
		// Converts hex to an RGB array
		var toRGB = function(code) {
			if (code.length === 4) {
				code = code.replace(/(\w)(\w)(\w)/gi, "$1$1$2$2$3$3");
			}
			var hex = /(\w{2})(\w{2})(\w{2})/.exec(code);
			return [parseInt(hex[1], 16), parseInt(hex[2], 16), parseInt(hex[3], 16)];
		};
		
		// Converts an RGB array to hex
		var toHex = function(ary) {
			return "#" + jQuery.map(ary, function(i) {
				var hex =  i.toString(16);
				hex = (hex.length === 1) ? "0" + hex : hex;
				return hex;
			}).join("");
		};
		
		var colorIncrement = function(color, range) {
			return jQuery.map(toRGB(color.end), function(n, i) {
				return (n - toRGB(color.start)[i])/range;
			});
		};
		
		var tagColor = function(color, increment, weighting) {
			var rgb = jQuery.map(toRGB(color.start), function(n, i) {
				var ref = Math.round(n + (increment[i] * weighting));
				if (ref > 255) {
					ref = 255;
				} else {
					if (ref < 0) {
						ref = 0;
					}
				}
				return ref;
			});
			return toHex(rgb);
		};
		
		$.fn.tagcloud = function(options) {
		
			var opts = $.extend({}, $.fn.tagcloud.defaults, options);
			var tagWeights = this.map(function(){
				return $(this).attr("rel");
			});
			tagWeights = jQuery.makeArray(tagWeights).sort(compareWeights);
			var lowest = tagWeights[0];
			var highest = tagWeights.pop();
			var range = highest - lowest;
			if(range === 0) {range = 1;}
			// Sizes
			var fontIncr, colorIncr;
			if (opts.size) {
				fontIncr = (opts.size.end - opts.size.start)/range;
			}
			// Colors
			if (opts.color) {
				colorIncr = colorIncrement (opts.color, range);
			}
			return this.each(function() {
				var weighting = $(this).attr("rel") - lowest;
				if (opts.size) {
					$(this).css({"font-size": opts.size.start + (weighting * fontIncr) + opts.size.unit});
				}
				if (opts.color) {
					$(this).css({"color": tagColor(opts.color, colorIncr, weighting)});
				}
			});
		};
		
		$.fn.tagcloud.defaults = {
			size: {start: 14, end: 18, unit: "pt"}
		};
	
	})(jQuery);

	/**
	 * Used for version test cases.
	 *
	 * @param {string} left A string containing the version that will become
	 *        the left hand operand.
	 * @param {string} oper The comparison operator to test against. By
	 *        default, the "==" operator will be used.
	 * @param {string} right A string containing the version that will
	 *        become the right hand operand. By default, the current jQuery
	 *        version will be used.
	 *
	 * @return {boolean} Returns the evaluation of the expression, either
	 *         true or false.
	 */
	$.JIGminVersion = function(min,displayError) {
		if(min){
			var current = $().jquery,
				m = min.split('.'),
				c = current.split('.'),
				mi = [],
				cu = [],
				met = false;
			mi[0] = !isNaN(parseInt(m[0],10)) ? parseInt(m[0],10) : 0;
			mi[1] = !isNaN(parseInt(m[1],10)) ? parseInt(m[1],10) : 0;
			mi[2] = !isNaN(parseInt(m[2],10)) ? parseInt(m[2],10) : 0;
			mi[3] = !isNaN(parseInt(m[3],10)) ? parseInt(m[3],10) : 0;
			cu[0] = !isNaN(parseInt(c[0],10)) ? parseInt(c[0],10) : 0;
			cu[1] = !isNaN(parseInt(c[1],10)) ? parseInt(c[1],10) : 0;
			cu[2] = !isNaN(parseInt(c[2],10)) ? parseInt(c[2],10) : 0;
			cu[3] = !isNaN(parseInt(c[3],10)) ? parseInt(c[3],10) : 0;
			for(var i = 0; i<4; i++){
				if(mi[i] <= cu[i]){
					met = true;
					if(mi[i] == cu[i]){
						continue;
					}else{
						break;
					}
				}else{
					met = false;
					break;
				}
			}
			if(met === true){
				return true;
			}else{
				if(displayError === true){
					$('.justified-image-grid').html('<span style=\"color:red;font-weight:bold\">Your jQuery version ('+$().jquery+') is old, this plugin needs at least 1.7, please go to the plugin settings and choose another jQuery source. If this does not work then your theme or a plugin is not using WordPress best practices and forces the loading of an old version. In that case contact the author of Justified Image Grid.</span>');
				}else{
					return false;
				}
			}
	
		}
		return false;
	};
}
(function (){
	loadJustifiedImageGrid(jQuery); // adds ability to re-add to the jQuery object if a newly loaded jQuery 'reset' it
})();