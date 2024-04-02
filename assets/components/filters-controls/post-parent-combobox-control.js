/**
 * WordPress dependencies
 */
import { ComboboxControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { mapPostsToOptions } from '../../utils/helpers';
import { searchPosts, fetchPostById } from '../../utils/api';

const PostParentComboboxControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const {
		postTypes,
		postParentOption,
		postParent,
		enableManualSelection,
		enablePostParent,
	} = attributes;
	const [ options, setOptions ] = useState( [] );

	/**
	 * Check if postParent is set
	 */
	useEffect( () => {
		if ( postParent && postParent !== 0 ) {
			getSelectedPost();
		}
	}, [ postParent ] );

	/**
	 * Fetch saved post by id
	 */
	const getSelectedPost = async () => {
		const post = await fetchPostById( postParent );

		setOptions( mapPostsToOptions( post ) );
	};

	/**
	 * Fetch new options based on the entered search term
	 *
	 * @param {string} searchValue - Entered search term
	 */
	const onFilterValueChange = async ( searchValue ) => {
		let subtype = 'any';

		if ( postTypes.length > 0 ) {
			subtype = postTypes.map( ( type ) => type.value ).join( ',' );
		}

		const posts = await searchPosts( searchValue, subtype );

		setOptions( mapPostsToOptions( posts ) );
	};

	return (
		! enableManualSelection &&
		enablePostParent &&
		postParentOption === 'specific-parent' && (
			<ComboboxControl
				label={ __( 'Selecteer berichten' ) }
				hideLabelFromVision={ true }
				value={ postParent }
				options={ options }
				onChange={ ( value ) =>
					setAttributes( { postParent: value ?? undefined } )
				}
				help={ __(
					'Selecteer het hoofdbericht waar de subberichten van getoond moeten worden.'
				) }
				onFilterValueChange={ onFilterValueChange }
			/>
		)
	);
};

export default PostParentComboboxControl;
