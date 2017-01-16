<?php
/**
 * Sets up the filters and actions for WP SEO Social
 *
 * @package WP_SEO_Social
 */

/* Hook into WP SEO first to add our fields */
add_action( 'wp_seo_post_meta_fields',          'wp_seo_social_the_meta_fields' );
add_action( 'wp_seo_add_term_meta_fields',      'wp_seo_social_the_meta_fields_term' );
add_action( 'wp_seo_edit_term_meta_fields',     'wp_seo_social_the_meta_fields_edit_term' );

$slugs = array(
	'post_id'    => 'post',
	'term_id'    => 'add_term',
	'edit_term'
);

$settings_fields = array(
	'og_title',
	'og_description',
	'og_image',
	'og_type',
);

foreach ( $slugs as $object => $slug ) {
	foreach ( $settings_fields as $field ) {
		switch ( $field ) {
			case 'og_title':
			case 'og_description':
				add_action(
					'wp_seo_social_' . $slug . '_meta_fields_' . $field . '_label',
					'wp_seo_social_the_meta_' . $field . '_label'
				);
				add_action(
					'wp_seo_social_' . $slug . '_meta_fields_' . $field . '_input',
					'wp_seo_social_' . $object . '_to_the_' . $slug . '_meta_' . $field . '_input'
				);
				add_action(
					'wp_seo_social_' . $slug . '_meta_fields_after_' . $field . '_input',
					'wp_seo_social_' . $object . '_to_the_' . $field . '_character_count'
				);
				break;
			case 'og_image':
			case 'og_type':
				add_action(
					'wp_seo_social_' . $slug . '_meta_fields_' . $field . '_label',
					'wp_seo_social_the_meta_' . $field . '_label'
				);
				add_action(
					'wp_seo_social_' . $slug . '_meta_fields_' . $field . '_input',
					'wp_seo_social_' . $object . '_to_the_' . $slug . '_meta_' . $field . '_input'
				);
				break;
		}
	}
}
