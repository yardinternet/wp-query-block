/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { RadioControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import ConnectionToggleControl from './connection-toggle-control';
import ConnectionSelectControl from './connection-select-control';
import ConnectionRadioControl from './connection-radio-control';
import { fetchRegisteredPostTypes, fetchBlockSettings } from '../../utils/api';
import { mapPostTypesToOptions } from '../../utils/helpers';
import { filterPostTypes } from '../../utils/post-types';

const ConnectionControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const {
		postTypes,
		enableConnection,
		enableManualSelection,
		connectionOption,
	} = attributes;
	const [ connections, setConnections ] = useState( [] );

	const { currentPostId, currentPostTitle } = useSelect(
		( select ) => ( {
			currentPostId: select( 'core/editor' ).getCurrentPostId(),
			currentPostTitle:
				select( 'core/editor' ).getEditedPostAttribute( 'title' ),
		} ),
		[]
	);

	/**
	 * Fetch connections of selected post types
	 */
	useEffect( () => {
		const getConnections = async () => {
			const settings = await fetchBlockSettings();

			if ( ! settings.connections || settings.connections.length < 1 ) {
				setConnections( [] );
				return;
			}

			// Filter connections that match selected post types
			const match = settings.connections.filter( ( a ) =>
				postTypes.some( ( b ) => a.from === b.value )
			);

			if ( match.length < 1 ) {
				setConnections( [] );
				return;
			}

			const allPostTypes = await fetchRegisteredPostTypes();
			const filteredPostTypes = filterPostTypes( allPostTypes );
			const mappedPostTypes = mapPostTypesToOptions( filteredPostTypes );

			const connectionPostTypes = match.map( ( connection ) => {
				return mappedPostTypes.find(
					( postType ) => postType.value === connection.to
				);
			} );

			if ( connectionPostTypes.length < 1 ) {
				setConnections( [] );
				return;
			}

			setConnections( connectionPostTypes );
		};

		getConnections();
	}, [ postTypes ] );

	/**
	 * Auto-fill connectionPosts with the current post when:
	 *  - toggle is enabled
	 *  - the "current-post-connections" option is selected.
	 */
	useEffect( () => {
		if (
			enableConnection &&
			connectionOption === 'current-post-connections' &&
			connections.length > 0
		) {
			const newConnectionPosts = {};

			connections.forEach( ( connection ) => {
				newConnectionPosts[ connection.value ] = {
					value: currentPostId,
					label: currentPostTitle,
				};
			} );

			setAttributes( {
				connectionPosts: newConnectionPosts,
			} );
		}
	}, [
		enableConnection,
		connectionOption,
		connections,
		currentPostId,
		currentPostTitle,
		setAttributes,
	] );

	return (
		! enableManualSelection &&
		connections.length !== 0 && (
			<>
				<ConnectionToggleControl { ...props } />

				{ enableConnection && (
					<ConnectionRadioControl
						connectionOption={ connectionOption }
						setAttributes={ setAttributes }
					/>
				) }

				{ connectionOption === 'specific-post-connections' &&
					connections.map( ( connection ) => {
						return (
							<div key={ connection.value }>
								<ConnectionSelectControl
									connection={ connection }
									{ ...props }
								/>
							</div>
						);
					} ) }
			</>
		)
	);
};

export default ConnectionControl;
