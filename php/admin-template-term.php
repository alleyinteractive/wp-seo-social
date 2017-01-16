<?php
/**
 * Admin template tags for terms.
 *
 * @package WP_SEO_Social
 */

/**
 * Prints markup and fires actions to construct the WP SEO Social metabox for new terms.
 *
 * @param WP_term $taxonomy Term object of the term being edited.
 */
function wp_seo_social_the_meta_fields_term( $taxonomy ) {
	$fields = array(
		'og_title',
		'og_description',
		'og_image',
		'og_type',
	);
	$slug = 'add_term';
	wp_seo_social_term_opening_markup();
	foreach ( $fields as $field ) :
		wp_seo_social_generate_field_markup_term( $slug, $field, $taxonomy );
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
 * @param WP_Post $post Post object of the post being edited.
 */
function wp_seo_social_generate_field_markup_term( $slug, $field, $taxonomy ) {
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
			do_action( 'wp_seo_social_' . $slug . '_meta_fields_' . $field . '_input', $taxonomy );

			/**
			 * Fires after the field input in the post metabox.
			 *
			 * @param int $post_id The ID of the post being edited.
			 */
			do_action( 'wp_seo_social_' . $slug . '_meta_fields_after_' . $field . '_input', $taxonomy );
			?>
		</td>
	</tr>
	</div>
<?php }

/**
 * Prints a form label for a meta OG title input.
 */
function wp_seo_social_the_meta_og_title_label() {
	?>
	<label for="wp_seo_meta_og_title"><?php esc_html_e( 'Open Graph Title Tag', 'wp-seo' ); ?></label>
<?php }

/**
 * Prints a form input for a meta OG title.
 *
 * @param string $value The input's current value.
 */
function wp_seo_social_the_meta_og_title_input( $value ) {
	?>
	<input type="text" id="wp_seo_meta_og_title" name="seo_meta[og_title]" value="<?php echo esc_attr( $value ); ?>" size="96" />
	<?php
}

/**
 * Prints markup for displaying a meta title input's character count.
 *
 * @param string $count The starting character count.
 */
function wp_seo_social_the_og_title_character_count( $count ) {
	?>
	<p>
		<?php esc_html_e( 'Open Graph Title character count: ', 'wp-seo' ); ?>
		<span class="og-title-character-count"></span>
		<?php /* translators: %d: title character count */ ?>
		<noscript><?php echo esc_html( sprintf( __( '%d (save changes to update)', 'wp-seo' ), $count ) ); ?></noscript>
	</p>
	<?php
}
