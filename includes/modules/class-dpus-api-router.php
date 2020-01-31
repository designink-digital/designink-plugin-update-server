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

use Designink\WordPress\Framework\v1_0_1\Module;

if ( ! class_exists( 'DPUS_Api_Router', false ) ) {

	/**
	 * The module responsible for setting up the API routes for getting plugin transients and plugins api information.
	 */
	final class DPUS_Api_Router extends Module {

		/**
		 * Module entry point
		 */
		final public static function construct() {
			add_action( 'init', array( __CLASS__, '_init' ) );
		}

		/**
		 * WordPress 'init' hook
		 */
		final public static function _init() {
			add_action( 'rest_api_init', array( __CLASS__, 'set_transient_info_endpoint' ) );
			add_action( 'rest_api_init', array( __CLASS__, 'set_plugins_info_endpoint' ) );
		}

		/**
		 * Register the plugins info endpoint for API calls.
		 */
		final public static function set_plugins_info_endpoint() {
			register_rest_route( 'designink/api', '/plugin-updates/plugins-api', array(
				'methods' => 'GET',
				'callback' => array( __CLASS__, 'handle_plugins_api_info_request' ),
			) );
		}

		/**
		 * Register the plugin transient info endpoint for API calls.
		 */
		final public static function set_transient_info_endpoint() {
			register_rest_route( 'designink/api', '/plugin-updates/transients', array(
				'methods' => 'GET',
				'callback' => array( __CLASS__, 'handle_plugin_transient_request' ),
			));
		}

		/**
		 * Get the values for a plugins api request and print the raw JSON, then exit the script.
		 */
		final static public function handle_plugins_api_info_request() {

			if ( empty( $_GET['plugin'] ) ) {
				http_response_code( 404 );
				print( __( "No plugin specified." ) );
				exit( 0 );
			}

			$plugin = $_GET['plugin'];

			$posts = get_posts( array(
				'post_type' => DPUS_Hosted_Plugin::post_type(),
				'post_status' => 'publish'
			) );

			$info = new \stdClass();

			foreach ( $posts as $post ) {
				$Plugin = new DPUS_Hosted_Plugin( $post );
				$slug = $Plugin->get_hosting_options_meta()->slug;

				if ( $slug === $plugin ) {
					$Github_Plugin = new DPUS_Github_Plugin( $Plugin );
					$info = $Github_Plugin->get_plugins_api_info();
					break;
				}
			}

			header( 'Content-Type: application/json' );
			print( json_encode( $info ) );
			exit( 0 );
		}

		/**
		 * Get the values for specified plugin transients and print the raw JSON, then exit the script.
		 */
		final public static function handle_plugin_transient_request() {
			$plugins = array();
			$transients = array();

			if ( ! empty( $_GET['plugins'] ) ) {
				$plugins = explode( ',', $_GET['plugins'] );
			}

			$posts = get_posts( array(
				'post_type' => DPUS_Hosted_Plugin::post_type(),
				'post_status' => 'publish'
			) );

			foreach ( $posts as $post ) {
				$Plugin = new DPUS_Hosted_Plugin( $post );
				$slug = $Plugin->get_hosting_options_meta()->slug;

				if ( in_array( $slug, $plugins ) ) {
					$Github_Plugin = new DPUS_Github_Plugin( $Plugin );
					$transients[] = $Github_Plugin->get_plugin_transient_info();
				}
			}

			header( 'Content-Type: application/json' );
			print( json_encode( $transients ) );
			exit( 0 );
		}

	}

}
