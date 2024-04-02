/**
 * WordPress dependencies
 */
import { RadioControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const DEFAULT_POST_PARENT_OPTIONS = [
	{
		label: __( 'Toon alleen hoofdberichten' ),
		value: 'only-parents',
	},
	{
		label: __( 'Toon subberichten van een specifieke hoofdbericht' ),
		value: 'specific-parent',
	},
];

const PostParentRadioControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postParentOption, enableManualSelection, enablePostParent } =
		attributes;

	/**
	 * Save option in attributes and set/remove postParent attribute
	 *
	 * @param {string} value - Selected option
	 */
	const onChange = ( value ) => {
		setAttributes( { postParentOption: value } );

		if ( value === 'only-parents' ) {
			setAttributes( { postParent: 0 } );
		} else {
			setAttributes( { postParent: undefined } );
		}
	};

	return (
		! enableManualSelection &&
		enablePostParent && (
			<RadioControl
				label={ __( 'Hoofd- en subberichten' ) }
				hideLabelFromVision={ true }
				selected={ postParentOption }
				options={ DEFAULT_POST_PARENT_OPTIONS }
				onChange={ onChange }
			/>
		)
	);
};

export default PostParentRadioControl;
