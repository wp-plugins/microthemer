// JavaScript Document
jQuery(document).ready(function($){
	// set accordian elements based on docs page
	var $docsWrap = $('#docs-wrap');
	// CSS Reference
	if ($docsWrap.hasClass('css-ref')) {
		var hiddenEls = 'li.property-item';
		var triggerEls = 'li.list-heading';
	}
	// Videos
	else if ($docsWrap.hasClass('help-videos')) {
		var hiddenEls = 'div.inner-box';
		var triggerEls = 'h3.box-title';
	}
	
	// accordian
	var $allPanels = $(hiddenEls);
	$(triggerEls).click(function() {
		// if they click the currently open content, do nothing after sliding all up
		if ( $(this).parent().hasClass('show-content') ) {
			var keepShut = true;
		}
		$allPanels.slideUp(function() {
			$('.show-content').removeClass('show-content');
		});
		if (keepShut) {
			return false;
		}
		else {
			$(this).siblings(hiddenEls).slideDown(function() {
				$(this).parent().addClass('show-content');
				// videos page - load videos only when expanded
				if ( $('#docs-wrap').hasClass('help-videos') ) {
					var $iframe = $(this).find('iframe');
					$iframe.attr('src', $iframe.attr('rel') );
				}
			});
			return false;
		}
	});	
});

