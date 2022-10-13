<?php
/**
 * Primary Category Template.
 *
 * @package wp-primary-category
 */

$get_categories = get_categories();
wp_nonce_field( 'wp_gb_primary_category_nonce_action', 'wp_gb_primary_category_nonce' );
?>
<div>
	<h3><?php esc_html_e( 'Select Primary Category', 'wp-gb-primary-category' ); ?></h3>
	<select name="wp-gb-primary-category" id="wp-gb-primary-category">
		<option value="0"><?php esc_html_e( 'Select Category', 'wp-gb-primary-category' ); ?></option>
		<?php
			if ( is_array( $get_categories ) && ! empty( $get_categories ) ) {
				foreach ( $get_categories as $category ) {
					?>
					<option <?php selected( $meta, $category->term_id, true ); ?> value="<?php echo esc_attr( $category->term_id ); ?>" ><?php echo esc_html( $category->name ); ?></option>
					<?php
				}
			}
		?>
	</select>
</div>