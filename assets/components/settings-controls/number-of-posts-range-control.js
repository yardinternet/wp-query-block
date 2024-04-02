/**
 * WordPress dependencies
 */
import { RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const NumberOfPostsRangeControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postsPerPage } = attributes;

	return (
		<RangeControl
			label={ __( 'Aantal berichten' ) }
			value={ postsPerPage }
			min={ 1 }
			max={ 100 }
			onChange={ ( value ) => setAttributes( { postsPerPage: value } ) }
		/>
	);
};

export default NumberOfPostsRangeControl;
