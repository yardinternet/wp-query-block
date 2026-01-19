/**
 * Internal dependencies
 */
import AsyncSelectPostsControl from '../shared/async-select-posts-control';

const ConnectionSelectControl = ( props ) => {
	const { connection, attributes, setAttributes } = props;
	const { connectionPosts } = attributes;

	/**
	 * Save the selected posts as an attribute
	 *
	 * @param {Array} selectedPosts - The new posts to save
	 */
	const onChange = ( selectedPosts ) => {
		const newOption = {
			[ connection.value ]:
				selectedPosts === null ? undefined : selectedPosts,
		};

		if ( ! connectionPosts ) {
			setAttributes( { connectionPosts: newOption } );
		} else {
			const newConnectionPosts = { ...connectionPosts, ...newOption };
			setAttributes( { connectionPosts: newConnectionPosts } );
		}
	};

	return (
		<AsyncSelectPostsControl
			subtype={ connection.value }
			enable={ true }
			handleChange={ onChange }
			isClearable={ true }
			isMulti={ false }
			label={ connection.label }
			value={ connectionPosts[ connection.value ] }
		/>
	);
};

export default ConnectionSelectControl;
