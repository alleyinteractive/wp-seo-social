<?php
/**
 * Admin template tags for post-like objects.
 *
 * @package WP_SEO_Social
 */

/**
 * Prints markup and fires actions to construct the default WP SEO Social post metabox.
 *
 * @param WP_Post $post Post object of the post being edited.
 */
function wp_seo_social_the_meta_fields( $post ) {
	$slug = 'post';
	wp_seo_social_table_opening_markup( $slug );
	wp_seo_social_generate_heading();
	foreach ( WP_SEO_Social_Settings()->fields_to_whitelist as $field ) :
		wp_seo_social_generate_field_markup( $slug, $field, $post );
	endforeach;
	wp_seo_social_table_closing_markup();
}

/**
 * Generates opening markup for the default post SEO Social metabox.
 *
 * @param $slug String used in markup for context.
 */
function wp_seo_social_table_opening_markup( $slug ) {
	?>
	<table class="wp-seo-social-<?php echo esc_attr( $slug ); ?>-meta-fields wp-seo-<?php echo esc_attr( $slug ); ?>-meta-fields"><tbody>
<?php }

/**
 * Generates closing markup for the default post SEO Social metabox.
 */
function wp_seo_social_table_closing_markup() {
	?>
	</tbody></table>
<?php }

/**
 * Generates markup for fields in the default WP SEO Social metabox.
 *
 * @param $slug String used in markup for context.
 * @param $field String used in markup for field type.
 * @param WP_Post $post Post object of the post being edited.
 */
function wp_seo_social_generate_field_markup( $slug, $field, $post ) {
	?>
	<tr>
		<th scope="row">
			<?php
			/**
			 * Fires to print the field input label in the post metabox.
			 */
			do_action( 'wp_seo_social_' . $slug . '_meta_fields_' . $field . '_label' );
			?>
		</th>
		<td>
			<?php
			/**
			 * Fires to print the field input in the post metabox.
			 *
			 * @param int $post_id The ID of the post being edited.
			 */
			do_action( 'wp_seo_social_' . $slug . '_meta_fields_' . $field . '_input', $post->ID );

			/**
			 * Fires after the field input in the post metabox.
			 *
			 * @param int $post_id The ID of the post being edited.
			 */
			do_action( 'wp_seo_social_' . $slug . '_meta_fields_after_' . $field . '_input', $post->ID );
			?>
		</td>
	</tr>
<?php }
