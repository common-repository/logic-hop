
function LogicHop (data) {
	this.data = data;
	this.payload = {};

	if ( ! this.data.js_track ) {
		this.setCookie( this.data.cookie );
	}
}

LogicHop.prototype.addPayloadData = function ( key, data ) {
	this.payload[key] = data;
};

LogicHop.prototype.setCookie = function ( c ) {
	if ( c != null ) {
		var cookie = c.name + '=' + c.value;
		cookie += ';path=' + c.path;
		cookie += ';expires=' + c.expires;
		cookie += ';domain=' + c.domain;
		cookie += ';samesite=lax';
		if ( c.secure ) {
			cookie += ';secure';
		}
		document.cookie = cookie;
		this.data.cookie_set = c.value;
	}
};

LogicHop.prototype.getCookie = function ( name ) {
	if ( this.data.cookie_set ) {
		return this.data.cookie_set;
	}
	var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
  return ( v ) ? v[2] : null;
};

LogicHop.prototype.parseLogic = function () {
	var self = this;

	if ( this.data.js_track && this.data.anti_flicker ) {
		this.flicker_timeout = setTimeout( this.pageTimeout.bind( this ), this.data.anti_flicker_timeout )
	}

	self.data.send 				= (self.data.js_track) ? true : false;
	self.data.conditions 		= [];
	self.data.conditions_json 	= [];
	self.data.variables 		= [];
	self.data.referrer 			= ('referrer' in document) ? document.referrer : '';

	jQuery('.logichop-js').each(function () {

		var cid = jQuery(this).attr('data-cid');
		var json = jQuery(this).attr('data-condition');
		var data_var = jQuery(this).attr('data-var');
		if (cid) {
			self.data.send = true;
			if (jQuery.inArray(cid, self.data.conditions) == -1) {
				self.data.conditions.push(cid);
			}
		}
		if (json) {
			self.data.send = true;
			if (jQuery.inArray(json, self.data.conditions_json) == -1) {
				self.data.conditions_json.push(json);
			}
		}

		if (data_var && self.data.js_vars) {
			self.data.send = true;
			if (jQuery.inArray(data_var, self.data.variables) == -1) {
				self.data.variables.push(data_var);
			}
		}
	});

	if (self.data.send) {
		var post_data = self.getQueryStrings();
		post_data.action = 'logichop_parse_logic';
		post_data.uncache = new Date().valueOf();
		post_data.data = self.data;
		post_data.logichop_cookie = self.getCookie( self.data.cookie_name );
		post_data.URL = window.location.pathname;
		post_data.payload = self.payload;

		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: self.data.ajaxurl,
			data: post_data,
			cache: false,
			success: function (data) {
				if (data.success) {
					self.setCookie( data.cookie );
					self.data.loaded = true;
					self.checkPageGoals();
					if (data.redirect) window.location = data.redirect;
					if (data.conditions.length > 0) self.displayConditions(data.conditions);
					if (data.conditions_json.length > 0) self.displayConditionsJson(data.conditions_json);
					if (data.variables.length > 0) self.displayVariables(data.variables);
					if (typeof data.header_css != 'undefined' && data.header_css != '') {
						jQuery('#logichop_header_css').append(data.header_css);
					}
					if (typeof data.css != 'undefined' && data.css != '') {
						jQuery('body').addClass(data.css);
					}
					if (typeof data.css_preview != 'undefined') {
						self.data.css_preview = data.css_preview;
					}
					self.pageRendered();
				}
			}
		});
	} else {
		self.pageRendered();
	}
};

