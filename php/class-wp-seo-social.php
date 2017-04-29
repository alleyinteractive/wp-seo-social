<?php
/**
 * Class file for WP_SEO_Social
 *
 * @package WP_SEO_Social
 */

/**
 * WP SEO Social core functionality.
 */
class WP_SEO_Social {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	private static $_instance = null;

	/**
	 * Admin notices.
	 *
	 * @var array
	 */
	private $_admin_notices = array( 'updated' => array(), 'error' => array() );

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	private $_plugin_id = 'wp-seo-social/wp-seo-social.php';

	/**
	 * Class setup.
	 */
	protected function setup() {
		if ( class_exists( 'WP_SEO' ) && $this->check_wp_seo_version() ) :
			require_once WP_SEO_SOCIAL_PATH . '/php/class-wp-seo-social-wp-seo-filters.php';
			require_once WP_SEO_SOCIAL_PATH . '/php/class-wp-seo-social-settings.php';
			require_once WP_SEO_SOCIAL_PATH . '/php/social-filters.php';
			if ( is_admin() ) :
				require_once WP_SEO_SOCIAL_PATH . '/php/admin-functions-post.php';
				require_once WP_SEO_SOCIAL_PATH . '/php/admin-functions-term.php';
				require_once WP_SEO_SOCIAL_PATH . '/php/admin-template.php';
				require_once WP_SEO_SOCIAL_PATH . '/php/admin-template-term.php';
				require_once WP_SEO_SOCIAL_PATH . '/php/admin-template-post.php';
			endif;
		else :
			$this->_admin_notices['error'][] = __( 'A current version of WP SEO is required to use WP SEO Social', 'wp-seo-social' );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'admin_init', array( $this, 'deactivate' ) );
		endif;
	}

	/**
	 * Get the instance of this class.
	 *
	 * @codeCoverageIgnore
	 */
	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new WP_SEO_Social;
			self::$_instance->setup();
		}
		return self::$_instance;
	}

	/**
	 * Print admin notices, either 'updated' (green) or 'error' (red)
	 */
	public function admin_notices() {
		foreach ( $this->_admin_notices as $class => $notices ) {
			foreach ( $notices as $notice ) {
				printf( '<div class="%s"><p>%s</p></div>', esc_attr( $class ), esc_html( $notice ) );
			}
		}
	}

	/**
	 * Deactivate plugin if it is active
	 */
	public function deactivate() {
		if ( is_plugin_active( $this->_plugin_id ) ) {
			deactivate_plugins( $this->_plugin_id );
		}
	}

	/**
	 * Check the WP SEO version to make sure it's > 0.13.
	 */
	public function check_wp_seo_version( ) {
		if ( defined( 'WP_SEO_VERSION' ) ) {
			return version_compare( WP_SEO_VERSION, '0.13.0', '>=' );
		} else {
			return false;
		}
	}
}

/**
 * Helper function to use the class instance.
 *
 * @return WP_SEO_Social
 */
function wp_seo_social() {
	return WP_SEO_Social::instance();
}
add_action( 'after_setup_theme', 'wp_seo_social', 8 );
