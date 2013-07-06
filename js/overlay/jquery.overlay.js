// only run code if in iframe of Microthemer UI
try { window.parent.document; 
if (window.parent.TvrSharedVars) {
	(function($){
		$.fn.extend({	
			add_hover_overlay: function() {
				var defaults = {};
				var options = $.extend(defaults, options);
				return this.each(function() {
					$(this).mouseenter(
						function() {
							// only add mouse event lister if element doesn't already have a static overlay
							if (!$(this).hasClass('tvr-static-overlay')) {
								// don't apply overlay if highlighting has been disabled
								if (window.parent.TvrSharedVars.highlightEnabled) { 
									$(this).addClass('tvr-hover-overlay').add_overlay();
								}
							}
						}
					);
				});
			},
			
			add_overlay: function(options) {
				var sides = [ 'left', 'top', 'right', 'bottom' ];
				var defaults = { padding: true, margin: true, border: true, content: true };
				var options = $.extend(defaults, options);	
				return this.each(function() {
					var j = window.TvrExportVars.overlayCount;
					var _thisEl = this;
					var $_thisEl = $(this);
					// don't apply more than one overlay to an element or invisible elements
					if (!$(this).hasClass('tvr-element-overlay') && $(this).is(':visible')){
						$(this).addClass('tvr-element-overlay');
						// don't add static class if hover class has been added
						if (!$(this).hasClass('tvr-hover-overlay')) { 
							$(this).addClass('tvr-static-overlay');
						}
						// var compStyle = document.defaultView.getComputedStyle(this, null); // fails in ie7...
						var props = {};
						var fs = parseInt($_thisEl.css('font-size'));
						// get top left position
						var offset = $(this).offset();
						offset.top = offset.top + parseInt($_thisEl.css('border-top-width')); // needed for 
						offset.left = offset.left + parseInt($_thisEl.css('border-left-width'));
						// if highlighting has been disabled, display:none
						if (!window.parent.TvrSharedVars.highlightEnabled) {
							var displayMode = 'display:none;';
						} else {
							var displayMode = '';
						}
						$('body').prepend('<div id="over-cont-'+j+'" class="tvr-overlay tvr-container-overlay" style="width:'+$(this).innerWidth()+'px;height:'+$(this).innerHeight()+'px;'+displayMode+'"></div>');
						// save reference to container element
						var $contElement = $('#over-cont-'+j);
						// check if mouseout event needs to remove the overlay
						if ($(this).hasClass('tvr-hover-overlay')) {
							$contElement.mouseleave(function() {
							  $contElement.remove();
							  $(_thisEl).removeClass('tvr-element-overlay tvr-hover-overlay')
							});
						} 
						// offset with jquery - this works too: top:'+offset.top+'px;left:'+offset.left+'px;
						$contElement.offset({ top: offset.top, left: offset.left });
						// Margin overlay should have negative margins identical to the element's margins
						if (options.margin){
							$contElement.append('<div class="tvr-overlay tvr-margin-overlay"></div>');
							for (s in sides){
								var length = 0 - parseInt($_thisEl.css('margin-' + sides[s]))
									- parseInt($_thisEl.css('border-' + sides[s] + '-width'));
								props[sides[s]] = (length / fs) + 'em';
							}
							$contElement.find('.tvr-margin-overlay').css(props);
						}
						// Border overlay
						if (options.border){
							$contElement.append('<div class="tvr-overlay tvr-border-overlay"></div>');
							$contElement.find('.tvr-border-overlay').css({
								top:     (0 - parseInt($_thisEl.css('border-top-width')) / fs) + 'em',
								right:   (0 - parseInt($_thisEl.css('border-right-width')) / fs) + 'em',
								bottom:  (0 - parseInt($_thisEl.css('border-bottom-width')) / fs) + 'em',
								left:    (0 - parseInt($_thisEl.css('border-left-width')) / fs) + 'em'
							});
						}
						// Padding overlay is easy - it just sits inside the element
						if (options.padding){
							$contElement.append('<div class="tvr-overlay tvr-padding-overlay"></div>');
						}
						// Content overlay
						if (options.content){
							$contElement.append('<div class="tvr-overlay tvr-content-overlay"></div>');
							props = {};
							for (s in sides){
								props[sides[s]] = (parseInt($_thisEl.css('padding-' + sides[s])) / fs) + 'em';
							}
							$contElement.find('.tvr-content-overlay').css(props);
						}
						window.TvrExportVars.overlayCount++;
					}
				});	
			}
		});
	})(jQuery);
	
	// for easy communication with iframes & parent: http://web-developer.com/forum/showthread.php?t=250600
	
	window.TvrExportVars = {}
	jQuery(document).ready(function($){
		
		// if visual mode
		if (window.parent.TvrSharedVars.context == 'visual') {
			// update visual view prog indicator if page reloaded
			if (window.parent.TvrSharedVars.progCount > 0) {
				window.parent.TvrSharedVars.update_prog_indicator('minus');
			}
			$('body').attr('title', 'Double-click an element to create editing options for it').addClass('tvr-crosshair');
			// create object for storing css selector suggestions
			var TvrSugCSS = {};
			// Main Frontend UI Object
			var TvrUiF = {
				// remove any artificial microthemer classes and extra spaces
				clean_classes: function(classString) {
					classString = classString.replace('  ', ' ')
					.replace('tvr-crosshair', '').replace('  ', ' ')
					.replace('tvr-element-overlay', '').replace('  ', ' ')
					.replace('tvr-static-overlay', '').replace('  ', ' ');
					return classString.replace(/^\s+|\s+$/g, ""); // removes trailing white space
				},
				// build selector suggestion list
				build_selector_suggestions: function ($el) {
					// reset object
					for (var member in TvrSugCSS) delete TvrSugCSS[member];
					// provide options for links in every state, input with types
					var TvrExcep = {};
					TvrExcep['a'] = {};
					TvrExcep['a'][':visited'] = 'visited link';
					TvrExcep['a'][':hover'] = 'hover state link';
					TvrExcep['a'][':active'] = 'active state link';
					
					// el type
					var el = $el.prop('tagName').toLowerCase();
					if (el == 'input') {
						var inputType = $el.attr('type');
					}
					var elID = $el.attr('id');
					var elClass = $el.attr('class');
					// remove any artificial microthemer classes
					if (elClass) {
						elClass = TvrUiF.clean_classes(elClass);
					}
					var $par = $el.parent().not('html'); // for checking parents
					var ogCount = 0;
					var optValue = '';
					var defaultSel = [];
					var k = -1;
					var htmlString = '&#60;'+el;
					if (el == 'input') {
						htmlString+= ' type=&#34;'+inputType+'&#34;';
					}
					// all els of particular type
					var optGroup = 'Element Type Targeting (very broad)';
					TvrSugCSS[optGroup] = {};
					TvrSugCSS[optGroup][el] = 'all <'+el+'>s';
					if (el == 'input') {
						TvrSugCSS[optGroup][el+'[type=&#34;'+inputType+'&#34;]'] = '<'+el+'>s of type: "'+inputType+'"';
					} else if (el == 'a') {
						// show link state options
						for (key in TvrExcep[el]) {
							TvrSugCSS[optGroup][el+key] = '<'+el+'>s ('+TvrExcep[el][key]+')';
						}
					}
					++ogCount;
					// el classes
					if (elClass) {
						htmlString+= ' class="'+elClass+'"';
						optGroup = 'Element With Class Targeting';
						TvrSugCSS[optGroup] = {};
						var elClassArray = elClass.split(' ');
						for (var i = 0; i < elClassArray.length; i++) {
							// populate optgroup
							optValue = el+'.'+elClassArray[i];
							TvrSugCSS[optGroup][optValue] = '<'+el+'>s with a class of: "'+elClassArray[i]+'"';
							// show link state options if a
							if (el == 'a') {
								for (key in TvrExcep[el]) {
									TvrSugCSS[optGroup][optValue+key] = '<'+el+'>s with a class of: "'
									+elClassArray[i]+' ('+TvrExcep[el][key]+')';
								}
							}
							// candidate for targeting
							if (i == 0) {
								defaultSel[++k] = optValue;
							}
						}
						var hasClasses = true;
						var optimumClass = true;
						++ogCount;
					}
					// parent with classes
					if ($par.prop('tagName')) {
						var $parWithClass = TvrUiF.find_parent_attributes($par, 'class');
						if ($parWithClass) {
							optGroup = 'Parent With Class Targeting';
							TvrSugCSS[optGroup] = {};
							var parWithClass = $parWithClass.prop('tagName').toLowerCase();
							var parClass = TvrUiF.clean_classes($parWithClass.attr('class'));
							var parClassArray = parClass.split(' ');
							for (var i = 0; i < parClassArray.length; i++) {
								optValue = parWithClass+'.'+parClassArray[i]+' '+el;
								TvrSugCSS[optGroup][optValue] = '<'+el+'>s with parent <'+parWithClass+'>s that have a class of "'+parClassArray[i]+'"';
								// show link state options if a
								if (el == 'a') {
									for (key in TvrExcep[el]) {
										TvrSugCSS[optGroup][optValue+key] = '<'+el+'>s with parent <'+parWithClass
										+'>s that have a class of "'+parClassArray[i]+'" ('+TvrExcep[el][key]+')';
									}
								}
								// candidate for targeting
								if (!optimumClass && i == 0) {
									defaultSel[++k] = optValue;
								}
							}
							var parentHasClasses = true;
							++ogCount;
						}
					}
					// both parent and element have class
					if (hasClasses && parentHasClasses) {
						optGroup = 'Parent & Element With Class Targeting';
						TvrSugCSS[optGroup] = {};
						for (var i = 0; i < parClassArray.length; i++) {
							for (var j = 0; j < elClassArray.length; j++) {
								optValue = parWithClass+'.'+parClassArray[i]+' '+el+'.'+elClassArray[j];
								TvrSugCSS[optGroup][optValue] = '<'+el+'>s that have a class of "'+elClassArray[j]+'" with parent <'
								+parWithClass+'>s that have a class of "'+parClassArray[i]+'"';
								// show link state options if a
								if (el == 'a') {
									for (key in TvrExcep[el]) {
										TvrSugCSS[optGroup][optValue+key] = '<'+el+'>s that have a class of "'+elClassArray[j]+'" with parent <'
										+parWithClass+'>s that have a class of "'+parClassArray[i]+'" ('+TvrExcep[el][key]+')';
									}
								}
								// candidate for targeting
								if (i == 0) {
									defaultSel[++k] = optValue;
								}
							}
						}
						++ogCount;
					}
					// el id
					if (window.parent.TvrSharedVars.hasValue(elID)) {
						htmlString+= ' id="'+elID+'"';
						optGroup = 'Element ID Targeting';
						TvrSugCSS[optGroup] = {};
						optValue = el+'#'+elID;
						TvrSugCSS[optGroup][optValue] = '<'+el+'> with the unique id of "'+elID+'"';
						// show link state options if a
						if (el == 'a') {
							for (key in TvrExcep[el]) {
								TvrSugCSS[optGroup][optValue+key] = '<'+el+'> with the unique id of "'+elID+'" ('+TvrExcep[el][key]+')';
							}
						}
						// candidate for targeting
						defaultSel[++k] = optValue;
						var hasID = true;
						var optimumID = true;
						++ogCount;
					}
					// parent with id
					if ($par.prop('tagName')) {
						var $parWithID = TvrUiF.find_parent_attributes($par, 'id');
						if ($parWithID) {
							optGroup = 'Parent With ID Targeting';
							TvrSugCSS[optGroup] = {};
							var parWithID = $parWithID.prop('tagName').toLowerCase();
							var parID = $parWithID.attr('id');
							optValue = parWithID+'#'+parID+' '+el;
							TvrSugCSS[optGroup][optValue] = '<'+el+'>s with parent <'+parWithID+'> that has a unique id of "'+parID+'"';
							// show link state options if a
							if (el == 'a') {
								for (key in TvrExcep[el]) {
									TvrSugCSS[optGroup][optValue+key] = '<'+el+'>s with parent <'+parWithID+'> that has a unique id of "'+parID+'" ('+TvrExcep[el][key]+')';
								}
							}
							// candidate for targeting
							if (!optimumID) {
								defaultSel[++k] = optValue;
							}
							var parentHasID = true;
							++ogCount;
						}
					}
					// parent has id element has class
					if (hasClasses && parentHasID) {
						optGroup = 'Parent With ID & Element With Class Targeting';
						TvrSugCSS[optGroup] = {};
						for (var j = 0; j < elClassArray.length; j++) {
							optValue = parWithID+'#'+parID+' '+el+'.'+elClassArray[j];
							TvrSugCSS[optGroup][optValue] = '<'+el+'>s that have a class of "'+elClassArray[j]+'" with parent <'
							+parWithID+'> that has a unique id of "'+parID+'"';
							// show link state options if a
							if (el == 'a') {
								for (key in TvrExcep[el]) {
									TvrSugCSS[optGroup][optValue+key] = '<'+el+'>s that have a class of "'+elClassArray[j]+'" with parent <'
									+parWithID+'> that has a unique id of "'+parID+'" ('+TvrExcep[el][key]+')';
								}
							}
							// candidate for targeting
							if (j == 0) {
								defaultSel[++k] = optValue;
							}
						}
						++ogCount;
					}
					// both parent and element have an id
					if (hasID && parentHasID) {
						optGroup = 'Parent & Element With ID Targeting';
						TvrSugCSS[optGroup] = {};
						optValue = parWithID+'#'+parID+' '+el+'#'+elID;
						TvrSugCSS[optGroup][optValue] = '<'+el+'> that has a unique id of "'+elID+'" with parent <'+parWithID+'> that has a unique id of "'
						+parID+'"';
						// show link state options if a
						if (el == 'a') {
							for (key in TvrExcep[el]) {
								TvrSugCSS[optGroup][optValue+key] = '<'+el+'> that has a unique id of "'+elID+'" with parent <'+parWithID
								+'> that has a unique id of "'+parID+'" ('+TvrExcep[el][key]+')';
							}
						}
						// candidate for targeting
						defaultSel[++k] = optValue;
						++ogCount;
					}
					// body class targeting
					//... maybe add later
					
					// save default CSS selector in shared var
					
					if (k > -1) {
						window.parent.TvrSharedVars.intelli_css = defaultSel[k];
					} else {
						window.parent.TvrSharedVars.intelli_css = el;
					}
					// save num optGroups
					TvrSugCSS['Intelli'] = {};
					TvrSugCSS['Intelli']['ogCount'] = ogCount;
					// save values for target refining controls
					var $firstChild = $el.children().not('.tvr-container-overlay, script').filter(':first');
					var $prevEl = $el.prevAll().not('.tvr-container-overlay, script, head').filter(':first');
					var $nextEl = $el.nextAll().not('.tvr-container-overlay, script').filter(':first');
					// note $par will never be an overlay as we won't get inside it in the first place
					TvrSugCSS['Intelli']['parent'] = ($par.prop('tagName')) ? $par : false;
					TvrSugCSS['Intelli']['child'] = ($firstChild.prop('tagName')) ? $firstChild : false;
					TvrSugCSS['Intelli']['prev'] = ($prevEl.prop('tagName')) ? $prevEl : false;
					TvrSugCSS['Intelli']['next'] = ($nextEl.prop('tagName')) ? $nextEl : false;
					
					/* Conisder tooltip with firebug-like html display 
					function htmlEscape(str) {
					return String(str)
								.replace(/&/g, '&amp;')
								.replace(/"/g, '&quot;')
								.replace(/'/g, '&#39;')
								.replace(/</g, '&lt;')
								.replace(/>/g, '&gt;');
					}
					var hoverhtmlinspection = '+htmlEscape($el.html().substring(0, 500))+';
					*/
					
					// finish off html string and save
					if (
						el != 'area' &&
						el != 'base' &&
						el != 'br' &&
						el != 'col' &&
						el != 'command' &&
						el != 'embed' &&
						el != 'hr' &&
						el != 'img' &&
						el != 'input' &&
						el != 'meta' &&
						el != 'param' &&
						el != 'source'
						) {
							// beta - get inner html markup for title
							// alert($el.html());
							htmlString+= '&#62;...&#60;/'+el+'&#62;';
					} else {
						htmlString+= ' /&#62;';
					}
					TvrSugCSS['Intelli']['htmlString'] = htmlString;
					// change any existing highlighting
					TvrUiF.switch_highlight('intelli');
					// enable highlighting if not already
					if (!window.parent.TvrSharedVars.highlightEnabled) {
						window.parent.TvrSharedVars.toggle_highlighting();
					}
					// make object available to parent
					window.parent.TvrSharedVars.TvrSugCSS = TvrSugCSS;
					// trigger intelli function in parent window
					if (!window.parent.TvrSharedVars.intelli) {
						window.parent.TvrSharedVars.display_visual_add_sec_sel('Selector', true);
					} 
					// or just update the wizard controls
					else {
						window.parent.TvrSharedVars.update_selector_wizard();
					}
					
					
					/*var str = '';
					for (key in TvrSugCSS) {
						for (key2 in TvrSugCSS[key]) {
							str+= key + ": " + key2+ ": " + TvrSugCSS[key][key2] +"\n";
						}
					}
					alert(str);*/
				},
				// find parent width class/if
				find_parent_attributes: function ($par, type) {
					// check if the attribute still has a value after removing artificial tvr classes
					if ($par.attr(type) && TvrUiF.clean_classes($par.attr(type)) ) {
						return $par; 
					} else {
						$par = $par.parent().not('html');
						// alert($par.prop('tagName').toLowerCase());
						if ($par.prop('tagName')) {
							return TvrUiF.find_parent_attributes($par, type);
						} else {
							return false;
						}
					}	
				},
				// used to tease apart selectors with :hover, :active and :visited
				allocateOverlayMode: function (con) {
					// ensure that the CSS selector doesn't have any custom encoding
					if (con == 'reg') {
						var curSelCss = window.parent.TvrSharedVars.custom_to_non_escaped(window.parent.TvrSharedVars.curSelCSS);
					} else if (con == 'intelli') {
						var curSelCss = window.parent.TvrSharedVars.custom_to_non_escaped(window.parent.TvrSharedVars.intelli_css);
					}
					// use global counter to ensure unqiue ids when add_overlay() is called multiple times
					window.TvrExportVars.overlayCount = 0;
					// remove css pseudo selectors that break jquery (:link is fine)
					curSelCss = curSelCss.replace(/:hover/g, '').replace(/:active/g, '').replace(/:visited/g, '') 
					try{
						$(curSelCss).add_overlay();  // ie7 fails here: Exception thrown and not caught = bug
					} catch(exceptionObject){ 
						// empty catch fixes ie7 bug 
					}
				},
				// switch highlight function
				switch_highlight: function(con) {
					// remove existing overlays
					$('.tvr-container-overlay').remove();
					// reset highlight classes
					$('.tvr-element-overlay').removeClass('tvr-element-overlay tvr-static-overlay');
					// apply overlay to new element in focus (after teasing apart selectors with :hover)
					TvrUiF.allocateOverlayMode(con);
				}, 
				showOverlays: function() {
					$('.tvr-container-overlay').show();
				},
				hideOverlays: function() {
					$('.tvr-container-overlay').hide();
				},
				/* prevent default single-click behaviour unless a 2nd click doesn't happen within 300ms */
				singleClick: function($clicked, e, tagName, tagType, $par, parentLink) {
					e.preventDefault();
					setTimeout(function() {
					  var dblclick = parseInt($clicked.data('double'), 10);
					  if (dblclick > 0) {
						$clicked.data('double', dblclick-1);
						if (parentLink) {
							return false;
						}
					  } else {
						if (tagName == 'a') {
							window.location = $clicked.attr("href");
						} else if (parentLink) {
							window.location = $par.attr("href");
						} else if (tagType == 'submit') {
							var $form = $clicked.parents('form');
							if ($form) {
								$form.trigger('submit');
							}
						} 
					  }
					}, 300);
				}   
			}
			
			// Bind single click event to body - 
			// http://stackoverflow.com/questions/4153376/jquery-preventdefault-the-linked-anchor-if-double-clicked
			// http://stackoverflow.com/questions/1067464/need-to-cancel-click-mouseup-events-when-double-click-event-detected
			$('body').click(function(e) {
				var $clicked = $(e.target);
				// if clicked has link parent we need to prevent default on parent link
				var $par = $clicked.parent();
				if ($par.prop('tagName') && $par.prop('tagName').toLowerCase() == 'a') {
					parentLink = true;
				} else {
					parentLink = false;
				}
				var tagName = $clicked.prop('tagName').toLowerCase();
				var tagType = $clicked.attr('type');
				if (tagType) {
					tagType = tagType.toLowerCase();
				}
				if (tagName == 'a' || parentLink || tagType == 'submit') {
					TvrUiF.singleClick($clicked, e, tagName, tagType, $par, parentLink);
				}
			}).dblclick(function(e) {
				e.preventDefault();   
				var $clicked = $(e.target);
				$clicked.data('double', 2);
				if (window.parent.TvrSharedVars.totalSecs < 1) {
					alert('Please create a section for adding selectors to first');
					window.parent.TvrSharedVars.display_visual_add_sec_sel('Section');
					return false;
				}
				if ($clicked.hasClass('tvr-overlay')) {
					alert('Please disable highlighting (light bulb icon) to create editing options for the currently highlighted element');
					return false;
				}
				var sugObject = TvrUiF.build_selector_suggestions($clicked);	
			});
			
			// make various functions available to parent window
			window.TvrExportVars.switch_highlight = TvrUiF.switch_highlight;
			window.TvrExportVars.showOverlays = TvrUiF.showOverlays;
			window.TvrExportVars.hideOverlays = TvrUiF.hideOverlays;
			window.TvrExportVars.build_selector_suggestions = TvrUiF.build_selector_suggestions;
			
			// need to make sure images are loaded before calling add_overlay()
			$(window).load(function() {	
				// still in intelli selector mode
				if (window.parent.TvrSharedVars.intelli) {
					// make shared var available to parent again
					TvrUiF.allocateOverlayMode('intelli');
				} 
				// regulkar highlighting should persist
				else { 
					TvrUiF.allocateOverlayMode('reg');
				}
			});
		}
	});
}

}
catch (e) { }