LogicHop.prototype.displayConditions = function (conditions) {
	var self = this;
	for (var i = 0; i < conditions.length; i++) {
		jQuery('.logichop-js[data-cid="' + conditions[i].cid + '"]').each(function () {
			var el = jQuery(this);
			var not = el.attr('data-not');
			var condition = conditions[i].condition;
			if (not === 'true') {
				condition = !condition;
			}
			if (condition) {
				var event = (el.attr('data-event')) ? el.attr('data-event').toLowerCase() : '';

				el.addClass(el.attr('data-css-add')).removeClass(el.attr('data-css-remove'));

				if (event == 'hide') el.hide();
				if (event == 'show') el.show();
				if (event == 'toggle') el.toggle();
				if (event == 'slideup') el.slideUp();
				if (event == 'slidedown') el.slideDown();
				if (event == 'slidetoggle') el.slideToggle();
				if (event == 'fadein') el.fadeIn();
				if (event == 'fadeout') el.fadeOut();
				if (event == 'fadetoggle') el.fadeToggle();

				if (event == 'callback') {
					var callback = window[el.attr('data-callback')];
					if (typeof callback === 'function') {
						var args = [el];
						callback.apply(null, args);
					}
				}

				if ( jQuery( el ).is( ':visible' ) ) {
					jQuery( el ).addClass( 'logichop-visible' );
				}
			}
		});
	}
};

LogicHop.prototype.displayConditionsJson = function (conditions_json) {
	for (var i = 0; i < conditions_json.length; i++) {
		jQuery('.logichop-js[data-hash="' + conditions_json[i].hash + '"]').each(function () {
			var el = jQuery(this);
			var event = (el.attr('data-event')) ? el.attr('data-event').toLowerCase() : '';

			if (!event) el.show();
			if (event == 'show') el.show();
			if (event == 'hide') el.hide();
			if (event == 'toggle') el.toggle();
			if (event == 'slideup') el.slideUp();
			if (event == 'slidedown') el.slideDown();
			if (event == 'slidetoggle') el.slideToggle();
			if (event == 'fadein') el.fadeIn();
			if (event == 'fadeout') el.fadeOut();
			if (event == 'fadetoggle') el.fadeToggle();

			if ( jQuery( el ).is( ':visible' ) ) {
				jQuery( el ).addClass( 'logichop-visible' );
			}
		});
	}
};

