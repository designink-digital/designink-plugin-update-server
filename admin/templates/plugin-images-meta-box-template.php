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

<div class="di-hosted-plugin-plugin-images">
	<div class="icons">
		<h2 class="subsection-header">Icons</h2>

		<table>
			<thead>
				<th>Small Icon</th>
				<th>Large Icon</th>
			</thead>

			<tbody>
				<!-- Icon Images -->
				<tr>
					<td class="half">
						<!-- Small -->
						<img
							class="demo-image"
							id="di_hosted_plugin_images_icons_1x_image"
							src=""
						/>
						<input
							id="di_hosted_plugin_images_icons_1x_input"
							name="di_hosted_plugin[plugin_images][icons][1x]"
							type="hidden"
							value=""
						/>
					</td>

					<td class="half">
						<!-- Large -->
						<img
							class="demo-image"
							id="di_hosted_plugin_images_icons_2x_image"
							src=""
						/>
						<input
							id="di_hosted_plugin_images_icons_2x_input"
							name="di_hosted_plugin[plugin_images][icons][2x]"
							type="hidden"
							value=""
						/>
					</td>
				</tr>

				<!-- Icon Buttons -->
				<tr>
					<td class="half">
						<!-- Small -->
						<input
							class="button button-primary"
							data-type="icons" data-size="1x"
							id="di_hosted_plugin_images_icons_1x_select_button"
							type="button"
							value="Add"
						/>

						<input
							class="button button-delete hidden"
							data-type="icons" data-size="1x"
							id="di_hosted_plugin_images_icons_1x_remove_button"
							type="button"
							value="Remove"
						/>
					</td>

					<td class="half">
						<!-- Large -->
						<input
							class="button button-primary"
							data-type="icons" data-size="2x"
							id="di_hosted_plugin_images_icons_2x_select_button"
							type="button"
							value="Add"
						/>

						<input
							class="button button-delete hidden"
							data-type="icons" data-size="2x"
							id="di_hosted_plugin_images_icons_2x_remove_button"
							type="button"
							value="Remove"
						/>
					</td>
				</tr>

				<!-- Icon Recommended Sizes -->
				<tr>
					<th colspan="2"><sub>Recommended sizes:</sub></th>
				</tr>

				<tr>
					<td class="half">
						<!-- Small -->
						<sup>128px x 128px</sup>
					</td>

					<td class="half">
						<!-- Large -->
						<sup>256px x 256px</sup>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="banners">
		<h2 class="subsection-header">Banners</h2>

		<table>

			<!-- Small Banner -->

			<tr>
				<th>Small Banner</th>
			</tr>

			<tr>
				<td>
					<!-- Image and input -->
					<img
						class="demo-image"
						id="di_hosted_plugin_images_banners_1x_image"
						src=""
					/>
					<input
						id="di_hosted_plugin_images_banners_1x_input"
						name="di_hosted_plugin[plugin_images][banners][1x]"
						type="hidden"
						value=""
					/>
				</td>
			</tr>

			<tr>
				<td>
					<!-- Add and remove buttons -->
					<input
						class="button button-primary"
						data-type="banners" data-size="1x"
						id="di_hosted_plugin_images_banners_1x_select_button"
						type="button"
						value="Add"
					/>

					<input
						class="button button-delete hidden"
						data-type="banners" data-size="1x"
						id="di_hosted_plugin_images_banners_1x_remove_button"
						type="button"
						value="Remove"
					/>
				</td>
			</tr>

			<tr>
				<td>
					<!-- Recommended size -->
					<sup>Recommended size: 772px x 250px</sup>
				</td>
			</tr>

			<!-- Large Banner -->

			<tr>
				<th>Large Banner</th>
			</tr>

			<tr>
				<td>
					<!-- Image and input -->
					<img
						class="demo-image"
						id="di_hosted_plugin_images_banners_2x_image"
						src=""
					/>
					<input
						id="di_hosted_plugin_images_banners_2x_input"
						name="di_hosted_plugin[plugin_images][banners][2x]"
						type="hidden"
						value=""
					/>
				</td>
			</tr>

			<tr>
				<!-- Add and remove buttons -->
				<td>
					<input
						class="button button-primary"
						data-type="banners" data-size="2x"
						id="di_hosted_plugin_images_banners_2x_select_button"
						type="button"
						value="Add"
					/>

					<input
						class="button button-delete hidden"
						data-type="banners" data-size="2x"
						id="di_hosted_plugin_images_banners_2x_remove_button"
						type="button"
						value="Remove"
					/>
				</td>
			</tr>

			<tr>
				<td>
					<!-- Recommended size -->
					<sup>Recommended size: 1544px x 500px</sup>
				</td>
			</tr>
		</table>
	</div>
</div>
