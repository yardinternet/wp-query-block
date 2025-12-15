/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const ConnectionToggleControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { enableConnection } = attributes;

	/**
	 * Save state in attributes and reset connection posts attribute if the toggle is disabled
	 *
	 * @param {boolean} state - State of toggle
	 */
	const onChange = ( state ) => {
		setAttributes( { enableConnection: state } );

		if ( ! state ) {
			setAttributes( { connectionPosts: {} } );
		}
	};

	return (
		<ToggleControl
			label={ __( 'Filter op connectie', 'yard-query-block' ) }
			checked={ enableConnection }
			onChange={ onChange }
		/>
	);
};

export default ConnectionToggleControl;
