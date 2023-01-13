<?php
/**
 * Primary Category Template.
 *
 * @package wp-primary-category
 */

if ( property_exists( $get_taxonomy, 'labels' ) && property_exists( $get_taxonomy->labels, 'singular_name' ) ) {
	$tax_name = $get_taxonomy->labels->singular_name;
} else {
	$tax_name = $taxonomy;
}

?>
<div class="wp-gb-wrapper">
	<h4><?php echo esc_html( 'Select Primary ' . $tax_name ); ?></h4>
	<select name="<?php echo esc_attr( 'wp-gb-primary-' . $taxonomy ); ?>" id="<?php echo esc_attr( 'wp-gb-primary-' . $taxonomy ); ?>">
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