/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const NextOccurrenceToggleControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postTypes, nextOccurrenceOnly, enableManualSelection } = attributes;

	const includesYardEvents = postTypes.some(
		( type ) => type.value === 'yard-event'
	);

	return (
		includesYardEvents &&
		! enableManualSelection && (
			<ToggleControl
				label={ __(
					'Toon per reeks alleen de eerstvolgende datum',
					'yard-query-block'
				) }
				checked={ nextOccurrenceOnly }
				onChange={ ( state ) =>
					setAttributes( { nextOccurrenceOnly: state } )
				}
			/>
		)
	);
};

export default NextOccurrenceToggleControl;
