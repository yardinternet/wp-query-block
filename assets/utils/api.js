/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Fetch all registered post types
 *
 * @see https://developer.wordpress.org/rest-api/reference/post-types/
 */
export const fetchRegisteredPostTypes = () => {
	return apiFetch( { path: 'wp/v2/types?per_page=100' } );
};

/**
 * Search through posts by subtype with a search term
 *
 * @param {string} search
 * @param {string} subtype
 *
 * @see https://developer.wordpress.org/rest-api/reference/search-results/
 */
export const searchPosts = ( search = '', subtype = 'any' ) => {
	return apiFetch( {
		path: `wp/v2/search?subtype=${ subtype }&search=${ search }&per_page=100`,
	} );
};

/**
 * Fetch post by an id
 *
 * @param {string} id
 *
 * @see https://developer.wordpress.org/rest-api/reference/search-results/
 */
export const fetchPostById = ( id = '' ) => {
	return apiFetch( { path: `wp/v2/search?include=${ id }` } );
};

/**
 * Fetch taxonomies by post type
 *
 * @param {string} type
 *
 * @see https://developer.wordpress.org/rest-api/reference/taxonomies/
 */
export const fetchTaxonomiesByPostType = ( type = '' ) => {
	return apiFetch( { path: `wp/v2/taxonomies?type=${ type }&per_page=100` } );
};

/**
 * Fetch terms by taxonomy
 *
 * @param {string} taxonomy
 */
export const fetchTermsByTaxonomy = ( taxonomy = '' ) => {
	return apiFetch( { path: `wp/v2/${ taxonomy }?per_page=100` } );
};

/**
 * Fetch available block settings
 */
export const fetchBlockSettings = () => {
	return apiFetch( { path: 'yard/query-block/v1/settings' } );
};
