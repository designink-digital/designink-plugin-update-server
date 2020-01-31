<?php
/**
 * DesignInk Plugin Update Server
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to answers@designinkdigital.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to https://designinkdigital.com
 *
 * @author    DesignInk Digital
 * @copyright Copyright (c) 2008-2020, DesignInk, LLC
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use Designink\WordPress\Framework\v1_0_1\Plugin\Post_Type;

if ( ! class_exists( 'DPUS_Hosted_Plugin_Post_Type', false ) ) {

	/**
	 * The Hosted Plugin post type.
	 */
	final class DPUS_Hosted_Plugin_Post_Type extends Post_Type {

		/**
		 * The required name from the abstract.
		 * 
		 * @return string The Post Type name.
		 */
		final public static function post_type() { return 'di_hosted_plugin'; }

		/**
		 * The required options from the abstract.
		 * 
		 * @return array The Post Type options.
		 */
		final protected function post_type_options() {
			return array(
				'labels' => array(
					'add_new_item' => __( 'Host New Plugin' ),
					'menu_name' => __( 'Hosted Plugins' ),
				),
				'singular_name' => __( 'Hosted Plugin' ),
				'plural_name'	=> __( 'Hosted Plugins' ),
				'public'		=> false,
				'show_in_menu'	=> true,
				'show_ui'		=> true,
				'has_archive'	=> true,
				'show_in_menu'	=> 'plugins.php',
				'supports'		=> array( 'title' ),
			);
		}

	}

}
