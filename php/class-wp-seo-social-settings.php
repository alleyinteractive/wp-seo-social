<?php
/**
 * Class file for WP_SEO_Social_Settings
 *
 * @package WP_SEO_Social
 */

/**
 * Manages extension of WP_SEO_Settings
 */
class WP_SEO_Social_Settings {
	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Post types that can be viewed individually and have per-entry meta values.
	 *
	 * @see WP_SEO_Social_Settings::setup().
	 *
	 * @var array Post type objects.
	 */
	private $single_post_types = array();

	/**
	 * Post types with archives, which can have meta fields set for them.
	 *
	 * @see  WP_SEO_Social_Settings::setup().
	 *
	 * @var array Post type objects.
	 */
	private $archived_post_types = array();

	/**
	 * Taxonomies with archive pages, which can have meta fields set for them.
	 *
	 * @see  WP_SEO_Social_Settings::setup().
	 *
	 * @var array Term objects.
	 */
	private $taxonomies = array();

	/**
	 * Fields that are classified as text fields
	 *
	 * @see  WP_SEO_Social_Settings::register_settings().
	 *
	 * @var array Field ID's.
	 */
	public $handle_as_text = array();

	/**
	 * Fields to whitelist.
	 *
	 * @var array Field ID's to whitelist.
	 */
	public $fields_to_whitelist = array(
		'og_title',
		'og_description',
		'og_image',
		'og_type',
	);

	/**
	 * The default options to save.
	 *
	 * @var array.
	 */
	public $default_options = array();

	/**
	 * Storage unit for the current option values of the plugin.
	 *
	 * @var array.
	 */
	public $options = array();

