/**
 * WordPress dependencies
 */
import { applyFilters } from '@wordpress/hooks';

export const excludePostTypes = [
	'attachment',
	'nav_menu_item',
	'post',
	'tribe_organizer',
	'tribe_venue',
	'visibility_preset',
	'wp_block',
	'wp_navigation',
	'wp_template',
	'wp_template_part',
	'yard-pattern',
];

/**
 * Remove unwanted post types from all post types
 *
 * @param {Object} postTypes - All post types
 */
export const filterPostTypes = ( postTypes = {} ) => {
	const excluded = applyFilters(
		'yard.query-exclude-post-types',
		excludePostTypes
	);

	return Object.keys( postTypes )
		.filter( ( item ) => excluded.indexOf( item ) === -1 )
		.map( ( item ) => postTypes[ item ] );
};
