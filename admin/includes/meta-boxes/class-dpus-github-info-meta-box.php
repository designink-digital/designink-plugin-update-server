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

use Designink\WordPress\Framework\v1_0_1\Plugin\Admin\Meta_Box;

if ( ! class_exists( 'DPUS_Github_Info_Meta_Box', false ) ) {

	/**
	 * Hosting options Meta Box for a Hosted Plugin.
	 */
	final class DPUS_Github_Info_Meta_Box extends Meta_Box {

		/** @var string The context to display this Meta Box in.  */
		public $context = 'side';

		/** @var array The arguments to pass to the template. */
		private $args;

		/**
		 * Pass in Github Plugin to template to prevent so many long API calls.
		 */
		public function __construct( array $args = array() ) {
			parent::__construct();
			$this->args = $args;
		}

		/**
		 * The required meta_key() abstract function.
		 * 
		 * @return string The meta key.
		 */
		final public static function meta_key() {
			return 'github_info';
		}

		/**
		 * The required get_title() abstract function.
		 * 
		 * @return string The title.
		 */
		final public static function get_title() {
			return 'GitHub Info';
		}

		/**
		 * The required get_id() abstract function.
		 * 
		 * @return string The ID.
		 */
		final public static function get_id() {
			return sprintf( '%s_%s', DPUS_Hosted_Plugin_Post_Type::post_type(), self::meta_key() );
		}

		/**
		 * The required render() abstract function.
		 */
		final protected static function render() {
			$Plugin = Designink_Plugin_Update_Server::instance();
			$Meta_Box = self::instance();
			$Plugin->get_admin_module()->get_template( 'github-info-meta-box', $Meta_Box->args );
		}

		/**
		 * The inherited abstract function to attach to the 'save_post' hook.
		 * 
		 * @param int $postId The Post ID.
		 * @param \WP_Post $post The Post object.
		 * 
		 * @return int The post ID.
		 */
		final protected static function save_post( int $post_id, \WP_Post $Post = null ) { }

	}

}
