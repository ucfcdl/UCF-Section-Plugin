=== UCF Sections ===
Contributors: ucfwebcom
Tags: ucf, sections
Requires at least: 4.7.5
Tested up to: 4.7.5
Stable Tag: 1.0.3
License: GPLv3 or later
License URI: http://www.gnu.org/copyleft/gpl-3.0.html

Provides a shortcode, functions, and default styles for displaying Sections.


== Description ==

Adds a new post type called Sections that can be added to pages using a ucf-section shortcode. Sections will display content from within the Wordpress editor.

The ucf-section shortcode has one option:
* slug - the slug of the section to be displayed


== Installation ==

= Manual Installation =
1. Upload the plugin files (unzipped) to the `/wp-content/plugins` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the "Plugins" screen in WordPress

= WP CLI Installation =
1. `$ wp plugin install --activate https://github.com/UCF/UCF-Section-Plugin/archive/master.zip`.  See [WP-CLI Docs](http://wp-cli.org/commands/plugin/install/) for more command options.



== Changelog ==

= 1.0.4 =
Enhancements:
* Added stylesheet and javascript field for sections
* Added mechanism for enqueuing the stylesheets to section specific tags in the head
* Added mechanism for enqueuing the javascripts to section specific tags in the footer

= 1.0.3 =
Enhancements:
* Added the ability to add css classes to the section wrapper.
* Added the ability to define the id of the section wrapper.
* Added the ability to add a title to the section wrapper (which can be used in templates to define the menu text).

= 1.0.2 =
Bug Fixes:
* Corrects incorrect sections being returned when providing a slug instead of an id.

= 1.0.1 =
Bug Fixes:
* Fixes [#2](https://github.com/UCF/UCF-Section-Plugin/issues/2) by parsing `$labels` args and setting them to variables.
* Fixes [#1](https://github.com/UCF/UCF-Section-Plugin/issues/1) by renaming the non-existent `$output` variable to `$before`, `$content` and `$after` respectively.
* Adds additional comments.

= 1.0.0 =
* Initial release


== Upgrade Notice ==

n/a


== Installation Requirements ==

None


== Wishlist/TODOs ==
* None
