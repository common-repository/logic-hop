<?php

	if (!defined('ABSPATH')) { header('location: /'); die; }
	
	$conditions_text = array (
		'info_default'	=> __('Select a Condition Type to get started.', 'logichop'), 
		'if' 			=> __('If', 'logichop'),
		'and' 			=> __('And', 'logichop'),
		'or' 			=> __('Or', 'logichop'),
		'select' 		=> __('Select', 'logichop'),
		'select_type'	=> __('Select Type', 'logichop'),
		'details' 		=> __('Details', 'logichop'),
		'add_cond' 		=> __('Add Condition', 'logichop'),
		'remove_cond' 	=> __('Remove Condition', 'logichop'),
		'select_type'	=> __('Select Type', 'logichop'),
		'show_logic' 	=> __('Show Conditional Logic', 'logichop'),
		'hide_logic'	=> __('Hide Conditional Logic', 'logichop'),
		'category'		=> __('Condition Category', 'logichop'),
	);
	
	