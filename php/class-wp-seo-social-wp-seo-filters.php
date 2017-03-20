<?php
/**
 * Class file for WP_SEO_Social_WP_SEO_Filters
 *
 * @package WP_SEO_Social
 */

/**
 * Manages filters of WP SEO
 */
class WP_SEO_Social_WP_SEO_Filters {
	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Get the instance of this class.
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new WP_SEO_Social_WP_SEO_Filters;
			self::$instance->setup();
		}
		return self::$instance;
	}

	/**
	 * Setup the class
	 */
	protected function setup() {
		add_filter( 'wp_seo_sanitize_as_integer', array( $this, 'filter_wp_seo_sanitize_as_integer_field' ) );
		add_filter( 'wp_seo_sanitize_as_text_field', array( $this, 'filter_wp_seo_sanitize_as_text_field' ) );
		add_filter( 'wp_seo_whitelisted_settings', array( $this, 'filter_wp_seo_whitelisted_settings' ) );
		add_filter( 'wp_seo_options_page_menu_title', array( $this, 'filter_wp_seo_options_page_menu_title' ) );
		add_filter( 'wp_seo_intersect_term_option',  array( $this, 'filter_wp_seo_intersect_term_option' ) );
		add_filter( 'wp_seo_whitelisted_fields', array( $this, 'filter_wp_seo_whitelisted_fields' ) );
		add_filter( 'wp_seo_box_heading', array( $this, 'filter_wp_seo_box_heading' ) );
		add_filter( 'wp_seo_arbitrary_tags', array( $this, 'filter_wp_seo_arbitrary_tags' ) );
	}
	/**
	 * Filter the WP SEO menu title.
	 *
	 * @return Translated and filtered title for menu.
	 */
	public function filter_wp_seo_options_page_menu_title() {
		return __( 'SEO & Social', 'wp-seo-social' );
	}

	/**
	 * Filter intersect data to add our fields.
	 *
	 * @param array $array Option values with default keys and values.
	 * @return array Option values with default keys and values.
	 */
	public function filter_wp_seo_whitelisted_settings( $array ) {
		$extra_fields = array(
			'og_title',
			'og_description',
			'og_image',
			'og_type',
		);
		return array_merge( $array, $extra_fields );
	}

	/**
	 * Fields for integer sanitization.
	 *
	 * @return array Fields for sanitization.
	 */
	public function filter_wp_seo_sanitize_as_integer_field() {
		$sanitize_as_integer = array(
			'home_og_image',
		);
		foreach ( WP_SEO_Settings()->single_post_types as $type ) {
			$sanitize_as_integer[] = "single_{$type->name}_og_image";
		}
		// Post type and other archives.
		foreach ( WP_SEO_Settings()->archived_post_types as $type ) {
			if ( is_object( $type ) ) {
				$type = $type->name;
			}
			$sanitize_as_integer[] = "archive_{$type}_og_image";
		}

		// Taxonomy archives.
		foreach ( WP_SEO_Settings()->taxonomies as $type ) {
			if ( is_object( $type ) ) {
				$type = $type->name;
			}
			$sanitize_as_integer[] = "taxonomy_{$type}_og_image";
		}

		foreach ( array( 'search', '404', 'archive_author' ) as $type ) {
			$sanitize_as_integer[] = "{$type}_og_image";
		}

		return $sanitize_as_integer;
	}

	/**
	 * Fields for text field sanitization.
	 *
	 * @return array Fields for sanitization.
	 */
	public function filter_wp_seo_sanitize_as_text_field() {
		$sanitize_as_text_field = array(
			'home_og_title',
			'home_og_description',
			'home_og_type',
		);
		foreach ( WP_SEO_Settings()->single_post_types as $type ) {
			$sanitize_as_text_field[] = "single_{$type->name}_og_title";
			$sanitize_as_text_field[] = "single_{$type->name}_og_description";
			$sanitize_as_text_field[] = "single_{$type->name}_og_type";
		}
		// Post type and other archives.
		foreach ( array_merge( WP_SEO_Settings()->archived_post_types ) as $type ) {
			if ( is_object( $type ) ) {
				$type = $type->name;
			}
			$sanitize_as_text_field[] = "archive_{$type}_og_title";
			$sanitize_as_text_field[] = "archive_{$type}_og_description";
			$sanitize_as_text_field[] = "archive_{$type}_og_type";
		}

		// Taxonomy archives.
		foreach ( WP_SEO_Settings()->taxonomies as $type ) {
			if ( is_object( $type ) ) {
				$type = $type->name;
			}
			$sanitize_as_text_field[] = "taxonomy_{$type}_og_title";
			$sanitize_as_text_field[] = "taxonomy_{$type}_og_description";
			$sanitize_as_text_field[] = "taxonomy_{$type}_og_type";
		}

		foreach ( array( 'search', '404', 'archive_author' ) as $type ) {
			$sanitize_as_text_field[] = "{$type}_og_title";
			$sanitize_as_text_field[] = "{$type}_og_description";
			$sanitize_as_text_field[] = "{$type}_og_type";
		}

		return $sanitize_as_text_field;
	}

	/**
	 * Filter intersect data to add our fields.
	 *
	 * @param array $array Option values with default keys and values.
	 * @return array Option values with default keys and values.
	 */
	public function filter_wp_seo_intersect_term_option( $array ) {
		$extra_fields = array(
			'og_title'         => '',
			'og_description'   => '',
			'og_image'         => '',
			'og_type'          => '',
		);
		return array_merge( $array, $extra_fields );
	}

	/**
	 * Filter the whitelisted fields so ours validate.
	 *
	 * @param array $array Array of whitelisted fields.
	 * @return Array of filtered whitelisted fields.
	 */
	public function filter_wp_seo_whitelisted_fields( $array ) {
		return array_merge( $array, WP_SEO_Social_Settings()->fields_to_whitelist );
	}

	/**
	 * Filter the WP SEO metabox heading.
	 *
	 * @return Translated and filtered title for metabox.
	 */
	public function filter_wp_seo_box_heading() {
		return __( 'Search & Social Optimization', 'wp-seo-social' );
	}

	/**
	 * Filter arbitrary tags output to print out OG tags.
	 *
	 * @param array $arbitrary_tags Array of any existing arbitrary tags.
	 * @return Filtered array of tags plus arbitrary tags.
	 */
	public function filter_wp_seo_arbitrary_tags( $arbitrary_tags ) {
		$pretags = array();
		$tags = array();
		$key = wp_seo_get_key();
		if ( is_singular() ) {
			if ( WP_SEO_Settings()->has_post_fields( get_post_type() ) ) {
				foreach ( WP_SEO_Social_Settings()->fields_to_whitelist as $field ) {
					$field_string = get_post_meta( get_the_ID(), '_meta_' . $field, true );
					$pretags[ $field ] = $field_string;
				}
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			if ( WP_SEO_Settings()->has_term_fields( get_queried_object()->taxonomy )
				&& $option = get_option( WP_SEO()->get_term_option_name( get_queried_object() ) )
			) {
				foreach ( WP_SEO_Social_Settings()->fields_to_whitelist as $field ) {
					if ( isset( $option[ $field ] ) ) {
						$field_string = $option[ $field ];
						$pretags[ $field ] = $field_string;
					}
				}
			}
		}
		foreach ( WP_SEO_Social_Settings()->fields_to_whitelist as $field ) {
			if ( empty( $pretags[ $field ] ) ) {
				/**
				 * Filter the format strings of whitelisted custom tags.
				 *
				 * @param  string 		The format string retrieved from the settings.
				 * @param  string $key	The key of the setting retrieved.
				 */
				$field_string = apply_filters( 'wp_seo_meta_' . $field . '_format', WP_SEO_Settings()->get_option( $key . '_' . $field ), $key );
				$pretags[ $field ] = $field_string;
			}
			// Format all but the og image tags.
			if ( 'og_image' === $field ) {
				$og_img_src = wp_get_attachment_image_src( $pretags[ $field ], 'og_image' );
				if ( ! empty( $og_img_src ) ) {
					$pretags[ $field ] = $og_img_src[0];
				} else {
					$pretags[ $field ] = false;
				}
			} else {
				$pretags[ $field ] = WP_SEO()->format( $pretags[ $field ] );
			}
		}
		foreach ( $pretags as $key => $value ) {
			if ( $value && ! is_wp_error( $value ) ) {
				$tags[] = array(
					'name' => $key,
					'content' => $value,
				);
			}
		}
		return array_merge(
			$arbitrary_tags,
			$tags
		);
	}

}

/**
 * Helper function to use the class instance.
 *
 * @return object
 */
function wp_seo_social_wp_seo_filters() {
	return WP_SEO_Social_WP_SEO_Filters::instance();
}
add_action( 'after_setup_theme', 'WP_SEO_Social_WP_SEO_Filters', 9 );
