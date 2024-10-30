LogicHop.prototype.renderDataPreviewForm = function ($, data) {

	if ( this.data_preview ) {
		$('.logichop-data-modal').remove();
		this.data_preview = false;
		return;
	}

	this.data_preview = true;

	var self = this;
	var modal = $('<div/>').addClass('logichop-data-modal');
	var content = $('<div/>').addClass('logichop-data-content');
	var content = $('<div/>').addClass('logichop-data-content');
	var content_display = $('<div/>').addClass('logichop-data-content-display');
	var data_form = $('<form/>').attr('id', 'logichop-data-form').attr('method', 'get');
	var title = $('<header><span class="dashicons dashicons-marker"></span> Logic Hop Data Preview Tool</header>');
	var toggle = $('<span class="dashicons dashicons-no logichop-data-toggle"></span>');

	toggle.click(function () {
		$('.logichop-data-modal').toggle();}
	);

	var ignore = [
		'UID',
		'Token',
		'lh',
		'Query',
		'QueryStore',
		'Pages',
		'Goals',
		'PagesSession',
		'GoalsSession',
		'ViewsSession',
		'Category',
		'Categories',
		'CategoriesSession',
		'Tag',
		'Tags',
		'TagsSession',
		'Path'
	];

	var d = data;
	for ( var k in d ) {
		if ( ignore.indexOf(k) == -1 ) {
			if ( typeof d[k] === 'object' ) {
				var dd = d[k];
				for ( var kk in dd ) {
					data_form.append( this.getDataInput($, k + ': ' + kk, k + '-' + kk, dd[kk]) );
				}
			} else {
				data_form.append( this.getDataInput($, k, k, d[k]) );
			}
		}
	}

	var label = $('<label/>').html('Variable Input');
	var textarea = $('<textarea/>').attr('rows', '3').attr('name', 'logichop-preview-vars').addClass('logichop-preview-vars');
	textarea.click( function () { $(this).toggleClass('active'); });
	data_form.append( $('<div/>').append( label, textarea ) );

	var hidden = $('<input/>').attr('type', 'hidden').attr('name', 'logichop-preview').addClass('active').val('true');
	data_form.append( hidden );

	var submit = $('<a/>').html('Update Data').addClass('logichop-data-submit');
	submit.click(function () {
		$('#logichop-data-form input').each(function() {
    	if (! $(this).hasClass('active')) {
				$(this).attr('name',null)
    	}
    });
		if (! $('.logichop-preview-vars').hasClass('active') ) {
			$('.logichop-preview-vars').attr('name',null);
		} else {
			var pv = $('.logichop-preview-vars').val();
			$('.logichop-preview-vars').val( pv.replace(/->/g, '-') );
		}
		$('#logichop-data-form').submit();
	});

	title.append( toggle );
	content_display.append( data_form );
	content.append( content_display, submit );

	modal.appendTo('body').append( title, content ).draggable();
};

LogicHop.prototype.getDataInput = function ($, k, i, v) {
	var label = $('<label/>').html(k);
	var input = $('<input/>').attr('name', i).val(v);
	input.click( function () { $(this).toggleClass('active'); });
	var group = $('<div/>').append( label, input );

	return group;
}

jQuery(document).ready(function () {
	jQuery(document).keyup(function(e) {
  	if (e.key === 'Escape') {
			logichop.getVariables().then(
				function ( data ) {
					logichop.renderDataPreviewForm( jQuery, data )
				}
			);
  	}
	});
});
