/**
 * Internal dependencies
 */
import AsyncSortableSelectPostsControl from '../shared/async-sortable-select-posts-control';

const ConnectionSelectControl = ( props ) => {
	const { connection, attributes, setAttributes } = props;
	const { connectionPosts, postsPerPage } = attributes;

	/**
	 * Save the selected posts as an attribute
	 *
	 * @param {Array} selectedPosts - The new posts to save
	 */
	const onChange = ( selectedPosts ) => {
		const newOption = { [ connection.value ]: selectedPosts };

		if ( ! connectionPosts ) {
			setAttributes( { connectionPosts: newOption } );
		} else {
			const newConnectionPosts = { ...connectionPosts, ...newOption };
			setAttributes( { connectionPosts: newConnectionPosts } );
		}
	};

	return (
		<AsyncSortableSelectPostsControl
			subtype={ connection.value }
			enable={ true }
			handleChange={ onChange }
			isOptionDisabled={ () => connectionPosts?.length >= postsPerPage }
			label={ connection.label }
			value={ connectionPosts[ connection.value ] || [] }
		/>
	);
};

export default ConnectionSelectControl;
