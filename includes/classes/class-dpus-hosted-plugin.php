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

use Designink\WordPress\Framework\v1_0_1\Plugin\Post;

if ( ! class_exists( 'DPUS_Hosted_Plugin', false ) ) {

	/**
	 * A class wrapper for dealing with Post Series functionality.
	 */
	final class DPUS_Hosted_Plugin extends Post {

		/**
		 * The required abstract function for the expected post type.
		 * 
		 * @return string The post type.
		 */
		final public static function post_type() { return DPUS_Hosted_Plugin_Post_Type::post_type(); }

		/**
		 * Override the parent return type for typehinting.
		 * 
		 * @return \DPUS_Hosting_Options_Meta The hosting options Meta.
		 */
		final public function get_hosting_options_meta() {
			return $this->get_meta( DPUS_Hosting_Options_Meta::meta_key() );
		}

		/**
		 * Override the parent return type for typehinting.
		 * 
		 * @return \DPUS_Plugin_Headers_Meta The hosting options Meta.
		 */
		final public function get_plugin_headers_meta() {
			return $this->get_meta( DPUS_Plugin_Headers_Meta::meta_key() );
		}

		/**
		 * Override the parent return type for typehinting.
		 * 
		 * @return \DPUS_Plugin_Images_Meta The hosting options Meta.
		 */
		final public function get_plugin_images_meta() {
			return $this->get_meta( DPUS_Plugin_Images_Meta::meta_key() );
		}

		/**
		 * Override the parent return type for typehinting.
		 * 
		 * @return \DPUS_Plugins_Api_Meta The hosting options Meta.
		 */
		final public function get_plugins_api_meta() {
			return $this->get_meta( DPUS_Plugins_Api_Meta::meta_key() );
		}

		/**
		 * Membership Post Series constructor.
		 *
		 * @param int|string|\WP_Post $id Membership Plan slug, post object or related post ID
		 */
		public function __construct( $id ) {
			parent::__construct( $id );
			$this->add_meta( new DPUS_Hosting_Options_Meta( $this ) );

			if ( 'publish' === $this->Post->post_status ) {
				$this->add_meta( new DPUS_Plugin_Headers_Meta( $this ) );
				$this->add_meta( new DPUS_Plugin_Images_Meta( $this ) );
				$this->add_meta( new DPUS_Plugins_Api_Meta( $this ) );
			}
		}

		/**
		 * Find all WC_MPS_Post_Series post types Posts attached to a WC_Memberhip_Plan and return them in an array.
		 * 
		 * @param int $plan_id The ID of the Membership Plan to get Series for.
		 * 
		 * @return \WC_MPS_Post_Series[] The Series (plural).
		 */
		final public static function get_series_from_plan_id( int $plan_id ) {
			$series_ids = get_post_meta( $plan_id, '_post_series', true );
			$Series = array();

			if ( is_array( $series_ids ) ) {
				foreach ( $series_ids as $id ) {
					$Series[] = new WC_MPS_Post_Series( $id );
				}
			}

			return $Series;
		}

		/**
		 * Find all WC_MPS_Post_Series that have Posts with the given ID under them.
		 * 
		 * @param int $post_id The ID of the Post to get Series for.
		 * 
		 * @return \WC_MPS_Post_Series[] The Series (plural).
		 */
		final public static function get_series_with_post( int $post_id ) {
			global $post;
			$Query = new \WP_Query( array( 'post_type' => WC_MPS_Post_Series_Post_Type::post_type(), 'posts_per_page' => -1 ) );
			$Series = array();

			while ( $Query->have_posts() ) {
				$Query->the_post();
				$S = new self( $post );

				if ( $S->get_series_meta()->get_post( $post_id ) ) {
					array_push( $Series, $S );
				}
			}

			wp_reset_postdata();
			return $Series;
		}

	}

}
