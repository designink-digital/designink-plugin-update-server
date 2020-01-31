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

use Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice;

defined( 'ABSPATH' ) or exit;

if ( ! class_exists(' DPUS_Github_Plugin_Api', false ) ) {

	/**
	 * A class to control all of the network communication aspects of retrieving information from a GitHub repository.
	 */
	class DPUS_Github_Plugin_Api {

		/** @var DPUS_Github_Plugin The parent Github Plugin object */
		private $Github_Plugin;

		/** @var string The base URL for GitHub's API */
		private $api_url = 'https://api.github.com';

		/**
		 * Construct the API wrapper.
		 * 
		 * @param DPUS_Github_Plugin $Github_Plugin The parent Github Plugin wrapper.
		 */
		public function __construct( DPUS_Github_Plugin $Github_Plugin ) {
			$this->Github_Plugin = $Github_Plugin;
		}

		/**
		 * Using the plugin slug, find the primary plugin file headers.
		 * 
		 * @return array|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice The plugin headers or any error.
		 */
		final public function get_plugin_headers() {

			$Hosting_Meta = $this->Github_Plugin->Plugin->get_hosting_options_meta();
			$cached_headers = DPUS_Github_Api_Cache::get_plugin_headers( $Hosting_Meta->slug );

			if ( false !== $cached_headers ) {
				return $cached_headers;
			} else {
				$request = $this->create_get_request( 'plugin-file' );
				$body = wp_remote_retrieve_body( $request );
				$headers = self::parse_header_data( $body );
				$latest_tag = $this->get_latest_tag();

				if ( Admin_Notice::is_admin_notice( $latest_tag ) ) {
					return $latest_tag;
				}

				$headers['Version'] = DPUS_Github_Plugin::git_version_to_wp( $latest_tag->git_tag->tag );

				DPUS_Github_Api_Cache::set_plugin_headers( $Hosting_Meta->slug, $headers );
				DPUS_Github_Api_Cache::save();

				return $headers;
			}

		}

		/**
		 * Get the plugin headers usable in a plugins api call.
		 * 
		 * @return array|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice The plugin headers for plugins API export or any error.
		 */
		final public function get_plugin_headers_api() {
			$plugin_headers = $this->get_plugin_headers();

			if ( Admin_Notice::is_admin_notice( $plugin_headers ) ) {
				return $plugin_headers;
			}

			$export = array();
			$export_guide = array(
				'name' => 'Name',
				'homepage' => 'PluginURI',
				'version' => 'Version',
				'author' => 'Author',
				'author_profile' => 'AuthorURI',
				'requires' => 'RequiresWP',
				'tested' => 'TestedWP',
				'requires_php' => 'RequiresPHP',
			);

			foreach ( $export_guide as $alias => $key ) {
				if ( isset( $plugin_headers[ $key ] ) ) {
					$export[ $alias ] = $plugin_headers[ $key ];
				}
			}

			return $export;
		}

		/**
		 * Get the plugin headers usable in a plugin transient call.
		 * 
		 * @return array|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice The plugin headers for plugins API export or any error.
		 */
		final public function get_plugin_headers_transient() {
			$plugin_headers = $this->get_plugin_headers();
			$slug = $this->Github_Plugin->Plugin->get_hosting_options_meta()->slug;

			if ( Admin_Notice::is_admin_notice( $plugin_headers ) ) {
				return $plugin_headers;
			}

			$export = array(
				'plugin' => sprintf( '%1$s/%1$s.php', $slug ),
			);

			$export_guide = array(
				'version' => 'Version',
				'uri' => 'PluginURI'
			);

			foreach ( $export_guide as $alias => $key ) {
				if ( isset( $plugin_headers[ $key ] ) ) {
					$export[ $alias ] = $plugin_headers[ $key ];
				}
			}

			return $export;
		}

		/**
		 * Get all GitHub tags for the project.
		 * 
		 * @return array|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice All of the project tags or any errors.
		 */
		final public function get_github_tags() {
			$Hosting_Meta = $this->Github_Plugin->Plugin->get_hosting_options_meta();

			if ( DPUS_Github_Api_Cache::are_all_github_tags_set( $Hosting_Meta->slug ) ) {
				$cached_tags = DPUS_Github_Api_Cache::get_github_tags( $Hosting_Meta->slug );
				return $cached_tags;
			} else {
				$request = $this->create_get_request( 'tags' );
				$body = wp_remote_retrieve_body( $request );
				$response_code = wp_remote_retrieve_response_code( $request );

				if ( 200 === $response_code ) {
					// If 200 response code
					$tags = json_decode( $body );

					if ( is_array( $tags ) && ! empty( $tags ) ) {
						// Return all the tags
						DPUS_Github_Api_Cache::set_github_tags( $Hosting_Meta->slug, $tags, true );
						DPUS_Github_Api_Cache::save();
						return $tags;
					} else if ( is_array( $tags ) && empty( $tags ) ) {
						// ... but if there are no tags!
						$message = "There seem to be no tags initialized for the project. Make sure there is at least one tag.";

						return new Admin_Notice( 'error', __( $message ), array( 'status_code' => $response_code, 'header' => __( "No tags found" ) ) );
					} else {
						// ... or something else happened!
						return new Admin_Notice( 'error', $body, array( 'status_code' => $response_code, 'header' => __( "Error parsing tags array" ) ) );
					}

				} else if ( $response_code === 404 ) {
					// Or if no tags are found
					return new Admin_Notice( 'error', $body, array(
							'status_code' => $response_code,
							'header' => __( "Could not find any project tags" ),
							'hint' => __( "Is your repository private?" ),
						)
					);
				} else {
					// Or something else
					return new Admin_Notice( 'error', $body, array( 'status_code' => $response_code, 'header' => __( "Error loading project tags" ) ) );
				}

			}

		}

		/**
		 * Get the tags and refs from the most recent tag.
		 * 
		 * @return object|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice
		 */
		final public function get_latest_tag() {
			return $this->get_tag( 'latest' );
		}

		/**
		 * Get the tags and refs from the very first tag.
		 * 
		 * @return object|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice
		 */
		final public function get_first_tag() {
			return $this->get_tag( 'first' );
		}

		/**
		 * Gets a specific tag information. Locates data for the GitHub tag, the Git tag, and the Git refs.
		 * 
		 * @param string $version The tag to get. Can be a tag version, or 'latest', or 'first'.
		 * 
		 * @return object|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice The tag object representation or any error.
		 */
		final public function get_tag( string $version ) {
			$tags = $this->get_github_tags();

			if ( Admin_Notice::is_admin_notice( $tags ) ) {
				return $tags;
			}

			$github_tag = null;

			// Get GitHub tag
			switch( $version ) {
				// Get latest tag
				case 'latest':
					$github_tag = $tags[0];
					break;
				// Get first tag
				case 'first':
					$first_tag_index = count( $tags ) - 1;
					$github_tag = $tags[ $first_tag_index ];
					break;
				// Search for tag by name
				default:
					foreach ( $tags as $t ) {
						if ( $t->name === $version ) {
							$github_tag = $t;
							break;
						}
					}
			}

			if ( null === $github_tag ) {
				return new Admin_Notice( 'error', __( "No tags found to retrieve information for." ) );
			}

			// Get tag refs
			$git_refs = $this->get_git_tag_refs( $github_tag->name );
			if ( Admin_Notice::is_admin_notice( $git_refs ) ) {
				return $git_refs;
			}

			// Get Git tag
			$git_tag = $this->get_git_tag( $git_refs->object->sha );
			if ( Admin_Notice::is_admin_notice( $git_tag ) ) {
				return $git_tag;
			}

			// Get tag commit
			$tag_commit = $this->get_tag_commit( $version, $github_tag->commit->sha );
			if ( Admin_Notice::is_admin_notice( $tag_commit ) ) {
				return $tag_commit;
			}

			// Assemble export object
			$tag_info = new \stdClass();
			$tag_info->github_tag = $github_tag;
			$tag_info->git_refs = $git_refs;
			$tag_info->git_tag = $git_tag;
			$tag_info->commit = $tag_commit;

			return $tag_info;
		}

		/**
		 * Return an object containing the Git refs for the plugin. See (https://developer.github.com/v3/git/refs/) for example return data.
		 * 
		 * @param string $tag The tag version to get refs for.
		 * 
		 * @return object|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice An object representation of the tag refs or any errors.
		 */
		final public function get_git_tag_refs( string $tag ) {
			$Hosting_Meta = $this->Github_Plugin->Plugin->get_hosting_options_meta();
			$cached_ref = DPUS_Github_Api_Cache::get_git_tag_ref( $Hosting_Meta->slug, $tag );

			if ( false !== $cached_ref ) {
				return $cached_ref;
			} else {
				$request = $this->create_get_request( 'git-tag-ref', array( 'tag' => $tag ) );
				$body = wp_remote_retrieve_body( $request );
				$response_code = wp_remote_retrieve_response_code( $request );

				if ( 200 === $response_code ) {
					$ref = json_decode( $body );

					if ( 'object' === gettype( $ref ) ) {
						DPUS_Github_Api_Cache::set_git_tag_ref( $Hosting_Meta->slug, $tag, $ref );
						DPUS_Github_Api_Cache::save();
						return $ref;
					} else {
						return new Admin_Notice( 'error', $body, array( 'status_code' => $response_code, 'header' => __( "Error parsing Git tag ref object" ) ) );
					}

				} else {
					return new Admin_Notice( 'error', $body, array( 'status_code' => $response_code, 'header' => __( "Error loading Git tag ref" ) ) );
				}

			}

		}

		/**
		 * Return the Git tag specified by the hash. See (https://developer.github.com/v3/git/tags/) for example return data.
		 * 
		 * @param string $tag_sha The hash of the tag to retrieve.
		 * 
		 * @return object|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice The Git tag or any errors.
		 */
		final public function get_git_tag( string $tag_sha ) {
			$Hosting_Meta = $this->Github_Plugin->Plugin->get_hosting_options_meta();
			$cached_tag = DPUS_Github_Api_Cache::get_git_tag_by_sha( $Hosting_Meta->slug, $tag_sha );

			if ( false !== $cached_tag ) {
				return $cached_tag;
			} else {
				$request = $this->create_get_request( 'git-tag', array( 'tag' => $tag_sha ) );
				$body = wp_remote_retrieve_body( $request );
				$response_code = wp_remote_retrieve_response_code( $request );

				if ( 200 === $response_code ) {
					$tag = json_decode( $body );

					if ( 'object' === gettype( $tag ) ) {
						DPUS_Github_Api_Cache::set_git_tag( $Hosting_Meta->slug, $tag->tag, $tag );
						DPUS_Github_Api_Cache::save();
						return $tag;
					} else {
						return new Admin_Notice( 'error', $body, array( 'status_code' => $response_code, 'header' => __( "Error parsing Git tag object" ) ) );
					}
				} else {
					return new Admin_Notice( 'error', $body, array( 'status_code' => $response_code, 'header' => __( "Error loading Git tag" ) ) );
				}

			}

		}

		/**
		 * Retrieve a single commit related to a tag from the project by hash. See (https://developer.github.com/v3/git/commits/) for example return data.
		 * 
		 * @param string $version	 	The version of the tag to retrieve the commit for.
		 * @param string $commit_sha	The hash of the commit to retrieve.
		 * 
		 * @return object|\Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Admin_Notice The Git commit or any errors.
		 */
		final public function get_tag_commit( string $version, string $commit_sha ) {
			$Hosting_Meta = $this->Github_Plugin->Plugin->get_hosting_options_meta();
			$cached_commit = DPUS_Github_Api_Cache::get_tag_commit( $Hosting_Meta->slug, $version );

			if ( false !== $cached_commit ) {
				return $cached_commit;
			} else {
				$request = $this->create_get_request( 'commit', array( 'commit' => $commit_sha ) );
				$body = wp_remote_retrieve_body( $request );
				$response_code = wp_remote_retrieve_response_code( $request );

				if ( 200 === $response_code ) {
					$commit = json_decode( $body );

					if ( 'object' === gettype( $commit ) ) {
						DPUS_Github_Api_Cache::set_tag_commit( $Hosting_Meta->slug, $version, $commit );
						DPUS_Github_Api_Cache::save();
						return $commit;
					} else {
						return new Admin_Notice( 'error', $body, array( 'status_code' => $response_code, 'header' => __( "Error parsing commit object" ) ) );
					}

				} else {
					return new Admin_Notice( 'error', $body, array( 'status_code' => $response_code, 'header' => __( "Error loading commit" ) ) );
				}

			}
		}

		/**
		 * Create a request to a specified resource using proper expected data types and authorization.
		 * 
		 * @param string $request_type The type of resource to locate from a GitHub project.
		 * @param array $args The arguments to forward on to $this->create_url().
		 * 
		 * @return array|\WP_Error The full request or any errors.
		 */
		final private function create_get_request( string $request_type, array $args = array() ) {
			$request_options = array();
			$hosting_meta = $this->Github_Plugin->Plugin->get_hosting_options_meta()->export_meta();

			// Maybe set token in Authorization header
			if ( ! empty( $hosting_meta['github_token'] ) ) {
				$request_options['headers']['Authorization'] = sprintf( 'token %s', $hosting_meta['github_token'] );
			}

			// Set correct data type in headers
			switch ( $request_type ) {
				case 'plugin-file':
					$request_options['headers']['Accept'] = 'application/vnd.github.v3.raw';
					break;
				default:
					$request_options['headers']['Accept'] = 'application/vnd.github.v3.json';
					break;
			}

			$url = $this->create_url( $request_type, $args );
			$request = wp_remote_get( $url, $request_options );

			return $request;
		}

		/**
		 * Returns a URL specifying the location of the requested resource.
		 * 
		 * @param string $request_type The type of resource to locate from a GitHub project.
		 * @param array $args Extra data is required by some resource locations.
		 * 
		 * @return string The URL where the specified resource is located.
		 */
		final private function create_url( string $request_type, array $args = array() ) {
			$url = '';

			switch ( $request_type ) {
				// Documentation: https://developer.github.com/v3/repos/contents/#get-contents
				case 'plugin-file':
					$Hosting_Meta = $this->Github_Plugin->Plugin->get_hosting_options_meta();
					$url = sprintf( '%s/contents/%s.php', $this->create_url( 'repo-base' ), $Hosting_Meta->slug );
					break;

				// Documentation: Not found, but it just works, okay?
				case 'tags':
					$url = sprintf( '%s/tags', $this->create_url( 'repo-base' ) );
					break;

				// Documentation: https://developer.github.com/v3/repos/commits/#get-a-single-commit
				case 'commit':
					$commit = key_exists( 'commit', $args ) ? $args['commit'] : '';
					$url = sprintf( '%s/commits/%s', $this->create_url( 'repo-base' ), $commit );
					break;

				// Documentation: https://developer.github.com/v3/git/tags/
				case 'git-tag':
					$tag = key_exists( 'tag', $args ) ? $args['tag'] : '';
					$url = sprintf( '%s/git/tags/%s', $this->create_url( 'repo-base' ), $tag );
					break;

				// Documentation: No API docs found, but it seems to be modeled after https://git-scm.com/book/en/v2/Git-Internals-Git-References
				case 'git-tag-ref':
					$tag = key_exists( 'tag', $args ) ? $args['tag'] : '';
					$url = sprintf( '%s/git/refs/tags/%s', $this->create_url( 'repo-base' ), $tag );
					break;

				// Documentation: https://developer.github.com/v3/repos/releases/#get-the-latest-release
				case 'latest-release':
					$url = sprintf( '%s/latest', $this->create_url( 'releases' ) );
					break;

				// Documentation: https://developer.github.com/v3/repos/releases/
				case 'releases':
					$url = sprintf( '%s/releases', $this->create_url( 'repo-base' ) );
					break;

				// Documentation: https://developer.github.com/v3/repos/#get
				case 'repo-base':
					$Hosting_Meta = $this->Github_Plugin->Plugin->get_hosting_options_meta();
					$url = sprintf( '%s/repos/%s/%s', $this->api_url, $Hosting_Meta->github_user, $Hosting_Meta->github_repo );
					break;
			}

			return $url;
		}

		/**
		 * Take an entire file's content and read for the RegEx matches to the header values.
		 * 
		 * @param string $data The file contents to seach for header values.
		 * 
		 * @return array The file headers found.
		 */
		final public static function parse_header_data( string $data ) {
			$headers = array();

			// Copied from wp-admin/includes/plugin.php from function {{ get_plugin_data() }}, added the 'tested up to' header.
			$default_headers = array(
				'Name'        => 'Plugin Name',
				'PluginURI'   => 'Plugin URI',
				'Version'     => 'Version',
				'Description' => 'Description',
				'Author'      => 'Author',
				'AuthorURI'   => 'Author URI',
				'TextDomain'  => 'Text Domain',
				'DomainPath'  => 'Domain Path',
				'Network'     => 'Network',
				'RequiresWP'  => 'Requires at least',
				'RequiresPHP' => 'Requires PHP',
				'TestedWP'    => 'Tested up to',
				// Site Wide Only is deprecated in favor of Network.
				'_sitewide'   => 'Site Wide Only',
			);

			preg_match_all( '/^\s+\*\s+([a-zA-Z0-9 ]+):\s+(.+)$/m', $data, $matches );

			// Set all found properties
			for ( $i = 0; $i < count( $matches[0] ); $i++ ) {
				$headers[ $matches[1][ $i ] ] = $matches[2][ $i ];
			}

			// Switch properties to aliases exported by WP
			foreach ( $default_headers as $alias => $property ) {
				if ( isset( $headers[ $property ] ) && $alias !== $property ) {
					$headers[ $alias ] = $headers[ $property ];
					unset( $headers[ $property ] );
				}
			}

			return $headers;
		}

	}

}
