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
					defaultOptions={ defaultOptions }
					isClearable={ true }
					loadingMessage={ () => __( 'Laden…' ) }
					loadOptions={ debounce( loadOptions, 500 ) }
					noOptionsMessage={ () =>
						__(
							'Geen berichten gevonden. Probeer een andere zoekterm.'
						)
					}
					onChange={ ( selectedPost ) =>
						setAttributes( { postParent: selectedPost } )
					}
					placeholder={ __( 'Selecteer bericht…' ) }
					value={ postParent }
				/>
			</>
		)
	);
};

export default PostParentSelectControl;
