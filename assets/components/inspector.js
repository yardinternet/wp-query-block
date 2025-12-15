/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import { useState } from '@wordpress/element';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import DisplayDateToggleControl from './display-controls/display-date-toggle-control';
import DisplayExcerptToggleControl from './display-controls/display-excerpt-toggle-control';
import DisplayImageToggleControl from './display-controls/display-image-toggle-control';
import DisplayLabelToggleControl from './display-controls/display-label-toggle-control';
import ExcludePostsSelectControl from './filters-controls/exclude-posts-select-control';
import ExcludePostsToggleControl from './filters-controls/exclude-posts-toggle-control';
import ManualSelectionOrderCheckboxControl from './filters-controls/manual-selection-order-checkbox-control';
import ManualSelectionToggleControl from './filters-controls/manual-selection-toggle-control';
import ManualSelectionSelectControl from './filters-controls/manual-selection-select-control';
import NumberOfPostsRangeControl from './settings-controls/number-of-posts-range-control';
import OffsetRangeControl from './settings-controls/offset-range-control';
import OrderSelectControl from './settings-controls/order-select-control';
import OrderbySelectControl from './settings-controls/orderby-select-control';
import PostParentSelectControl from './filters-controls/post-parent-select-control';
import PostParentRadioControl from './filters-controls/post-parent-radio-control';
import PostParentToggleControl from './filters-controls/post-parent-toggle-control';
import PostStatusToggleControl from './filters-controls/post-status-toggle-control';
import PostStatusSelectControl from './filters-controls/post-status-select-control';
import PostTypeSelectControl from './settings-controls/post-type-select-control';
import StickyPostSelectControl from './filters-controls/sticky-post-select-control';
import StickyPostToggleControl from './filters-controls/sticky-post-toggle-control';
import TaxonomyControl from './filters-controls/taxonomy-control';
import ConnectionControl from './filters-controls/connection-control';
import TemplateSelectControl from './display-controls/template-select-control';

import { getInspectorControls } from './../config/inspector-config';

const Inspector = ( props ) => {
	const { attributes } = props;
	const { postTypes } = attributes;
	const inspectorConfig = getInspectorControls( attributes );
	const [ filterPanelOpen, setFilterPanelOpen ] = useState(
		[
			'enableManualSelection',
			'enableStickyPost',
			'enableExcludePosts',
			'enablePostParent',
			'enableTaxonomies',
		].some(
			( key ) =>
				attributes[ key ] === true || attributes[ key ] === 'true'
		)
	);

	return (
		<InspectorControls>
			{ inspectorConfig.showSettingsPanel && (
				<PanelBody
					title={ __( 'Instellingen', 'yard-query-block' ) }
					initialOpen={ true }
				>
					{ inspectorConfig.showPostTypeSelectControl && (
						<PostTypeSelectControl { ...props } />
					) }
					{ postTypes.length > 0 && (
						<>
							{ inspectorConfig.showNumberOfPostsRangeControl && (
								<NumberOfPostsRangeControl { ...props } />
							) }
							{ inspectorConfig.showOffsetRangeControl && (
								<OffsetRangeControl { ...props } />
							) }
							{ inspectorConfig.showOrderbySelectControl && (
								<OrderbySelectControl { ...props } />
							) }
							{ inspectorConfig.showOrderSelectControl && (
								<OrderSelectControl { ...props } />
							) }
						</>
					) }
				</PanelBody>
			) }
			{ postTypes.length > 0 && inspectorConfig.showFiltersPanel && (
				<PanelBody
					title={ __( 'Filters', 'yard-query-block' ) }
					initialOpen={ filterPanelOpen }
					onToggle={ () => setFilterPanelOpen( ( open ) => ! open ) }
				>
					{ inspectorConfig.showManualSelectionToggleControl && (
						<ManualSelectionToggleControl { ...props } />
					) }
					{ inspectorConfig.showManualSelectionSelectControl && (
						<ManualSelectionSelectControl { ...props } />
					) }
					{ inspectorConfig.showManualSelectionOrderCheckboxControl && (
						<ManualSelectionOrderCheckboxControl { ...props } />
					) }
					{ inspectorConfig.showStickyPostToggleControl && (
						<StickyPostToggleControl { ...props } />
					) }
					{ inspectorConfig.showStickyPostSelectControl && (
						<StickyPostSelectControl { ...props } />
					) }
					{ inspectorConfig.showExcludePostsToggleControl && (
						<ExcludePostsToggleControl { ...props } />
					) }
					{ inspectorConfig.showExcludePostsSelectControl && (
						<ExcludePostsSelectControl { ...props } />
					) }
					{ inspectorConfig.showPostParentToggleControl && (
						<PostParentToggleControl { ...props } />
					) }
					{ inspectorConfig.showPostParentRadioControl && (
						<PostParentRadioControl { ...props } />
					) }
					{ inspectorConfig.showPostParentSelectControl && (
						<PostParentSelectControl { ...props } />
					) }
					{ inspectorConfig.showPostStatusToggleControl && (
						<PostStatusToggleControl { ...props } />
					) }
					{ inspectorConfig.showPostStatusSelectControl && (
						<PostStatusSelectControl { ...props } />
					) }
					{ inspectorConfig.showTaxonomyControl && (
						<TaxonomyControl { ...props } />
					) }
					{ inspectorConfig.showConnectionControl && (
						<ConnectionControl { ...props } />
					) }
				</PanelBody>
			) }
			{ postTypes.length > 0 && inspectorConfig.showDisplayPanel && (
				<PanelBody
					title={ __( 'Weergave', 'yard-query-block' ) }
					initialOpen={ false }
				>
					{ inspectorConfig.showTemplateSelectControl && (
						<TemplateSelectControl { ...props } />
					) }
					{ inspectorConfig.showDisplayImageToggleControl && (
						<DisplayImageToggleControl { ...props } />
					) }
					{ inspectorConfig.showDisplayDateToggleControl && (
						<DisplayDateToggleControl { ...props } />
					) }
					{ inspectorConfig.showDisplayExcerptToggleControl && (
						<DisplayExcerptToggleControl { ...props } />
					) }
					{ inspectorConfig.showDisplayLabelToggleControl && (
						<DisplayLabelToggleControl { ...props } />
					) }
					<p>
						{ __(
							'Let op: Niet alle opties hebben invloed op elk sjabloon.',
							'yard-query-block'
						) }
					</p>
				</PanelBody>
			) }
		</InspectorControls>
	);
};

export default Inspector;
