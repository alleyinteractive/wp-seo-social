<?php
/**
 * Plugin Name: WP SEO Social
 * Plugin URI: https://github.com/alleyinteractive/wp-seo-social
 * Description: WP SEO extension that adds tags for major social networks.
 * Version: 0.0.1
 * Author: Alley Interactive, Matthew Boynes, David Herrera, Davis Shaver
 * Author URI: https://www.alleyinteractive.com/
 *
 * @package WP_SEO_Social
 */

/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if ( ! class_exists( 'WP_SEO_Social' ) ) :
	define( 'WP_SEO_SOCIAL_PATH', dirname( __FILE__ ) );
	require_once WP_SEO_SOCIAL_PATH . '/php/class-wp-seo-social.php';
endif;
