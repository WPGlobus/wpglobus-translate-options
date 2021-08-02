=== WPGlobus Translate Options ===
Contributors: alexgff, tivnetinc, tivnet
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SLF8M4YNZHNQN
Tags: WPGlobus, localization, multilingual, translate, translation
Requires at least: 5.5
Tested up to: 5.8
Stable tag: trunk
Requires PHP: 5.6
License: GPL-3.0-or-later
License URI: https://spdx.org/licenses/GPL-3.0-or-later.html

== Description ==

**WPGlobus Translate Options** is a free add-on to the [WPGlobus Multilingual WordPress Plugin](https://wordpress.org/plugins/wpglobus/). It enables selective translation of the texts residing in the `wp_options` database table.

You need to use WPGlobus Translate Options when the active theme or a 3rd party plugin (a slider, for example) has its own option panel, where you enter texts.

> **NOTE:** This plugin requires [WPGlobus](https://wordpress.org/plugins/wpglobus/). Please install and activate WPGlobus before using the `Translate Options` add-on.

= More info =

* [Multilingual Texts in WordPress Theme Options Panel](https://wpglobus.com/extensions-archive/multilingual-texts-in-wordpress-theme-options-panel/)
* [WPGlobus Translate Options @ WPGlobus.com](https://wpglobus.com/extensions-archive/extension-translate-options-archive/).
* [GitHub code repository](https://github.com/WPGlobus/wpglobus-translate-options).
* [Premium WPGlobus add-ons](https://wpglobus.com/shop/)

== Installation ==

You can install this plugin directly from your WordPress dashboard:
1. Go to the *Plugins* menu and click *Add New*.
1. Search for *WPGlobus*.
1. Click *Install Now* next to the *WPGlobus Translate Options* plugin.
1. Activate the plugin.

Alternatively, see the guide to [Manually Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

== Frequently Asked Questions ==

= How do I contribute to WPGlobus Translate Options? =

We appreciate all contributions, ideas, critique, and help.

* To speed up our development, please report bugs, with reproduction steps on [WPGlobus Translate Options GitHub](https://github.com/WPGlobus/wpglobus-translate-options).
* Plugin and theme authors: please try WPGlobus Translate Options and let us know if you find any compatibility problems.
* Contact us directly on [WPGlobus.com](https://wpglobus.com/contact-us/).

= More info? =

Please check out the [WPGlobus Website](https://wpglobus.com/extensions-archive/extension-translate-options-archive/) for additional information.

== Screenshots ==

1. Menu item.
2. WPGlobus Translate Options interface.
3. Interface to add option for translation.

== Changelog ==

= 1.9.0 =
* Revised code.

= 1.8.0 =
* Plugin tab moved upper on WPGlobus Options page.

= 1.7.0 =
* Using `extract_text` instead of `text_filter` function to get text from multilingual string.

= 1.6.0 =
* Tested up to WP 5.3.
* Small improvements.

= 1.5.8 =
* Tested up to WP 5.2.
* Renamed `Theme options` tab to `Theme properties`.

= 1.5.7 =
* readme.txt - better texts and additional links.
* Fixed: Too many tags (new WP rules).
* License GPL-3.0.

= 1.5.6 =
* [core] added support Cookie Notice by dFactory (https://wordpress.org/plugins/cookie-notice/)

= 1.5.5 =
* [core] fixed "can't use method return value in write context" on Theme options tab

= 1.5.4 =
* [core] Fixed "can't use method return value in write context".

= 1.5.3 =
* Tested up to WP 4.9.

= 1.5.2 =
* [core] Added theme's info to 'All options' page.

= 1.5.1 =
* [core] Correct path to load theme info page.

= 1.5.0 =
* [core] Added Theme page on options screen.

= 1.4.6 =
* [core] Exclude from translation the objects.

= 1.4.5 =
* Revised 'option_' filter.

= 1.4.4 =
* Revised code.

= 1.4.3 =
* Added Translate Options panel to WPGlobus admin central page.

= 1.4.2 =
* Removed unused globals.

= 1.4.1 =
* Added a link to the settings page to the plugins list.

= 1.4.0 =
* Added section to Customizer

= 1.3.1 =
* Fixed jumping "options to translate" block
* Better English texts
* Added 3 masks into exclude list

= 1.3.0 =
* Added search in options

= 1.2.3 =
* Removed 'theme_mods_' from disabled masks

= 1.2.2 =
* Fixed Redux fields renamed as wpglobus_...

= 1.2.1 =
* Fixed warning Empty needle

= 1.2.0 =
* Added ability to see source of option

= 1.1.0 =
* Some interface improvements
* Working correctly with string type options

= 1.0.1 =
* Some css updates

= 1.0.0 =
* Initial release
