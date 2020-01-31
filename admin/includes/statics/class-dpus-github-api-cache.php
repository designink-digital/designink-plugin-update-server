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

if ( ! class_exists( 'DPUS_Github_Api_Cache', false ) ) {

	/**
	 * A class to control the temporary storage of API requests to GitHub for a more responsive experience.
	 */
	final class DPUS_Github_Api_Cache {

		/** @var array The key used to retrieve and store the transient data. */
		const TRANSIENT_KEY = 'dpus_github_api_cache';

		/** @var array The time before the API information expires. Set to hourly. */
		const TRANSIENT_EXPIRATION = 60*60;

		/** @var array The retrieved GitHub information to store in the cache. */
		private static $_data;

		/**
		 * Store the transient in the static class on initialization.
		 */
		final public static function load() {
			self::$_data = get_transient( self::TRANSIENT_KEY );

			if ( false === self::$_data ) {
				self::$_data = array();
				self::save();
			}
		}

		/**
		 * Get a GitHub tag from the cache or return FALSE.
		 * 
		 * @param string $slug		The plugin slug to get API info for.
		 * @param string $version	The tag version to get (can be 'latest' or 'first').
		 * 
		 * @return false|object The GitHub tag or FALSE.
		 */
		final public static function get_github_tag( string $slug, string $version ) {

			if ( isset( self::$_data[ $slug ][ $version ]['github_tag'] ) ) {
				return self::$_data[ $slug ][ $version ]['github_tag'];
			} else {
				return false;
			}

		}

		/**
		 * Set a GitHub tag in the cache.
		 * 
		 * @param string $slug			The plugin slug to set API info for.
		 * @param string $version		The tag version to set (can be 'latest' or 'first').
		 * @param object $github_tag	The tag object to set in cache.
		 */
		final public static function set_github_tag( string $slug, string $version, \stdClass $github_tag ) {
			self::maybe_init_version( $slug, $version );
			self::$_data[ $slug ][ $version ]['github_tag'] = $github_tag;
		}

		/**
		 * Get all GitHub tag from the cache or return FALSE.
		 * 
		 * @param string $slug The plugin slug to get API info for.
		 * 
		 * @return array The GitHub tags available in cache.
		 */
		final public static function get_github_tags( string $slug ) {
			$tags = array();

			if ( isset( self::$_data[ $slug ] ) && is_array( self::$_data[ $slug ] ) ) {
				foreach ( self::$_data[ $slug ] as $version ) {
					if ( isset( $version['github_tag'] ) ) {
						array_push( $tags, $version['github_tag'] );
					}
				}
			}

			return $tags;
		}

		/**
		 * Set multiple GitHub tags in the cache.
		 * 
		 * @param string $slug		The plugin slug to set API info for.
		 * @param array $github_tag	The tags to set in cache.
		 * @param bool $all_tags	Whether or not a complete set of tags is being set.
		 */
		final public static function set_github_tags( string $slug, array $github_tags, bool $all_tags ) {

			foreach ( $github_tags as $github_tag ) {
				self::maybe_init_version( $slug, $github_tag->name );
				self::$_data[ $slug ][ $github_tag->name ]['github_tag'] = $github_tag;
			}

			if ( true === $all_tags ) {
				self::$_data[ $slug ]['all_github_tags_set'] = true;
			}
		}

		/**
		 * Return whether or not all GitHub tags were set in one fell swoop.
		 * 
		 * @param string $slug The plugin slug to see if all GitHub tags have been set for.
		 * 
		 * @return bool Whether or not all GitHub tags have been set.
		 */
		final public static function are_all_github_tags_set( string $slug ) {

			if ( isset( self::$_data[ $slug ]['all_github_tags_set'] ) && true === self::$_data[ $slug ]['all_github_tags_set'] ) {
				return true;
			} else {
				return false;
			}

		}

		/**
		 * Get a Git tag from the cache or return FALSE.
		 * 
		 * @param string $slug		The plugin slug to get API info for.
		 * @param string $version	The tag version to get (can be 'latest' or 'first').
		 * 
		 * @return false|object The Git tag or FALSE.
		 */
		final public static function get_git_tag( string $slug, string $version ) {

			if ( isset( self::$_data[ $slug ][ $version ]['git_tag'] ) ) {
				return self::$_data[ $slug ][ $version ]['git_tag'];
			} else {
				return false;
			}

		}

		/**
		 * Search every available Git tag in the cache related to a version for one matching the hash.
		 * 
		 * @param string $slug		The plugin slug to get API info for.
		 * @param string $sha		The hash of the Git tag to get.
		 * 
		 * @return false|object The Git tag or FALSE.
		 */
		final public static function get_git_tag_by_sha( string $slug, string $sha ) {

			if ( is_array( self::$_data[ $slug ] ) ) {
				foreach ( self::$_data[ $slug ] as $version ) {
					if ( isset( $version['git_tag'] ) && $sha === $version['git_tag']->sha ) {
						return $version['git_tag'];
					}
				}
			} else {
				return false;
			}

			return false;
		}

		/**
		 * Set a Git tag in the cache.
		 * 
		 * @param string $slug		The plugin slug to set API info for.
		 * @param string $version	The tag version to set (can be 'latest' or 'first').
		 * @param object $git_tag	The tag object to set in cache.
		 */
		final public static function set_git_tag( string $slug, string $version, \stdClass $git_tag ) {
			self::maybe_init_version( $slug, $version );
			self::$_data[ $slug ][ $version ]['git_tag'] = $git_tag;
		}

		/**
		 * Get a commit for a Git tag from the cache or return FALSE.
		 * 
		 * @param string $slug		The plugin slug to get API info for.
		 * @param string $version	The tag version of the commit to get.
		 * 
		 * @return false|object The Git commit or FALSE.
		 */
		final public static function get_tag_commit( string $slug, string $version ) {

			if ( isset( self::$_data[ $slug ][ $version ]['commit'] ) ) {
				return self::$_data[ $slug ][ $version ]['commit'];
			} else {
				return false;
			}

		}

		/**
		 * Search every available tag commit in the cache related to a version for one matching the hash.
		 * 
		 * @param string $slug		The plugin slug to get API info for.
		 * @param string $sha		The hash of the commit to get.
		 * 
		 * @return false|object The Git commit or FALSE.
		 */
		final public static function get_tag_commit_by_sha( string $slug, string $sha ) {

			if ( is_array( self::$_data[ $slug ] ) ) {
				foreach ( self::$_data[ $slug ] as $version ) {
					if ( isset( $version['commit'] ) && $sha === $version['commit']->sha ) {
						return $version['commit'];
					}
				}
			} else {
				return false;
			}

			return false;
		}

		/**
		 * Set a Git tag commit in the cache.
		 * 
		 * @param string $slug			The plugin slug to set API info for.
		 * @param string $version		The tag version to set (can be 'latest' or 'first').
		 * @param object $tag_commit	The tag commit object to set in cache.
		 */
		final public static function set_tag_commit( string $slug, string $version, \stdClass $tag_commit ) {
			self::maybe_init_version( $slug, $version );
			self::$_data[ $slug ][ $version ]['commit'] = $tag_commit;
		}

		/**
		 * Get a Git tag ref from the cache or return FALSE.
		 * 
		 * @param string $slug		The plugin slug to get API info for.
		 * @param string $version	The tag version to get (can be 'latest' or 'first').
		 * 
		 * @return false|object The Git tag ref or FALSE.
		 */
		final public static function get_git_tag_ref( string $slug, string $version ) {

			if ( isset( self::$_data[ $slug ][ $version ]['git_tag_ref'] ) ) {
				return self::$_data[ $slug ][ $version ]['git_tag_ref'];
			} else {
				return false;
			}

		}

		/**
		 * Set a Git tag ref in the cache.
		 * 
		 * @param string $slug		The plugin slug to set API info for.
		 * @param string $version	The tag version to set (can be 'latest' or 'first').
		 * @param object $tag_ref	The tag ref object to set in cache.
		 */
		final public static function set_git_tag_ref( string $slug, string $version, \stdClass $tag_ref ) {
			self::maybe_init_version( $slug, $version );
			self::$_data[ $slug ][ $version ]['git_tag_ref'] = $tag_ref;
		}

		/**
		 * Get a plugin's headers from the cache or return FALSE.
		 * 
		 * @param string $slug The plugin slug to get headers for.
		 * 
		 * @return false|array The plugin headers or FALSE.
		 */
		final public static function get_plugin_headers( string $slug ) {

			if ( isset( self::$_data[ $slug ]['plugin_headers'] ) ) {
				return self::$_data[ $slug ]['plugin_headers'];
			} else {
				return false;
			}

		}

		/**
		 * Set a plugin's headers in the cache.
		 * 
		 * @param string $slug		The plugin slug to set API info for.
		 * @param string $headers	The headers of the plugin to set in cache.
		 */
		final public static function set_plugin_headers( string $slug, array $headers ) {
			self::maybe_init_version( $slug );
			self::$_data[ $slug ]['plugin_headers'] = $headers;
		}

		/**
		 * Save the static class data to the transient.
		 */
		final public static function save() {
			set_transient( self::TRANSIENT_KEY, self::$_data, self::TRANSIENT_EXPIRATION );
		}

		/**
		 * Clear the cache, or optionally just the cache of a single slug.
		 * 
		 * @param string $slug The slug of the cache to clear, or an empty string to clear the entire cache.
		 */
		final public static function clear_cache( string $slug = '' ) {

			if ( $slug === '' ) {
				self::$_data = array();
			} else {
				unset( self::$_data[ $slug ] );
			}

			self::save();
		}

		/**
		 * Check if slug and version arrays have been created and create them if they have not been.
		 * 
		 * @param string $slug The plugin slug to check arrays for.
		 * @param string $version The tag version to check arrays for.
		 */
		final private static function maybe_init_version( string $slug, string $version = '' ) {
			if ( ! isset( self::$_data[ $slug ] ) ) {
				self::$_data[ $slug ] = array();
			}

			if ( ! empty( $version ) ) {
				if ( ! isset( self::$_data[ $slug ][ $version ] ) ) {
					self::$_data[ $slug ][ $version ] = array();
				}
			}
		}

	}

}
