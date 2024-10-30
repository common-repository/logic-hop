jQuery(function($) {

	$('body').on('paste', '.logicblock .content', build_timer);
	$('body').on('keyup', '.logicblock .content', build_timer);
	$('body').on('change', '.logicblock .condition, .logicblock .condition_not', build_logic_block);

	$('body').on('click', '.logic-block-var-btn', function (e) {
		var block = '.content-' + $(this).parent().data('block');
		logichop_modal_open(block);
		e.preventDefault();
	});

	function build_timer () {
		setTimeout( build_logic_block, 100);
	}

	function build_logic_block () {
		var json = [];

		$('.logicblock').each(function (index) {
			var block = {};
			block.id = $(this).data('block');
			block.logic = $(this).find('.logic').val();
			block.condition = $(this).find('.condition').val();
			block.condition_not = $(this).find('.condition_not').val();
			block.content = $(this).find('.content').val();
			json[block.id] = block;
		});

		$('#logichop_logicblock_json').val( JSON.stringify(json) );
		build_logic_tags(json);
	}

	function build_logic_tags (json) {
		var excerpt = '';

		for (var i = 0; i < json.length; i++) {
			var block = json[i];
			if (block.logic == 'if' || block.logic == 'elseif') {
				excerpt += '{% ' + block.logic + ' condition: '
				if (block.condition_not == 'not_met') excerpt += '!';
				excerpt += block.condition;
				excerpt += ' %}';
				excerpt += block.content;
			}
			if (block.logic == 'else') {
				excerpt += '{% else %}';
				excerpt += block.content;
			}
			if (block.logic == 'endif') {
				excerpt += '{% endif %}';
			}
		}

		$('#excerpt').val( excerpt );
	}

	function render_blocks () {
		if (logichop_block_data) {
			for (var i = 0; i < logichop_block_data.length; i++) {
				if ( i > 1 ) {
					add_block(i);
				}
				var block = $('#logicblocks').find('[data-block="' + i + '"]');
				if (block) {
					var logic = logichop_block_data[i].logic;
					block.show();
					block.find('.logic').val(logic);
					block.find('.condition').val(logichop_block_data[i].condition);
					block.find('.condition_not').val(logichop_block_data[i].condition_not);
					block.find('.content').val(logichop_block_data[i].content).addClass('content-' + i);
					if (logic == 'else') {
						block.find('.content, .content-label').show();
						block.find('.logic').css('margin-bottom', '15px');
					} else if (logic == 'elseif') {
						block.find('.condition').show();
						block.find('.condition_not').show();
					}
					if (logic != 'endif') {
						block.find('.content-label').show();
						block.find('.content').show();
						block.find('.logic-block-var-btn').show();
						block.find('.logic-block-media-btn').show();
					}
				}
			}
		}
	}

	function add_block (number) {
		var new_block = $('.logicblock-template').clone();
		new_block.removeClass('logicblock-template hidden').addClass('logicblock').attr('data-block', number);
		new_block.find('.content').addClass('content-' + number);
		$('#logicblocks').append(new_block);
	}

	$('body').on('change', '.logicblock .logic', function () {
		var logic = $(this).val();
		var block = $(this).parent();

		$(this).siblings().hide();
		$(this).css('margin-bottom', 0);
		block.nextAll().remove();

		if (logic == 'else') {
			$(this).siblings('.content, .content-label').show();
			$(this).css('margin-bottom', '15px');
		} else if (logic == 'elseif') {
			$(this).siblings().show();
		}

		if (logic != 'endif') {
			block.find('.logic-block-var-btn').show();
			block.find('.logic-block-media-btn').show();

			var number = parseInt(block.data('block')) + 1;
			add_block(number);
		}

		build_logic_block();
	});

	render_blocks();

	var file_frame;

	$('body').on('click', '.logic-block-media-btn', function (e) {

		var block = '.content-' + $(this).parent().data('block');

		file_frame = wp.media({
			title: 'Insert/Add Media',
			frame: 'post',
      state: 'insert',
			button: {
				text: 'Insert Media',
			},
			multiple: false
		});

		file_frame.open();

		e.preventDefault();

		file_frame.on( 'insert', function (selection) {

			var state = file_frame.state();
			selection = selection || state.get('selection');
			if (! selection) return;

			var attachment = selection.first();
			var display = state.display(attachment).toJSON();
			attachment = attachment.toJSON();
			delete attachment.caption;

			var html = '';
			var options = {};

      var props = wp.media.string.props( display, attachment );

			if ( props.linkUrl ) {
      	options.url = props.linkUrl;
      }
			if ( display.linkUrl ) {
      	options.url = display.linkUrl;
      }

			if ( 'image' === attachment.type ) {
				var src = attachment.sizes[display.size];
        html = wp.media.string.image( props, src );
      } else if ( 'video' === attachment.type ) {
        html = wp.media.string.video( props, attachment );
      } else if ( 'audio' === attachment.type ) {
      	html = wp.media.string.audio( props, attachment );
      } else {
        html = wp.media.string.link( props );
      }

			var _content = $(block).val();
			var position = $(block).getCursorPosition();
			var content = _content.substr(0, position) + html + _content.substr(position);
			$(block).val( content ).trigger('keyup');
		});
	});

	$( 'a.add_media' ).on( 'click', function() {
		wp.media.model.settings.post.id = wp_media_post_id;
	});

	$.fn.getCursorPosition = function () {
    var el = $(this).get(0);
    var pos = 0;
    if ('selectionStart' in el) {
      pos = el.selectionStart;
    } else if ('selection' in document) {
      el.focus();
      var Sel = document.selection.createRange();
      var SelLength = document.selection.createRange().text.length;
      Sel.moveStart('character', -el.value.length);
      pos = Sel.text.length - SelLength;
    }
    return pos;
  }

});
