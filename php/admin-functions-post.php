<?php
/**
 * Administration functions for post objects
 *
 * @package WP_SEO_Social
 */

/**
 * Call printing function for the OG title input for a given post.
 *
 * @param int $post_id Post ID.
 */
function wp_seo_social_post_id_to_the_post_meta_og_title_input( $post_id ) {
	wp_seo_social_the_meta_og_title_input( get_post_meta( $post_id, '_meta_og_title', true ) );
}

/**
 * Call printing function for the OG title character count for a given post.
 *
 * @param int $post_id Post ID.
 */
function wp_seo_social_post_id_to_the_post_og_title_character_count( $post_id ) {
	wp_seo_social_the_og_title_character_count( strlen( get_post_meta( $post_id, '_meta_og_title', true ) ) );
}

/**
 * Call printing function for the OG description input for a given post.
 *
 * @param int $post_id Post ID.
 */
function wp_seo_social_post_id_to_the_post_meta_og_description_input( $post_id ) {
	wp_seo_social_the_meta_og_description_input( get_post_meta( $post_id, '_meta_og_description', true ) );
}

/**
 * Call printing function for the OG description character count for a given post.
 *
 * @param int $post_id Post ID.
 */
function wp_seo_social_post_id_to_the_post_og_description_character_count( $post_id ) {
	wp_seo_social_the_og_description_character_count( strlen( get_post_meta( $post_id, '_meta_og_description', true ) ) );
}

/**
 * Call printing function for the OG image input for a given post.
 *
 * @param int $post_id Post ID.
 */
function wp_seo_social_post_id_to_the_post_meta_og_image_input( $post_id ) {
	wp_seo_social_the_meta_og_image_input( get_post_meta( $post_id, '_meta_og_image', true ) );
}

/**
 * Call printing function for the OG type input for a given post.
 *
 * @param int $post_id Post ID.
 */
function wp_seo_social_post_id_to_the_post_meta_og_type_input( $post_id ) {
	wp_seo_social_the_meta_og_type_input( get_post_meta( $post_id, '_meta_og_type', true ) );
}

