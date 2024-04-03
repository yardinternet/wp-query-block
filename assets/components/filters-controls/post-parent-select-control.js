/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import AsyncSelectPostsControl from '../shared/async-select-posts-control';

const PostParentSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const {
		postParentOption,
		postParent,
		enableManualSelection,
		enablePostParent,
	} = attributes;

	return (
		<AsyncSelectPostsControl
			attributes={ attributes }
			enable={
				! enableManualSelection &&
				enablePostParent &&
				postParentOption === 'specific-parent'
			}
			handleChange={ ( selectedPost ) =>
				setAttributes( { postParent: selectedPost } )
			}
			isClearable={ true }
			isMulti={ false }
			label={ __(
				'Selecteer het hoofdbericht waar de subberichten van getoond moeten worden.'
			) }
			value={ postParent }
		/>
	);
};

export default PostParentSelectControl;
