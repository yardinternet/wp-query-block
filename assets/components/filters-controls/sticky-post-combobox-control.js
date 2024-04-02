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

const StickyPostComboboxControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postTypes, enableStickyPost, stickyPost } = attributes;
	const [ options, setOptions ] = useState( [] );

	/**
	 * Check if stickyPost is set
	 */
	useEffect( () => {
		if ( stickyPost ) {
			getSelectedPost();
		}
	}, [ stickyPost ] );

	/**
	 * Fetch saved post by id
	 */
	const getSelectedPost = async () => {
		const post = await fetchPostById( stickyPost );

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
		enableStickyPost && (
			<ComboboxControl
				label={ __( 'Selecteer bericht' ) }
				hideLabelFromVision={ true }
				value={ stickyPost }
				options={ options }
				onChange={ ( value ) => setAttributes( { stickyPost: value } ) }
				help={ __(
					'Selecteer het bericht dat als eerste in de lijst moet worden weergegeven.'
				) }
				onFilterValueChange={ onFilterValueChange }
			/>
		)
	);
};

export default StickyPostComboboxControl;
