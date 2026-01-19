/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { getSubtype } from '../../utils/helpers';
import AsyncSelectPostsControl from '../shared/async-select-posts-control';

const PostParentSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const {
		postTypes,
		postParentOption,
		postParent,
		enableManualSelection,
		enablePostParent,
	} = attributes;

	return (
		<AsyncSelectPostsControl
			subtype={ getSubtype( postTypes ) }
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
				'Selecteer het hoofdbericht waar de subberichten van getoond moeten worden.',
				'yard-query-block'
			) }
			value={ postParent }
		/>
	);
};

export default PostParentSelectControl;
