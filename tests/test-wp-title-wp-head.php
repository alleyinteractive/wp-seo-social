<?php
/**
 * Tests for Wfunctions that hook into wp_head.
 *
 * @package WP_SEO_Social
 */
class WP_SEO_Social_WP_Head_Tests extends WP_UnitTestCase {

	var $taxonomy  = 'demo_taxonomy';
	var $post_type = 'demo_post_type';
	var $options   = array();

	function setUp() {
		parent::setUp();

		register_taxonomy( $this->taxonomy, 'post' );
		register_post_type( $this->post_type, array( 'rewrite' => true, 'has_archive' => true, 'public' => true ) );
		WP_SEO_Settings()->set_properties();
		WP_SEO_Social_Settings()->set_properties();
		WP_SEO_Social_Settings()->register_settings();

		$this->_update_option_for_tests();
		WP_SEO_Settings()->set_options();

		global $wp_rewrite;
		$wp_rewrite->init();
		$wp_rewrite->flush_rules();
	}

	function tearDown() {
		parent::tearDown();
		// Leave the place as we found it.
		_wp_seo_reset_post_types();
		_wp_seo_reset_taxonomies();
		delete_option( WP_SEO_Settings::SLUG );
		WP_SEO_Settings()->set_properties();
		WP_SEO_Settings()->set_options();
	}

	/**
	 * Update the plugin option with titles, descriptions, and keywords for each test.
	 *
	 * This option should include all of the expected values used in these
	 * tests. Not each test uses all values, but setting them all is a little
	 * cleaner, and the option has to be set one way or another.
	 */
	function _update_option_for_tests() {
		$this->options['post_types'] = array( 'post' );
		$this->options['taxonomies'] = array( 'category' );

		foreach ( array(
			'home',
			'single_post',
			"single_{$this->post_type}",
			'archive_author',
			'archive_category',
			"archive_{$this->taxonomy}",
			"archive_{$this->post_type}",
			'archive_date',
			'search',
			'404',
			'feed',
		) as $key ) {
			$this->options[ "{$key}_og_title" ]       = "demo_{$key}_og_title";
			$this->options[ "{$key}_og_description" ] = "demo_{$key}_og_description";
			$this->options[ "{$key}_og_image" ]       = "demo_{$key}_og_image";
			$this->options[ "{$key}_og_type" ] = "demo_{$key}_og_type";
		}

		update_option( WP_SEO_Settings::SLUG, WP_SEO_Settings()->sanitize_options( $this->options ) );
	}

	/**
	 * Test that WP_SEO::wp_head() echoes all <meta> tags with expected values.
	 *
	 * @param  string $description The expected meta description content.
	 * @param  string $keywords The expected meta keywords content.
	 */
	function _assert_all_meta( $og_title, $og_description, $og_image, $og_type ) {
		$expected = <<<EOF
<meta name='og_title' content='{$og_title}' /><!-- WP SEO -->
<meta name='og_description' content='{$og_description}' /><!-- WP SEO -->
<meta name='og_image' content='{$og_image}' /><!-- WP SEO -->
<meta name='og_type' content='{$og_type}' /><!-- WP SEO -->
EOF;
		$this->assertSame( strip_ws( $expected ), strip_ws( get_echo( array( WP_SEO(), 'wp_head' ) ) ) );
	}

	/**
	 * Wrapper for checking _assert_title() and _assert_all_meta() on option values.
	 *
	 * @param  string $key The option to test. Use a name that prefixes
	 *     '_title', '_description', and '_keywords' in the option, like 'home'.
	 */
	function _assert_option_filters( $key ) {
		$this->_assert_all_meta( $this->options["{$key}_og_title"], $this->options["{$key}_og_description"], $this->options["{$key}_og_image"] , $this->options["{$key}_og_type"] );
	}

