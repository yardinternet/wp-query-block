/**
 * External dependencies
 */
import AsyncSelect from 'react-select/async';
import debounce from 'debounce-promise';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { mapPostsToOptions, getSubtype } from '../../utils/helpers';
import { searchPosts } from '../../utils/api';

const PostParentSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const {
		postTypes,
		postParentOption,
		postParent,
		enableManualSelection,
		enablePostParent,
	} = attributes;
	const [ defaultOptions, setDefaultOptions ] = useState( [] );

	/**
	 * Load posts as options based on input value
	 *
	 * @param {string}   input
	 * @param {Function} callback
	 */
	const loadOptions = async ( input, callback ) => {
		if ( ! input ) return callback( [] );

		const subtype = getSubtype( postTypes );
		const posts = await searchPosts( input, subtype );

		if ( ! posts ) return callback( [] );

		const options = mapPostsToOptions( posts );
		setDefaultOptions( options );
		callback( options );
	};

	return (
		! enableManualSelection &&
		enablePostParent &&
		postParentOption === 'specific-parent' && (
			<>
				<p className="yard-query-inspector-label">
					{ __(
						'Selecteer het hoofdbericht waar de subberichten van getoond moeten worden.'
					) }
				</p>
				<AsyncSelect
					isClearable={ true }
					defaultOptions={ defaultOptions }
					value={ postParent }
					onChange={ ( selectedPost ) =>
						setAttributes( { postParent: selectedPost } )
					}
					loadOptions={ debounce( loadOptions, 500 ) }
				/>
			</>
		)
	);
};

export default PostParentSelectControl;
