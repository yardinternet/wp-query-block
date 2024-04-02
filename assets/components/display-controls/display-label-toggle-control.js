/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const DisplayLabelToggleControl = ( props ) => {
	const { setAttributes, attributes } = props;
	const { displayLabel } = attributes;

	return (
		<ToggleControl
			label={ __( 'Toon label' ) }
			checked={ displayLabel }
			onChange={ () => {
				setAttributes( { displayLabel: ! displayLabel } );
			} }
		/>
	);
};

export default DisplayLabelToggleControl;
