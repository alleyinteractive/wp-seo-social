<?php

/**
 * Sanitizes image field.
 *
 * @param mixed $input The input's current value.
 * @return int $input The sanitized value.
 */
function wp_seo_sanitize_image_field( $input ) {
	if ( ! is_int( $input ) || ! defined( 'FILTER_SANITIZE_NUMBER_INT' ) ) {
		return;
	}
	return filter_var( $input, FILTER_SANITIZE_NUMBER_INT );
}
