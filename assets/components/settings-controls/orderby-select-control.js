/**
 * WordPress dependencies
 */
import { SelectControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const DEFAULT_ORDERBY_OPTIONS = [
	{ label: __( 'Publicatiedatum', 'yard-query-block' ), value: 'date' },
	{ label: __( 'Titel', 'yard-query-block' ), value: 'title' },
	{
		label: __( 'Attribuut volgorde', 'yard-query-block' ),
		value: 'menu_order',
	},
	{ label: __( 'Willekeurig', 'yard-query-block' ), value: 'rand' },
];

const TRIBE_EVENTS_DATE_OPTION = {
	label: __( 'Tribe event datum', 'yard-query-block' ),
	value: 'event_date',
};

const YARD_EVENTS_DATE_OPTION = {
	label: __( 'Yard event datum', 'yard-query-block' ),
	value: 'yard_event_date',
};

const OrderbySelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postTypes, orderBy } = attributes;
	const [ options, setOptions ] = useState( DEFAULT_ORDERBY_OPTIONS );

	/**
	 * Dynamically update options based on selected post types
	 */
	useEffect( () => {
		const updatedOptions = [ ...DEFAULT_ORDERBY_OPTIONS ];

		const includesTribeEvents = postTypes.some(
			( type ) => type.value === 'tribe_events'
		);
		const includesYardEvents = postTypes.some(
			( type ) => type.value === 'yard-event'
		);

		if ( includesTribeEvents ) {
			updatedOptions.push( TRIBE_EVENTS_DATE_OPTION );
		}

		if ( includesYardEvents ) {
			updatedOptions.push( YARD_EVENTS_DATE_OPTION );
		}

		setOptions( updatedOptions );
	}, [ postTypes ] );

	return (
		<SelectControl
			label={ __( 'Sorteer op', 'yard-query-block' ) }
			value={ orderBy }
			options={ options }
			onChange={ ( value ) => setAttributes( { orderBy: value } ) }
		/>
	);
};

export default OrderbySelectControl;
