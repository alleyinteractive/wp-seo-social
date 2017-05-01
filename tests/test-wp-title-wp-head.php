<?php
/**
 * Tests functions that hook into wp_head.
 *
 * @package WP_SEO_Social
 */

/**
 * Tests for WP_Head
 */
class WP_SEO_Social_WP_Head_Tests extends WP_UnitTestCase {

	/**
	 * Demo taxonomy
	 *
	 * @var string
	 */
	var $taxonomy  = 'demo_taxonomy';

	/**
	 * Demo post type
	 *
	 * @var string
	 */
	var $post_type = 'demo_post_type';

	/**
	 * Demo image path for upload
	 *
	 * @var string
	 */
	var $image_path = __DIR__ . '/image.png';

	/**
	 * Demo option
	 *
	 * @var string
	 */
	var $options   = array();

	/**
	 * Setup the tests.
	 */
	function setUp() {
		parent::setUp();
		$this->attachment_id = $this->factory->attachment->create_upload_object(
			$this->image_path,
			0
		);
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

	/**
	 * Clean up after ourselves.
	 */
	function tearDown() {
		parent::tearDown();
		_wp_seo_reset_post_types();
		_wp_seo_reset_taxonomies();
		delete_option( WP_SEO_Settings::SLUG );
		WP_SEO_Settings()->set_properties();
		WP_SEO_Settings()->set_options();
	}

	/**
	 * Update the plugin option with open graph elements for each test.
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
			'taxonomy_category',
			"taxonomy_{$this->taxonomy}",
			"archive_{$this->post_type}",
			'archive_date',
			'search',
			'404',
			'feed',
		) as $key ) {
			$this->options[ "{$key}_og_title" ]       = "demo_{$key}_og_title";
			$this->options[ "{$key}_og_description" ] = "demo_{$key}_og_description";
			$this->options[ "{$key}_og_image" ]       = $this->attachment_id;
			$this->options[ "{$key}_og_type" ]        = "demo_{$key}_og_type";
		}

		update_option( WP_SEO_Settings::SLUG, WP_SEO_Settings()->sanitize_options( $this->options ) );
	}

	/**
	 * Test that WP_SEO::wp_head() echoes all OG <meta> tags with expected values.
	 *
	 * @param  string $og_title The expected OG title content.
	 * @param  string $og_description The expected OG description content.
	 * @param  int    $og_image The expected OG image content.
	 * @param  string $og_type The expected OG type content.
	 */
	function _assert_all_meta( $og_title, $og_description, $og_image, $og_type ) {
		$og_img_src = wp_get_attachment_image_url( $og_image, 'og_image' );
		$expected = <<<EOF
<meta name='og:title' content='{$og_title}' /><!-- WP SEO -->
<meta name='og:description' content='{$og_description}' /><!-- WP SEO -->
<meta name='og:image' content='{$og_img_src}' /><!-- WP SEO -->
<meta name='og:type' content='{$og_type}' /><!-- WP SEO -->
EOF;
		$this->assertSame( strip_ws( $expected ), strip_ws( get_echo( array( WP_SEO(), 'wp_head' ) ) ) );
	}

	/**
	 * Wrapper for checking assert_all_meta() on OG option values.
	 *
	 * @param  string $key The option to test. Use a name that prefixes
	 *     '_title', '_description', and '_keywords' in the option, like 'home'.
	 */
	function _assert_option_filters( $key ) {
		$this->_assert_all_meta( $this->options[ "{$key}_og_title" ], $this->options[ "{$key}_og_description" ], $this->options[ "{$key}_og_image" ] , $this->options[ "{$key}_og_type" ] );
	}

	/**
	 * Test default options on single page.
	 */
	function test_single() {
		$this->go_to( get_permalink( $this->factory->post->create() ) );
		$this->_assert_option_filters( 'single_post' );
	}

	/**
	 * Test default options on custom post type.
	 */
	function test_singular() {
		$this->go_to( get_permalink( $this->factory->post->create( array( 'post_type' => $this->post_type ) ) ) );
		$this->_assert_option_filters( "single_{$this->post_type}" );
	}

	/**
	 * Test custom meta OG on single.
	 */
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
		update_post_meta( $post_id, '_meta_og_image', $this->attachment_id );
		update_post_meta( $post_id, '_meta_og_type', 'website' );
		$this->_assert_all_meta( '_custom_meta_og_title', '_custom_meta_og_description', $this->attachment_id, 'website' );

		update_post_meta( $post_id, '_meta_og_title', '#title#' );
		update_post_meta( $post_id, '_meta_og_description', '#excerpt#' );
		update_post_meta( $post_id, '_meta_og_image', $this->attachment_id );
		update_post_meta( $post_id, '_meta_og_type', 'article' );
		$this->_assert_all_meta( $post_title, $post_excerpt, $this->attachment_id, 'article' );
	}

	/**
	 * Test default options on home page.
	 */
	function test_home() {
		$this->go_to( '/' );
		$this->_assert_option_filters( 'home' );
	}

	/**
	 * Test default options on author archive.
	 */
	function test_author_archive() {
		$author_id = $this->factory->user->create( array( 'user_login' => 'user-a' ) );
		$this->factory->post->create( array( 'post_author' => $author_id ) );
		$this->go_to( get_author_posts_url( $author_id ) );
		$this->_assert_option_filters( 'archive_author' );
	}

	/**
	 * Test default options on category page.
	 */
	function test_category() {
		$category_id = $this->factory->term->create( array( 'name' => 'cat-a', 'taxonomy' => 'category' ) );
		$this->go_to( get_term_link( $category_id, 'category' ) );
		$this->_assert_option_filters( 'taxonomy_category' );
	}

	/**
	 * Test default options on custom taxonomy page.
	 */
	function test_tax() {
		$term_id = $this->factory->term->create( array( 'name' => 'demo-a', 'taxonomy' => $this->taxonomy ) );
		$this->go_to( get_term_link( $term_id, $this->taxonomy ) );
		$this->_assert_option_filters( "taxonomy_{$this->taxonomy}" );
	}

	/**
	 * Test custom options on a category page.
	 */
	function test_category_custom() {
		$term_name = rand_str();
		$term_description = rand_str();
		$term_id = $this->factory->term->create( array( 'name' => $term_name, 'description' => $term_description, 'taxonomy' => 'category' ) );
		$option_name = WP_SEO()->get_term_option_name( get_term( $term_id, 'category' ) );
		$new_attachment_id = $this->factory->attachment->create_upload_object(
			$this->image_path,
			0
		);
		$this->go_to( get_term_link( $term_id, 'category' ) );
		update_option( $option_name, array(
			'og_title'       => '_custom_og_title',
			'og_description' => '_custom_og_description',
			'og_image'       => $new_attachment_id,
			'og_type'        => '_custom_og_type',
		) );
		$this->_assert_all_meta( '_custom_og_title', '_custom_og_description', $new_attachment_id, '_custom_og_type' );
		update_option( $option_name, array(
			'og_title'       => '#term_name#',
			'og_description' => '#term_description#',
			'og_image'       => $new_attachment_id,
			'og_type'        => 'website',
		) );
		$this->_assert_all_meta( $term_name, $term_description, $new_attachment_id, 'website' );
	}
}
