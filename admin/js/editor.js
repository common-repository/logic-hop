jQuery(function($) {

	function is_tinymce_active () {
		var tinymce_active = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
		return tinymce_active;
	}

	var selected_text = '';
	$('#content').on('mouseout mouseup keyup', function () {
		if (window.getSelection()) selected_text = window.getSelection().toString();
	})

	$('#logichop-modal-backdrop, .logichop-modal-close, .logichop-modal-cancel').on('click', logichop_modal_close);

	function logichop_modal_close () {
		$('#logichop-modal-backdrop').hide();
		$('#logichop-modal-wrap').hide();
		$('.logichop-modal-form input').val('');
		$('.logichop-modal-form select').each(function () { $(this)[0].selectedIndex = 0; });
	}

	$('.logichop-modal-content .nav-tab').on('click', function (e) {
		$('.logichop-modal-content .nav-tab').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');
		$('.nav-tab-display').removeClass('nav-tab-display-active');
		$('.' + $(this).attr('data-tab')).addClass('nav-tab-display-active');
		e.preventDefault();
	});

	$('.logichop_insert_condition').on('click', function (e) {
		var type = $('#logichop_condition_logic').val();
		var cid = $('#logichop_condition').val();
		var condition = $('#logichop_condition option:selected').attr('data-slug');

		var output = '';
		var _text = logichop_get_text();

		if (cid && condition) {
			if (type == 'if') {
				if (!_text) _text = '## IF CONTENT ##';
				output = '{% if condition: ' + condition + ' %} ' + _text + ' {% endif %}';
			}
			if (type == 'if not') {
				if (!_text) _text = '## IF NOT CONTENT ##';
				output = '{% if condition: !' + condition + ' %} ' + _text + ' {% endif %}';
			}
			if (type == 'if else') {
				if (!_text) _text = '## IF CONTENT ##';
				output = '{% if condition: ' + condition + ' %} ' + _text + ' {% else %} ## ELSE CONTENT ## {% endif %}';
			}
			if (type == 'if not else') {
				if (!_text) _text = '## IF NOT CONTENT ##';
				output = '{% if condition: !' + condition + ' %} ' + _text + ' {% else %} ## ELSE CONTENT ## {% endif %}';
			}
			if (type == 'else if') {
				if (!_text) _text = '## ELSE IF CONTENT ## ';
				output = ' {% elseif condition: ' + condition + ' %} ' + _text;
			}
			if (type == 'else if not') {
				if (!_text) _text = '## ELSE IF NOT CONTENT ## ';
				output = '{% elseif condition: !' + condition + ' %} ' + _text;
			}
		}
		if (type == 'else') {
			if (!_text) _text = '## ELSE CONTENT ## ';
			output = '{% else %} ' + _text;
		}

		if ( output ) {
			window.send_to_editor( output );
			logichop_modal_close();
		}
		e.preventDefault();
	});

	$('.logichop_insert_query_condition').on('click', function (e) {
		var _var 	= $('#logichop_query_logic').val();
		var _opr 	= $('#logichop_query_operator').val();
		var _val 	= $('#logichop_query_value').val();
		var _text 	= logichop_get_text();

		if (_var && _opr && _val) {
			if (!_text) _text = '## IF CONTENT ##';
			window.send_to_editor( '{% if query: ' + _var + ' ' + _opr + ' ' + _val + ' %} ' + _text + ' {% endif %}');
			logichop_modal_close();
		}
		e.preventDefault();
	});

	$('#logichop_insert_goal').on('click', function (e) {
		var gid = $('#logichop_goal').val();
		var goal = $('#logichop_goal option:selected').attr('data-slug');
		var delete_goal = ($('#logichop_goal_delete').val() == 'delete') ? ' | delete: true' : '';
		if (gid && goal) {
			var _output = '{{ goal: ' + goal + delete_goal + ' }} ';
			if (is_tinymce_active()) _output += '&nbsp;';
			window.send_to_editor(_output);
			logichop_modal_close();
		}
		e.preventDefault();
	});

	$('#logichop_insert_conditional_goal').on('click', function (e) {
		var cid = $('#logichop_conditional').val();
		var condition = $('#logichop_conditional option:selected').attr('data-slug');
		var operator = $('#logichop_conditional_goal_not').val();
		var gid = $('#logichop_conditional_goal').val();
		var goal = $('#logichop_conditional_goal option:selected').attr('data-slug');
		var delete_goal = ($('#logichop_conditional_goal_delete').val() == 'delete') ? ' | delete: true' : '';
		if (cid && condition && gid && goal) {
			var _output = '{{ goal: ' + goal + ' | condition: ' + operator + condition + delete_goal + ' }} ';
			if (is_tinymce_active()) _output += '&nbsp;';
			window.send_to_editor(_output);
			logichop_modal_close();
		}
		e.preventDefault();
	});

	$('#logichop_insert_logicblock').on('click', function (e) {
		var block = $('#logichop_logicblock').val();
		if (block) {
			var _output = '[logichop_block id="' + block + '"] ';
			window.send_to_editor(_output);
			logichop_modal_close();
		}
		e.preventDefault();
	});

	$('.logichop_insert_data').on('click', function (e) {
		var id 			= $(this).attr('data-input');
		var target 	= $(this).attr('data-target');
		var _var 		= $(id).val();
		var _default	= ($(id + '_default').val()) 	? ' | default: ' 	+ $(id + '_default').val() 	: '';
		var _event		= ($(id + '_event').val()) 		? ' | event: ' 		+ $(id + '_event').val() 	: '';
		var _case		= ($(id + '_case').val()) 		? ' | case: ' 		+ $(id + '_case').val() 	: '';
		var _class		= ($(id + '_class').val()) 		? ' | class: ' 		+ $(id + '_class').val() 	: '';

		if (_var) {
			var _output = '{{ var: ' + _var + _default + _event + _case + _class + ' }}';
			if (target) {
				var position = $(target).getCursorPosition();
				var content = $(target).val();
				var newContent = content.substr(0, position) + _output + content.substr(position);
				$(target).val(newContent).trigger('keyup');
			} else {
				if (is_tinymce_active()) _output += '&nbsp;';
				window.send_to_editor(_output);
			}
			logichop_modal_close();
		}
		e.preventDefault();
	});

	$('#logichop_insert_else').on('click', function (e) {
		window.send_to_editor('{% else %}' + logichop_get_text() );
		logichop_modal_close();
		e.preventDefault();
	});

	$('.logichop-meta-clear').on('click', function (e) {
		var element = $(this).closest('.logichop-meta');
		element.removeClass('half-set set').children('select, input').val('');
		element.children('input[type="number"]').val('0');
		e.preventDefault();
	});

	$('#_logichop_page_leadscore').on('change', function () {
		if ($(this).val() != 0) {
			$(this).parent().addClass('set');
		} else {
			$(this).parent().removeClass('set');
		}
	});

	$('#_logichop_page_goal').on('change', function () {
		if ($(this).val()) {
			$(this).parent().addClass('set');
		} else {
			$(this).parent().removeClass('set');
		}
	});

	$('#_logichop_disable_js_mode').on('change', function () {
	 if ($(this).val()) {
		 $(this).parent().addClass('set');
		 $('#_disable_meta').show();
	 } else {
		 $(this).parent().removeClass('set');
		 $('#_disable_meta').hide();
	 }
 });

	$('#_logichop_page_goal_condition, #_logichop_page_goal_on_condition').on('change', function () {
		if ($('#_logichop_page_goal_condition').val() && $('#_logichop_page_goal_on_condition').val()) {
			$(this).parent().removeClass('half-set').addClass('set');
		} else if ($('#_logichop_page_goal_condition').val() || $('#_logichop_page_goal_on_condition').val()) {
			$(this).parent().removeClass('set').addClass('half-set');
		} else {
			$(this).parent().removeClass('half-set set');
		}
	});

	$('#_logichop_page_goal_js_event, #_logichop_page_goal_js_element, #_logichop_page_goal_js').on('change', logichop_js_goal_form);
	$('#_logichop_page_goal_js_element').on('keyup', logichop_js_goal_form);

	function logichop_js_goal_form () {
		if ($('#_logichop_page_goal_js_event').val() && $('#_logichop_page_goal_js_element').val() && $('#_logichop_page_goal_js').val()) {
			$(this).parent().removeClass('half-set').addClass('set');
		} else if ($('#_logichop_page_goal_js_event').val() || $('#_logichop_page_goal_js_element').val() || $('#_logichop_page_goal_js').val()) {
			$(this).parent().removeClass('set').addClass('half-set');
		} else {
			$(this).parent().removeClass('half-set set');
		}
	}

	$('#_logichop_page_condition, #_logichop_page_redirect').on('change', logichop_redirect_form);
	$('#_logichop_page_redirect').on('keyup', logichop_redirect_form);

	function logichop_redirect_form () {
		if ($('#_logichop_page_condition').val() && $('#_logichop_page_redirect').val()) {
			$(this).parent().removeClass('half-set').addClass('set');
		} else if ($('#_logichop_page_condition').val() || $('#_logichop_page_redirect').val()) {
			$(this).parent().removeClass('set').addClass('half-set');
		} else {
			$(this).parent().removeClass('half-set set');
		}
	}

	function logichop_get_text () {
		var text = '';
		var visual = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();

		if (visual) {
			text = tinyMCE.activeEditor.selection.getContent();
		} else {
			text = selected_text;
		}

		return text;
	}

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

function logichop_modal_open (data_only) {
	var data_only = (typeof data_only != 'undefined') ? data_only : false;
	if (data_only) {
		jQuery('.logichop_insert_data').attr('data-target', data_only);
		jQuery('#logichop-modal-conditions-tab, #logichop-modal-blocks-tab, #logichop-modal-goals-tab').hide()
		jQuery('#logichop-modal-conditions, #logichop-modal-blocks, #logichop-modal-goals').hide();
		jQuery('#logichop-modal-data').addClass('nav-tab-display-active');
		jQuery('#logichop-modal-data-tab').addClass('nav-tab-active');
	}
	jQuery('#logichop-modal-backdrop').fadeIn();
	jQuery('#logichop-modal-wrap').fadeIn();
}
