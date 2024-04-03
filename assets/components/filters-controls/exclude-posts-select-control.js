/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import AsyncSelectPostsControl from '../shared/async-select-posts-control';

const ExcludePostsSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { enableExcludePosts, excludePosts } = attributes;

	return (
		<AsyncSelectPostsControl
			attributes={ attributes }
			enable={ enableExcludePosts }
			handleChange={ ( selectedPosts ) =>
				setAttributes( { excludePosts: selectedPosts } )
			}
			isClearable={ false }
			isMulti={ true }
			label={ __(
				'Selecteer de berichten die je niet in deze lijst wilt tonen.'
			) }
			value={ excludePosts }
		/>
	);
};

export default ExcludePostsSelectControl;