	/**
	 * Tests for the core filters on each supported type of request.
	 *
	 * Most requests should be subject to _assert_option_filters(), at least.
	 */
	function test_single() {
		$this->go_to( get_permalink( $this->factory->post->create() ) );
		$this->_assert_option_filters( 'single_post' );
	}

	function test_singular() {
		$this->go_to( get_permalink( $this->factory->post->create( array( 'post_type' => $this->post_type ) ) ) );
		$this->_assert_option_filters( "single_{$this->post_type}" );
	}

	// A post with custom values should not use the single_{type}_ values.
	function test_single_custom() {
		$post_title   = rand_str();
		$post_excerpt = rand_str();
		$post_id = $this->factory->post->create( array(
			'post_title' => $post_title,
			'post_excerpt' => $post_excerpt,
		) );

		$this->go_to( get_permalink( $post_id ) );
		update_post_meta( $post_id, '_meta_og_title', '_custom_meta_og_title' );
		update_post_meta( $post_id, '_meta_og_description', '_custom_meta_og_description' );
		update_post_meta( $post_id, '_meta_og_image', '_custom_meta_og_image' );
		update_post_meta( $post_id, '_meta_og_type', '_custom_meta_og_type' );
		$this->_assert_all_meta( '_custom_meta_og_title', '_custom_meta_og_description', '_custom_meta_og_image', '_custom_meta_og_type' );

		// Formatting tags should be converted.
		update_post_meta( $post_id, '_meta_og_title', '#title#' );
		update_post_meta( $post_id, '_meta_og_description', '#excerpt#' );
		update_post_meta( $post_id, '_meta_og_image', '#title#' );
		update_post_meta( $post_id, '_meta_og_type', '#title#' );
		$this->_assert_all_meta( $post_title, $post_excerpt, $post_title, $post_title );
	}

	function test_home() {
		$this->go_to( '/' );
		$this->_assert_option_filters( 'home' );
	}

	function test_author_archive() {
		$author_ID = $this->factory->user->create( array( 'user_login' => 'user-a' ) );
		$this->factory->post->create( array( 'post_author' => $author_ID ) );
		$this->go_to( get_author_posts_url( $author_ID ) );
		$this->_assert_option_filters( 'archive_author' );
	}

	function test_category() {
		$category_ID = $this->factory->term->create( array( 'name' => 'cat-a', 'taxonomy' => 'category' ) );
		$this->go_to( get_term_link( $category_ID, 'category' ) );
		$this->_assert_option_filters( 'archive_category' );
	}

	function test_tax() {
		$term_ID = $this->factory->term->create( array( 'name' => 'demo-a', 'taxonomy' => $this->taxonomy ) );
		$this->go_to( get_term_link( $term_ID, $this->taxonomy ) );
		$this->_assert_option_filters( "archive_{$this->taxonomy}" );
	}

	// A term with custom values should not use the archive_{taxonomy}_ fields.
	function test_category_custom() {
		$term_name = rand_str();
		$term_description = rand_str();
		$term_id = $this->factory->term->create( array( 'name' => $term_name, 'description' => $term_description, 'taxonomy' => 'category' ) );
		$option_name = WP_SEO()->get_term_option_name( get_term( $term_id, 'category' ) );

		$this->go_to( get_term_link( $term_id, 'category' ) );

		update_option( $option_name, array(
			'og_title'       => '_custom_og_title',
			'og_description' => '_custom_og_description',
			'og_image'       => '_custom_og_image',
			'og_type'        => '_custom_og_type',
		) );
		$this->_assert_all_meta( '_custom_og_title', '_custom_og_description',  '_custom_og_image', '_custom_og_type' );

		// Formatting tags should be converted.
		update_option( $option_name, array(
			'og_title'       => '#term_name#',
			'og_description' => '#term_description#',
			'og_image'       => '#term_name#',
			'og_type'        => '#term_name#',
		) );
		$this->_assert_all_meta( $term_name, $term_description, $term_name, $term_name );
	}

}
