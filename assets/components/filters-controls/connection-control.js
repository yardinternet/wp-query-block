/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import ConnectionToggleControl from './connection-toggle-control';
import ConnectionSelectControl from './connection-select-control';
import { fetchRegisteredPostTypes, fetchBlockSettings } from '../../utils/api';
import { mapPostTypesToOptions } from '../../utils/helpers';
import { filterPostTypes } from '../../utils/post-types';

const ConnectionControl = ( props ) => {
	const { attributes } = props;
	const { postTypes, enableConnection, enableManualSelection } = attributes;
	const [ connections, setConnections ] = useState( [] );

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

	return (
		! enableManualSelection &&
		connections.length !== 0 && (
			<>
				<ConnectionToggleControl { ...props } />

				{ enableConnection &&
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
