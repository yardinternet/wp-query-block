/**
 * External dependencies
 */
import Select from 'react-select';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

const PostStatusSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const { enablePostStatus, postStatus } = attributes;

	const options = [
		{
			label: __( 'Concept', 'yard-query-block' ),
			value: 'draft',
		},
		{
			label: __( 'In behandeling', 'yard-query-block' ),
			value: 'pending',
		},
		{
			label: __( 'Privé', 'yard-query-block' ),
			value: 'private',
		},
		{
			label: __( 'Gepland', 'yard-query-block' ),
			value: 'future',
		},
		{
			label: __( 'Gepubliceerd', 'yard-query-block' ),
			value: 'publish',
		},
	];

	return (
		enablePostStatus && (
			<>
				<p className="yard-query-inspector-label">
					{ __(
						'Selecteer de status van berichten die je in deze lijst wilt tonen.',
						'yard-query-block'
					) }
				</p>
				<Select
					isMulti
					value={ postStatus ? postStatus : [] }
					options={ options }
					onChange={ ( option ) =>
						setAttributes( { postStatus: option } )
					}
				/>
			</>
		)
	);
};

export default PostStatusSelectControl;
