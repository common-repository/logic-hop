jQuery(function($) {

	if ( $('#logichop_redirect_id').val() != '' ) {
		jQuery.ajax({
			type: 'POST',
			dataType: 'json',
			url: logichop.ajaxurl,
			data: {
				action: 'post_title_lookup',
				id: $('#logichop_redirect_id').val(),
				type: logichop.post_types,
				query: 'posts'
			},
			success: function(data) {
				if (data.title) {
					$('.page_lookup').val( data.title ).addClass( 'logichop-ajax-locked' ).data('title', data.title ).removeClass('logichop-ajax-loading');
				} else {
					$('#logichop_redirect_id').val('');
				}
			}
		});
	}

	$('.page_lookup').autoComplete({
		minChars: 1,
		source: function(term, response) {
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: logichop.ajaxurl,
				data: {
					action: 'post_lookup',
					lookup: term,
					type: logichop.post_types,
					query: 'posts'
				},
				success: function(data) {
					response(data);
					$('.page_lookup').removeClass('logichop-ajax-loading');
				}
			});
		},
		renderItem: function (item, search){
				return '<div class="autocomplete-suggestion" data-val="' + item.title + '" data-id="' + item.id + '">' + item.title + '</div>';
		},
		onSelect: function( event, title, post ) {
			$('.page_lookup').val(title).addClass('logichop-ajax-locked').data('title', title);
			$('#logichop_redirect_id').val( $(post).data('id') );
		}
	}).on('keypress', function (e) {
		var key = e.charCode || e.keyCode || 0;
		if (key == 13) {
  		e.preventDefault();
		} else {
			$(this).addClass('logichop-ajax-loading');
		}
	}).on('change', function () {
		$(this).removeClass('logichop-ajax-loading');
		if ( $(this).hasClass('logichop-ajax-locked') ) {
			if ( $(this).data('title') != jQuery(this).val() ) {
				$(this).val('').removeClass('logichop-ajax-locked');
				$('.logichop-ajax-data').val('');
			}
		}
	});

	$('body').on('click', '.logic-block-var-btn', function (e) {
		logichop_modal_open('#logichop_redirect_url');
		e.preventDefault();
	});
});
