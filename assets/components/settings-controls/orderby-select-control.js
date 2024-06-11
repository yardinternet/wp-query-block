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
	label: __( 'Event datum', 'yard-query-block' ),
	value: 'event_date',
};

const OrderbySelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postTypes, orderBy } = attributes;
	const [ options, setOptions ] = useState( DEFAULT_ORDERBY_OPTIONS );

	/**
	 * Change options if tribe_events post type is selected
	 */
	useEffect( () => {
		const includesTribeEvents = postTypes.some(
			( type ) => type.value === 'tribe_events'
		);

		if (
			includesTribeEvents &&
			! options.includes( TRIBE_EVENTS_DATE_OPTION )
		) {
			setOptions( [ ...options, TRIBE_EVENTS_DATE_OPTION ] );
		}

		if (
			! includesTribeEvents &&
			options.includes( TRIBE_EVENTS_DATE_OPTION )
		) {
			setOptions( DEFAULT_ORDERBY_OPTIONS );
		}
	}, [ postTypes, options ] );

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
