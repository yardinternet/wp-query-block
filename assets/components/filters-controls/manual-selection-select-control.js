/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { getSubtype } from '../../utils/helpers';
import AsyncSortableSelectPostsControl from '../shared/async-sortable-select-posts-control';

const ManualSelectionSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const {
		postTypes,
		postsPerPage,
		enableManualSelection,
		manualSelectionPosts,
	} = attributes;

	return (
		<AsyncSortableSelectPostsControl
			subtype={ getSubtype( postTypes ) }
			enable={ enableManualSelection }
			handleChange={ ( selectedPosts ) =>
				setAttributes( { manualSelectionPosts: selectedPosts } )
			}
			isOptionDisabled={ () =>
				manualSelectionPosts.length >= postsPerPage
			}
			label={ __(
				'Selecteer de berichten die je in deze lijst wilt tonen.',
				'yard-query-block'
			) }
			value={ manualSelectionPosts }
		/>
	);
};

export default ManualSelectionSelectControl;
