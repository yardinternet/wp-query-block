/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';

/**
 * Internal dependencies
 */
import ConnectionToggleControl from './connection-toggle-control';
import ConnectionSelectControl from './connection-select-control';
import { fetchRegisteredPostTypes } from '../../utils/api';
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
			// TODO: Now it fetches all post types, but we only want post types that are connected.
			const allPostTypes = await fetchRegisteredPostTypes();
			const filteredPostTypes = filterPostTypes( allPostTypes );
			const mappedPostTypes = mapPostTypesToOptions( filteredPostTypes );
			setConnections( mappedPostTypes );
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
