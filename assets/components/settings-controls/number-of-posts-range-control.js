/**
 * WordPress dependencies
 */
import { RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { applyFilters } from '@wordpress/hooks';

const NumberOfPostsRangeControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { postsPerPage } = attributes;

	const minPosts = applyFilters( 'yard.query-min-number-of-posts', 1 );
	const maxPosts = applyFilters( 'yard.query-max-number-of-posts', 100 );

	return (
		<RangeControl
			label={ __( 'Aantal berichten', 'yard-query-block' ) }
			value={ postsPerPage }
			min={ minPosts }
			max={ maxPosts }
			onChange={ ( value ) => setAttributes( { postsPerPage: value } ) }
		/>
	);
};

export default NumberOfPostsRangeControl;
