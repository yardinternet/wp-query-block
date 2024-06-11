/**
 * WordPress dependencies
 */
import { RadioControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const DEFAULT_POST_PARENT_OPTIONS = [
	{
		label: __( 'Toon alleen hoofdberichten', 'yard-query-block' ),
		value: 'only-parents',
	},
	{
		label: __(
			'Toon subberichten van een specifieke hoofdbericht',
			'yard-query-block'
		),
		value: 'specific-parent',
	},
];

const PostParentRadioControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postParentOption, enableManualSelection, enablePostParent } =
		attributes;

	/**
	 * Save option in attributes and reset postParent attribute
	 *
	 * @param {string} value - Selected option
	 */
	const onChange = ( value ) => {
		setAttributes( { postParentOption: value, postParent: {} } );
	};

	return (
		! enableManualSelection &&
		enablePostParent && (
			<RadioControl
				label={ __( 'Hoofd- en subberichten', 'yard-query-block' ) }
				hideLabelFromVision={ true }
				selected={ postParentOption }
				options={ DEFAULT_POST_PARENT_OPTIONS }
				onChange={ onChange }
			/>
		)
	);
};

export default PostParentRadioControl;
