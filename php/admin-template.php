<?php
/**
 * Generic admin template tags.
 *
 * @package WP_SEO_Social
 */

/**
 * Prints a form label for a meta OG description input.
 */
function wp_seo_social_the_meta_og_description_label() {
	?>
	<label for="wp_seo_meta_og_description"><?php esc_html_e( 'Open Graph Description', 'wp-seo-social' ); ?></label>
	<?php
}

/**
 * Prints a form input for a meta OG description.
 *
 * @param string $value The input's current value.
 */
function wp_seo_social_the_meta_og_description_input( $value ) {
	?>
	<textarea id="wp_seo_meta_og_description" name="seo_meta[og_description]" rows="2" cols="96"><?php echo esc_textarea( $value ); ?></textarea>
	<?php
}

/**
 * Prints markup for displaying a meta OG description input's character count.
 *
 * @param string $count The starting character count.
 */
function wp_seo_social_the_og_description_character_count( $count ) {
	?>
	<p>
		<?php esc_html_e( 'OG description character count: ', 'wp-seo-social' ); ?>
		<span class="og_description-character-count"></span>
		<?php /* translators: %d: description character count */ ?>
		<noscript><?php echo esc_html( sprintf( __( '%d (save changes to update)', 'wp-seo-social' ), $count ) ); ?></noscript>
	</p>
	<?php
}

/**
 * Prints a form label for a meta OG image input.
 */
function wp_seo_social_the_meta_og_image_label() {
	?>
	<label for="wp_seo_meta_og_image"><?php esc_html_e( 'Open Graph Image', 'wp-seo-social' ); ?></label>
<?php }

/**
 * Prints a form input for a meta OG image.
 *
 * @param string $value The input's current value.
 */
function wp_seo_social_the_meta_og_image_input( $value ) {
	$og_image_args = array(
		'field' => 'og_image',
		'slug'  => 'seo_meta',
	);
	WP_SEO_Fields()->render_image_field( $og_image_args, $value );
}

/**
 * Prints a form label for a meta OG type input.
 */
function wp_seo_social_the_meta_og_type_label() {
	?>
	<label for="wp_seo_meta_og_type"><?php esc_html_e( 'Open Graph Type', 'wp-seo-social' ); ?></label>
<?php }

/**
 * Prints a form input for a meta OG type.
 *
 * @param string $value The input's current value.
 */
function wp_seo_social_the_meta_og_type_input( $value ) {
	$og_type_args = array(
		'field' => 'og_type',
		'boxes' => array(
			'website' => 'Website',
			'article' => 'Article',
		),
	);
	WP_SEO_Fields()->render_dropdown( $og_type_args, $value, 'seo_meta' );
}

/**
 * Prints a form label for a meta OG title input.
 */
function wp_seo_social_the_meta_og_title_label() {
	?>
	<label for="wp_seo_meta_og_title"><?php esc_html_e( 'Open Graph Title Tag', 'wp-seo-social' ); ?></label>
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
		<?php esc_html_e( 'Open Graph Title character count: ', 'wp-seo-social' ); ?>
		<span class="og_title-character-count"></span>
		<?php /* translators: %d: title character count */ ?>
		<noscript><?php echo esc_html( sprintf( __( '%d (save changes to update)', 'wp-seo-social' ), $count ) ); ?></noscript>
	</p>
	<?php
}
