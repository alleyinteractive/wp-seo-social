<?php
/**
 * Admin template tags for terms.
 *
 * @package WP_SEO_Social
 */

/**
 * Prints markup and fires actions to construct the WP SEO Social metabox for edit terms.
 *
 * @param WP_term $taxonomy Term object of the term being edited.
 */
function wp_seo_social_the_meta_fields_edit_term( $term ) {
	$slug = 'edit_term';
	wp_seo_social_edit_term_opening_markup();
	foreach ( WP_SEO_Social_Settings()->fields_to_whitelist as $field ) :
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
 *
 * @param WP_term $taxonomy Term object of the term being edited.
 */
function wp_seo_social_the_meta_fields_term() {
	$slug = 'add_term';
	wp_seo_social_term_opening_markup();
	foreach ( WP_SEO_Social_Settings()->fields_to_whitelist as $field ) :
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
 * @param $slug String used in markup for context.
 * @param $field String used in markup for field type.
 * @param $taxonomy String of taxonomy being manipulated.
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
