/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const StickyPostToggleControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { enableStickyPost, enableManualSelection } = attributes;

	/**
	 * Save state in attributes and reset stickyPost attribute if the toggle is disabled
	 *
	 * @param {boolean} state - State of toggle
	 */
	const onChange = ( state ) => {
		setAttributes( { enableStickyPost: state } );

		if ( ! state ) {
			setAttributes( { stickyPost: undefined } );
		}
	};

	return (
		! enableManualSelection && (
			<ToggleControl
				label={ __( 'Klevend bericht' ) }
				checked={ enableStickyPost }
				onChange={ onChange }
			/>
		)
	);
};

export default StickyPostToggleControl;
