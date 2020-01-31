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

use Designink\WordPress\Framework\v1_0_1\Utility;
use Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice;
use Designink\WordPress\Plugin_Update_Helper\v1_0_0\Plugin_Helper_Settings_Module;

defined( 'ABSPATH' );

if ( ! class_exists( 'DPUS_Github_Plugin', false ) ) {

	/**
	 * A wrappper class for plugins that are hosted using GitHub.
	 */
	final class DPUS_Github_Plugin {

		/** @var \DPUS_Hosted_Plugin The Hosted Plugin post associated with the plugin. */
		public $Plugin;

		/** @var \DPUS_Github_Plugin_Api The API interface to GitHub concerning the associated Plugin. */
		public $Github_Api;

		/** @var bool|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice Whether or not the specified plugin resource is valid and any error message encountered. */
		private $valid = false;

		/**
		 * Get the validity value;
		 * 
		 * @return bool|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice The stored validity value.
		 */
		final public function get_valid() { return $this->valid; }

		/**
		 * Construct the API and validate.
		 * 
		 * @param \DPUS_Hosted_Plugin $Plugin The parent Hosted Plugin.
		 */
		public function __construct( DPUS_Hosted_Plugin $Plugin ) {
			$this->Plugin = $Plugin;
			$this->Github_Api = new DPUS_Github_Plugin_Api( $this );
			$this->validate_plugin_resource();
		}

		/**
		 * Return the validity check performed when constructing this Plugin.
		 * 
		 * @return bool Whether or not the Plugin is determinied as valid.
		 */
		final public function is_valid() {

			if ( true === $this->valid ) {
				return true;
			} else {
				return false;
			}

		}

		/**
		 * Given a GitHub resource, verify that the resource exists and that plugin headers exist and that the name and slug are specified, then set $this->valid.
		 * 
		 * @return true|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice Returns TRUE if the resource is a valid, hostable plugin. Otherwise, returns an Admin Notice with details.
		 */
		final public function validate_plugin_resource() {
			$Headers = $this->Github_Api->get_plugin_headers();

			if ( Admin_Notice::is_admin_notice( $Headers ) ) {
				$this->valid = $Headers;
				return $Headers;
			}

			$name_empty = empty( $Headers['Name'] );

			if ( $name_empty ) {
				$Notice = new Admin_Notice( 'error', __( "There appears to be no plugin name in the plugin headers." ), array( 'status_code' => 200, 'hint' => 'Is your plugin slug correct?' ) );
				$this->valid = $Notice;
				return $Notice;
			}

			$this->valid = true;
			return true;
		}

		/**
		 * Returns the API information about the plugin and whether saved header meta values override actual plugin headers.
		 * 
		 * @return array The plugin API information expected by the WordPress dashboard.
		 */
		final public function get_plugins_api_info() {

			// Get the plugin headers and relavant meta data to provide plugin API info
			$plugin_api_headers = $this->Github_Api->get_plugin_headers_api();
			$plugins_api_meta = $this->get_api_meta_values();
			$plugin_info = array();

			if ( 'override' === $this->Plugin->get_plugin_headers_meta()->override_headers ) {
				$plugin_info = array_merge( $plugin_api_headers, $plugins_api_meta );
			} else {
				$plugin_info = array_merge( $plugins_api_meta, $plugin_api_headers );
			}

			// Get the latest tag for version information
			$latest_tag = $this->Github_Api->get_latest_tag();

			if ( Admin_Notice::is_admin_notice( $latest_tag ) ) {
				return $latest_tag;
			}

			// Get the first tag to find the creation date.
			$first_tag = $this->Github_Api->get_first_tag();

			if ( Admin_Notice::is_admin_notice( $first_tag ) ) {
				return $first_tag;
			}

			$slug = $this->Plugin->get_hosting_options_meta()->slug;

			// Set remaining plugin information properties
			$plugin_info['version'] = self::git_version_to_wp( $latest_tag->git_tag->tag );
			$plugin_info['slug'] = $slug;
			$plugin_info['download_link'] = $latest_tag->github_tag->zipball_url;
			$plugin_info['last_updated'] = $latest_tag->git_tag->tagger->date;
			$plugin_info['added'] = $first_tag->git_tag->tagger->date;

			// Maybe make author field into an HTML link if a profile is provided
			if ( key_exists( 'author_profile', $plugin_info ) ) {
				$plugin_info['author'] = sprintf( '<a href="%s" target="_blank">%s</a>', $plugin_info['author_profile'], $plugin_info['author'] );
			}

			return $plugin_info;
		}

		/**
		 * Get the Plugin headers and headers meta and combine them to return a complete plugin transient for update reporting.
		 * 
		 * @return array The plugin transient data expected by WordPress for update reporting.
		 */
		final public function get_plugin_transient_info() {

			// Get plugin transient data from the plugin headers and the meta
			$plugin_transient_headers = $this->Github_Api->get_plugin_headers_transient();
			$plugin_transient_meta = $this->get_transient_meta_values();
			$transient_info = array();

			if ( 'override' === $this->Plugin->get_plugin_headers_meta()->override_headers ) {
				$transient_info = array_merge( $plugin_transient_headers, $plugin_transient_meta );
			} else {
				$transient_info = array_merge( $plugin_transient_meta, $plugin_transient_headers );
			}

			// Get latest tag for current version
			$latest_tag = $this->Github_Api->get_latest_tag();

			if ( Admin_Notice::is_admin_notice( $latest_tag ) ) {
				return $latest_tag;
			}

			$transient_info['slug'] = $this->Plugin->get_hosting_options_meta()->slug;
			$transient_info['new_version'] = self::git_version_to_wp( $latest_tag->git_tag->tag );
			$transient_info['package'] = $latest_tag->github_tag->zipball_url;

			return $transient_info;
		}

		/**
		 * Returns the Git version without the 'v'.
		 * 
		 * @param string $tag The string containing a git-styled version to parse.
		 * 
		 * @return string The WordPress expected version (without the leading 'v'), or the initial string if there is no 'v'.
		 */
		final public static function git_version_to_wp( string $tag ) {
			if ( 1 === preg_match( '/^v(?:\d+\.?)+$/', $tag ) ) {
				return substr( $tag, 1 );
			}

			return $tag;
		}

		/**
		 * Returns all of the usable values for plugin transient information that is saved in the Hosted Plugin Meta.
		 * 
		 * @return array The valid plugin transient info from the stored Meta values.
		 */
		final public function get_transient_meta_values() {
			$transient_info = array();

			$hosting_meta = $this->Plugin->get_hosting_options_meta()->export_meta();
			$headers_meta = $this->Plugin->get_plugin_headers_meta()->export_meta();
			$images_meta = $this->Plugin->get_plugin_images_meta()->export_resolved_meta();
			$api_meta = $this->Plugin->get_plugins_api_meta()->export_meta();

			$transient_info = Utility::guided_array_merge(
				array(
					'homepage' => '',
					'icons' => array(),
					'banners' => array(),
					'tested' => '',
					'requires' => '',
					'requires_php' => ''
				),
				$headers_meta, $images_meta, $api_meta
			);

			// WordPress calls the homepage property 'url' for transients
			$transient_info['url'] = $transient_info['homepage'];

			// Maybe set private token
			if ( ! empty( $hosting_meta['github_token'] ) ) {
				$ssl_key = Plugin_Helper_Settings_Module::get_ssl_key();
				$transient_info['token'] = base64_encode( @openssl_encrypt( $hosting_meta['github_token'], 'aes-256-cbc', $ssl_key, OPENSSL_RAW_DATA ) );
			}

			return $transient_info;
		}

		/**
		 * Returns all of the usable values for the plugins API information that is saved in the Hosted Plugin Meta.
		 * 
		 * @return array The valid plugins API information from the stored Meta values.
		 */
		final public function get_api_meta_values() {
			$api_info = array();

			$headers_meta = $this->Plugin->get_plugin_headers_meta()->export_meta();
			$images_meta = $this->Plugin->get_plugin_images_meta()->export_resolved_meta();
			$api_meta = $this->Plugin->get_plugins_api_meta()->export_api_meta();

			$api_info = Utility::guided_array_merge(
				array(
					'author' => '',
					'author_profile' => '',
					'homepage' => '',
					'banners' => Utility::guided_array_merge(
						array( '1x' => '', '2x' => '' ),
						$images_meta['banners']
					),
					'tested' => '',
					'requires' => '',
					'requires_php' => '',
					'sections' => '',
					'donate_link' => ''
				),
				$headers_meta, $api_meta
			);

			// Alias the image names, because WordPress does that here
			$api_info['banners']['low'] = $api_info['banners']['1x'];
			$api_info['banners']['high'] = $api_info['banners']['2x'];

			// The remaining meta values are hardcoded until they can be implemented
			$api_info['rating'] = 100;
			$api_info['num_ratings'] = 82643;
			$api_info['ratings'] = array(
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 0,
				5 => $api_info['num_ratings'],
			);

			$api_info['support_threads'] = 42;
			$api_info['support_threads_resolved'] = 42;
			$api_info['active_installs'] = 9500000;

			return $api_info;
		}

	}

}
