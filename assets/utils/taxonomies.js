/**
 * WordPress dependencies
 */
import { applyFilters } from '@wordpress/hooks';

export const excludeTaxonomies = [
	'category',
	'nav_menu',
	'post_tag',
	'yard-pattern-category',
];

/**
 * Remove unwanted taxonomies from all taxonomies
 *
 * @param {Object} taxonomies - All taxonomies
 */
export const filterTaxonomies = ( taxonomies = {} ) => {
	const excluded = applyFilters(
		'yard.query-exclude-taxonomies',
		excludeTaxonomies
	);

	return Object.keys( taxonomies )
		.filter( ( item ) => excluded.indexOf( item ) === -1 )
		.map( ( item ) => taxonomies[ item ] );
};
