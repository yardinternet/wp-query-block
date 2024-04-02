/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const DisplayDateToggleControl = ( props ) => {
	const { setAttributes, attributes } = props;
	const { displayDate } = attributes;

	return (
		<ToggleControl
			label={ __( 'Toon datum' ) }
			checked={ displayDate }
			onChange={ () => {
				setAttributes( { displayDate: ! displayDate } );
			} }
		/>
	);
};

export default DisplayDateToggleControl;
