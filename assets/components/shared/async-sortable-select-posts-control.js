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
import { useState, useEffect, useCallback } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { mapPostsToOptions } from '../../utils/helpers';
import { searchPosts } from '../../utils/api';

const MultiValue = ( props ) => {
	const { attributes, listeners, setNodeRef, transform } = useSortable( {
		id: props.data.value,
	} );

	const style = {
		transform: CSS.Transform.toString( transform ),
	};

	const onMouseDown = ( e ) => {
		e.preventDefault();
		e.stopPropagation();
	};

	const innerProps = { ...props.innerProps, onMouseDown };

	return (
		<div
			className="yard-async-sortable-select__multi-value__wrapper"
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

const AsyncSortableSelectPostsControl = ( props ) => {
	const { subtype, enable, handleChange, isOptionDisabled, label, value } =
		props;
	const [ defaultOptions, setDefaultOptions ] = useState( [] );

	/**
	 * Search posts based on input value
	 *
	 * @param {string} input
	 */
	const getPostsAsOptions = useCallback(
		async ( input = '' ) => {
			const posts = await searchPosts( input, subtype );
			return posts ? mapPostsToOptions( posts ) : [];
		},
		[ subtype ]
	);

	/**
	 * Load posts on init
	 */
	useEffect( () => {
		const getOptions = async () => {
			const options = await getPostsAsOptions();
			setDefaultOptions( options );
		};

		getOptions();
	}, [ getPostsAsOptions ] );

	/**
	 * Load posts based on input value when the user start searching
	 *
	 * @param {string}   input
	 * @param {Function} callback
	 */
	const loadOptions = async ( input, callback ) => {
		if ( ! input ) return callback( [] );

		const options = await getPostsAsOptions( input );

		setDefaultOptions( options );
		callback( options );
	};

	/**
	 * Update the array when the user has changed the order of the labels
	 */
	const handleDragEnd = useCallback(
		( event ) => {
			const { active, over } = event;

			if ( ! active || ! over ) return;

			const oldIndex = value.findIndex(
				( item ) => item.value === active.id
			);
			const newIndex = value.findIndex(
				( item ) => item.value === over.id
			);
			const newArray = arrayMove( value, oldIndex, newIndex );

			handleChange( newArray );
		},
		[ value, handleChange ]
	);

	return (
		enable && (
			<>
				<p className="yard-query-inspector-label">{ label }</p>
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
							className="yard-async-sortable-select"
							classNamePrefix="yard-async-sortable-select"
							closeMenuOnSelect={ false }
							components={ {
								MultiValue,
								MultiValueRemove,
							} }
							defaultOptions={ defaultOptions }
							isMulti
							isOptionDisabled={ isOptionDisabled }
							loadingMessage={ () =>
								__( 'Laden…', 'yard-query-block' )
							}
							loadOptions={ debounce( loadOptions, 500 ) }
							noOptionsMessage={ () =>
								__(
									'Geen berichten gevonden. Probeer een andere zoekterm.',
									'yard-query-block'
								)
							}
							onChange={ handleChange }
							placeholder={ __(
								'Selecteer bericht…',
								'yard-query-block'
							) }
							value={ value }
						/>
					</SortableContext>
				</DndContext>
			</>
		)
	);
};

export default AsyncSortableSelectPostsControl;
