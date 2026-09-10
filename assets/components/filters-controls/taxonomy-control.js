/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import TaxonomyToggleControl from './taxonomy-toggle-control';
import TaxonomyRadioControl from './taxonomy-radio-control';
import TaxonomyRelationControl from './taxonomy-relation-control';
import TaxonomySelectControl from './taxonomy-select-control';
import { fetchTaxonomiesByPostType } from '../../utils/api';
import { filterTaxonomies } from '../../utils/taxonomies';

const TaxonomyControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const {
		postTypes,
		enableTaxonomies,
		enableManualSelection,
		taxonomyOption,
		taxonomyRelation,
	} = attributes;
	const [ taxonomies, setTaxonomies ] = useState( [] );

	const { currentPostId } = useSelect(
		( select ) => ( {
			currentPostId: select( 'core/editor' ).getCurrentPostId(),
		} ),
		[]
	);

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

	/**
	 * Auto-fill taxonomyTerms from the current post when in 'current-post-taxonomies' mode.
	 * Fetches terms per taxonomy using the WP REST API ?post={id} filter.
	 */
	useEffect( () => {
		if (
			! enableTaxonomies ||
			taxonomyOption !== 'current-post-taxonomies'
		) {
			return;
		}

		if ( ! currentPostId || taxonomies.length === 0 ) {
			return;
		}

		const fillTerms = async () => {
			const newTerms = {};

			for ( const taxonomy of taxonomies ) {
				const terms = await apiFetch( {
					path: `/wp/v2/${ taxonomy.rest_base }?post=${ currentPostId }&per_page=100`,
				} );

				if ( terms?.length ) {
					newTerms[ taxonomy.slug ] = terms.map( ( t ) => ( {
						label: t.name,
						value: t.slug,
					} ) );
				}
			}

			setAttributes( { taxonomyTerms: newTerms } );
		};

		fillTerms();
	}, [ taxonomyOption, enableTaxonomies, currentPostId, taxonomies, setAttributes ] );

	return (
		! enableManualSelection &&
		taxonomies.length !== 0 && (
			<>
				<TaxonomyToggleControl { ...props } />

				{ enableTaxonomies && (
					<TaxonomyRadioControl
						taxonomyOption={ taxonomyOption }
						setAttributes={ setAttributes }
					/>
				) }

				{ enableTaxonomies &&
					taxonomyOption === 'specific-terms' &&
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

				{ enableTaxonomies && taxonomies.length >= 2 && (
					<TaxonomyRelationControl
						taxonomyRelation={ taxonomyRelation }
						setAttributes={ setAttributes }
					/>
				) }
			</>
		)
	);
};

export default TaxonomyControl;
