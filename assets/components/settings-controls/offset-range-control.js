/**
 * WordPress dependencies
 */
import { RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const OffsetRangeControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { offset } = attributes;

	return (
		<RangeControl
			label={ __( 'Afwijking' ) }
			value={ offset }
			min={ 0 }
			max={ 10 }
			onChange={ ( value ) => setAttributes( { offset: value } ) }
		/>
	);
};

export default OffsetRangeControl;
