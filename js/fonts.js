// JavaScript Document
jQuery(document).ready(function($){
	// get google fonts and display 15 at a time when request compltes
	function isNumber(n) {
	  return !isNaN(parseFloat(n)) && isFinite(n);
	}
	
	var TvrFonts = {
			url: 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyBY_w3-Yj6T54CQP5RNZrruBHEvwenEOmU',
			order: $('#sort-order').attr('rel'),
			page: parseInt($('#page-num').attr('rel')),
			start: parseInt($('#start-slice').attr('rel')),
			end: parseInt($('#end-slice').attr('rel')),
			total: 0,
			totalPages: 0,
			resultsPerPage: parseInt($('#results-per-page').attr('rel')),
			pageNav: '<ul>',
			family: '',
			fontList: '<ul>',
			dummyText: "Grumpy wizards make toxic brew for the evil Queen and Jack.",
			first: true,
			str: '',
			chosenVariant: '',
			// change font variation
			formatVariant: function($clicked) {
				$clicked.addClass('active').siblings().removeClass('active');
				var $fontDiv = $clicked.parents('li.font-entry').find('div.font');
				var cssRule = '';
				var variantArray = $clicked.text().split(' ');
				var style1 = variantArray[0];
				if (isNumber(style1)) {
					$fontDiv.css('font-weight', style1);
				} else if (style1 == 'Normal') {
					$fontDiv.css('font-weight', 'normal');
				} 
				if (style1 == 'italic' || (variantArray[1] && variantArray[1] == 'italic')) {
					$fontDiv.css('font-style', 'italic');
				} else {
					$fontDiv.css('font-style', 'normal');
				}
				// save the chosen variant for inserting
				TvrFonts.chosenVariant = '('+$clicked.text().trim()+')';
			},
			// insert font into Microthemer UI
			insertFont: function($clicked) {
				var fontFam = $clicked.attr('rel');
				// default to normal if no variant set (must be an active font variant for the actual font in question)
                var variantDefined = $clicked.closest('.font-entry').find('span.variant.active').length;
				if (TvrFonts.chosenVariant == '' || variantDefined < 1) {
					TvrFonts.chosenVariant = '(400 normal)';
				}
				fontFam = fontFam+' '+TvrFonts.chosenVariant;
				window.parent.TvrSharedVars.insert_google_font(fontFam);
			},
			handle_ajax_error: function(x,e) {
				if(x.status==0){
					$('#ajax-message, #loading-gif').hide();
					alert('Connection Error. Request Failed. Please Try again.');
				}
				else if(x.status==404){
					$('#ajax-message, #loading-gif').hide();
					alert('Requested URL not found.');
				}
				else if(x.status==500){
					$('#ajax-message, #loading-gif').hide();
					alert('Internel Server Error.');
				}
				else if(e=='parsererror'){
					$('#ajax-message, #loading-gif').hide();
					alert('Error.\nParsing JSON Request failed.');
				}
				else if(e=='timeout'){
					$('#ajax-message, #loading-gif').hide();
					alert('Request Timed out.');
				}
				else {
					$('#ajax-message, #loading-gif').hide();
					alert('Unknow Error.\n'+x.responseText);
				}
			}
		}

		
	$.ajax({
		type: "GET",
		url: TvrFonts.url,
		data: 'sort='+TvrFonts.order,
		dataType: 'jsonp',
		async: true, // so that renaming sections or selectors always works as expected
		timeout:20000, // 20 seconds
		error:function(x,e){
			TvrFonts.handle_ajax_error(x,e);
			// alert('Google fonts could not be loaded');	
		},
		success: function(Fonts){
			// set up page nav
			TvrFonts.total = Fonts['items'].length; 
			TvrFonts.totalPages = Math.ceil(TvrFonts.total/TvrFonts.resultsPerPage);
			// hide next link if last page
			if (TvrFonts.totalPages == TvrFonts.page) {
				$('#next-page').hide();
			}
			// create page nav
			for (var i=1;i<=TvrFonts.totalPages;i++) {
				if (i == TvrFonts.page) {
					TvrFonts.pageNav+= '<li><span>'+i+'</span></li>';
				} else {
					TvrFonts.pageNav+= '<li><a href="?page='+i+'&order='+TvrFonts.order+'">'+i+'</a></li>';
				}
			}
			TvrFonts.pageNav+= '</ul>';
			
			var i = 0;
			for (num in Fonts['items']) {
				// only use 20 fonts per page
				if (num < TvrFonts.start) {
					continue; 
				} else if (num >= TvrFonts.end) {
					break; 
				}
				++i;
				if (i == TvrFonts.resultsPerPage) {
					var itemClass = 'last';
				} else {
					var itemClass = '';
				}
				var fontFam = Fonts['items'][num]['family'];
				TvrFonts.fontList+= '<li class="font-entry '+itemClass+'">';
				TvrFonts.fontList+= '<div class="font" style="font-family:\''+fontFam+'\';">'+TvrFonts.dummyText+'</div>';
				TvrFonts.fontList+= '<span class="font-name">'+fontFam+' - <span class="link use-font" rel="'+fontFam+'">Use This Font </span></span>';
				// format variants
				var variant = Fonts['items'][num]['variants']
				.join('</span> | <span class="variant link">');
				variant = '<span class="variant link">'+variant
				.replace(/italic/g, ' italic')
				.replace('regular', 'Normal 400');
				variant+='</span>';
				TvrFonts.fontList+= ' <span class="font-variants">'+variant+'</span>';
				TvrFonts.fontList+= '</li>';		
				if (TvrFonts.first) {
					TvrFonts.first = false;
				} else {
					TvrFonts.family+='|';
				}
				TvrFonts.family+=Fonts['items'][num]['family'].replace(' ', '+');	
			}
			$('#google-fonts').attr('href', '//fonts.googleapis.com/css?family='+TvrFonts.family)
			$('#output').html(TvrFonts.fontList);
			$('#pagination').html(TvrFonts.pageNav);
		},
		complete: function() {
			
		}
	});
		
		
	// Events
	
	$('#order').change(function() {
		window.location = '?page='+TvrFonts.page+'&order='+$(this).val();
	});
	
	// font variant changed
	$('#output').click(function(e) {
		var $clicked = $(e.target);
		try {
			cClasses = $clicked.attr('class');
			if (!cClasses) {
				var cClasses = 'tvr-undefined'; // so rougue clicks don't trigger 'undefined' errors
			}
		} catch(err) {}
		// trigger appropriate action
		if (cClasses.indexOf('variant') >= 0) {
			TvrFonts.formatVariant($clicked);
		}
		if (cClasses.indexOf('use-font') >= 0) {
			TvrFonts.insertFont($clicked);
		}	
	});
	
	
});