LogicHop.prototype.displayVariables = function (vars) {
	var self = this;
	for (var i = 0; i < vars.length; i++) {
		if (vars[i].data_var && typeof vars[i].value != "undefined") {
			jQuery('.logichop-js[data-var="' + vars[i].data_var + '"]').each(function () {
				var el = jQuery(this);
				var type = (el.attr('data-type')) ? el.attr('data-type').toLowerCase() : false;
				var event = (el.attr('data-event')) ? el.attr('data-event').toLowerCase() : '';
				var charcase = (el.attr('data-case')) ? el.attr('data-case').toLowerCase() : false;
				var spaces = (el.attr('data-spaces')) ? el.attr('data-spaces').toLowerCase() : false;
				var prepend = (el.attr('data-prepend')) ? el.attr('data-prepend') : false;
				var append = (el.attr('data-append')) ? el.attr('data-append') : false;
				var _default = (el.attr('data-default')) ? el.attr('data-default') : false;

				if ( ! type && el.get(0).tagName == 'A' ) {
					type = 'href';
				} else if ( ! type && el.get(0).tagName == 'IMG' ) {
					type = 'src';
				} else if ( ! type && el.get(0).tagName == 'INPUT' ) {
					type = 'value';
				} else if ( ! type ) {
					type = 'replace';
				}

				if ( vars[i].value != null || _default !== false ) {
					var value = vars[i].value;

					if ( value == null ) {
						value = _default;
					}

					if (charcase) {
						if (value) {
							value = value.textf(charcase);
						}
					}

					if (spaces) value = value.replace(/ /g, spaces);

					if (type == 'replace' || type == 'html') {
						el.html(value).addClass(el.attr('data-css-add')).removeClass(el.attr('data-css-remove'));
					}

					if (type == 'append') {
						el.append(value).addClass(el.attr('data-css-add')).removeClass(el.attr('data-css-remove'));
					}

					if (type == 'prepend') {
						el.prepend(value).addClass(el.attr('data-css-add')).removeClass(el.attr('data-css-remove'));
					}

					if (type == 'text') {
						el.text(value).addClass(el.attr('data-css-add')).removeClass(el.attr('data-css-remove'));
					}

					if (type == 'value') {
						el.val(value).addClass(el.attr('data-css-add')).removeClass(el.attr('data-css-remove'));
					}

					if ( type == 'class' ) {
						el.addClass( value );
					}

					if ( type == 'src' ) {
						var src = el.attr( 'src' );
						if ( src ) {
							var new_src = src.replace( /#VAR#/, value );
							el.attr( 'src', new_src );
						}
					}

					if ( type == 'href' ) {
						var href = el.attr( 'href' );
						if ( href ) {
							var new_href = href.replace( /#VAR#/, value );
							el.attr( 'href', new_href );
						}
					}

					if ( prepend ) el.prepend( prepend );
					if ( append ) el.append( append );

					if (event == 'hide') el.hide();
					if (event == 'show') el.show();
					if (event == 'toggle') el.toggle();
					if (event == 'slideup') el.slideUp();
					if (event == 'slidedown') el.slideDown();
					if (event == 'slidetoggle') el.slideToggle();
					if (event == 'fadein') el.fadeIn();
					if (event == 'fadeout') el.fadeOut();
					if (event == 'fadetoggle') el.fadeToggle();
				}

			});
		}
	}
};

LogicHop.prototype.checkPageGoals = function () {
	var self = this;

	jQuery('.logichop-goal').each(function () {
		var goal = jQuery(this).val();
		var condition = (jQuery(this).attr('data-condition')) ? jQuery(this).attr('data-condition') : false;
		var condition_not = (jQuery(this).attr('data-not')) ? true : false;
		var delete_goal = (jQuery(this).attr('data-delete')) ? true : false;
		if (goal) {
			self.updateGoal(goal, condition, condition_not, delete_goal);
		}
	});
};

LogicHop.prototype.enablePageGoals = function () {
	var self = this;

	jQuery('.logichop-click').on('click', get_goal);
	jQuery('.logichop-dblclick').on('dblclick', get_goal);
	jQuery('.logichop-mouseenter').on('mouseenter', get_goal);
	jQuery('.logichop-mouseleave').on('mouseleave', get_goal);
	jQuery('.logichop-keypress').on('keypress', get_goal);
	jQuery('.logichop-keydown').on('keydown', get_goal);
	jQuery('.logichop-keyup').on('keyup', get_goal);
	jQuery('.logichop-submit').on('submit', get_goal);
	jQuery('.logichop-change').on('change', get_goal);
	jQuery('.logichop-focus').on('focus', get_goal);
	jQuery('.logichop-blur').on('blur', get_goal);
	jQuery('.logichop-load').on('load', get_goal);
	jQuery('.logichop-resize').on('resize', get_goal);
	jQuery('.logichop-scroll').on('scroll', get_goal);
	jQuery('.logichop-unload').on('unload', get_goal);

	function get_goal (event) {
		var goal = jQuery(event.target).data('goal');
		var delete_goal = (jQuery(this).attr('data-delete')) ? true : false;
		if (goal) {
			self.updateGoal(goal, false, false, delete_goal);
		}
	}

	if (typeof self.data.goal_ev != 'undefined' && typeof self.data.goal_el != 'undefined' && typeof self.data.goal_js != 'undefined') {
		jQuery(self.data.goal_el).on(self.data.goal_ev, function () {
			self.updateGoal(self.data.goal_js);
		});
	}
};

LogicHop.prototype.getVariable = function (data_var) {
	data_var = (typeof data_var !== 'undefined') ? data_var : false;

	var self = this;
	var post_data = {
			action: 'logichop_data',
			data_var: data_var,
			uncache: new Date().valueOf(),
			logichop_cookie: self.getCookie( self.data.cookie_name )
		};

	return new Promise( function ( resolve, reject ) {
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: self.data.ajaxurl,
			data: post_data,
			cache: false,
			success: function ( data ) {
				resolve( data );
			},
			error: function () {
				reject( new Error( 'fail' ) );
			}
		});
	});
};

LogicHop.prototype.getVariables = function () {
	var self = this;
	var post_data = {
			action: 'logichop_data_debug',
			uncache: new Date().valueOf(),
			logichop_cookie: self.getCookie( self.data.cookie_name )
		};

	return new Promise( function ( resolve, reject ) {
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: self.data.ajaxurl,
			data: post_data,
			cache: false,
			success: function ( data ) {
				resolve( data );
			},
			error: function () {
				reject( new Error( 'fail' ) );
			}
		});
	});
};

