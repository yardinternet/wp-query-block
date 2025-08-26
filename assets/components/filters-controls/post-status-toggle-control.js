/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const PostStatusToggleControl = ( props ) => {
	const { setAttributes, attributes } = props;
	const { enablePostStatus, enableManualSelection } = attributes;

	return (
		! enableManualSelection && (
			<ToggleControl
				label={ __( 'Filter op status', 'yard-query-block' ) }
				checked={ enablePostStatus }
				onChange={ ( state ) =>
					setAttributes( { enablePostStatus: state } )
				}
			/>
		)
	);
};

export default PostStatusToggleControl;
