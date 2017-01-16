<?php

/**
 * Mimic WP_UnitTestCase::reset_post_types() for supporting older versions of WP.
 *
 * @see https://core.trac.wordpress.org/changeset/29860.
 */
$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../../wp-seo/wp-seo.php';
	require dirname( __FILE__ ) . '/../wp-seo-social.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require_once $_tests_dir . '/includes/bootstrap.php';
