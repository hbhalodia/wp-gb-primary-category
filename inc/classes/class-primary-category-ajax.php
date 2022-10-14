<?php
/**
 * Class used to add the ajax functionality.
 *
 * @package wp-gb-primary-category
 */

/**
 * Class used to add the ajax functions.
 *
 * class WP_GB_Primary_Category_Ajax
 */
class WP_GB_Primary_Category_Ajax {

	/**
	 * Constructor Function.
	 */
	public function __construct() {
		$this->setup_hooks();
	}

	/**
	 * Add action/fiters.
	 *
	 * @return void
	 */
	public function setup_hooks(): void {

		add_action( 'wp_ajax_wp_gb_primary_category', array( $this, 'wp_gb_primary_category_callback_autcomplete' ) );
	}

	/**
	 * Callback function for ajax.
	 *
	 * @return mixed
	 */
	public function wp_gb_primary_category_callback_autcomplete() {
	}
}
new WP_GB_Primary_Category_Ajax();
