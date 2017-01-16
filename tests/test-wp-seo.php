<?php
/**
 * Tests for class-wp-seo-social.php.
 *
 * @package WP_SEO_Social
 */
class WP_SEO_Social_Test_Case extends WP_UnitTestCase {
	function test_wp_seo_social_loaded() {
		$this->assertTrue( class_exists( 'WP_SEO_Social' ) );
	}
}
