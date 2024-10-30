function logicHopConditionBuilder () {

	var self = this;
	this.json_vars = {};
	this.condition_json;
	this.condition_count = 0;
	this.condition_logic = 'and';
	this.condition_class = 'form-inline';
	this.condition_input_class = 'form-control';
	this.info_default = {};
	this.post_types = [];
	this.terms = [];
	this.conditions = {};
	this.condition_types = [];

	this.addVars = function (key, value) {
		self.json_vars[key] = value;
	}

	this.addPostType = function (type) {
		self.post_types.push(type);
	}

	this.addPostTypes = function () {
		if ( self.post_types.length > 0 ) {
			Object.keys(self.conditions).forEach(function (k) {
				if ( self.conditions[k].inputs ) {
					Object.keys(self.conditions[k].inputs).forEach(function (k2) {
						if ( self.conditions[k].inputs[k2].name.includes('page') ) {
							if ( self.conditions[k].inputs[k2].source ) {
								Array.prototype.push.apply( self.conditions[k].inputs[k2].source, self.post_types );
							}
						}
					});
				}
			});
		}
	}

	this.addTerm = function (term) {
		self.terms.push(term);
	}

	this.addTerms = function () {
		if ( self.terms.length > 0 ) {
			Object.keys(self.conditions).forEach(function (k) {
				if ( self.conditions[k].inputs ) {
					Object.keys(self.conditions[k].inputs).forEach(function (k2) {
						if ( typeof self.conditions[k].inputs[k2].query !== "undefined" ) {
							if ( self.conditions[k].inputs[k2].query.includes('terms') ) {
								if ( self.conditions[k].inputs[k2].source ) {
									Array.prototype.push.apply( self.conditions[k].inputs[k2].source, self.terms );
								}
							}
						}
					});
				}
			});
		}
	}

	this.parseJSON = function (_json) {

		var json = {};

		try {
			json = JSON.parse(_json, function (key, value) {
				if (typeof value === 'string' && value.substring(0,5) == 'vars:') {
   					var index = value.substring(5);
    				if (index in self.json_vars) {
     			 		return self.json_vars[index];
    				}
  				}
  				return value;
			});
		} catch (e) {}

		return json;
	}

	this.setData = function (_conditions) {
		self.info_default = logichop_text.info_default;
		self.conditions = self.parseJSON(_conditions);
		self.condition_types = Object.values(self.conditions).map( condition => condition.category ).filter((value, index, self) => self.indexOf(value) === index).sort()
		self.addPostTypes();
		self.addTerms();
	};

	this.addCondition = function (name, value) {
		self.conditions[name] = value;
	}

	this.init = function (type) {

		self.condition_count++;

		if (self.condition_count == 1) {
			var logic = jQuery('<select class="' + self.condition_input_class + ' logic"><option value="if">' + logichop_text.if + '</option></select>');
		} else {
			var logic = jQuery('<select class="' + self.condition_input_class + ' logic"><option value="and">' + logichop_text.and + '</option><option value="or">' + logichop_text.or + '</option></select>');
		}

		var category = jQuery('<select class="' + self.condition_input_class + ' category"><option value="">' + logichop_text.category + '</option></select>');
		jQuery.each(self.condition_types, function (key, value) {
			category.append( jQuery('<option></option>').attr('value', value).text(value));
		});

		var condition_set = '';
		if (type) {
			condition_set = 'loginchop-condition-set';
		}

		var buttons = '<a href="#" title="' + logichop_text.details + '" class="btn-info">' + logichop_text.details + '</a><div class="info"></div><a href="#" title="' + logichop_text.add_cond + '" class="btn-add button-secondary">+</a><a href="#" title="' + logichop_text.remove_cond + '" class="btn-remove"><small>' + logichop_text.remove_cond + '</small></a></div>';
		var condition = jQuery('<div class="' + self.condition_class + ' logichop-condition ' + condition_set + '"><span class="params"></span>' + buttons);

		condition.prepend(category);
		condition.prepend(logic);

		if( type ) {
			// Set the correct category and display the type
			var category_for_type = self.conditions[type].category
			category.val(category_for_type)
			this.displayType(condition, category_for_type).val(type)
		}

		jQuery('.logichop-conditions').append(condition);
		self.updateLogic(self.condition_logic);

		condition.children('.info').html(self.info_default);

		return condition;
	};

	this.refresh = function (type) {
		self.init(false);
	};

	this.displayType = function(element, category) {
		// First, delete any existing type selector...
		jQuery(element).find('.type').remove();

		// Remove paramters too
		element.children('.params').html('');

		// Then, add the new selector
		var select = jQuery('<select class="' + self.condition_input_class + ' type"><option value="">' + logichop_text.select_type + '</option></select>');
		jQuery.each(self.conditions, function (key, value) {
			if( value.category == category ) {
				select.append( jQuery('<option></option>').attr('value', key).text(value.label));
			}
		});

		select.insertAfter(element.find('select').last());
		return select
	}

	this.display = function (element, condition, data) {

		var params = element.children('.params');
		var inputs = self.conditions[condition].inputs;

		params.html('');

		jQuery.each(inputs, function (key, input) {
			if (input.type == 'select') {
				var select = jQuery('<select class="' + self.condition_input_class + ' ' + input.name + '"></select>');
				if (input.name != 'operator') select.append( jQuery('<option value="">' + logichop_text.select + ' ' + input.label + '</option>'));
				jQuery.each(input.options, function (key, value) {
					select.append( jQuery('<option></option>').attr('value', key).text(value));
				});
				if (data) select.val(data[input.name]);
				params.append(select);
			} else if (input.type == 'datalist') {
				var data_input = jQuery('<input class="datalist ' + self.condition_input_class + ' ' + input.name + '" list="' + input.name + '">');
				var datalist = jQuery('<datalist id="' + input.name + '"></datalist>');
				if (input.name != 'operator') datalist.append( jQuery('<option value="">' + logichop_text.select + ' ' + input.label + '</option>'));
				jQuery.each(input.options, function (key, value) {
					datalist.append( jQuery('<option></option>').attr('value', value).attr('data-value', key));
				});
				if (data) {
					var data_value = datalist.find('[data-value="' + data[input.name] + '"]').attr('value');
					if (data_value) {
						data_input.val(data_value);
					} else {
						data_input.val(data[input.name]);
					}
				}
				params.append(data_input);
				params.append(datalist);
			} else if (input.type == 'ajax') {
				var value = '';
				var args = '';
				if (data && data[input.name]) value = data[input.name]; // FINDS EXISTING DATA
				if (!input.query) input.query = 'posts';
				var loader_class = (value != '') ? ' logichop-ajax-loading' : '';
				var el_html = '<form onsubmit="return false;" class="logichop-ajax-container">';
				el_html += '<input class="' + self.condition_input_class + ' ' + input.name + '_lookup logichop-ajax' + loader_class + '" type="text" placeholder="' + input.label + '">';
				el_html += '<input class="' + input.name + ' logichop-ajax-data" type="hidden" value="' + value + '">';
				el_html += '</form>';

				var el = jQuery(el_html);
				params.append( el );

				var data_storage = '.' + input.name;
				var data_lookup = data_storage + '_lookup';

				if (value != '') {
					jQuery.ajax({
						type: 'POST',
						dataType: 'json',
						url: logichop.ajaxurl,
						data: {
							action: 'post_title_lookup',
							id: value,
							type: input.source,
							query: input.query
						},
						success: function(data) {
							if (data.title) {
								el.children(data_lookup).val( data.title ).addClass( 'logichop-ajax-locked' ).data('title', data.title ).removeClass('logichop-ajax-loading');
							} else {
								el.children(data_storage).val('');
							}
						}
					});
				}

				el.children(data_lookup).autoComplete({
					minChars: 1,
					source: function(term, response) {
						jQuery.ajax({
							type: 'POST',
							dataType: 'json',
							url: logichop.ajaxurl,
							data: {
								action: 'post_lookup',
								lookup: term,
								type: input.source,
								query: input.query
							},
							success: function(data) {
								response(data);
								el.children(data_lookup).removeClass('logichop-ajax-loading');
							}
						});
					},
					renderItem: function (item, search){
    					return '<div class="autocomplete-suggestion" data-val="' + item.title + '" data-id="' + item.id + '">' + item.title + '</div>';
					},
					onSelect: function(event, title, post) {
						el.children(data_lookup).val(title).addClass('logichop-ajax-locked').data('title', title);
						el.children(data_storage).val( jQuery(post).data('id') );
					}
				}).on('keypress', function () {
					jQuery(this).addClass('logichop-ajax-loading');
				}).on('change', function () {
					jQuery(this).removeClass('logichop-ajax-loading');
					if ( jQuery(this).hasClass('logichop-ajax-locked') ) {
						if ( jQuery(this).data('title') != jQuery(this).val() ) {
							jQuery(this).val('').removeClass('logichop-ajax-locked');
							jQuery(this).siblings(data_storage).val('');
						}
					}
				});

			} else {
				var value = '';
				var args = '';
				if (input.type == 'number') {
					value = 1;
					args = 'min="0" step="1"';
				}
				if (input.name == 'url') value = 'http://';
				if (data) {
					if (data[input.name] || data[input.name] === 0) {
						value = data[input.name];
					}
				}
				var placeholder = (input.placeholder) ? 'placeholder="' + input.placeholder + '"' : '';
				params.append( jQuery('<input class="' + self.condition_input_class + ' ' + input.name + '" type="' + input.type + '" value="' + value + '"' + args + ' ' + placeholder + '>') );
			}
		});

		if (self.conditions[condition].info) element.children('.info').html(self.conditions[condition].info);
	};

	this.formatJson = function () {

		var logic;
		var mappings = [];
		var condition;

		jQuery('.logichop-condition').each(function (index, element) {
			var type = jQuery(this).find('.type').val();
			logic = jQuery(this).find('.logic').val();
			var params = jQuery(this).find('.params');

			if (type) {
				var map = self.conditions[type].map;

				var pages = []; // ARRAY FOR PATH/HISTORY SUPPORT

				jQuery.each(self.conditions[type].inputs, function (key, input) {
					var value = params.find('.' + input.name).val().replace(/([\\"])/g, ''); // PREVENT QUOTES AND BACKSLASHES IN VALUES
					if (params.find('.' + input.name).hasClass('datalist')) {
						var option_data_value = jQuery('#' + input.name).find('option[value="' + value + '"]').attr('data-value');
						if (option_data_value) value = option_data_value;
					}
					if (input.name.search('page-') < 0) {
						var regex = new RegExp('#' + input.name, 'g');
						map = map.replace(regex, value);
					} else {
						if (value) pages.unshift(parseInt(value));
					}
				});

				if (pages) {
					var regex = new RegExp('#pages', 'g');
					map = map.replace(regex, pages);
				}

				mappings.push(map);
			}
		});

		if (logic == 'if') {
			condition = mappings.toString();
		} else {
			condition = '{"' +  logic + '": [' + mappings.toString() + ']}';
		}

		self.condition_json = condition;
		jQuery('.output').html(self.condition_json);
	};

	this.parseData = function (json) {
		var logic;
		var json_objects = [];
		var json_keys = Object.keys(json);

		if (json_keys[0] == 'and' || json_keys[0] == 'or') {
			json_objects = json[json_keys[0]];
			logic = json_keys[0];
		} else {
			json_objects.push(json);
		}

		jQuery.each(json_objects, function (index, data) {
			var data_keys = Object.keys(data);
			var data_values = [];
			var data_string = JSON.stringify(data);

			data_values.operator = data_keys[0];

			// LOOP THROUGH ALL CONDITIONS TO GET _slug_
			jQuery.each(self.conditions, function (key, value) {
				if (data_string.search(value.lookup) >= 0) {
					data_values.type = key;
					return false;
				}
			});

			// LOOP THROUGH ALL CONDITION INPUTS TO GET/SET VALUES
			jQuery.each(self.conditions[data_values.type].inputs, function (key, input) {
				if (input.name != 'operator') {
					if (data_values.type == 'path') {
						var path = data[data_values.operator][0].compare_array_slice[0].pop();
						data_values[input.name] = path;
					} else if (data_values.type == 'time_elapsed') {
						var value = data[data_values.operator][input.map];
						if (typeof(value) == 'object') {
							var time = value['-'][1].var;
							data_values[input.name] = time.substr(time.indexOf('.') + 1);
						} else {
							data_values[input.name] = value;
						}
					} else {
						var value = data[data_values.operator][input.map];
						if (typeof(value) == 'object') {
							var lookup_function = self.conditions[data_values.type].type;
							if (self[lookup_function] && typeof self[lookup_function] === 'function') {
								data_values[input.name] = self[lookup_function](value);
							}
						} else {
							data_values[input.name] = value;
						}
					}
				}
			});

			var element = self.init(data_values.type);
			self.display(element, data_values.type, data_values);
		});

		if (logic) self.updateLogic(logic);
		self.formatJson();
	};

	this.updateLogic = function (logic) {
		jQuery('.logic').each(function () {
			if (jQuery(this).val() != 'if') jQuery(this).val(logic);
		});
	};

	this.saveCondition = function () {
		jQuery('#excerpt').html(this.condition_json);
	};

	this.valueKeyExists = function (value) {
		try {
  			return value.key_exists[0];
		} catch (e) {
			return '';
		}
	}

	this.valueInArray = function (value) {
		try {
  			return value.in[0];
		} catch (e) {
			return '';
		}
	}

	this.valueSubStrIndex = function (value) {
		try {
  			return value.var.substr(value.var.indexOf('.') + 1);
		} catch (e) {
			return '';
		}
	}

	this.valueSubStrLastIndex = function (value) {
		try {
  			return value.var.substr(value.var.lastIndexOf('.') + 1);
		} catch (e) {
			return '';
		}
	}
}

var logicHopCB = new logicHopConditionBuilder();

jQuery(function($) {

	if (logichop.conditions) {
		logicHopCB.setData(logichop.conditions);

		if (logichop_data) {
			logicHopCB.parseData(logichop_data);
		} else {
			logicHopCB.init(false);
		}
	} else {
		$('#logichop-condition-builder-error').show();
	}

	$('.logichop-conditions').on('click', '.btn-add', function (e) {
		logicHopCB.init(false);
		logicHopCB.saveCondition();
		e.preventDefault();
	});

	$('body').on('click', '.btn-save', function (e) {
		logicHopCB.saveCondition();
		e.preventDefault();
	});

	$('body').on('click', '.logichop-condition-logic', function (e) {
		if ($('.logichop-condition-excerpt').hasClass('logichop-condition-excerpt-hide')) {
			$(this).html(logichop_text.hide_logic);
			$('.logichop-condition-excerpt').removeClass('logichop-condition-excerpt-hide');
		} else {
			$(this).html(logichop_text.show_logic);
			$('.logichop-condition-excerpt').addClass('logichop-condition-excerpt-hide');
		}
		e.preventDefault();
	});

	$('body').on('click', '#logichop_css_condition', function (e) {
		if ($('.logichop-css').hasClass('logichop-condition-excerpt-hide')) {
			$('.logichop-css').removeClass('logichop-condition-excerpt-hide');
		} else {
			$('.logichop-css').addClass('logichop-condition-excerpt-hide');
		}
	});

	$('.logichop-conditions').on('change', '.category', function() {
		var category = $(this).val();
		if (category) {
			logicHopCB.displayType($(this).parent(), category);
		}
	})

	$('.logichop-conditions').on('change', '.type', function () {
		var type = $(this).val();
		if (type) {
			logicHopCB.display($(this).parent(), $(this).val(), false);
			$(this).parent().addClass('loginchop-condition-set');
		} else {
			$(this).parent().removeClass('loginchop-condition-set');
		}
	});

	$('.logichop-conditions').on('click', '.btn-remove', function (e) {
		$(this).parent().remove();
		logicHopCB.condition_count--;

		if ($('.logichop-condition').first().find('.logic').val() != 'if') {
			$('.logichop-condition').first().find('.logic').empty();
			$('.logichop-condition').first().find('.logic').append('<option value="if">' + logichop_text.if + '</option>');
		}

		if (logicHopCB.condition_count == 0) logicHopCB.init();
		logicHopCB.formatJson();
		logicHopCB.saveCondition();
		e.preventDefault();
	});

	$('.logichop-conditions').on('click', '.btn-info', function (e) {
		$(this).parent().find('.info').toggle();
		e.preventDefault();
	});

	$('.logichop-conditions').on('change', '.logic', function () {
		logicHopCB.condition_logic = $(this).val();
		logicHopCB.updateLogic(logicHopCB.condition_logic);
		logicHopCB.saveCondition();
	});

	$('.logichop-conditions').on('change keyup', 'input, select', function () {
		logicHopCB.formatJson();
		logicHopCB.saveCondition();
	});
});
