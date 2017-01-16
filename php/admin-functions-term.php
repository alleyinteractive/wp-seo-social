<?php
/**
 * Administration functions for term objects
 *
 * @package WP_SEO_Social
 */

/**
 * Call printing function for the OG title input for a new term.
 */
function wp_seo_social_term_id_to_the_add_term_meta_og_title_input() {
	wp_seo_social_the_meta_og_title_input( null );
}

/**
 * Call printing function for the OG title character count for a new term.
 */
function wp_seo_social_term_id_to_the_og_title_character_count() {
	wp_seo_social_the_og_title_character_count( 0 );
}

/**
 * Call printing function for the OG description input for a new term.
 */
function wp_seo_social_term_id_to_the_add_term_meta_og_description_input() {
	wp_seo_social_the_meta_og_description_input( null );
}

/**
 * Call printing function for the OG description character count for a new term.
 */
function wp_seo_social_term_id_to_the_og_description_character_count() {
	wp_seo_social_the_og_description_character_count( 0 );
}

/**
 * Call printing function for the OG image input for a new term
 */
function wp_seo_social_term_id_to_the_add_term_meta_og_image_input() {
	wp_seo_social_the_meta_og_image_input( null );
}

/**
 * Call printing function for the OG description input for a new term.
 */
function wp_seo_social_term_id_to_the_add_term_meta_og_type_input() {
	wp_seo_social_the_meta_og_type_input( null );
}

/**
 * Call printing function for the OG title input for a new term.
 */
function wp_seo_social_term_data_to_the_meta_og_title_input() {
	wp_seo_social_the_meta_og_title_input( null );
}

/**
 * Call printing function for OG title character count for a new term.
 */
function wp_seo_social_term_data_to_the_og_title_character_count( $term_id, $taxonomy ) {
	wp_seo_social_the_og_title_character_count( 0 );
}
