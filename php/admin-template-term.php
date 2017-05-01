<?php
/**
 * Admin template tags for terms.
 *
 * @package WP_SEO_Social
 */

/**
 * Generates markup for fields in the default WP SEO Social metabox.
 */
function wp_seo_social_generate_heading() {
	echo sprintf(
		'<h4>%1$s</h4>',
		esc_html( __( 'Open Graph', 'wp-seo-social' ) )
	);
}

/**
 * Prints markup and fires actions to construct the WP SEO Social metabox for edit terms.
 *
 * @param WP_term $term Term object of the term being edited.
 */
function wp_seo_social_the_meta_fields_edit_term( $term ) {
	$slug = 'edit_term';
	wp_seo_social_edit_term_opening_markup();
	wp_seo_social_generate_heading();
	foreach ( WP_SEO_Social_Settings()->wp_seo_social_fields as $field ) :
		wp_seo_social_generate_field_markup_term( $slug, $field, $term );
	endforeach;
	wp_seo_social_edit_term_closing_markup();
}

/**
 * Generates opening markup for the edit term WP SEO Social metabox.
 */
function wp_seo_social_edit_term_opening_markup() {
	?>
	<table class="form-table wp-seo-term-meta-fields"><tbody>
<?php }

/**
 * Generates closing markup for the edit term WP SEO Social metabox.
 */
function wp_seo_social_edit_term_closing_markup() {
	?>
	</tbody></table>
<?php }

/**
 * Prints markup and fires actions to construct the WP SEO Social metabox for new terms.
 */
function wp_seo_social_the_meta_fields_term() {
	$slug = 'add_term';
	wp_seo_social_term_opening_markup();
	wp_seo_social_generate_heading();
	foreach ( WP_SEO_Social_Settings()->wp_seo_social_fields as $field ) :
		wp_seo_social_generate_field_markup_term( $slug, $field );
	endforeach;
	wp_seo_social_term_closing_markup();
}

/**
 * Generates opening markup for the add term WP SEO Social metabox.
 */
function wp_seo_social_term_opening_markup() {
	?>
	<div class="wp-seo-term-meta-fields">
<?php }

/**
 * Generates closing markup for the add term WP SEO Social metabox.
 */
function wp_seo_social_term_closing_markup() {
	?>
	</div>
<?php }

/**
 * Generates markup for fields in the add term WP SEO Social metabox.
 *
 * @param string $slug Used in markup for context.
 * @param string $field Used in markup for field type.
 * @param string $term Taxonomy being manipulated.
 */
function wp_seo_social_generate_field_markup_term( $slug, $field, $term = false ) {
	?>
	<div class="form-field">
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
			do_action( 'wp_seo_social_' . $slug . '_meta_fields_' . $field . '_input', $term );

			/**
			 * Fires after the field input in the post metabox.
			 *
			 * @param int $post_id The ID of the post being edited.
			 */
			do_action( 'wp_seo_social_' . $slug . '_meta_fields_after_' . $field . '_input', $term );
			?>
		</td>
	</tr>
	</div>
<?php }
