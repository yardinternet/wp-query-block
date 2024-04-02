/**
 * WordPress dependencies
 */
import { SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const TemplateSelectControl = ( props ) => {
	const { setAttributes, attributes } = props;
	const { template } = attributes;

	const templateOptions = [ { label: 'Standaard', value: 'default' } ];

	/**
	 * @todo Retrieve from template if option is available and what the options are
	 *
	 * Options:
	 * - Template name (not the file name)
	 */
	return (
		<SelectControl
			label={ __( 'Sjabloon' ) }
			value={ template }
			options={ templateOptions }
			onChange={ ( value ) => setAttributes( { template: value } ) }
		/>
	);
};

export default TemplateSelectControl;
