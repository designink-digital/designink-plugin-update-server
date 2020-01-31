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

?>

<div class="tag">
	<div class="tag-name">
		<?php echo $tag->git_tag->tag; ?>
	</div>

	<div class="commit-date">
		Tagged on <?php echo date( 'M j, Y H:i:s', strtotime( $tag->git_tag->tagger->date ) ); ?>.
	</div>

	<div class="tag-message">
		<div><strong>Tag message:</strong></div>
		<div><?php echo $tag->git_tag->message; ?></div>
	</div>

	<div class="commit-message">
		<div><strong>Commit message:</strong></div>
		<div><?php echo $tag->commit->commit->message; ?></div>
	</div>

	<div class="author">
		<div><strong>Tagged by:</strong></div>
		<div>
			<a href="<?php echo $tag->commit->author->html_url; ?>" target="_blank">
				<img class="profile-image" src="<?php echo $tag->commit->author->avatar_url; ?>" />
				<?php echo $tag->commit->commit->author->name; ?>
			</a>
		</div>
	</div>

	<div class="total-changes">
		<?php echo $tag->commit->stats->total; ?> total changes
	</div>
</div>
