// TODO Fails when one of [ margin, border ] uses absolute values and the other uses relative

(function($){
	$.fn.extend({

		overlay: function(options) {
			var defaults = {},
			    options = $.extend(defaults, options);

			return this.each(function() {
				// TODO Probably want to set this up once, then show/hide it
				// on hover
				$(this).hover(
					function() {
						$(this).add_overlay();
					},

					function() {
						$(this).find('.overlay').remove();
					}
				);
			});
		},

		add_overlay: function(options) {
			var sides    = [ 'left', 'top', 'right', 'bottom' ],
			    defaults = { padding: true, margin: true, border: true, content: true };
			    options  = $.extend(defaults, options);

			return this.each(function() {
				var compStyle = document.defaultView.getComputedStyle(this, null),
				    $this     = $(this).css({ position: 'relative' }),
				    props     = {},
				    fs        = parseInt($this.css('font-size')),
				    s,
				    d,
				    o,
				    prop;

				var propsToGet = {
					content: [ 'padding' ],
					margin:  [ 'margin', 'border' ],
					border:  [ 'border' ]
				};

				var vals = {};

				for (o in options) {
					if (!options[o])
						continue;

					for (p in propsToGet[o]) {
						for (s in sides) {
							fullProp = propsToGet[o][p] + '-' + sides[s] + (propsToGet[o][p] == 'border' ? '-width' : '');
							vals[fullProp] = [ parseInt($this.css(fullProp)) ];
							$this.css('font-size', fs * 2);
							vals[fullProp][1] = parseInt($this.css(fullProp));
							$this.css('font-size', fs);
						}
					}
				}

				for (d in defaults) {
					if (!options[d])
						continue;

					if (d != 'padding') {
						for (s in sides) {
							prop = d == 'content' ? 'padding-' + sides[s]
								: d + '-' + sides[s] + (d == 'border' ? '-width' : '');

							props[sides[s]] = d == 'content' ? vals[prop][0]
								: d == 'margin' ? 0 - vals[prop][0] - vals['border-' + sides[s] + '-width'][0]
								: d == 'border' ? 0 - vals[prop][0] : '';

							props[sides[s]] = vals[prop][0] == vals[prop][1]
								? props[sides[s]] + 'px'
								: (props[sides[s]] / fs) + 'em';
						}
					}

					$('<div class="overlay ' + d + '-overlay"></div>').appendTo(this).css(props);
				}
			});
		}
	});
})(jQuery);