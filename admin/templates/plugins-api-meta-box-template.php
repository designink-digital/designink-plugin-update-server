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

global $post;

$Github_Plugin = new DPUS_Github_Plugin( new DPUS_Hosted_Plugin( $post ) );
$Plugins_Api_Meta = $Github_Plugin->Plugin->get_plugins_api_meta();

$post_type = $post->post_type;

?>

<div class="di-hosted-plugin-plugins-api-meta-box">
	<div class="general">
		<h2 class="subsection-header">General</h2>

		<table>
			<tr>
				<th scope="row">Donate Link</th>

				<td>
					<input
						id="<?php echo $post_type; ?>_plugins_api_donate_link"
						name="<?php echo $post_type; ?>[plugins_api][donate_link]"
						type="text"
						value="<?php echo $Plugins_Api_Meta->donate_link; ?>"
					/>
				</td>
			</tr>
		</table>
	</div>

	<div class="contributors">
		<!-- <h2>Contributors</h2> -->
	</div>

	<div class="sections">
		<h2 class="subsection-header">Sections</h2>

		<div id="di-hosted-plugin-sections"></div>

		<div class="add-section">
			<input
				class="button button-primary"
				id="di-hosted-plugin-sections-add-section"
				type="button"
				value="Add Section"
			/>
		</div>
	</div>

	<div class="ratings">
		<!-- <h2>Ratings</h2> -->
		<!-- Eventually encompass fields: rating, ratings, num_ratings, support_threads, support_threads_resolved, active_installs -->
	</div>
</div>
