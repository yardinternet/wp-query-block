/**
 * WordPress dependencies
 */
import { ComboboxControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { mapPostsToOptions, getSubtype } from '../../utils/helpers';
import { searchPosts, fetchPostById } from '../../utils/api';

const StickyPostComboboxControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postTypes, enableStickyPost, stickyPost } = attributes;
	const [ options, setOptions ] = useState( [] );

	/**
	 * Check if stickyPost is set
	 */
	useEffect( () => {
		const getSelectedPost = async () => {
			const post = await fetchPostById( stickyPost );

			setOptions( mapPostsToOptions( post ) );
		};

		if ( stickyPost ) {
			getSelectedPost();
		}
	}, [ stickyPost ] );

	/**
	 * Fetch new options based on the entered search term
	 *
	 * @param {string} searchValue - Entered search term
	 */
	const onFilterValueChange = async ( searchValue ) => {
		const subtype = getSubtype( postTypes );
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
