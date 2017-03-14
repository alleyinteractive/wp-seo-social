<?php
/**
 * Tests for WP_SEO_Social extension.
 *
 * @package WP_SEO_Social
 */

/**
 * Tests for class-wp-seo-social.php.
 */
class WP_SEO_Social_Test_Case extends WP_UnitTestCase {

	/**
	 * Test that the main class has been loaded.
	 */
	function test_wp_seo_social_loaded() {
		$this->assertTrue( class_exists( 'WP_SEO_Social' ) );
	}
}
