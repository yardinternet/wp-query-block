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

const ExcludePostsSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postTypes, enableExcludePosts, excludePosts } = attributes;
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
		enableExcludePosts && (
			<>
				<p className="yard-query-inspector-label">
					{ __( 'Vul je zoekterm in. Zoekt op hele woorden.' ) }
				</p>
				<AsyncSelect
					isMulti
					backspaceRemovesValue={ false }
					defaultOptions={ defaultOptions }
					value={ excludePosts }
					onChange={ ( selectedPosts ) =>
						setAttributes( { excludePosts: selectedPosts } )
					}
					loadOptions={ debounce( loadOptions, 500 ) }
				/>
			</>
		)
	);
};

export default ExcludePostsSelectControl;
