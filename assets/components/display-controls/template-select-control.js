/**
 * WordPress dependencies
 */
import { SelectControl } from '@wordpress/components';
import { useEffect, useState, useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { fetchBlockSettings } from '../../utils/api';

const DEFAULT_TEMPLATE_OPTIONS = [ { label: 'Standaard', value: 'default' } ];

const TemplateSelectControl = ( props ) => {
	const { setAttributes, attributes } = props;
	const { template } = attributes;

	const [ templateOptions, setTemplateOptions ] = useState(
		DEFAULT_TEMPLATE_OPTIONS
	);

	const getTemplateOptions = useCallback( async () => {
		try {
			const settings = await fetchBlockSettings();

			if ( settings?.templates.length > 1 ) {
				setTemplateOptions( settings.templates );
			}
		} catch ( error ) {
			setTemplateOptions( DEFAULT_TEMPLATE_OPTIONS );
		}
	}, [] );

	useEffect( () => {
		getTemplateOptions();
	}, [ getTemplateOptions ] );

	return (
		<SelectControl
			label={ __( 'Sjabloon', 'yard-query-block' ) }
			value={ template }
			options={ templateOptions }
			onChange={ ( value ) => setAttributes( { template: value } ) }
		/>
	);
};

export default TemplateSelectControl;
