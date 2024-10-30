jQuery(function($) {

	render_logic_bar();

	$('.logic-bar-setting').change(render_logic_bar);
	$('#excerpt, #logichop_logicbar_css_styles').keyup(render_logic_bar);

	function render_logic_bar () {
		var styles = build_bar_styles();
		var content =  $('#excerpt').val();
		$('#logic-bar').html( $.parseHTML( content ) );
		$('#logic-bar-editor-styles').html( styles );
		$('#logichop_logicbar_editor_styles').val( styles );
		$('#logic-bar-user-styles').html( $('#logichop_logicbar_css_styles').val() );
	}

	function build_bar_styles () {
		var slug = $('#logichop_logicbar_slug').val();
		var classname = (slug) ? '.logic-bar-' + slug : '.logichop-logic-bar';
		return classname + ' {' +
						'color:#' + $('#logichop_logicbar_font_color').val() + ';' +
						'background:#' + $('#logichop_logicbar_bg_color').val() + ';' +
					'}' +
					classname + ' a {' +
						'color:#' + $('#logichop_logicbar_link_color').val() + ';' +
						'background-color:#' + $('#logichop_logicbar_button_color').val() + ';' +
					'}';
	}

});