LogicHop.prototype.checkCondition = function (condition, condition_true, condition_false) {
	condition = (typeof condition !== 'undefined') ? condition : false;
	condition_true = (typeof condition_true !== 'undefined') ? condition_true : null;
	condition_false = (typeof condition_false !== 'undefined') ? condition_false : null;

	var post_data = {
			action: 'logichop_condition',
			cid: condition,
			uncache: new Date().valueOf(),
			logichop_cookie: this.getCookie( this.data.cookie_name )
		};

	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: this.data.ajaxurl,
		data: post_data,
		cache: false,
		success: function (data) {
			if (data.success && data.condition) {
				if ( condition_true !== null ) {
					condition_true( condition );
				}
			} else {
				if ( condition_false !== null ) {
					condition_false( condition );
				}
			}
		},
		error: function () {
			if ( condition_false !== null ) {
				condition_false( condition );
			}
		}
	});
};

LogicHop.prototype.updateGoal = function (goal, condition, condition_not, delete_goal) {
	condition = (typeof condition !== 'undefined') ? condition : false;
	condition_not = (typeof condition_not !== 'undefined') ? condition_not : false;
	delete_goal = (typeof delete_goal !== 'undefined') ? delete_goal : false;

	var post_data = {
			action: 'logichop_goal',
			goal: goal,
			uncache: new Date().valueOf(),
			logichop_cookie: this.getCookie( this.data.cookie_name )
		};
	if (condition) post_data.condition = condition;
	if (condition_not) post_data.condition_not = true;
	if (delete_goal) post_data.delete_goal = true;

	jQuery.ajax({
		type: 'POST',
		dataType: 'json',
		url: this.data.ajaxurl,
		data: post_data,
		cache: false,
		success: function (data) {
			return true;
		}
	});
};

LogicHop.prototype.getQueryStrings = function () {
	var match,
        pl     = /\+/g,
        search = /([^&=]+)=?([^&]*)/g,
        decode = function (s) { return decodeURIComponent(s.replace(pl, ' ')); },
        query  = window.location.search.substring(1);
	var qs = {};
    while (match = search.exec(query)) qs[decode(match[1])] = decode(match[2]);
    return qs;
};

LogicHop.prototype.logicBarSetup = function () {
	var self = this;

	var inputs = document.querySelectorAll( '.logichop-logic-bar' );
	inputs.forEach( function ( el, index, array ) {

		var hash = el.getAttribute( 'data-hash' );
		var delay = el.getAttribute( 'data-delay' );

		if ( delay == 'none' ) {
				self.logicBarPosition();
		} else if ( delay == 'scroll' ) {
			window.onscroll = function () {
				self.logicBarDisplay( hash );
			};
		} else if ( delay == 'click' ) {
			document.addEventListener( 'click', function ( e ) {
				self.logicBarDisplay( hash );
			} );
		}  else if ( delay == 'exit' ) {
			document.addEventListener( 'mouseout', function ( e ) {
    		if ( e.toElement === null && e.relatedTarget === null ) {
	        self.logicBarDisplay( hash );
    		}
			} );
		} else {
			var timeout = parseInt( delay );
			if ( ! isNaN( timeout ) ) {
				setTimeout( function () {
					self.logicBarDisplay( hash );
				}, timeout );
			}
		}
	} );
}

LogicHop.prototype.logicBarDisplay = function ( hash ) {
	var bar = document.getElementById( 'bar-' + hash );
	var block = document.getElementById( 'block-' + hash );

	if ( bar ) {
		if ( bar.classList.contains( 'logic-bar-hide' ) ) {
			bar.classList.remove( 'logic-bar-hide' );
			this.logicBarPosition();
			if ( block ) {
				block.classList.remove( 'logic-bar-hide' );
			}
		}
	}
}

LogicHop.prototype.logicBarPosition = function () {

	jQuery( '.logichop-logic-bar:visible' ).each( function () {
		if ( ! jQuery( this ).hasClass( 'logichop-logic-bar-popup' ) ) {
			var margin = parseInt( jQuery( 'html' ).css( 'margin-top' ) );
			var height = jQuery( this ).outerHeight();
			jQuery( 'body' ).css( 'padding-top', height + 'px' );
			jQuery( this ).css( 'top', margin + 'px' );
		}
	} );
}

