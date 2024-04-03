/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import AsyncSortableSelectPostsControl from '../shared/async-sortable-select-posts-control';

const ManualSelectionSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postsPerPage, enableManualSelection, manualSelectionPosts } =
		attributes;

	return (
		<AsyncSortableSelectPostsControl
			attributes={ attributes }
			enable={ enableManualSelection }
			handleChange={ ( selectedPosts ) =>
				setAttributes( { manualSelectionPosts: selectedPosts } )
			}
			isOptionDisabled={ () =>
				manualSelectionPosts.length >= postsPerPage
			}
			label={ __(
				'Selecteer de berichten die je in deze lijst wilt tonen.'
			) }
			value={ manualSelectionPosts }
		/>
	);
};

export default ManualSelectionSelectControl;
