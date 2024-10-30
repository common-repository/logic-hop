
LogicHop.prototype.previewSetup = function ($) { 
	var self = this;
	var loaded = false;
	
	var modal = $('<div/>');
	var content = $('<div/>').addClass('logichop-preview-content');
	var controls = $('<div/>').addClass('logichop-preview-controls');
	var conditions_toggle = $('<div/>').addClass('logichop-preview-selector active').html('CONDITIONS');
	var variables_toggle = $('<div/>').addClass('logichop-preview-selector').html('VARIABLES');
	var css_toggle = $('<div/>').addClass('logichop-preview-selector').html('CSS CLASSES');
	var content_display = $('<div/>').addClass('logichop-preview-content-display');
	var conditions = $('<ul/>').attr('id', 'logichop-preview-conditions');
	var variables = $('<ul/>').attr('id', 'logichop-preview-variables').hide();
	var css_classes = $('<ul/>').attr('id', 'logichop-preview-css-classes').hide();
	var title = $('<header><span class="dashicons dashicons-marker"></span> Logic Hop </header>');
	var button = $('<span class="dashicons dashicons-arrow-right logichop-preview-toggle"></span>');
	
	title.append( button );
	controls.append( conditions_toggle, variables_toggle, css_toggle );
	content_display.append( conditions, variables, css_classes );
	content.append( controls, content_display );
	
	button.on('click', function() {
		button.toggleClass('dashicons-arrow-left dashicons-arrow-right');
		modal.toggleClass('active');
		content.toggle();
		
		if (modal.hasClass('active')) {
			modal.draggable();
		} else {
			modal.draggable('destroy').attr('style', '');
			conditions.show();
			variables.hide();
			css_classes.hide();		
			$('.logichop-preview-selector').removeClass('active');
			conditions_toggle.addClass('active');	
		}
		
		if (!loaded) {
			self.previewConditions($);
			content.show();
			loaded = true;
		}
	});
	
	conditions_toggle.on('click', function() {
		$('.logichop-preview-selector').removeClass('active');
		$(this).addClass('active');
		self.previewConditions($);
		conditions.show();
		variables.hide();
		css_classes.hide();
	});
	variables_toggle.on('click', function() {
		$('.logichop-preview-selector').removeClass('active');
		$(this).addClass('active');
		self.previewVariables($);
		variables.show();
		conditions.hide();
		css_classes.hide();
	});
	css_toggle.on('click', function() {
		$('.logichop-preview-selector').removeClass('active');
		$(this).addClass('active');
		self.previewCSS($);
		css_classes.show();
		variables.hide();
		conditions.hide();
	});
	
	modal.appendTo('body')
		.addClass('logichop-preview-modal')
		.append( title, content );
};

LogicHop.prototype.previewConditions = function ($) {
	$('#logichop-preview-conditions').html('');
	$('.logichop-js').each( function () {		
		if ( $(this).data('title') ) {
			var el = $(this);
			var title = el.data('title');
			var classes = (el.is(':visible')) ? 'active' : '';
			
			if (typeof title === 'object') title = JSON.stringify( title );
			
			var icon = $('<div class="dashicons"></div>');
			var content = $('<div/>').addClass('logichop-preview-content-title').html(title);
			
			$('<li/>').appendTo('#logichop-preview-conditions')
				.on('click', function () { 
							el.fadeToggle();
							$(this).toggleClass('active');
						})
				.hover(
						function () {
							el.addClass('logichop-preview-active');
						},
						function () {
							el.removeClass('logichop-preview-active');
						}
					)
				.addClass(classes)
				.addClass('logichop-clearfix')
				.append( icon, content );
		}
	});
}

LogicHop.prototype.previewVariables = function ($) { 
	$('#logichop-preview-variables').html('');
	$('.logichop-js').each( function () {		
		if ( $(this).data('var') ) {
			var el = $(this);
			var variable = el.data('var');
			var classes = (el.is(':visible')) ? 'active' : '';
			
			var icon = $('<div class="dashicons"></div>');
			var content = $('<div/>').addClass('logichop-preview-content-title');
			
			var input = $('<input type="text">')
				.val( el.html() )
				.keyup(function () {
					el.html( $(this).val() );
				})
				.change(function () {
					el.html( $(this).val() );
				})
				.on('click', function (e) {
					e.stopPropagation();
				});
			
			content.html(variable)
				.append(input);
						
			$('<li/>').appendTo('#logichop-preview-variables')
				.on('click', function () { 
							el.fadeToggle();
							$(this).toggleClass('active');
						})
				.hover(
						function () {
							el.addClass('logichop-preview-active');
						},
						function () {
							el.removeClass('logichop-preview-active');
						}
					)
				.addClass(classes)
				.addClass('logichop-clearfix')
				.append(icon, content);
		}
	});
};

LogicHop.prototype.previewCSS = function ($) { 
	
	$('#logichop-preview-css-classes').html('');
	
	if (typeof this.data.css_preview !== 'object') return;
	
	$.each( this.data.css_preview , function( key, value ) {
		
		var title = value.class;
		var classes = ( value.active ) ? 'active' : '';
		
		var icon = $('<div class="dashicons"></div>');
		var content = $('<div/>').addClass('logichop-preview-content-title').html(title);
		
		$('<li/>').appendTo('#logichop-preview-css-classes')
			.on('click', function () { 
					$(this).toggleClass('active');
					var active 		= ( $(this).hasClass('active') ) ? true : false;
					var body_class 	= ( title.substring(0, 5) == 'body.' ) ? true : false;
					var class_name	= ( body_class ) ? title.substring(5) : title;
					
					if ( body_class ) {
						if (active) {
							$('body').addClass( class_name );
						} else {
							$('body').removeClass( class_name );
						}
					} else {
						if (active) {
							var _class = '.' + title + ' { display: block !important; }';
						} else {
							var _class = '.' + title + ' { display: none !important; }';
						}
						$('#logichop_header_css').append(_class);
					}
				})
			.hover(
					function () {
						if (title.substring(0, 5) != 'body.') {
							$('.' + title).addClass('logichop-preview-active');
						}
					},
					function () {
						if (title.substring(0, 5) != 'body.') {
							$('.' + title).removeClass('logichop-preview-active');
						}
					}
				)
			.addClass(classes)
			.addClass('logichop-clearfix')
			.append( icon, content );
	});
};

jQuery(document).ready(function () {
	logichop.previewSetup(jQuery);
	
	jQuery( '.logichop-preview-button' ).find( 'a' ).click( function ( e ) {
		e.preventDefault();
		jQuery( '.logichop-preview-toggle' ).trigger( 'click' );
	});
});


