const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { __ } = wp.i18n;
const { useSelect, useDispatch } = wp.data;
const { TextControl, ComboboxControl } = wp.components;
const { useState } = wp.element;
const { union, map, isEmpty } = lodash;

const RenderWpGbPrimaryCategoryMeta = () => {

	const { wpGbPrimarycategoryId, categoryTypes } = useSelect( ( select ) => {

		const categoryTypes = select( 'core' ).getEntityRecords( 'taxonomy', 'category', { per_page: 20 } );
		return {
			wpGbPrimarycategoryId: String( select( 'core/editor' ).getEditedPostAttribute( 'meta' )['wp_gb_primary_category'] ),
			categoryTypes: union(
				[
					{
						'name': __( 'Select Category', 'astro-gutenberg-block' ),
						'id': '0',
					}
				],
				categoryTypes,
			),
		};
	} );

	const { editPost } = useDispatch( 'core/editor' );

	const categoryList = map( categoryTypes, ( { name, id } ) => {
		return {
			label: name,
			value: id,
		};
	} );

	const [ newWpGbPrimaryCategoryId, setWpGbPrimaryCategoryId ] = useState( wpGbPrimarycategoryId );
	const [ newCategoryList, setNewCategoryList ] = useState( categoryList );

	const setNewWpGbPrimaryCategoryIdValue = ( value ) => {
		setWpGbPrimaryCategoryId( value );
		editPost( { meta: { wp_gb_primary_category: String( value ) } } );
	}

	const setNewCategoryListFilteredOptions = ( value )=> {

		if ( '' === value ) {
			setNewCategoryList( categoryList );
		} else {
			let queryData = wp.data.select( 'core' ).getEntityRecords( 'taxonomy', 'category', { per_page: 5, search: value } );

			if ( null !== queryData && 'undefined' !== typeof queryData && 0 < queryData.length ) {

				let newList = map( queryData, ( { name, id } ) => {
					return {
						label: name,
						value: id,
					};
				} );
				setNewCategoryList( newList );
			}
		}
	}

	const fetchWpGbPrimaryCategoryName = ( value ) => {
		if ( '0' === wpGbPrimarycategoryId ) {
			return __( 'Primary Catgory Not Selected', 'astro-gutenberg-block' );
		} else {
			if ( '0' !== value ) {
				const newData = wp.data.select( 'core' ).getEntityRecords( 'taxonomy', 'category', { include: [ parseInt( value ) ] } );

				if ( null !== newData && 'undefined' !== typeof newData && 0 < newData.length ) {
					return (
						<>
							<a href={ newData[0].link }>
								{ newData[0].name }
							</a>
						</>
					);
				} else {
					return __( 'Loading...', 'astro-gutenberg-block' );
				}
			} else {
				return __( 'Primary Catgory Not Selected', 'astro-gutenberg-block' );
			}
		}
	}

	return (
		<>
			<ComboboxControl
				help={ __( 'Search Category to get Suggestions', 'astro-gutenberg-block' ) }
				value={ newWpGbPrimaryCategoryId }
				onChange={ ( value ) => setNewWpGbPrimaryCategoryIdValue( value ) }
				options={ newCategoryList }
				onFilterValueChange={ ( inputValue ) =>
					setNewCategoryListFilteredOptions( inputValue )
				}
			/>
			<br />
			{ fetchWpGbPrimaryCategoryName( newWpGbPrimaryCategoryId ) }
			<TextControl
				type="hidden"
				value={ '0' === wpGbPrimarycategoryId ? '0' : wpGbPrimarycategoryId }
				onChange={ ( value ) => editPost( { meta: { wp_gb_primary_category: String( value ) } } ) }
			/>
		</>
	);
}

const PluginWpGbPrimaryCategory = () => (
	// Create sidebar's (drop-down) panel.
	<PluginDocumentSettingPanel
		name="wp_gb_primary_category"
		title={ __( 'Select Primary Category', 'wp-gb-primary-category' ) }
		className="wp-gb-primary-category"
	>
		<RenderWpGbPrimaryCategoryMeta />
	</PluginDocumentSettingPanel>
);

// Register sidebar's (drop-down) panel.
registerPlugin(
	'plugin-wp-gb-primary-category',
	{
		render: PluginWpGbPrimaryCategory,
		icon: null
	}
);
