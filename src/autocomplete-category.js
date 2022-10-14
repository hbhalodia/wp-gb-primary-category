/**
 * This file is used to implement duplicate post on ajax call.
 *
 * @package ie-duplicate-post.
 */

jQuery( document ).ready(
	function () {
		jQuery( '.wp-gb-primary-category' ).keyup(
			function() {
				let text = jQuery( '#wp-gb-primary-category-2' ).val();

				if ( text.length > 2 ) {
					jQuery.ajax( {
						type: 'POST',
						url: wpGbPrimaryCategory.ajaxUrl,
						data: {
							action: 'wp_gb_primary_category',
							text: text,
							nonce: wpGbPrimaryCategory.ajaxCode
						},
						success: function( response ) {
						},
					} );
				}
			}
		);
	}
);