/**
 * External dependencies
 */
import Select from 'react-select';

/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { fetchTermsByTaxonomy } from '../../utils/api';
import { mapTermsToOptions } from '../../utils/helpers';

const TaxonomySelectControl = ( props ) => {
	const { taxonomy, attributes, setAttributes } = props;
	const { taxonomyTerms } = attributes;
	const [ options, setOptions ] = useState( [] );

	useEffect( () => {
		const getTerms = async () => {
			const allTerms = await fetchTermsByTaxonomy( taxonomy.rest_base );
			const mappedTerms = mapTermsToOptions( allTerms );

			setOptions( mappedTerms );
		};

		getTerms();
	}, [ taxonomy.rest_base ] );

	/**
	 * Save the selected terms as an attribute
	 *
	 * @param {array} option - The new terms to save
	 */
	const onChange = ( option ) => {
		const newOption = { [ taxonomy.slug ]: option };

		if ( ! taxonomyTerms ) {
			setAttributes( { taxonomyTerms: newOption } );
		} else {
			const newTaxonomyTerms = { ...taxonomyTerms, ...newOption };
			setAttributes( { taxonomyTerms: newTaxonomyTerms } );
		}
	};

	return (
		<>
			<p className="yard-query-inspector-label">{ taxonomy.name }</p>
			<Select
				isMulti
				value={
					taxonomyTerms?.[ taxonomy.slug ]
						? taxonomyTerms[ taxonomy.slug ]
						: []
				}
				options={ options }
				onChange={ onChange }
			/>
		</>
	);
};

export default TaxonomySelectControl;
