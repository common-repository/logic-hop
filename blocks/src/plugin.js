import './_editor.scss';
import icon from './_icon';

const { __ } = wp.i18n;
const { registerBlockType, createBlock } = wp.blocks;
const { InspectorControls, InnerBlocks } = wp.editor;
const { withAPIData, TextControl, PanelBody, PanelRow, ToggleControl, SelectControl, Dropdown, Modal } = wp.components;
const { withSelect, withDispatch } = wp.data;
const { Fragment, Component } = wp.element;
const { compose } = wp.compose
const { registerPlugin } = wp.plugins;
const { PluginSidebar, PluginPostStatusInfo, PluginSidebarMoreMenuItem } = wp.editPost;

class LogicHopGutenberg extends Component {

	constructor () {
	 	super();
	 	this.state = {
			datavar_var: '',
			datavar_case: '',
			datavar_default: '',
			datavar_prepend: '',
			datavar_append: '',
		 	datavar_logic_tag: '',
			render_settings: ( logichop_block_data.disable_js == '' ) ? __( 'Post-Load' ) : __( 'Pre-Load' ),
	 	};
 	}

	variableInputsChange ( prop, value ) {
		this.setState({ [prop]: value }, function () {
			let logic_tag = '';
			if ( this.state.datavar_var != '' ) {
				logic_tag += '{{ var: ' + this.state.datavar_var;
				if ( this.state.datavar_default != '' ) logic_tag += ' | default: ' + this.state.datavar_default;
				if ( this.state.datavar_case != '' ) logic_tag += ' | case: ' + this.state.datavar_case;
				if ( this.state.datavar_prepend != '' ) logic_tag += ' | prepend: ' + this.state.datavar_prepend;
				if ( this.state.datavar_append != '' ) logic_tag += ' | append: ' + this.state.datavar_append;
				logic_tag += ' }}';
			}
			this.setState({
				'datavar_logic_tag': logic_tag
			});
		});
	}

	render() {
		const {
			meta: {
				_logichop_page_leadscore: _logichop_page_leadscore,
        _logichop_page_lead_freq: _logichop_page_lead_freq,
				_logichop_disable_js_mode: _logichop_disable_js_mode,
			} = {},
			updateMeta
		} = this.props;

		let variables = [{ value: '', label: __( 'Select a Variable' ) }];
		if (logichop_block_data && logichop_block_data.variables) {
			variables = [...variables, ...logichop_block_data.variables];
		}

		const self = this;

		let render_details = '';
		let render_settings = '';
		if ( logichop_block_data.caching ) {
			render_details = <PluginPostStatusInfo
					className="logic-hop-post-status-info"
					>
					<label>{ __( 'Logic Tag Render Settings: ' ) }</label>
					{ this.state.render_settings }
				</PluginPostStatusInfo>;

			render_settings = <PanelBody
					title={ __( 'Page Render Settings' ) }
					>
					<SelectControl
						label={ __( 'Logic Tags Rendered' ) }
						value={_logichop_disable_js_mode}
						options={[
							{ value: '', label: __( 'After page load with Javascript' ) },
							{ value: 'true', label: __( 'Before page load with PHP' ) },
						]}
						onChange={ ( value ) => {
							updateMeta( { _logichop_disable_js_mode: value || '' } );
							this.state.render_settings = ( value == '' ) ? __( 'Post-Load' ) : __( 'Pre-Load' );
						} }
					/>
				</PanelBody>;
		}

		return (
			<Fragment>
        <PluginPostStatusInfo
          className="logic-hop-post-status-info"
        >
					<label>{ __( 'Logic Hop Lead Score: ' ) }</label>
					{ _logichop_page_leadscore }
        </PluginPostStatusInfo>

				{ render_details }

				<PluginSidebarMoreMenuItem
					name="logic-hop-sidebar"
					type="sidebar"
					target="logic-hop-sidebar"
				>
					{ __( 'Logic Hop' ) }
				</PluginSidebarMoreMenuItem>
				<PluginSidebar
					name="logic-hop-sidebar"
					title={ __( 'Logic Hop' ) }
				>
          <PanelBody
							title={ __( 'Lead Score' ) }
					>
							<TextControl
		            label={ __( 'Lead Score' ) }
		            value={_logichop_page_leadscore}
		            type="number"
		            onChange={ ( value ) => {
		              updateMeta( { _logichop_page_leadscore: value || 0 } );
		            } }
		          />
		            <SelectControl
		              label={ __( 'Lead Score Frequency' ) }
		              value={_logichop_page_lead_freq}
		              options={[
		                { value: '', label: __( 'Select a Frequency' ) },
		                { value: 'every', label: __( 'Increment on every complete' ) },
		                { value: 'first', label: __( 'Increment on first complete only' ) },
		                { value: 'session', label: __( 'Increment on first complete each session' ) },
		                { value: 'set', label: __( 'Set as Lead Score' ) },
		              ]}
		              onChange={ ( value ) => {
		  							updateMeta( { _logichop_page_lead_freq: value || 'every' } );
		  						} }
		            />
          </PanelBody>
					{ render_settings }
					<PanelBody
							title={ __( 'Data Variables' ) }
					>
						<SelectControl
							label={ __( 'Variable' ) }
							options={ variables }
							onChange={( value ) => {
									this.variableInputsChange( 'datavar_var', value );
								}
							}
						/>
						<TextControl
						label={ __( 'Logic Tag' ) }
						type="text"
						value={this.state.datavar_logic_tag}
						onFocus={( e ) => {
							e.target.select();
							document.execCommand('copy');
						}}
						onChange={() => { return; }}
						help={ __( 'Paste into a Block to display variable.' ) }
						/>
						<SelectControl
							label={ __( 'Case' ) }
							options={[
								{ value: '', label: __( 'Default Case' ) },
								{ value: 'first', label: __( 'Uppercase first letter' ) },
								{ value: 'words', label: __( 'Uppercase words' ) },
								{ value: 'lower', label: __( 'Lowercase' ) },
								{ value: 'upper', label: __( 'Uppercase' ) },
							]}
							onChange={ ( value ) => {
								this.variableInputsChange( 'datavar_case', value );
							} }
						/>
						<TextControl
						label={ __( 'Default' ) }
						type="text"
						value={this.state.datavar_default}
						onChange={ ( value ) => {
							this.variableInputsChange( 'datavar_default', value );
						} }
						/>
						<TextControl
						label={ __( 'Prepend' ) }
						type="text"
						value={this.state.datavar_prepend}
						onChange={ ( value ) => {
							this.variableInputsChange( 'datavar_prepend', value );
						} }
						/>
						<TextControl
						label={ __( 'Append' ) }
						type="text"
						value={this.state.datavar_append}
						onChange={ ( value ) => {
							this.variableInputsChange( 'datavar_append', value );
						} }
						/>
					</PanelBody>
				</PluginSidebar>
			</Fragment>
		);
	}
}

const applyWithSelect = withSelect( ( select ) => {
	const { getEditedPostAttribute } = select( 'core/editor' );
	return {
		meta: getEditedPostAttribute( 'meta' ),
	};
} );

const applyWithDispatch = withDispatch( ( dispatch, { meta } ) => {
	const { editPost } = dispatch( 'core/editor' );
	return {
		updateMeta( newMeta ) {
			editPost( { meta: { ...meta, ...newMeta } } );
		},
	};
} );

const logichopRender = compose( [
	applyWithSelect,
	applyWithDispatch
] )( LogicHopGutenberg );

registerPlugin( 'logic-hop', {
	icon: icon,
	render: logichopRender,
} );
