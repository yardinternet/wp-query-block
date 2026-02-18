/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { RadioControl } from '@wordpress/components';

const CONNECTION_OPTIONS = [
	{
		label: __(
			'Toon berichten verbonden aan dit bericht',
			'yard-query-block'
		),
		value: 'current-post-connections',
	},
	{
		label: __(
			'Toon berichten verbonden aan een specifiek bericht',
			'yard-query-block'
		),
		value: 'specific-post-connections',
	},
];

const ConnectionRadioControl = ( { connectionOption, setAttributes } ) => {
	const handleChange = ( value ) => {
		setAttributes( {
			connectionOption: value,
			connectionPosts: {}, // reset when switching mode
		} );
	};

	return (
		<RadioControl
			label={ __( 'Connectie opties', 'yard-query-block' ) }
			hideLabelFromVision={ true }
			selected={ connectionOption }
			options={ CONNECTION_OPTIONS }
			onChange={ handleChange }
		/>
	);
};

export default ConnectionRadioControl;
