/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const ManualSelectionToggleControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { enableManualSelection } = attributes;

	/**
	 * Save state in attributes and reset attributes if the toggle is enabled
	 *
	 * @param {boolean} state - State of toggle
	 */
	const onChange = ( state ) => {
		setAttributes( { enableManualSelection: state } );

		if ( state ) {
			setAttributes( {
				enableStickyPost: false,
				stickyPost: undefined,
				enableExcludePosts: false,
				excludePosts: [],
				enablePostParent: false,
				postParentOption: 'only-parents',
				postParent: undefined,
				enableTaxonomies: false,
				taxonomyTerms: undefined,
			} );
		}
	};

	return (
		<ToggleControl
			label={ __( 'Handmatige selectie' ) }
			checked={ enableManualSelection }
			onChange={ onChange }
		/>
	);
};

export default ManualSelectionToggleControl;
