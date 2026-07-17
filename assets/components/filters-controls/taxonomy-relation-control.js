/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { RadioControl } from '@wordpress/components';

const RELATION_OPTIONS = [
	{
		label: __(
			'EN (berichten moeten aan alle taxonomieën voldoen)',
			'yard-query-block'
		),
		value: 'AND',
	},
	{
		label: __(
			'OF (berichten mogen aan één van de taxonomieën voldoen)',
			'yard-query-block'
		),
		value: 'OR',
	},
];

const TaxonomyRelationControl = ( { taxonomyRelation, setAttributes } ) => {
	const handleChange = ( value ) => {
		setAttributes( { taxonomyRelation: value } );
	};

	return (
		<RadioControl
			label={ __( 'Relatie tussen taxonomieën', 'yard-query-block' ) }
			selected={ taxonomyRelation }
			options={ RELATION_OPTIONS }
			onChange={ handleChange }
		/>
	);
};

export default TaxonomyRelationControl;
