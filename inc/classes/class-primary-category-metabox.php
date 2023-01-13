<?php
/**
 * Class used to add the metabox and search for primary category.
 *
 * @package wp-gb-primary-category
 */

/**
 * Class WP_GB_Primary_Category_Metabox
 *
 * Class to Implement the classic editor metabox.
 */
class WP_GB_Primary_Category_Metabox {

	/**
	 * Meta Name for the primary category.
	 *
	 * @var string
	 */
	private static $meta_key = 'wp_gb_primary_';

	/**
	 * Context of meta box.
	 *
	 * @var string
	 */
	private static $context = 'normal';

	/**
	 * Priority for the metabox.
	 *
	 * @var string
	 */
	private static $priority = 'default';

	/**
	 * Constructor function.
	 */
	public function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Function to execute hooks.
	 *
	 * @return void
	 */
	public function setup_hooks() {

		// Register all the meta based on post type here.
		add_action( 'init', array( $this, 'wp_gb_primary_category_custom_meta' ), 10 );

		add_action( 'add_meta_boxes', array( $this, 'wp_gb_primary_category_add_meta_box' ), 10, 1 );
		add_action( 'save_post', array( $this, 'wp_gb_primary_category_save_meta' ) );
	}

	/**
	 * Function used to add filter the post type where to show the crimary category meta.
	 *
	 * @return array
	 */
	private function wp_gb_post_type_support_for_meta(): array {

		$post_type = array( 'post' );

		/**
		 * Filters the support to register meta for different post types.
		 *
		 * @param array $post_type Add different post type name to add support for meta.
		 */
		$post_type_array = apply_filters( 'wp_gb_add_cpt_support_meta', $post_type );

		return $post_type_array;
	}

	/**
	 * Function to add the filter to get taxonomies for both classic and GB support.
	 *
	 * @return array
	 */
	private function wp_gb_get_taxonomies(): array {

		$tax = array( 'category', 'post_tag' );

		/**
		 * Filters the taxonomy whose meta we want to create from primary category.
		 *
		 * NOTE - Keep this filter in sync with values passed in filter `wp_gb_primary_category_filter_taxonomy` at `./class-primary-category-assets.php`.
		 * To prevent from unwanted/irregular results.
		 *
		 * This is because we have added the meta key support but if we do not add to GB panel it won't work out correctly.
		 *
		 * @param array $tax Taxonomy name to create meta keys.
		 */
		$taxonomies = apply_filters( 'wp_gb_add_taxonomies', $tax );

		return $taxonomies;
	}

	/**
	 * Function to get meta available on the register side.
	 *
	 * @return void
	 */
	public function wp_gb_primary_category_custom_meta(): void {

		$post_type_array = $this->wp_gb_post_type_support_for_meta();
		$taxonomies      = $this->wp_gb_get_taxonomies();

		foreach ( $post_type_array as $post_type ) {
			foreach( $taxonomies as $taxonomy ) {
				// Register Meta Here.
				register_post_meta( $post_type, self::$meta_key . $taxonomy, array(
					'show_in_rest'      => true,
					'type'              => 'string',
					'single'            => true,
					'default'           => "0",
					'auth_callback'     => function() {
						return current_user_can( 'edit_posts' );
					}
				) );
			}
		}
	}

	/**
	 * Function to register the metabox.
	 *
	 * @return void
	 */
	public function wp_gb_primary_category_add_meta_box(): void {

		// Check if current screen is Gutenberg Editor then no need to add the metabox this way.
		$current_screen = get_current_screen();
		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return;
		}

		/**
		 * To add support for classic editor post type. for multiple meta keys.
		 */
		$post_type_array = $this->wp_gb_post_type_support_for_meta();

		// Register the metabox.
		add_meta_box(
			self::$meta_key,
			__( 'Primary Categories Selection', 'wp-gb-primary-category' ),
			array( $this, 'wp_gb_primary_category_html_callback' ),
			$post_type_array,
			self::$context,
			self::$priority,
		);
	}

	/**
	 * Function to register the HTML callback for the metabox.
	 *
	 * @param \WP_Post $post Current Post Object.
	 *
	 * @return void
	 */
	public function wp_gb_primary_category_html_callback( \WP_Post $post ): void {
		$taxonomies = $this->wp_gb_get_taxonomies();

		wp_nonce_field( 'wp_gb_primary_category_nonce_action', 'wp_gb_primary_category_nonce' );

		foreach ( $taxonomies as $taxonomy ) {
			$meta           = get_post_meta( $post->ID, self::$meta_key . $taxonomy, true );
			$get_taxonomy   = get_taxonomy( $taxonomy );
			$get_categories = get_categories(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false
				)
			);
			include WP_GB_PRIMARY_CATEGORY_PATH . '/inc/templates/primary-category.php';
		}
	}

	/**
	 * Function to save the meta value to db.
	 *
	 * @param int $post_id Current Post Id.
	 *
	 * @return void
	 */
	public function wp_gb_primary_category_save_meta( $post_id ): void {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! isset( $_POST['wp_gb_primary_category_nonce'] ) || empty( $_POST['wp_gb_primary_category_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['wp_gb_primary_category_nonce'] ), 'wp_gb_primary_category_nonce_action' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$all_taxonomies = $this->wp_gb_get_taxonomies();

		foreach ( $all_taxonomies as $taxonomy ) {
			if ( ! isset( $_POST['wp-gb-primary-' . $taxonomy ] ) || empty( $_POST['wp-gb-primary-' . $taxonomy ] ) ) {
				delete_post_meta( $post_id, self::$meta_key . $taxonomy );
			}

			$save_meta_value = sanitize_text_field( $_POST['wp-gb-primary-' . $taxonomy ] );
			update_post_meta( $post_id, self::$meta_key . $taxonomy, $save_meta_value );
		}
	}
}
new WP_GB_Primary_Category_Metabox();
