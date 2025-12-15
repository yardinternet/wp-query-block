/**
 * WordPress dependencies
 */
import { applyFilters } from '@wordpress/hooks';

export const defaultConfig = {
	showSettingsPanel: true,
	showPostTypeSelectControl: true,
	showNumberOfPostsRangeControl: true,
	showOffsetRangeControl: true,
	showOrderbySelectControl: true,
	showOrderSelectControl: true,
	showFiltersPanel: true,
	showManualSelectionToggleControl: true,
	showManualSelectionSelectControl: true,
	showManualSelectionOrderCheckboxControl: true,
	showStickyPostToggleControl: true,
	showStickyPostSelectControl: true,
	showExcludePostsToggleControl: true,
	showExcludePostsSelectControl: true,
	showPostParentToggleControl: true,
	showPostParentRadioControl: true,
	showPostParentSelectControl: true,
	showPostStatusToggleControl: true,
	showPostStatusSelectControl: true,
	showTaxonomyControl: true,
	showConnectionControl: true,
	showDisplayPanel: true,
	showTemplateSelectControl: true,
	showDisplayImageToggleControl: true,
	showDisplayDateToggleControl: true,
	showDisplayExcerptToggleControl: true,
	showDisplayLabelToggleControl: true,
};

/**
 * Customize the available controls for the block editor.
 *
 * @param {Array} attributes - The block attributes.
 * @return {Object} - The inspector controls configuration.
 */
export const getInspectorControls = ( attributes ) => {
	return applyFilters(
		'yard.query-inspector-config',
		defaultConfig,
		attributes
	);
};
