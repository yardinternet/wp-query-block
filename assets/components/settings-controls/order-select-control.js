/**
 * WordPress dependencies
 */
import { SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const DEFAULT_ORDER_OPTIONS = [
	{ label: __( 'Oplopend', 'yard-query-block' ), value: 'ASC' },
	{ label: __( 'Aflopend', 'yard-query-block' ), value: 'DESC' },
];

const OrderSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { order, orderBy } = attributes;

	const getHelpText = () => {
		const isAscending = order === 'ASC';

		switch ( orderBy ) {
			case 'date':
			case 'event_date':
				return isAscending ? 'Oud - Nieuw' : 'Nieuw - Oud';
			case 'title':
				return isAscending ? 'A - Z' : 'Z - A';
			case 'menu_order':
				return isAscending ? '1 - 100' : '100 - 1';
			default:
				return '';
		}
	};

	return (
		orderBy !== 'rand' && (
			<SelectControl
				label={ __( 'Volgorde', 'yard-query-block' ) }
				value={ order }
				options={ DEFAULT_ORDER_OPTIONS }
				help={ getHelpText() }
				onChange={ ( value ) => setAttributes( { order: value } ) }
			/>
		)
	);
};

export default OrderSelectControl;
