/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const DisplaySubtitleToggleControl = ( props ) => {
	const { setAttributes, attributes } = props;
	const { displaySubtitle } = attributes;

	return (
		<ToggleControl
			label={ __( 'Toon subtitel', 'yard-query-block' ) }
			checked={ displaySubtitle }
			onChange={ () => {
				setAttributes( { displaySubtitle: ! displaySubtitle } );
			} }
		/>
	);
};

export default DisplaySubtitleToggleControl;
