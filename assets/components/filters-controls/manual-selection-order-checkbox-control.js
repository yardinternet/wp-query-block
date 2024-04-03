/**
 * WordPress dependencies
 */
import { CheckboxControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const ManualSelectionOrderCheckboxControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { enableManualSelection, keepManualSelectionOrder } = attributes;

	return (
		enableManualSelection && (
			<CheckboxControl
				label={ __( 'Behoud geselecteerde volgorde' ) }
				checked={ keepManualSelectionOrder }
				help={ __(
					'Versleep de labels om de volgorde van de berichten te veranderen.'
				) }
				onChange={ () =>
					setAttributes( {
						keepManualSelectionOrder: ! keepManualSelectionOrder,
					} )
				}
			/>
		)
	);
};

export default ManualSelectionOrderCheckboxControl;
