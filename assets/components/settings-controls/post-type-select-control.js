/**
 * External dependencies
 */
import Select from 'react-select';

/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { fetchRegisteredPostTypes } from '../../utils/api';
import { mapPostTypesToOptions } from '../../utils/helpers';
import { filterPostTypes } from '../../utils/post-types';

const PostTypeSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postTypes } = attributes;
	const [ options, setOptions ] = useState( [] );

	useEffect( () => {
		getOptions();
	}, [] );

	/**
	 * Reset attributes when the post types changed
	 */
	useEffect( () => {
		setAttributes( {
			enableManualSelection: false,
			manualSelectionPosts: [],
			enableStickyPost: false,
			stickyPost: undefined,
			enableExcludePosts: false,
			excludePosts: [],
			enablePostParent: false,
			postParentOption: 'only-parents',
			postParent: undefined,
			enableTaxonomies: false,
			taxonomyTerms: undefined,
		} );
	}, [ postTypes ] );

	/**
	 * Fetch and map all post types without the unwanted post types
	 *
	 * @todo Add external options
	 */
	const getOptions = async () => {
		const allPostTypes = await fetchRegisteredPostTypes();
		const filteredPostTypes = filterPostTypes( allPostTypes );
		const mappedPostTypes = mapPostTypesToOptions( filteredPostTypes );

		setOptions( mappedPostTypes );
	};

	return options.length > 0 ? (
		<>
			<p className="yard-query-inspector-label">
				{ __( 'Selecteer content type' ) }
			</p>
			<Select
				isMulti
				value={ postTypes }
				options={ options }
				onChange={ ( value ) => setAttributes( { postTypes: value } ) }
			/>
		</>
	) : (
		<Spinner />
	);
};

export default PostTypeSelectControl;
