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

use Designink\WordPress\Framework\v1_0_1\Admin\Admin_Notice_Queue;
use Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Screens\Post_Screens;

if ( ! class_exists( 'DPUS_Hosted_Plugin_Screens', false ) ) {

	/**
	 * Manage the Hosted Plugin post managment screens and meta boxes.
	 */
	final class DPUS_Hosted_Plugin_Screens extends Post_Screens {

		/**
		 * The post type.
		 * 
		 * @return string The post type.
		 */
		final public static function post_type() { return DPUS_Hosted_Plugin::post_type(); }

		/**
		 * Screens entry point, global code hook.
		 */
		final public static function construct_screen() { }

		/**
		 * Screen checked code.
		 * 
		 * @param \WP_Screen The current screen.
		 */
		final public static function current_screen( \WP_Screen $current_screen ) { }

		/**
		 * Viewing the single post.
		 * 
		 * @param \WP_Screen The current screen.
		 */
		final public static function view_post( \WP_Screen $current_screen ) {
			add_action( 'admin_enqueue_scripts', array( __CLASS__, '_admin_enqueue_scripts_edit' ) );
			self::add_meta_boxes_edit();
		}

		/**
		 * Viewing all posts.
		 * 
		 * @param \WP_Screen The current screen.
		 */
		final public static function view_posts( \WP_Screen $current_screen ) { }

		/**
		 * Adding a post.
		 * 
		 * @param \WP_Screen The current screen.
		 */
		final public static function add_post( \WP_Screen $current_screen ) {
			add_action( 'admin_enqueue_scripts', array( __CLASS__, '_admin_enqueue_scripts_new' ) );
			self::add_meta_boxes_new();
		}

		/**
		 * The WordPress 'admin_enqueue_scripts' hook for editing a post.
		 */
		final public static function _admin_enqueue_scripts_edit() {
			global $post;
			$Plugin = new DPUS_Hosted_Plugin( $post );
			$Github_Plugin = new DPUS_Github_Plugin( $Plugin );
			$Plugin_Admin = Designink_Plugin_Update_Server::instance()->get_admin_module();

			if ( $Github_Plugin->is_valid() ) {
				wp_enqueue_media();
				wp_enqueue_editor();

				$plugins_api_data = $Plugin->get_plugins_api_meta()->export_meta();
				$plugin_images_urls = $Plugin->get_plugin_images_meta()->export_resolved_meta();
				$plugin_images_ids = $Plugin->get_plugin_images_meta()->export_meta();

				$Plugin_Admin->enqueue_js( 'plugins-api-meta-box-sections-controller', array(), $plugins_api_data );
				$Plugin_Admin->enqueue_js( 'plugin-images-meta-box-controller', array(), array( 'ids' => $plugin_images_ids, 'urls' => $plugin_images_urls ) );
			}

			$Plugin_Admin->enqueue_css( 'hosted-plugin-post-edit-styles' );
		}

		/**
		 * The WordPress 'admin_enqueue_scripts' hook for adding a post.
		 */
		final public static function _admin_enqueue_scripts_new() {
			$Plugin_Admin = Designink_Plugin_Update_Server::instance()->get_admin_module();
			$Plugin_Admin->enqueue_css( 'hosted-plugin-post-edit-styles' );
		}

		/**
		 * Function to register related meta boxes for the edit post page.
		 */
		final private static function add_meta_boxes_edit() {
			$current_screen = get_current_screen();
			$post = null;

			// ID set in GET on post view, in POST on post save
			if ( isset( $_GET['post'] ) ) {
				$post = get_post( $_GET['post'] );
			} else if ( isset( $_POST['post_ID'] ) ) {
				$post = get_post( $_POST['post_ID'] );
			}

			if ( $post ) {
				$Github_Plugin = new DPUS_Github_Plugin( new DPUS_Hosted_Plugin( $post ) );

				$Hosting_Meta_Box = DPUS_Hosting_Options_Meta_Box::instance();
				$Hosting_Meta_Box->screen = $current_screen;
				$Hosting_Meta_Box->add_meta_box();

				if ( $Github_Plugin->is_valid() ) {
					$Headers_Meta_Box = DPUS_Plugin_Headers_Meta_Box::instance();
					$Headers_Meta_Box->screen = $current_screen;

					$Plugins_Api_Meta_Box = DPUS_Plugins_Api_Meta_Box::instance();
					$Plugins_Api_Meta_Box->screen = $current_screen;

					$Images_Meta_Box = DPUS_Plugin_Images_Meta_Box::instance();
					$Images_Meta_Box->screen = $current_screen;

					$Github_Info_Meta_Box = DPUS_Github_Info_Meta_Box::instance();
					$Github_Info_Meta_Box->screen = $current_screen;

					$Headers_Meta_Box->add_meta_box();
					$Plugins_Api_Meta_Box->add_meta_box();
					$Images_Meta_Box->add_meta_box();
					$Github_Info_Meta_Box->add_meta_box();
				} else if ( isset( $_GET['post'] ) ) {
					Admin_Notice_Queue::add_notice( $Github_Plugin->get_valid() );
				}

			}

		}

		/**
		 * Function to register related meta boxes for the new post page.
		 */
		final private static function add_meta_boxes_new() {
			$current_screen = get_current_screen();

			$Hosting_Meta_Box = DPUS_Hosting_Options_Meta_Box::instance();
			$Hosting_Meta_Box->screen = $current_screen;

			$Hosting_Meta_Box->add_meta_box();
		}

	}

}
