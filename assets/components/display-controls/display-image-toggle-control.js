/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const DisplayImageToggleControl = ( props ) => {
	const { setAttributes, attributes } = props;
	const { displayImage } = attributes;

	return (
		<ToggleControl
			label={ __( 'Toon afbeelding' ) }
			checked={ displayImage }
			onChange={ () => {
				setAttributes( { displayImage: ! displayImage } );
			} }
		/>
	);
};

export default DisplayImageToggleControl;
