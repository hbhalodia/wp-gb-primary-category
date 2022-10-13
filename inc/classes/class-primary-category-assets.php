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
	}

	/**
	 * Used to enqueue the block editor assets.
	 *
	 * @return void
	 */
	public function wp_gb_primary_category_enqueue_block_editor_asset(): void {

		// Enqueue Script for plugin sidebar.
		wp_register_script(
			'wp-gb-primary-category-js',
			WP_GB_PRIMARY_CATEGORY_URL . '/build/index.js',
			self::$editor_dependency,
			filemtime( WP_GB_PRIMARY_CATEGORY_PATH . '/build/index.js' ),
		);

		$taxonomy_name = apply_filters( 'wp_gb_primary_category_filter_taxonomy', 'category' );

		wp_localize_script(
			'wp-gb-primary-category-js',
			'wpGbPrimaryCategory',
			array(
				'Taxonomy' => $taxonomy_name,
			)
		);

		wp_enqueue_script( 'wp-gb-primary-category-js' );
	}
}
new WP_GB_Primary_Category_Assets();
