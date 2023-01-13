const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { __ } = wp.i18n;
const { useSelect, useDispatch } = wp.data;
const { TextControl, ComboboxControl } = wp.components;
const { useState } = wp.element;
const { union, map, isEmpty } = lodash;

const RenderWpGbPrimaryCategoryMeta = ( props ) => {
	const { wpGbPrimarycategoryId, categoryTypes, Taxonomy, TaxonomyName } = useSelect( ( select ) => {

		const Taxonomy      = props.primaryCategory?.taxonomy;
		const TaxonomyName  = props.primaryCategory?.taxonomyName;
		const categoryTypes = select( 'core' ).getEntityRecords( 'taxonomy', Taxonomy, { per_page: 20 } );
		return {
			wpGbPrimarycategoryId: String( select( 'core/editor' ).getEditedPostAttribute( 'meta' )[`wp_gb_primary_${Taxonomy}`] ),
			categoryTypes: union(
				[
					{
						'name': __( 'Select ' + TaxonomyName, 'wp-gb-primary-category' ),
						'id': '0',
					}
				],
				categoryTypes,
			),
			Taxonomy: Taxonomy,
			TaxonomyName: TaxonomyName,
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
		editPost( { meta: { [`wp_gb_primary_${Taxonomy}`]: String( value ) } } );
	}

	const setNewCategoryListFilteredOptions = ( value )=> {

		if ( '' === value ) {
			setNewCategoryList( categoryList );
		} else {
			let queryData = wp.data.select( 'core' ).getEntityRecords( 'taxonomy', Taxonomy, { per_page: 5, search: value } );

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
			return __( 'Primary ' + TaxonomyName  + ' Not Selected', 'wp-gb-primary-category');
		} else {
			if ( '0' !== value ) {
				const newData = wp.data.select( 'core' ).getEntityRecords( 'taxonomy', Taxonomy, { include: [ parseInt( value ) ] } );

				if ( null !== newData && 'undefined' !== typeof newData && 0 < newData.length ) {
					return (
						<>
							<h4><a href={ newData[0].link }>
								{ newData[0].name }
							</a></h4>
						</>
					);
				} else {
					return __( 'Loading...', 'wp-gb-primary-category');
				}
			} else {
				return __( 'Primary ' + TaxonomyName  + ' Not Selected', 'wp-gb-primary-category');
			}
		}
	}

	return (
		<>
			<ComboboxControl
				help={ __( 'Search ' + TaxonomyName  + ' to get Suggestions', 'wp-gb-primary-category') }
				value={ newWpGbPrimaryCategoryId }
				onChange={ ( value ) => setNewWpGbPrimaryCategoryIdValue( value ) }
				options={ newCategoryList }
				onFilterValueChange={ ( inputValue ) =>
					setNewCategoryListFilteredOptions( inputValue )
				}
			/>
			{ fetchWpGbPrimaryCategoryName( newWpGbPrimaryCategoryId ) }
			<TextControl
				type="hidden"
				value={ '0' === wpGbPrimarycategoryId ? '0' : wpGbPrimarycategoryId }
				onChange={ ( value ) => editPost( { meta: { [`wp_gb_primary_${Taxonomy}`]: String( value ) } } ) }
			/>
		</>
	);
}

const PluginWpGbPrimaryCategory = () => (
	// Create sidebar's (drop-down) panel.
	<PluginDocumentSettingPanel
		name="wp_gb_primary_category"
		title={ __( 'Primary Categories Selection', 'wp-gb-primary-category' ) }
		className="wp-gb-primary-category"
	>
		{
			wpGbPrimaryCategory['primaryCategories'].map( (taxonomy,index) => {
				return <RenderWpGbPrimaryCategoryMeta primaryCategory={taxonomy} />
			} )
		}
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
