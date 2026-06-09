/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { RadioControl } from '@wordpress/components';

const TAXONOMY_OPTIONS = [
	{
		label: __(
			'Toon berichten van dezelfde taxonomie als het huidige bericht',
			'yard-query-block'
		),
		value: 'current-post-taxonomies',
	},
	{
		label: __(
			'Toon berichten van specifieke taxonomie terms',
			'yard-query-block'
		),
		value: 'specific-terms',
	},
];

const TaxonomyRadioControl = ( { taxonomyOption, setAttributes } ) => {
	const handleChange = ( value ) => {
		setAttributes( {
			taxonomyOption: value,
			taxonomyTerms: {},
		} );
	};

	return (
		<RadioControl
			label={ __( 'Taxonomie opties', 'yard-query-block' ) }
			hideLabelFromVision={ true }
			selected={ taxonomyOption }
			options={ TAXONOMY_OPTIONS }
			onChange={ handleChange }
		/>
	);
};

export default TaxonomyRadioControl;
