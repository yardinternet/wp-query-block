/**
 * External dependencies
 */
import AsyncSelect from 'react-select/async';
import debounce from 'debounce-promise';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState, useEffect, useCallback } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { mapPostsToOptions, getSubtype } from '../../utils/helpers';
import { searchPosts } from '../../utils/api';

const AsyncSelectPostsControl = ( props ) => {
	const {
		attributes,
		enable,
		handleChange,
		isClearable,
		isMulti,
		label,
		value,
	} = props;
	const { postTypes } = attributes;
	const [ defaultOptions, setDefaultOptions ] = useState( [] );

	/**
	 * Search posts based on input value
	 *
	 * @param {string} input
	 */
	const getPostsAsOptions = useCallback(
		async ( input = '' ) => {
			const subtype = getSubtype( postTypes );
			const posts = await searchPosts( input, subtype );
			return posts ? mapPostsToOptions( posts ) : [];
		},
		[ postTypes ]
	);

	/**
	 * Load posts on init
	 */
	useEffect( () => {
		const getOptions = async () => {
			const options = await getPostsAsOptions();
			setDefaultOptions( options );
		};

		getOptions();
	}, [ getPostsAsOptions ] );

	/**
	 * Load posts based on input value when the user start searching
	 *
	 * @param {string}   input
	 * @param {Function} callback
	 */
	const loadOptions = async ( input, callback ) => {
		if ( ! input ) return callback( [] );

		const options = await getPostsAsOptions( input );

		setDefaultOptions( options );
		callback( options );
	};

	return (
		enable && (
			<>
				<p className="yard-query-inspector-label">{ label }</p>
				<AsyncSelect
					backspaceRemovesValue={ false }
					closeMenuOnSelect={ ! isMulti }
					defaultOptions={ defaultOptions }
					isClearable={ isClearable }
					isMulti={ isMulti }
					loadingMessage={ () => __( 'Laden…' ) }
					loadOptions={ debounce( loadOptions, 500 ) }
					noOptionsMessage={ () =>
						__(
							'Geen berichten gevonden. Probeer een andere zoekterm.'
						)
					}
					onChange={ handleChange }
					placeholder={ __( 'Selecteer bericht…' ) }
					value={ value }
				/>
			</>
		)
	);
};

export default AsyncSelectPostsControl;
