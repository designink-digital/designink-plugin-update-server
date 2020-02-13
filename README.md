# DesignInk Plugin Update Server

This project is a WordPress plugin, which provides a way to distribute custom plugins via a GitHub repository (even a private one). This plugin provides a new custom post type registered in the plugins menu of the Admin Dashboard which you can use to store information about the GitHub repositories. It utilizes the GitHub API to check for tagged versions and provides that information to outside plugins checking for updates. Each hosted plugin can have banners/icons and send custom header and plugins_api information.

## Installation

As this project is a WordPress plugin, you simply need a plugin ZIP file to upload in your Admin Dashboard. Being as GitHub appends the folder name with "-master" (for the branch), if you upload the ZIP file to your WordPress install directly from GitHub, it will install the plugin with the incorrect folder name, which will interfere with the plugin servers own updating process. To remedy this, you will need to:

1. Download the ZIP from GitHub
2. Extract the folder inside of the ZIP file
3. Rename the folder without the "-master" suffix
4. Re-zip the correctly named folder

After which, the ZIP file will install correctly. You could also install the ZIP file from GitHub, then rename the folder manually through FTP, but remember this will deactivate the plugin if you have activated it already.

## Usage

Once you have the plugin installed, it is very simple to use. Log into your WordPress Admin dashboard and navigate to "Plugin > Hosted Plugins". All you have to do is provide a GitHub user and repo, and then a separate field for a "plugin slug". The plugin slug is based on the underlying operating principal of the release system that the plugin ID will always have the form "plugin-slug/plugin-slug.php". If you are unfamiliar with the identification of plugins by WordPress, the pretty much means that the folder name of the plugin (in "/wp-content/plugins") should be the same as the primary PHP file inside of it. This was decided early on in the project development to maintain uniformity. For all intents and purposes, the GitHub repository should ideally be the same as the slug. Read the DesignInk [Plugin Update Helper](https://github.com/designink-digital/plugin-update-helper) page for more info on the slug. Any new hosted plugin will display "Disconnected" until the correct GitHub information is provided and the post is saved, after which the page will say "Connected" and display information about the plugin repository.

## Concerns

I understand that security is a large concern in todays digital world. If you provide the update server a GitHub API token to see a private repository, it is in your best interest to keep that token most secure. The update server is built with the DesignInk [Plugin Update Helper](https://github.com/designink-digital/plugin-update-helper), which registers an SSL encryption key field under "Settings > DesignInk Settings". Your private token is always encrypted before being sent, so make sure to provide a strong SSL key. However, this does not terminate the list of possible issues. Your WordPress installation is likely made up of some 3rd-party plugins that facilitate the things that you need to do with your website. Your SSL key is stored in the wp_options table, as it is a setting, and malicous code could very easily gain access to it. Keeping your plugins updated and using trusted plugins are some of the best ways to keep other parties from accessing your data. Thirdly, all data being sent from your website should be viewed as publicly visible. Even if you do have an SSL key and everything updated, mandate an HTTPS connection to and from your site. This should be the first thing you do for any site that you need to log into. To reiterate:

1. Use a strong SSL key in the DesignInk Settings.
2. Use only trusted plugins and keep them updated.
3. For the programming gods sakes, use SSL (HTTPS) for your site!

This plugin has NOT been thoroughly tested for security flaws and you are liable for your own usage of this plugin. If you find anything, we would highly appreciate an issue being created.
