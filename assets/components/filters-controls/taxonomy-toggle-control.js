/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const TaxonomyToggleControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { enableTaxonomies } = attributes;

	/**
	 * Save state in attributes and reset taxonomies attribute if the toggle is disabled
	 *
	 * @param {boolean} state - State of toggle
	 */
	const onChange = ( state ) => {
		setAttributes( { enableTaxonomies: state } );

		if ( ! state ) {
			setAttributes( { taxonomyTerms: undefined } );
		}
	};

	return (
		<ToggleControl
			label={ __( 'Filter op taxonomie' ) }
			checked={ enableTaxonomies }
			onChange={ onChange }
		/>
	);
};

export default TaxonomyToggleControl;
