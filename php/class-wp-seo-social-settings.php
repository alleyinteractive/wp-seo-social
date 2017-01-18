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
	private $handle_as_text = array();

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
		add_action( 'admin_init', array( $this, 'set_properties' ), 9 );
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'register_settings' ) );
		}
		add_filter( 'wp_seo_sanitize', array( $this, 'sanitize' ), 10, 2 );
		add_filter( 'wp_seo_options_page_menu_title', function() {
			return __( 'SEO & Social', 'wp-seo' );
		});
		add_filter( 'wp_seo_intersect_term_option', function( $array ) {
			$extra_fields = array(
				'og_title'         => '',
				'og_description'   => '',
				'og_image'         => '',
				'og_type'          => '',
			);
			$array = array_merge( $array, $extra_fields );
			return $array;
		});
		add_filter( 'wp_seo_whitelisted_fields', function( $array ) {
			$array = array_merge( $array, $this->fields_to_whitelist );
			return $array;
		});
		add_filter( 'wp_seo_box_heading', function( $heading ) {
			return __( 'Search & Social Optimization', 'wp-seo' );
		} );
		add_filter( 'wp_seo_arbitrary_tags', function( $arbitrary_tags ) {
			$pretags = array();
			$tags = array();
			$key = WP_SEO()->get_key();
			if ( is_singular() ) {
				if ( WP_SEO_Settings()->has_post_fields( $post_type = get_post_type() ) ) {
					foreach ( $this->fields_to_whitelist as $field ) {
						$field_string = get_post_meta( get_the_ID(), '_meta_' . $field, true );
						$pretags[ $field ] = $field_string;
					}
				}
			} elseif ( is_category() || is_tag() || is_tax() ) {
				if ( WP_SEO_Settings()->has_term_fields( $taxonomy = get_queried_object()->taxonomy ) && $option = get_option( WP_SEO()->get_term_option_name( get_queried_object() ) ) ) {
					foreach ( $this->fields_to_whitelist as $field ) {
						if ( isset( $option[ $field ] ) ) {
							$field_string = $option[ $field ];
							$pretags[ $field ] = $field_string;
						}
					}
				}
			}
			foreach ( $this->fields_to_whitelist as $field ) {
				if ( empty( $pretags[ $field ] ) ) {
					/**
					 * Filter the format strings of whitelisted custom tags.
					 *
					 * @param  string 		The format string retrieved from the settings.
					 * @param  string $key	The key of the setting retrieved.
					 */
					$field_string = apply_filters( 'wp_seo_meta_' . $field . '_format', WP_SEO_Settings()->get_option( $key . '_' . $field ), $key );
					$meta_field_value = WP_SEO()->format( $field_string );
					$pretags[ $field ] = $meta_field_value;
				} else {
					$pretags[ $field ] = WP_SEO()->format( $pretags[ $field ] );
				}
			}
			foreach ( $pretags as $key => $value ) {
				if ( $value && ! is_wp_error( $value ) ) {
					if ( 'og_image' === $key ) {
						$og_img_src = wp_get_attachment_image_src( $value, 'og_image' );
						if ( $og_img_src ) {
							$tags[] = array(
								'name' => $key,
								'content' => $og_img_src[0],
							);
						}
					} else {
						$tags[] = array(
							'name' => $key,
							'content' => $value,
						);
					}
				}
			}
			$arbitrary_tags = array_merge(
				$arbitrary_tags,
				$tags
			);
			return $arbitrary_tags;
		} );
	}

	/**
	 * Additional sanitization for our new fields
	 *
	 * @param  array $out The options currently being saved.
	 * @param  array $in  The options, raw.
	 * @return array $out The options to save.
	 */
	public function sanitize( $out, $in ) {
		$sanitize_as_text_field = $this->handle_as_text;
		foreach ( $sanitize_as_text_field as $field ) {
			$out[ $field ] = isset( $in[ $field ] ) && ( is_string( $in[ $field ] ) || is_integer( $in[ $field ] ) ) ? sanitize_text_field( $in[ $field ] ) : null;
		}
		return $out;
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
				'title'    => __( 'Social Taxonomies', 'wp-seo' ),
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
				'title'    => __( 'Add social fields to individual:', 'wp-seo' ),
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
				|| 'textarea' === $setting['args']['type']
				|| 'dropdown' === $setting['args']['type']
				|| 'image' === $setting['args']['type']
				|| ! in_array( $setting['args']['type'], WP_SEO_Settings()->field_types, true )
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
