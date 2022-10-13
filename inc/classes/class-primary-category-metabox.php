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
	private static $meta_key = 'wp_gb_primary_category';

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
		add_action( 'add_meta_boxes', array( $this, 'wp_gb_primary_category_add_meta_box' ), 10, 1 );
		add_action( 'save_post', array( $this, 'wp_gb_primary_category_save_meta' ) );
	}

	/**
	 * Function to register the metabox.
	 *
	 * @param string $post_type Current Post type.
	 *
	 * @return void
	 */
	public function wp_gb_primary_category_add_meta_box( string $post_type ): void {

		// Check if current screen is Gutenberg Editor then no need to add the metabox this way.
		$current_screen = get_current_screen();
		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			return;
		}

		// Register the metabox.
		add_meta_box(
			self::$meta_key,
			'Select Primary Category',
			array( $this, 'wp_gb_primary_category_html_callback' ),
			$post_type,
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
		$meta = get_post_meta( $post->ID, self::$meta_key, true );

		require_once WP_GB_PRIMARY_CATEGORY_PATH . '/inc/templates/primary-category.php';
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

		if ( ! isset( $_POST['wp-gb-primary-category'] ) || empty( $_POST['wp-gb-primary-category'] ) ) {
			delete_post_meta( $post_id, self::$meta_key );
			return;
		}

		$save_meta_value = sanitize_text_field( $_POST['wp-gb-primary-category'] );

		update_post_meta( $post_id, self::$meta_key, $save_meta_value );
	}
}
new WP_GB_Primary_Category_Metabox();
