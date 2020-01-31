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

use Designink\WordPress\Framework\v1_0_1\Plugin\Post\Post_Meta;

if ( ! class_exists( 'DPUS_Plugins_Api_Meta', false ) ) {

	/**
	 * A class for managing the Hosting Options Meta for a Hosted Plugin.
	 */
	final class DPUS_Plugins_Api_Meta extends Post_Meta {

		/** @var string The donation link. */
		public $donate_link;

		/** @var string The GitHub username. */
		public $sections;

		/** @var array Default Meta values. */
		private static $default_values = array(
			'donate_link' => '',
			'sections' => array(),
		);

		/**
		 * The required abstraction function meta_key()
		 * 
		 * @return string The meta key.
		 */
		final public static function meta_key() { return '_plugins_api'; }

		/**
		 * Construct the Hosting Options Meta.
		 * 
		 * @param \DPUS_Hosted_Plugin $Hosted_Plugin The parent 'di_hosted_plugin' post the Meta belongs to.
		 */
		public function __construct( DPUS_Hosted_Plugin $Plugin ) {

			if ( ! $Plugin ) {
				trigger_error( __( "No \\DPUS_Hosted_Plugin passed to \\DPUS_Plugins_Api_Meta constructor." ), E_USER_WARNING );
				return;
			}

			foreach ( self::$default_values as $property => $value ) {
				if ( property_exists( $this, $property ) ) {
					$this->{ $property } = $value;
				}
			}

			$this->Plugin = $Plugin;
			parent::__construct( $Plugin->get_post() );
		}

		/**
		 * Return an associative array representation of the Meta.
		 * 
		 * @return array The array representation of the Meta.
		 */
		final public function export_meta() {
			$export = array();

			foreach ( self::$default_values as $property => $default_value ) {
				if ( isset( $this->{ $property } ) ) {
					$export[ $property ] = $this->{ $property };
				}

				else {
					$export[ $property ] = $default_value;
				}
			}

			return $export;
		}

		/**
		 * Export values that correspond to expected plugins api information. Primarily converting the 'sections' structure.
		 */
		final public function export_api_meta() {
			$meta = $this->export_meta();
			$sections = array();

			foreach ( $meta['sections'] as $section ) {
				$sections[ $section['slug'] ] = $section['content'];
			}

			$meta['sections'] = $sections;
			return $meta;
		}

	}

}
