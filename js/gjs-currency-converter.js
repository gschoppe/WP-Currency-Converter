(function ( $ ) {
	$.fn.GJSCurrencyConvert = function( options ) {
		// eventually we take global locale settings here
		var settings = $.extend({
			autoRound : true
		}, options );
		if( !Number.prototype.formatNumber ) {
			Number.prototype.formatNumber = function(c, d, t){
				var n = this,
					d = d == undefined ? "." : d,
					t = t == undefined ? "," : t,
					s = n < 0 ? "-" : "",
					n = Math.abs( Number(n) || 0 );
				if( c == 'auto' ) {
					if( n >= 1 ) {
						c = 2;
					} else {
						var temp = this.toString().split(".");
						if(temp.length > 1) {
							c = temp[1].length || 0;
						} else {
							c = 0;
						}
					}
				} else if(typeof c !== 'undefined') {
					c = isNaN(c = Math.abs(c)) ? 2 : c;
					n = n.toFixed(c);
				} else {
					var temp = this.toString().split(".");
					if(temp.length > 1) {
						c = temp[1].length || 0;
					} else {
						c = 0;
					}
				}
				var i = String(parseInt(n)),
					j = (j = i.length) > 3 ? j % 3 : 0;
				return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
			};
		}
		if(!String.prototype.formatString) {
			String.prototype.formatString = function() {
				var format = this,
					args = arguments;
				return format.replace(/{(\d+)}/g, function(match, number) {
					return typeof args[number] != 'undefined' ? args[number] : match;
				});
			};
		}

		return this.each(function() {
			var $this = $(this),
				instance = {
					$this     : $this,
					value     : $this.data('value'),
					from      : $this.data('convert-from'),
					to        : $this.data('convert-to'),
					round     : $this.data('round'),
					format    : $this.data('format'),
					decimal   : $this.data('decimal'),
					separator : $this.data('separator'),
					showunit  : $this.data('showunit'),
				},
				url  = 'https://min-api.cryptocompare.com/data/price',
				data = {
					fsym  : instance.from,
					tsyms : instance.to
				};
			if( settings.autoRound && typeof instance.round === 'undefined' ) {
				instance.round = 'auto';
			}
			$.getJSON(url, data, (function(instance) {
				return function(response) {
					var result = response[instance.to];
					var converted = instance.value * result,
					formatted = converted.formatNumber(instance.round, instance.decimal, instance.separator);
					if(instance.format) {
						formatted = instance.format.formatString(formatted)
					} else {
						if( instance.showunit ) {
							formatted += '<span class="gjs-currency-unit">' + instance.to + '</span>';
						}
					}
					instance.$this.html(formatted);
				};
			})(instance)).fail((function(instance) {
				return function() {
					instance.$this.html('<span class="gjs-currency-error">ERROR</span>');
				};
			})(instance));
		});
	};
}( jQuery ));


// configuration
jQuery(function($) {
  $('.gjs-currency').GJSCurrencyConvert();
});
