/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const DisplayExcerptToggleControl = ( props ) => {
	const { setAttributes, attributes } = props;
	const { displayExcerpt } = attributes;

	return (
		<ToggleControl
			label={ __( 'Toon samenvatting', 'yard-query-block' ) }
			checked={ displayExcerpt }
			onChange={ () => {
				setAttributes( { displayExcerpt: ! displayExcerpt } );
			} }
		/>
	);
};

export default DisplayExcerptToggleControl;
