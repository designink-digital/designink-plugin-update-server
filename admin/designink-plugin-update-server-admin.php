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

use Designink\WordPress\Framework\v1_0_1\Plugin\Admin_Module;

if ( ! class_exists( 'Designink_Plugin_Update_Server_Admin', false ) ) {

	/**
	 * The admin functionality for the DesignInk Plugin Update Server.
	 */
	final class Designink_Plugin_Update_Server_Admin extends Admin_Module {

		/**
		 * Plugin admin entry point, load GitHub cache.
		 */
		final public static function construct() {
			parent::construct();
			DPUS_Github_Api_Cache::load();
		}

	}

}
