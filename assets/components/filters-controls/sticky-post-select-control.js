/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import AsyncSelectPostsControl from '../shared/async-select-posts-control';

const StickyPostSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { enableStickyPost, stickyPost } = attributes;

	return (
		<AsyncSelectPostsControl
			attributes={ attributes }
			enable={ enableStickyPost }
			handleChange={ ( selectedPost ) =>
				setAttributes( { stickyPost: selectedPost } )
			}
			isClearable={ true }
			isMulti={ false }
			label={ __(
				'Selecteer het bericht dat als eerste in de lijst moet worden weergegeven.'
			) }
			value={ stickyPost }
		/>
	);
};

export default StickyPostSelectControl;
