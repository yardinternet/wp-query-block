/**
 * External dependencies
 */
import Select from 'react-select';

/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { Spinner } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';
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

	const isMulti = applyFilters(
		'yard.query-post-type-select-control-is-multi',
		true
	);

	/**
	 * Fetch and map all post types without the unwanted post types
	 *
	 * @todo Add external options
	 */
	useEffect( () => {
		const getOptions = async () => {
			const allPostTypes = await fetchRegisteredPostTypes();
			const filteredPostTypes = filterPostTypes( allPostTypes );
			const mappedPostTypes = mapPostTypesToOptions( filteredPostTypes );

			setOptions( mappedPostTypes );
		};

		getOptions();
	}, [] );

	/**
	 * Handle post type change and reset attributes
	 *
	 * @param {Array} value
	 */
	const onChange = ( value ) => {
		setAttributes( {
			postTypes: isMulti ? value : [ value ],
			enableManualSelection: false,
			manualSelectionPosts: [],
			enableStickyPost: false,
			stickyPost: {},
			enableExcludePosts: false,
			excludePosts: [],
			enablePostParent: false,
			postParentOption: 'only-parents',
			postParent: {},
			enableTaxonomies: false,
			taxonomyTerms: undefined,
		} );
	};

	return options.length > 0 ? (
		<>
			<p className="yard-query-inspector-label">
				{ __( 'Selecteer content type', 'yard-query-block' ) }
			</p>
			<Select
				isMulti={ isMulti }
				value={ postTypes }
				options={ options }
				onChange={ onChange }
			/>
		</>
	) : (
		<Spinner />
	);
};

export default PostTypeSelectControl;
