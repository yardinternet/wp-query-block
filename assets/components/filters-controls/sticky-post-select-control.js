/**
 * External dependencies
 */
import AsyncSelect from 'react-select/async';
import debounce from 'debounce-promise';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { mapPostsToOptions, getSubtype } from '../../utils/helpers';
import { searchPosts } from '../../utils/api';

const StickyPostSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postTypes, enableStickyPost, stickyPost } = attributes;
	const [ defaultOptions, setDefaultOptions ] = useState( [] );

	/**
	 * Load posts on init
	 */
	useEffect( () => {
		const getOptions = async () => {
			const subtype = getSubtype( postTypes );
			const posts = await searchPosts( '', subtype );

			if ( ! posts ) return;

			const options = mapPostsToOptions( posts );
			setDefaultOptions( options );
		};

		getOptions();
	}, [ postTypes ] );

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
		enableStickyPost && (
			<>
				<p className="yard-query-inspector-label">
					{ __(
						'Selecteer het bericht dat als eerste in de lijst moet worden weergegeven.'
					) }
				</p>
				<AsyncSelect
					isClearable={ true }
					defaultOptions={ defaultOptions }
					value={ stickyPost }
					onChange={ ( selectedPost ) =>
						setAttributes( { stickyPost: selectedPost } )
					}
					loadOptions={ debounce( loadOptions, 500 ) }
				/>
			</>
		)
	);
};

export default StickyPostSelectControl;