LogicHop.prototype.logicBarDismiss = function () {
	if (jQuery('.logichop-logic-bar').is(':visible')) {
		jQuery('body').css('padding-top', 0);

		if ( ! jQuery('.logichop-logic-bar').hasClass('logichop-logic-bar-popup') ) {
			jQuery('.logichop-logic-bar').slideUp();
		} else {
			jQuery('.logichop-logic-bar').hide();
			jQuery('.logichop-logic-bar-screen-block').fadeOut();
		}
	}
}

LogicHop.prototype.pageRendered = function () {
	jQuery('.logichop-render-hide').removeClass('logichop-render-hide');
	jQuery('.logichop-fade-in').fadeTo('fast', 1);
	jQuery('.logichop-slide-down').slideDown();

	if ( this.flicker_timeout ) {
		clearTimeout( this.flicker_timeout );
	}

	this.logicBarSetup();

	if (typeof logichop_page_rendered == 'function') {
		logichop_page_rendered();
	}
};

LogicHop.prototype.pageTimeout = function () {
	jQuery('.logichop-render-hide').removeClass('logichop-render-hide');
	jQuery('.logichop-fade-in').fadeTo('fast', 1);
	jQuery('.logichop-slide-down').slideDown();

	this.logicBarSetup();

	if (typeof logichop_page_timeout == 'function') {
		logichop_page_timeout();
	}
};

if (typeof logichop.pid != 'undefined') {
	var logichop = new LogicHop(logichop);

	jQuery(document).ready(function () {
		logichop.parseLogic();
		logichop.enablePageGoals();

		jQuery('.logic-bar-close, .logic-bar-dismiss, .logichop-logic-bar-screen-block').click('click', function(e){
			logichop.logicBarDismiss();
			e.preventDefault();
		})
	});
}

window.onresize = function () {
	if (typeof logichop != 'undefined') {
		logichop.logicBarPosition();
	}
}

function logichop_var ( key ) {
	if ( typeof logichop != 'undefined' && logichop.data.vars !==  null ) {
		if ( typeof logichop.data.vars[ key ] != 'undefined' ) {
			return logichop.data.vars[ key ];
		}
	}
	return null;
}

function logichop_var_promise ( key ) {
	if ( typeof logichop != 'undefined' ) {
		return logichop.getVariable( key );
	}
	return null;
}

function logichop_condition (condition, condition_true, condition_false) {
	condition = (typeof condition !== 'undefined') ? condition : false;
	condition_true = (typeof condition_true !== 'undefined') ? condition_true : null;
	condition_false = (typeof condition_false !== 'undefined') ? condition_false : null;
	if (typeof logichop != 'undefined' && condition) {
		logichop.checkCondition(condition, condition_true, condition_false);
	}
}

function logichop_goal (goal, condition, condition_not) {
	condition = (typeof condition !== 'undefined') ?  condition : false;
	condition_not = (typeof condition_not !== 'undefined') ?  condition_not : false;
	if (typeof logichop != 'undefined') {
		logichop.updateGoal(goal, condition, condition_not);
	}
}

function logichop_goal_delete (goal, condition, condition_not) {
	condition = (typeof condition !== 'undefined') ?  condition : false;
	condition_not = (typeof condition_not !== 'undefined') ?  condition_not : false;
	if (typeof logichop != 'undefined') {
		logichop.updateGoal(goal, condition, condition_not, true);
	}
}

function logichop_debug () {
	if ( typeof logichop != 'undefined' ) {
		logichop.getVariables().then( function ( data ) { console.log( data ) } );
	}
}

String.prototype.textf = function(type) {
	if (type == 'lower') return this.toLowerCase();
	if (type == 'upper') return this.toUpperCase();
	if (type == 'words') return this.ucwords();
	if (type == 'first') return this.capitalize();
	return this;
}

String.prototype.ucwords = function() {
	return this.toLowerCase().replace(/\b[a-z]/g, function(letter) {
		return letter.toUpperCase();
	});
}

String.prototype.capitalize = function() {
	return this.charAt(0).toUpperCase() + this.slice(1);
}
