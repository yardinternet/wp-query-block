/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const PostParentToggleControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { enablePostParent, enableManualSelection } = attributes;

	/**
	 * Save state in attributes and remove post_parent parameter if the toggle is disabled
	 *
	 * @param {boolean} state - State of toggle
	 */
	const onChange = ( state ) => {
		setAttributes( { enablePostParent: state } );

		if ( ! state ) {
			setAttributes( { postParent: undefined } );
		}
	};

	return (
		! enableManualSelection && (
			<ToggleControl
				label={ __( 'Filter op hoofdbericht' ) }
				checked={ enablePostParent }
				onChange={ onChange }
			/>
		)
	);
};

export default PostParentToggleControl;