	/**
	 * Get the instance of this class.
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WP_SEO_Social_Settings;
			self::$instance->setup();
		}
		return self::$instance;
	}
	/**
	 * Setup the class
	 */
	protected function setup() {
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'set_properties' ), 9 );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}
	}

	/**
	 * Set class properties.
	 */
	public function set_properties() {
		/**
		 * Filter the post types that support per-entry social fields.
		 *
		 * @param array Associative array of post type keys and objects.
		 */
		$this->single_post_types = apply_filters(
			'wp_seo_social_single_post_types',
			wp_list_filter(
				get_post_types( array( 'public' => true ), 'objects' ),
				array( 'label' => false ),
				'NOT'
			)
		);

		/**
		 * Filter the taxonomies that support social fields on term archive pages.
		 *
		 * @param  array Associative array of taxonomy keys and objects.
		 */
		$this->taxonomies = apply_filters(
			'wp_seo_social_taxonomies',
			wp_list_filter(
				get_taxonomies(
					array(
						'public' => true,
					),
					'objects'
				),
				array(
					'label' => false,
				),
				'NOT'
			)
		);

		/**
		 * Filter the post types that support social fields on their archive pages.
		 *
		 * @param array Associative array of post type keys and objects.
		 */
		$this->archived_post_types = apply_filters(
			'wp_seo_social_archived_post_types',
			wp_list_filter(
				get_post_types(
					array( 'has_archive' => true ),
					'objects'
				),
				array( 'label' => false ),
				'NOT'
			)
		);

		/**
		 * Filter the options to save by default.
		 *
		 * These are also the settings shown when the option does not exist,
		 * such as when the the plugin is first activated.
		 *
		 * @param  array Associative array of setting names and values.
		 */
		$this->default_options = apply_filters(
			'wp_seo_social_default_options',
			array(
			'post_types' => array_keys( $this->single_post_types ),
				'taxonomies' => array_keys( $this->taxonomies ),
			)
		);

		/**
		 * Setup the options for easy access.
		 */
		$this->options = get_option(
			'wp-seo',
			$this->default_options
		);
	}

	/**
	 * Register the plugin settings.
	 */
	public function register_settings() {
		$social_sections = array(
			array(
				'section'  => 'social_post_types',
				'title'    => __( 'Social Post Types', 'wp-seo-social' ),
				'callback' => '__return_false',
			),
			array(
				'section'  => 'social_taxonomies',
				'title'    => __( 'Social Taxonomies', 'wp-seo-social' ),
				'callback' => '__return_false',
			),
		);
		foreach ( $social_sections as $section ) {
			add_settings_section(
				$section['section'],
				$section['title'],
				$section['callback'],
				'wp-seo'
			);
		}
		$social_settings = array(
			array(
				'id'       => 'home_og_title',
				'title'    => __( 'Open Graph Title Tag Format', 'wp-seo-social' ),
				'section'  => 'home',
				'args'     => array(
					'field' => 'home_og_title',
				),
			),
			array(
				'id'       => 'home_og_description',
				'title'    => __( 'Open Graph Description Tag Format', 'wp-seo-social' ),
				'section'  => 'home',
				'args'     => array(
					'field' => 'home_og_description',
					'type'  => 'textarea',
				),
			),
			array(
				'id'       => 'home_og_image',
				'title'    => __( 'Open Graph Image', 'wp-seo-social' ),
				'section'  => 'home',
				'args'     => array(
					'field' => 'home_og_image',
					'type'  => 'image',
				),
			),
			array(
				'id'       => 'home_og_type',
				'title'    => __( 'Open Graph Type', 'wp-seo-social' ),
				'section'  => 'home',
				'args'     => array(
					'field' => 'home_og_type',
					'type'  => 'dropdown',
					'boxes' => array(
						'website' => 'Website',
						'article' => 'Article',
					),
				),
			),
			array(
				'id'       => 'social_post_types',
				'title'    => __( 'Add social fields to individual:', 'wp-seo-social' ),
				'section'  => 'social_post_types',
				'args'     => array(
					'field' => 'post_types',
					'type'  => 'checkboxes',
					'boxes' => call_user_func_array(
						'wp_list_pluck', array( $this->single_post_types, 'label' )
					),
				),
			),
			array(
				'id'       => 'social_taxonomies',
				'title'    => __( 'Add social fields to individual:', 'wp-seo-social' ),
				'section'  => 'social_taxonomies',
				'args'     => array(
					'field' => 'taxonomies',
					'type'  => 'checkboxes',
					'boxes' => call_user_func_array(
						'wp_list_pluck', array(
							array_diff_key(
								$this->taxonomies,
								array(
									'post_format' => true,
								)
							),
							'label',
						)
					),
				),
			),
			array(
				'id'       => 'archive_author_og_title',
				'title'    => __( 'Open Graph Title Tag Format', 'wp-seo-social' ),
				'section'  => 'archive_author',
				'args'     => array(
					'field' => 'archive_author_og_title',
				),
			),
			array(
				'id'       => 'archive_author_og_description',
				'title'    => __( 'Open Graph Description Tag Format', 'wp-seo-social' ),
				'section'  => 'archive_author',
				'args'     => array(
					'field' => 'archive_author_og_description',
					'type'  => 'textarea',
				),
			),
			array(
				'id'       => 'archive_author_og_image',
				'title'    => __( 'Open Graph Image', 'wp-seo-social' ),
				'section'  => 'archive_author',
				'args'     => array(
					'field' => 'archive_author_og_image',
					'type'  => 'image',
				),
			),
			array(
				'id'       => 'archive_author_og_type',
				'title'    => __( 'Open Graph Type', 'wp-seo-social' ),
				'section'  => 'archive_author',
				'args'     => array(
					'field' => 'archive_author_og_type',
					'type'  => 'dropdown',
					'boxes' => array(
						'website' => 'Website',
						'article' => 'Article',
					),
				),
			),
			array(
				'id'       => 'archive_date_og_title',
				'title'    => __( 'Open Graph Title Tag Format', 'wp-seo-social' ),
				'section'  => 'archive_date',
				'args'     => array(
					'field' => 'archive_date_og_title',
				),
			),
			array(
				'id'       => 'archive_date_og_description',
				'title'    => __( 'Open Graph Description Tag Format', 'wp-seo-social' ),
				'section'  => 'archive_date',
				'args'     => array(
					'field' => 'archive_date_og_description',
					'type'  => 'textarea',
				),
			),
			array(
				'id'       => 'archive_date_og_image',
				'title'    => __( 'Open Graph Image', 'wp-seo-social' ),
				'section'  => 'archive_date',
				'args'     => array(
					'field' => 'archive_date_og_image',
					'type'  => 'image',
				),
			),
			array(
				'id'       => 'archive_date_og_type',
				'title'    => __( 'Open Graph Type', 'wp-seo-social' ),
				'section'  => 'archive_date',
				'args'     => array(
					'field' => 'archive_date_og_type',
					'type'  => 'dropdown',
					'boxes' => array(
						'website' => 'Website',
						'article' => 'Article',
					),
				),
			),
			array(
				'id'       => 'search_og_title',
				'title'    => __( 'Open Graph Title Tag Format', 'wp-seo-social' ),
				'section'  => 'search',
				'args'     => array(
					'field' => 'search_og_title',
				),
			),
			array(
				'id'       => 'search_og_description',
				'title'    => __( 'Open Graph Description Tag Format', 'wp-seo-social' ),
				'section'  => 'search',
				'args'     => array(
					'field' => 'search_og_description',
					'type'  => 'textarea',
				),
			),
			array(
				'id'       => 'search_og_image',
				'title'    => __( 'Open Graph Image', 'wp-seo-social' ),
				'section'  => 'search',
				'args'     => array(
					'field' => 'search_og_image',
					'type'  => 'image',
				),
			),
			array(
				'id'       => 'search_og_type',
				'title'    => __( 'Open Graph Type', 'wp-seo-social' ),
				'section'  => 'search',
				'args'     => array(
					'field' => 'search_og_type',
					'type'  => 'dropdown',
					'boxes' => array(
						'website' => 'Website',
						'article' => 'Article',
					),
				),
			),
			array(
				'id'       => '404_og_title',
				'title'    => __( 'Open Graph Title Tag Format', 'wp-seo-social' ),
				'section'  => '404',
				'args'     => array(
					'field' => '404_og_title',
				),
			),
			array(
				'id'       => '404_og_description',
				'title'    => __( 'Open Graph Description Tag Format', 'wp-seo-social' ),
				'section'  => '404',
				'args'     => array(
					'field' => '404_og_description',
					'type'  => 'textarea',
				),
			),
			array(
				'id'       => '404_og_image',
				'title'    => __( 'Open Graph Image', 'wp-seo-social' ),
				'section'  => '404',
				'args'     => array(
					'field' => '404_og_image',
					'type'  => 'image',
				),
			),
			array(
				'id'       => '404_og_type',
				'title'    => __( 'Open Graph Type', 'wp-seo-social' ),
				'section'  => '404',
				'args'     => array(
					'field' => '404_og_type',
					'type'  => 'dropdown',
					'boxes' => array(
						'website' => 'Website',
						'article' => 'Article',
					),
				),
			),
		);
		$taxonomy_settings = array();
		foreach ( $this->taxonomies as $taxonomy ) {
			$taxonomy_settings = array_merge(
				$taxonomy_settings,
				array(
					array(
						'id'       => 'archive_' . $taxonomy->name . '_og_title',
						'title'    => __( 'Open Graph Title Tag Format', 'wp-seo-social' ),
						'section'  => 'archive_' . $taxonomy->name,
						'args'     => array(
							'field' => 'archive_' . $taxonomy->name . '_og_title',
						),
					),
					array(
						'id'       => 'archive_' . $taxonomy->name . '_og_description',
						'title'    => __( 'Open Graph Description Tag Format', 'wp-seo-social' ),
						'section'  => 'archive_' . $taxonomy->name,
						'args'     => array(
							'field' => 'archive_' . $taxonomy->name . '_og_description',
							'type'  => 'textarea',
						),
					),
					array(
						'id'       => 'archive_' . $taxonomy->name . '_og_image',
						'title'    => __( 'Open Graph Image', 'wp-seo-social' ),
						'section'  => 'archive_' . $taxonomy->name,
						'args'     => array(
							'field' => 'archive_' . $taxonomy->name . '_og_image',
							'type'  => 'image',
						),
					),
					array(
						'id'       => 'archive_' . $taxonomy->name . '_og_type',
						'title'    => __( 'Open Graph Type', 'wp-seo-social' ),
						'section'  => 'archive_' . $taxonomy->name,
						'args'     => array(
							'field' => 'archive_' . $taxonomy->name . '_og_type',
							'type'  => 'dropdown',
							'boxes' => array(
								'website' => 'Website',
								'article' => 'Article',
							),
						),
					),
				)
			);
		}
		$post_settings = array();
		foreach ( $this->single_post_types as $post_type ) {
			$post_settings = array_merge(
				$post_settings,
				array(
					array(
						'id'       => 'single_' . $post_type->name . '_og_title',
						'title'    => __( 'Open Graph Title Tag Format', 'wp-seo-social' ),
						'section'  => 'single_' . $post_type->name,
						'args'     => array(
							'field' => 'single_' . $post_type->name . '_og_title',
						),
					),
					array(
						'id'       => 'single_' . $post_type->name . '_og_description',
						'title'    => __( 'Open Graph Description Tag Format', 'wp-seo-social' ),
						'section'  => 'single_' . $post_type->name,
						'args'     => array(
							'field' => 'single_' . $post_type->name . '_og_description',
							'type'  => 'textarea',
						),
					),
					array(
						'id'       => 'single_' . $post_type->name . '_og_image',
						'title'    => __( 'Open Graph Image', 'wp-seo-social' ),
						'section'  => 'single_' . $post_type->name,
						'args'     => array(
							'field' => 'single_' . $post_type->name . '_og_image',
							'type'  => 'image',
						),
					),
					array(
						'id'       => 'single_' . $post_type->name . '_og_type',
						'title'    => __( 'Open Graph Type', 'wp-seo-social' ),
						'section'  => 'single_' . $post_type->name,
						'args'     => array(
							'field' => 'single_' . $post_type->name . '_og_type',
							'type'  => 'dropdown',
							'boxes' => array(
								'website' => 'Website',
								'article' => 'Article',
							),
						),
					),
				)
			);
		}
		$archived_post_settings = array();
		foreach ( $this->archived_post_types as $post_type ) {
			$archived_post_settings = array_merge(
				$archived_post_settings,
				array(
					array(
						'id'       => 'archive_' . $post_type->name . '_og_title',
						'title'    => __( 'Open Graph Title Tag Format', 'wp-seo-social' ),
						'section'  => 'archive_' . $post_type->name,
						'args'     => array(
							'field' => 'archive_' . $post_type->name . '_og_title',
						),
					),
					array(
						'id'       => 'archive_' . $post_type->name . '_og_description',
						'title'    => __( 'Open Graph Description Tag Format', 'wp-seo-social' ),
						'section'  => 'archive_' . $post_type->name,
						'args'     => array(
							'field' => 'archive_' . $post_type->name . '_og_description',
							'type'  => 'textarea',
						),
					),
					array(
						'id'       => 'archive_' . $post_type->name . '_og_image',
						'title'    => __( 'Open Graph Image', 'wp-seo-social' ),
						'section'  => 'archive_' . $post_type->name . '_og_description',
						'args'     => array(
							'field' => 'archive_' . $post_type->name . '_og_image',
							'type'  => 'image',
						),
					),
					array(
						'id'       => 'archive_' . $post_type->name . '_og_type',
						'title'    => __( 'Open Graph Type', 'wp-seo-social' ),
						'section'  => 'archive_' . $post_type->name,
						'args'     => array(
							'field' => 'archive_' . $post_type->name . '_og_type',
							'type'  => 'dropdown',
							'boxes' => array(
								'website' => 'Website',
								'article' => 'Article',
							),
						),
					),
				)
			);
		}
		$all_settings = array_merge(
			$social_settings,
			$taxonomy_settings,
			$post_settings,
			$archived_post_settings
		);
		foreach ( $all_settings as $setting ) {
			add_settings_field(
				$setting['id'],
				$setting['title'],
				array( 'WP_SEO_Settings', 'field' ),
				'wp-seo',
				$setting['section'],
				$setting['args']
			);
			if ( ! isset( $setting['args']['type'] )
				|| in_array( $setting['args']['type'], WP_SEO_Settings()->field_types, true )
			) {
				$this->handle_as_text[] = $setting['id'];
			}
		}
	}

}

/**
 * Helper function to use the class instance.
 *
 * @return object
 */
function wp_seo_social_settings() {
	return WP_SEO_Social_Settings::instance();
}
add_action( 'after_setup_theme', 'WP_SEO_Social_Settings', 9 );
