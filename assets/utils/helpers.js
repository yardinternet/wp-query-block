/**
 * Map posts array to an array to use for options in a select control
 *
 * @param {Array} options - Array to map
 */
export const mapPostsToOptions = ( options = [] ) => {
	return options.map( ( item ) => ( {
		value: item.id,
		label: item.title ? item.title : `#${ item.id }: geen titel`,
	} ) );
};

/**
 * Map post types array to an array to use for options in a select control
 *
 * @param {Array} postTypes - Post types to map
 */
export const mapPostTypesToOptions = ( postTypes = [] ) => {
	return postTypes.map( ( item ) => ( {
		label: item.name.replace( '&#39;', "'" ),
		value: item.slug,
	} ) );
};

/**
 * Map terms array to an array to use for options in a select control
 *
 * @param {Array} terms - Terms to map
 */
export const mapTermsToOptions = ( terms = [] ) => {
	return terms.map( ( term ) => ( {
		label: term.name.replace( '&#39;', "'" ),
		value: term.slug,
	} ) );
};

/**
 * Get subtype that is used in for example a search request
 *
 * @param {Array} postTypes - Post types to map to subtype string
 */
export const getSubtype = ( postTypes = [] ) => {
	let subtype = 'any';

	if ( postTypes.length > 0 ) {
		subtype = postTypes.map( ( type ) => type.value ).join( ',' );
	}

	return subtype;
};
