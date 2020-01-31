<?php
/**
 * Plugin Name: DesignInk Plugin Update Server
 * Plugin ID: di-update/plugin/designink-plugin-update-server
 * Plugin URI: https://designinkdigital.com/
 * Description: A proprietary WordPress solution provided by DesignInk Digital to manage your own, personal plugin repository, integrated with GitHub.
 * Version: 1.5.0
 * Author: DesignInk Digital
 * Author URI: https://designinkdigital.com/
 * Text Domain: wporg
 * Domain Path: /languages
 * 
 * Copyright: (c) 2008-2020, DesignInk, LLC (answers@designinkdigital.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @author    DesignInk Digital
 * @copyright Copyright (c) 2008-2020, DesignInk, LLC
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 * 
 */

defined( 'ABSPATH' ) or exit;

use Designink\WordPress\Framework\v1_0_1\Plugin;

// Include DesignInk's framework
require_once __DIR__ . '/vendor/designink/designink-wp-framework/index.php';

// Include the plugin update helper
require_once __DIR__ . '/vendor/designink/plugin-update-helper/index.php';

if ( ! class_exists( 'Designink_Plugin_Update_Server', false ) ) {

	/**
	 * The plugin wrapper class.
	 */
	final class Designink_Plugin_Update_Server extends Plugin { }

	// Start it up.
	Designink_Plugin_Update_Server::instance();
}
