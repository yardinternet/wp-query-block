/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { getSubtype } from '../../utils/helpers';
import AsyncSelectPostsControl from '../shared/async-select-posts-control';

const ExcludePostsSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postTypes, enableExcludePosts, excludePosts } = attributes;

	return (
		<AsyncSelectPostsControl
			subtype={ getSubtype( postTypes ) }
			enable={ enableExcludePosts }
			handleChange={ ( selectedPosts ) =>
				setAttributes( { excludePosts: selectedPosts } )
			}
			isClearable={ false }
			isMulti={ true }
			label={ __(
				'Selecteer de berichten die je niet in deze lijst wilt tonen.',
				'yard-query-block'
			) }
			value={ excludePosts }
		/>
	);
};

export default ExcludePostsSelectControl;
