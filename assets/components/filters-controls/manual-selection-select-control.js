/**
 * External dependencies
 */
import { components } from 'react-select';
import AsyncSelect from 'react-select/async';
import debounce from 'debounce-promise';

import { closestCorners, DndContext } from '@dnd-kit/core';
import { restrictToParentElement } from '@dnd-kit/modifiers';
import {
	arrayMove,
	horizontalListSortingStrategy,
	SortableContext,
	useSortable,
} from '@dnd-kit/sortable';
import { CSS } from '@dnd-kit/utilities';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEffect, useState, useCallback } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { mapPostsToOptions, getSubtype } from '../../utils/helpers';
import { searchPosts } from '../../utils/api';

const MultiValue = ( props ) => {
	const onMouseDown = ( e ) => {
		e.preventDefault();
		e.stopPropagation();
	};
	const innerProps = { ...props.innerProps, onMouseDown };
	const { attributes, listeners, setNodeRef, transform } = useSortable( {
		id: props.data.value,
	} );
	const style = {
		transform: CSS.Transform.toString( transform ),
	};

	return (
		<div
			className="yard-sortable-select__multi-value__wrapper"
			style={ style }
			ref={ setNodeRef }
			{ ...attributes }
			{ ...listeners }
		>
			<components.MultiValue { ...props } innerProps={ innerProps } />
		</div>
	);
};

const MultiValueRemove = ( props ) => {
	return (
		<components.MultiValueRemove
			{ ...props }
			innerProps={ {
				onPointerDown: ( e ) => e.stopPropagation(),
				...props.innerProps,
			} }
		/>
	);
};

const ManualSelectionSelectControl = ( props ) => {
	const { attributes, setAttributes } = props;
	const {
		postTypes,
		postsPerPage,
		enableManualSelection,
		manualSelectionPosts,
	} = attributes;
	const [ defaultOptions, setDefaultOptions ] = useState( [] );

	/**
	 * Load posts on init
	 */
	useEffect( () => {
		const getOptions = async () => {
			const subtype = getSubtype( postTypes );
			const posts = await searchPosts( '', subtype );

			if ( ! posts ) return;

			const options = mapPostsToOptions( posts );
			setDefaultOptions( options );
		};

		getOptions();
	}, [ postTypes ] );

	/**
	 * Load posts as options based on input value
	 *
	 * @param {string}   input
	 * @param {Function} callback
	 */
	const loadOptions = async ( input, callback ) => {
		if ( ! input ) return callback( [] );

		const subtype = getSubtype( postTypes );
		const posts = await searchPosts( input, subtype );

		if ( ! posts ) return callback( [] );

		const options = mapPostsToOptions( posts );
		setDefaultOptions( options );
		callback( options );
	};

	const handleDragEnd = useCallback(
		( event ) => {
			const { active, over } = event;

			if ( ! active || ! over ) return;

			const oldIndex = manualSelectionPosts.findIndex(
				( item ) => item.value === active.id
			);
			const newIndex = manualSelectionPosts.findIndex(
				( item ) => item.value === over.id
			);
			const newArray = arrayMove(
				manualSelectionPosts,
				oldIndex,
				newIndex
			);

			setAttributes( { manualSelectionPosts: newArray } );
		},
		[ manualSelectionPosts, setAttributes ]
	);

	return (
		enableManualSelection && (
			<>
				<p className="yard-query-inspector-label">
					{ __(
						'Selecteer de berichten die je in deze lijst wilt tonen.'
					) }
				</p>
				<DndContext
					modifiers={ [ restrictToParentElement ] }
					onDragEnd={ handleDragEnd }
					collisionDetection={ closestCorners }
				>
					<SortableContext
						items={ defaultOptions.map(
							( option ) => option.value
						) }
						strategy={ horizontalListSortingStrategy }
					>
						<AsyncSelect
							backspaceRemovesValue={ false }
							className="yard-sortable-select"
							classNamePrefix="yard-sortable-select"
							closeMenuOnSelect={ false }
							components={ {
								MultiValue,
								MultiValueRemove,
							} }
							defaultOptions={ defaultOptions }
							isMulti
							isOptionDisabled={ () =>
								manualSelectionPosts.length >= postsPerPage
							}
							loadingMessage={ () => __( 'Laden…' ) }
							loadOptions={ debounce( loadOptions, 500 ) }
							noOptionsMessage={ () =>
								__(
									'Geen berichten gevonden. Probeer een andere zoekterm.'
								)
							}
							onChange={ ( selectedPosts ) =>
								setAttributes( {
									manualSelectionPosts: selectedPosts,
								} )
							}
							placeholder={ __( 'Selecteer berichten…' ) }
							value={ manualSelectionPosts }
						/>
					</SortableContext>
				</DndContext>
			</>
		)
	);
};

export default ManualSelectionSelectControl;
