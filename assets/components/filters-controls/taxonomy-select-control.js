/**
 * External dependencies
 */
import AsyncSelect from 'react-select/async';
import debounce from 'debounce-promise';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useCallback, useEffect, useMemo, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { fetchTermsByTaxonomy } from '../../utils/api';
import { mapTermsToOptions } from '../../utils/helpers';

const TaxonomySelectControl = ( props ) => {
	const { taxonomy, attributes, setAttributes } = props;
	const { taxonomyTerms } = attributes;
	const [ defaultOptions, setDefaultOptions ] = useState( [] );

	const getTermsAsOptions = useCallback(
		async ( input = '' ) => {
			const terms = await fetchTermsByTaxonomy(
				taxonomy.rest_base,
				input
			);
			return terms ? mapTermsToOptions( terms ) : [];
		},
		[ taxonomy.rest_base ]
	);

	useEffect( () => {
		const getOptions = async () => {
			const options = await getTermsAsOptions();
			setDefaultOptions( options );
		};

		getOptions();
	}, [ getTermsAsOptions ] );

	const loadOptions = useMemo(
		() => debounce( getTermsAsOptions, 500 ),
		[ getTermsAsOptions ]
	);

	/**
	 * Save the selected terms as an attribute
	 *
	 * @param {Array} option - The new terms to save
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
			<AsyncSelect
				backspaceRemovesValue={ false }
				closeMenuOnSelect={ false }
				defaultOptions={ defaultOptions }
				isMulti
				loadingMessage={ () =>
					__( 'Aan het zoeken…', 'yard-query-block' )
				}
				loadOptions={ loadOptions }
				noOptionsMessage={ () =>
					__( 'Geen items gevonden.', 'yard-query-block' )
				}
				onChange={ onChange }
				placeholder={ __( 'Selecteer een item', 'yard-query-block' ) }
				value={
					taxonomyTerms?.[ taxonomy.slug ]
						? taxonomyTerms[ taxonomy.slug ]
						: []
				}
			/>
		</>
	);
};

export default TaxonomySelectControl;
