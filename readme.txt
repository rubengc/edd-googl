=== EDD Googl ===
Contributors: rubengc
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=64N6CERD8LPZN
Tags: easy digital downloads, digital, download, downloads, edd, rubengc, googl, url, shortener, google, short, e-commerce
Requires at least: 4.0
Tested up to: 4.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically creates a Goo.gl shortened url on published downloads

== Description ==
This plugin requires [Easy Digital Downloads](http://wordpress.org/extend/plugins/easy-digital-downloads/ "Easy Digital Downloads") v2.3 or greater.

Once activated, EDD Googl will generate a Goo.gl URl when you saves a published download (include from Frontend Submission plugin). Also, has a function to update all shortened urls from EDD settings page.

For developers, you can use `edd_googl_shortlink( $download_id )` method to easily get a download shortlink.

There's a [GIT repository](https://github.com/rubengc/edd-googl) too if you want to contribute a patch.

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin
1. That's it!

OR you can just install it with WordPress by going to Plugins >> Add New >> and type this plugin's name

== Frequently Asked Questions ==

= How can I get a Google API Key?  =

1. Visit [Google developers console](https://console.developers.google.com)
1. Select an existing project or create a new one
1. In the left sidebar, navigate to API Administration menu
1. Navigate to Control Panel, search URL Shortener API and enable it
1. Navigate to credentials and create an API Key
1. That's all!

== Screenshots ==

== Upgrade Notice ==

== Changelog ==

= 1.0 =
* Initial release