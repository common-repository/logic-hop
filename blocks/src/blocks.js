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

registerBlockType( 'logic-hop/condition', {
    title: 'Logic Hop Condition',
    icon: icon,
    category: 'layout',
    attributes: {
      condition: {
        type: 'string',
        default: '',
      },
      conditionNot: {
          type: 'boolean',
          default: false,
      },
      conditionName: {
          type: 'string',
          default: '',
      },
    },
    edit: props => {
      const { attributes: { condition, conditionNot, conditionName }, className, setAttributes } = props;
      let options = [
                      { value: '', label: __( 'Select a Condition' ) },
                      { value: 'always_display', label: __( 'Always Display' ) }
                    ];
      if (logichop_block_data && logichop_block_data.conditions) {
        options = [...options, ...logichop_block_data.conditions];
      }
      let theCondition = ( !conditionNot ) ? conditionName : 'Not ' + conditionName;
      return [
        <InspectorControls>
            <PanelBody
                title={ __( 'Condition Settings' ) }
            >
                <PanelRow>
                    <SelectControl
                        label={ __( 'Condition' ) }
                        value={ condition }
                        options={ options }
                        onChange={ condition => {
                            let label = options.find( r => r.value === condition ).label;
                            setAttributes( { condition, conditionName: label } )
                          }
                        }
                    />
                </PanelRow>
                <PanelRow>
                  <ToggleControl
                      label={ __( 'Display when not met:' ) }
                      checked={ conditionNot }
                      onChange={ conditionNot => setAttributes( { conditionNot } ) }
                  />
                </PanelRow>
            </PanelBody>
        </InspectorControls>,
        <div className={ className }>
          <div class="conditionName">
            <strong>Logic Hop Condition:</strong> { theCondition }
          </div>
          <InnerBlocks templateLock={ false } />
        </div>
      ];
    },
    save: props => {
      const { attributes: { condition, conditionNot }, className, setAttributes } = props;
      let slug = (conditionNot) ? '!' + condition : condition;
      return <logichop condition={slug}><InnerBlocks.Content /></logichop>;
    },
} );

registerBlockType( 'logic-hop/goal', {
    title: 'Logic Hop Goal',
    icon: icon,
    category: 'widgets',
    attributes: {
      goal: {
        type: 'string',
        default: '',
      },
      goalName: {
        type: 'string',
        default: '',
      },
      deleteGoal: {
          type: 'boolean',
          default: false,
      },
    },
    edit: props => {
      const { attributes: { goal, goalName, deleteGoal }, className, setAttributes } = props;
      let options = [{ value: '', label: __( 'Select a Goal' ) }];
      if (logichop_block_data && logichop_block_data.goals) {
        options = [...options, ...logichop_block_data.goals];
      }
      let theGoalLabel = ( !deleteGoal ) ? __( 'Set Goal' ) : __( 'Delete Goal' );
      return [
        <InspectorControls>
            <PanelBody
                title={ __( 'Goal Settings' ) }
            >
                <PanelRow>
                    <SelectControl
                        label={ __( 'Goal' ) }
                        value={ goal }
                        options={ options }
                        onChange={ goal => {
                            let label = options.find( r => r.value === goal ).label;
                            setAttributes( { goal, goalName: label } )
                          }
                        }
                    />
                </PanelRow>
                <PanelRow>
                  <ToggleControl
                      label={ __( 'Delete Goal:' ) }
                      checked={ deleteGoal }
                      onChange={ deleteGoal => setAttributes( { deleteGoal } ) }
                  />
                </PanelRow>
            </PanelBody>
        </InspectorControls>,
        <div className={ className }>
          <strong>Logic Hop Goal</strong>
					<em>Not Visible When Published</em>
          <ul>
            <li><strong>{ theGoalLabel }:</strong> { goalName }</li>
          </ul>
        </div>
      ];
    },
    save: props => {
      return null;
    },
} );

registerBlockType( 'logic-hop/conditional-goal', {
    title: 'Logic Hop Conditional Goal',
    icon: icon,
    category: 'widgets',
    attributes: {
      condition: {
        type: 'string',
        default: '',
      },
      conditionNot: {
          type: 'boolean',
          default: false,
      },
      conditionName: {
          type: 'string',
          default: '',
      },
      goal: {
        type: 'string',
        default: '',
      },
      goalName: {
        type: 'string',
        default: '',
      },
      deleteGoal: {
          type: 'boolean',
          default: false,
      },
    },
    edit: props => {
      const { attributes: { condition, conditionNot, conditionName, goal, goalName, deleteGoal }, className, setAttributes } = props;
      let conditions = [{ value: '', label: __( 'Select a Condition' ) }];
      if (logichop_block_data && logichop_block_data.conditions) {
        conditions = [...conditions, ...logichop_block_data.conditions];
      }
      let goals = [{ value: '', label: __( 'Select a Goal' ) }];
      if (logichop_block_data && logichop_block_data.goals) {
        goals = [...goals, ...logichop_block_data.goals];
      }
      let theGoalLabel = ( !deleteGoal ) ? __( 'Set Goal' ) : __( 'Delete Goal' );
      let theCondition = ( !conditionNot ) ? conditionName : 'Not ' + conditionName;
      return [
        <InspectorControls>
            <PanelBody
                title={ __( 'Conditional Goal Settings' ) }
            >
                <PanelRow>
                    <SelectControl
                        label={ __( 'GoalsSession' ) }
                        value={ goal }
                        options={ goals }
                        onChange={ goal => {
                            let label = goals.find( r => r.value === goal ).label;
                            setAttributes( { goal, goalName: label } )
                          }
                        }
                    />
                </PanelRow>
                <PanelRow>
                  <ToggleControl
                      label={ __( 'Delete Goal:' ) }
                      checked={ deleteGoal }
                      onChange={ deleteGoal => setAttributes( { deleteGoal } ) }
                  />
                </PanelRow>
                <PanelRow>
                    <SelectControl
                        label={ __( 'Condition' ) }
                        value={ condition }
                        options={ conditions }
                        onChange={ condition => {
                            let label = conditions.find( r => r.value === condition ).label;
                            setAttributes( { condition, conditionName: label } )
                          }
                        }
                    />
                </PanelRow>
                <PanelRow>
                  <ToggleControl
                      label={ __( 'Complete when not met:' ) }
                      checked={ conditionNot }
                      onChange={ conditionNot => setAttributes( { conditionNot } ) }
                  />
                </PanelRow>
            </PanelBody>
        </InspectorControls>,
        <div className={ className }>
          <strong>Logic Hop Conditional Goal</strong>
					<em>Not Visible When Published</em>
          <ul>
            <li><strong>Condition:</strong> { theCondition }</li>
            <li><strong>{ theGoalLabel }:</strong> { goalName }</li>
          </ul>
        </div>
      ];
    },
    save: props => {
      return null;
    },
} );
