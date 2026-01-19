/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { getSubtype } from '../../utils/helpers';
import AsyncSelectPostsControl from '../shared/async-select-posts-control';

const StickyPostSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postTypes, enableStickyPost, stickyPost } = attributes;

	return (
		<AsyncSelectPostsControl
			subtype={ getSubtype( postTypes ) }
			enable={ enableStickyPost }
			handleChange={ ( selectedPost ) =>
				setAttributes( { stickyPost: selectedPost } )
			}
			isClearable={ true }
			isMulti={ false }
			label={ __(
				'Selecteer het bericht dat als eerste in de lijst moet worden weergegeven.',
				'yard-query-block'
			) }
			value={ stickyPost }
		/>
	);
};

export default StickyPostSelectControl;
