<?php
/**
 * Class used to enqueue the assets on the Gutenberg.
 *
 * @package wp-gb-primary-category
 */

/**
 * Class used to enqueue the assets.
 *
 * class WP_GB_Primary_Category_Assets
 */
class WP_GB_Primary_Category_Assets {

	/**
	 * Editor Dependency.
	 *
	 * @var array
	 */
	private static $editor_dependency = array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-core-data', 'wp-edit-post', 'wp-plugins', 'wp-rich-text' );

	/**
	 * Constructor function.
	 */
	public function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Add actions/filters.
	 *
	 * @return void
	 */
	public function setup_hooks(): void {

		// Actions.
		add_action( 'enqueue_block_editor_assets', array( $this, 'wp_gb_primary_category_enqueue_block_editor_asset' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'wp_gb_primary_category_admin_enqueue_script' ) );
	}

	/**
	 * Used to enqueue the block editor assets.
	 *
	 * @return void
	 */
	public function wp_gb_primary_category_enqueue_block_editor_asset(): void {

		$current_screen = get_current_screen();

		// Enqueue Script for plugin sidebar.
		wp_register_script(
			'wp-gb-primary-category-js',
			WP_GB_PRIMARY_CATEGORY_URL . '/build/index.js',
			self::$editor_dependency,
			filemtime( WP_GB_PRIMARY_CATEGORY_PATH . '/build/index.js' ),
		);

		$post_type_remove = array();

		/**
		 * Filters the post type to remove primary category from that post type screen.
		 *
		 * @param array $post_type_remove To remove the support for primary category from that post screen.
		 */
		$remove_post_type = apply_filters( 'wp_gb_primary_category_remove_from_post_type', $post_type_remove );

		if ( property_exists( $current_screen, 'id' ) && in_array( $current_screen->id, $remove_post_type, true ) ) {
			return;
		}

		$taxonomies = array(
			array(
				'taxonomy' => 'category',
				'taxonomyName' => 'Category',
			),
			array(
				'taxonomy' => 'post_tag',
				'taxonomyName' => 'Post Tags',
			)
			// ....
		);

		/**
		 * Filters the taxonomy you want to add in GB panel.
		 *
		 * NOTE - Keep this filter in sync with values passed change in filter `wp_gb_add_taxonomies` at `./class-primary-category-metabox.php`.
		 * To prevent from unwanted/irregular results.
		 *
		 * This is because we need to register meta key for all taxonomies and also to create GB panel for same.
		 * If meta key is not registered it won't be saving the data to DB.
		 *
		 * @param array $taxonomies The taxonomies needed to add on GB panel.
		 */
		$taxonomy = apply_filters( 'wp_gb_primary_category_filter_taxonomy', $taxonomies );

		if ( ! is_array( $taxonomy ) || empty( $taxonomy ) ) {
			return;
		}

		wp_localize_script(
			'wp-gb-primary-category-js',
			'wpGbPrimaryCategory',
			array(
				'primaryCategories' => $taxonomy,
			)
		);

		wp_enqueue_script( 'wp-gb-primary-category-js' );
	}

	/**
	 * Function to add the autocomplete js for classic editor.
	 *
	 * @return void
	 */
	public function wp_gb_primary_category_admin_enqueue_script(): void {

		wp_register_script(
			'wp-gb-primary-category-autocomplete',
			WP_GB_PRIMARY_CATEGORY_URL . '/src/autocomplete-category.js',
			array( 'jquery' ),
			filemtime( WP_GB_PRIMARY_CATEGORY_PATH . '/src/autocomplete-category.js' ),
			true
		);

		$nonce = wp_create_nonce( 'wp-gb-primary-category-ajaxnonce' );

		wp_localize_script(
			'wp-gb-primary-category-autocomplete',
			'wpGbPrimaryCategoryClassic',
			array(
				'ajaxCode' => $nonce,
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			)
		);

		wp_enqueue_script( 'wp-gb-primary-category-autocomplete' );

		wp_register_style(
			'wp-gb-primary-category-admin-css',
			WP_GB_PRIMARY_CATEGORY_URL . '/src/css/admin-index.css',
			array(),
			filemtime( WP_GB_PRIMARY_CATEGORY_PATH . '/src/css/admin-index.css' ),
		);

		wp_enqueue_style( 'wp-gb-primary-category-admin-css' );
	}
}
new WP_GB_Primary_Category_Assets();
