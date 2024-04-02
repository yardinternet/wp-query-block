/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import TaxonomyToggleControl from './taxonomy-toggle-control';
import TaxonomySelectControl from './taxonomy-select-control';
import { fetchTaxonomiesByPostType } from '../../utils/api';
import { filterTaxonomies } from '../../utils/taxonomies';

const TaxonomyControl = ( props ) => {
	const { attributes } = props;
	const { postTypes, enableTaxonomies, enableManualSelection } = attributes;
	const [ taxonomies, setTaxonomies ] = useState( [] );

	/**
	 * Fetch taxonomies of selected post types
	 */
	useEffect( () => {
		const getTaxonomies = async () => {
			let allTaxonomies = {};

			for ( const key in postTypes ) {
				const typeTaxonomies = await fetchTaxonomiesByPostType(
					postTypes[ key ].value
				);
				allTaxonomies = { ...allTaxonomies, ...typeTaxonomies };
			}

			const filteredTaxonomies = filterTaxonomies( allTaxonomies );

			setTaxonomies( filteredTaxonomies );
		};

		getTaxonomies();
	}, [ postTypes ] );

	return (
		! enableManualSelection &&
		taxonomies.length !== 0 && (
			<>
				<TaxonomyToggleControl { ...props } />

				{ enableTaxonomies &&
					taxonomies.map( ( taxonomy ) => {
						return (
							<div key={ taxonomy.slug }>
								<TaxonomySelectControl
									taxonomy={ taxonomy }
									{ ...props }
								/>
							</div>
						);
					} ) }
			</>
		)
	);
};

export default TaxonomyControl;
