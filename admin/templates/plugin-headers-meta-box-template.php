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

use Designink\WordPress\Framework\v1_0_1\Utility;

global $post;

$Github_Plugin = new DPUS_Github_Plugin( new DPUS_Hosted_Plugin( $post ) );
$Headers_Meta = $Github_Plugin->Plugin->get_plugin_headers_meta();

$plugin_headers = Utility::guided_array_merge(
	array(
		'Author' => '',
		'Name' => '',
		'AuthorURI' => '',
		'PluginURI' => '',
		'RequiresWP' => '',
		'TestedWP' => '',
		'RequiresPHP' => '',
	),
	$Github_Plugin->Github_Api->get_plugin_headers()
);

$post_type = $post->post_type;

?>

<div class="di-hosted-plugin-plugin-headers">
	<div style="padding: 0.5rem 0.25rem;">
		<label for="<?php echo $post_type; ?>_plugin_headers_override_headers" style="margin-right: 0.5rem;">
			Override Plugin Headers?
		</label>

		<input
			id="<?php echo $post_type; ?>_plugin_headers_override_headers"
			name="<?php echo $post_type; ?>[plugin_headers][override_headers]"
			type="checkbox"
			value="override"
			<?php echo 'override' === $Headers_Meta->override_headers ? 'checked' : '' ; ?>
		/>
	</div>

	<table>
		<thead>
			<tr>
				<th></th>
				<th>Override Value</th>
				<th>Plugin Headers Value</th>
			</tr>
		</thead>

		<tbody>
			<tr>
				<th scope="row">Plugin Name</th>
				<td></td>
				<td><?php echo $plugin_headers['Name']; ?></td>
			</tr>

			<tr>
				<th scope="row"><label for="<?php echo $post_type; ?>_plugin_headers_author">Plugin Author</label></th>
				<td>
					<input
						id="<?php echo $post_type; ?>_plugin_headers_author"
						name="<?php echo $post_type; ?>[plugin_headers][author]"
						type="text"
						value="<?php echo $Headers_Meta->author; ?>"
					/>
				</td>
				<td><?php echo $plugin_headers['Author']; ?></td>
			</tr>

			<tr>
				<th scope="row"><label for="<?php echo $post_type; ?>_plugin_headers_author_profile">Author Profile</label></th>
				<td>
					<input
						id="<?php echo $post_type; ?>_plugin_headers_author_profile"
						name="<?php echo $post_type; ?>[plugin_headers][author_profile]"
						type="text"
						value="<?php echo $Headers_Meta->author_profile; ?>"
					/>
				</td>
				<td>
					<a href="<?php echo $plugin_headers['AuthorURI']; ?>" target="_blank"><?php echo $plugin_headers['AuthorURI']; ?></a>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="<?php echo $post_type; ?>_plugin_headers_homepage">Plugin Homepage</label></th>
				<td>
					<input
						id="<?php echo $post_type; ?>_plugin_headers_homepage"
						name="<?php echo $post_type; ?>[plugin_headers][homepage]"
						type="text"
						value="<?php echo $Headers_Meta->homepage; ?>"
					/>
				</td>
				<td>
					<a href="<?php echo $plugin_headers['PluginURI']; ?>" target="_blank"><?php echo $plugin_headers['PluginURI']; ?></a>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="<?php echo $post_type; ?>_plugin_headers_requires">Requires WordPress Version</label></th>
				<td>
					<input
						id="<?php echo $post_type; ?>_plugin_headers_requires"
						name="<?php echo $post_type; ?>[plugin_headers][requires]"
						type="text"
						value="<?php echo $Headers_Meta->requires; ?>"
					/>
				</td>
				<td><?php echo $plugin_headers['RequiresWP']; ?></td>
			</tr>

			<tr>
				<th scope="row"><label for="<?php echo $post_type; ?>_plugin_headers_tested">Tested up to WordPress</label></th>
				<td>
					<input
						id="<?php echo $post_type; ?>_plugin_headers_tested"
						name="<?php echo $post_type; ?>[plugin_headers][tested]"
						type="text"
						value="<?php echo $Headers_Meta->tested; ?>"
					/>
				</td>
				<td><?php echo $plugin_headers['TestedWP']; ?></td>
			</tr>

			<tr>
				<th scope="row"><label for="<?php echo $post_type; ?>_plugin_headers_requires_php">Requires PHP Version</label></th>
				<td>
					<input
						id="<?php echo $post_type; ?>_plugin_headers_requires_php"
						name="<?php echo $post_type; ?>[plugin_headers][requires_php]"
						type="text"
						value="<?php echo $Headers_Meta->requires_php; ?>"
					/>
				</td>
				<td><?php echo $plugin_headers['RequiresPHP']; ?></td>
			</tr>
		</tbody>
	</table>
</div>
