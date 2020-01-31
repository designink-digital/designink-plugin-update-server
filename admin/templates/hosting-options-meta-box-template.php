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
$Hosting_Meta = $Github_Plugin->Plugin->get_hosting_options_meta();
$post_type = $post->post_type;

?>

<div class="di-hosted-plugin-hosting-options">
	<div>
		<table>
			<tr>
				<th scope="row"><label for="<?php echo $post_type; ?>_slug">Plugin Slug</label></th>
				<td>
					<input
						id="<?php echo $post_type; ?>_slug"
						name="<?php echo $post_type; ?>[hosting_options][slug]"
						type="text"
						value="<?php echo $Hosting_Meta->slug; ?>"
					/>
				</td>
			</tr>
		</table>
	</div>

	<hr />

	<div>
		<table>
			<tr>
				<th scope="row"><label for="<?php echo $post_type; ?>_github_user">GitHub User</label></th>
				<td>
					<input
						id="<?php echo $post_type; ?>_github_user"
						name="<?php echo $post_type; ?>[hosting_options][github_user]"
						type="text"
						value="<?php echo $Hosting_Meta->github_user; ?>"
					/>
				</td>
			</tr>

			<tr>
				<th><label for="<?php echo $post_type; ?>_github_repo">GitHub Repository</label></th>
				<td>
					<input
						id="<?php echo $post_type; ?>_github_repo"
						name="<?php echo $post_type; ?>[hosting_options][github_repo]"
						type="text"
						value="<?php echo $Hosting_Meta->github_repo; ?>"
					/>
				</td>
			</tr>

			<tr>
				<th><label for="<?php echo $post_type; ?>_github_token">GitHub Repository Token</label></th>
				<td>
					<input
						id="<?php echo $post_type; ?>_github_token"
						name="<?php echo $post_type; ?>[hosting_options][github_token]"
						type="text"
						value="<?php echo $Hosting_Meta->github_token; ?>"
					/>
				</td>
			</tr>
		</table>
	</div>

	<div class="status">

		<?php if ( $Github_Plugin->is_valid() ) : $latest_tag = $Github_Plugin->Github_Api->get_latest_tag(); ?>

			<b class="connected">Connected!</b>
			<span class="version-info">
				<?php printf( 'Current version: %s committed on %s', $latest_tag->git_tag->tag, date( 'm-d-Y H:i:s', strtotime( $latest_tag->git_tag->tagger->date ) ) ); ?>
			</span>

		<?php else : ?>

			<b class="disconnected">Disconnected</b>

		<?php endif; ?>

	</div>
</div>
