/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import DisplayDateToggleControl from './display-controls/display-date-toggle-control';
import DisplayExcerptToggleControl from './display-controls/display-excerpt-toggle-control';
import DisplayImageToggleControl from './display-controls/display-image-toggle-control';
import DisplayLabelToggleControl from './display-controls/display-label-toggle-control';
import ExcludePostsToggleControl from './filters-controls/exclude-posts-toggle-control';
import ManualSelectionToggleControl from './filters-controls/manual-selection-toggle-control';
import NumberOfPostsRangeControl from './settings-controls/number-of-posts-range-control';
import OffsetRangeControl from './settings-controls/offset-range-control';
import OrderSelectControl from './settings-controls/order-select-control';
import OrderbySelectControl from './settings-controls/orderby-select-control';
import PostParentComboboxControl from './filters-controls/post-parent-combobox-control';
import PostParentRadioControl from './filters-controls/post-parent-radio-control';
import PostParentToggleControl from './filters-controls/post-parent-toggle-control';
import PostTypeSelectControl from './settings-controls/post-type-select-control';
import StickyPostComboboxControl from './filters-controls/sticky-post-combobox-control';
import StickyPostToggleControl from './filters-controls/sticky-post-toggle-control';
import TaxonomyControl from './filters-controls/taxonomy-control';
import TemplateSelectControl from './display-controls/template-select-control';

const Inspector = ( props ) => {
	const { attributes } = props;
	const { postTypes } = attributes;

	return (
		<InspectorControls>
			<PanelBody title={ __( 'Instellingen' ) } initialOpen={ true }>
				<PostTypeSelectControl { ...props } />
				{ postTypes.length > 0 && (
					<>
						<NumberOfPostsRangeControl { ...props } />
						<OffsetRangeControl { ...props } />
						<OrderbySelectControl { ...props } />
						<OrderSelectControl { ...props } />
					</>
				) }
			</PanelBody>
			{ postTypes.length > 0 && (
				<PanelBody title={ __( 'Filters' ) } initialOpen={ false }>
					<ManualSelectionToggleControl { ...props } />
					{ /* @todo Add multi select control to select manual posts */ }
					<StickyPostToggleControl { ...props } />
					<StickyPostComboboxControl { ...props } />
					<ExcludePostsToggleControl { ...props } />
					{ /* @todo Add multi select control to select posts to exclude */ }
					<PostParentToggleControl { ...props } />
					<PostParentRadioControl { ...props } />
					<PostParentComboboxControl { ...props } />
					<TaxonomyControl { ...props } />
				</PanelBody>
			) }
			{ postTypes.length > 0 && (
				<PanelBody title={ __( 'Weergave' ) } initialOpen={ false }>
					<TemplateSelectControl { ...props } />
					<DisplayImageToggleControl { ...props } />
					<DisplayDateToggleControl { ...props } />
					<DisplayExcerptToggleControl { ...props } />
					<DisplayLabelToggleControl { ...props } />
					<p>
						{ __(
							'Let op: Niet alle opties hebben invloed op elk sjabloon.'
						) }
					</p>
				</PanelBody>
			) }
		</InspectorControls>
	);
};

export default Inspector;
