=== WPMU Global Search ===
Contributors: aliciagh
Tags: search, wpmu, multilingual, global, widget, multisite
Requires at least: WordPress MU 2.6
Tested up to: WordPress MU 2.9
Stable tag: trunk 

Adds the ability to search through blogs into your WordPress MU installation.

== Description ==

Easily search through all blogs into your WordPress MU posts by post title, post content or post author.
WPMU Global Search can manage multiple search forms. Currently in the following languages:

* English
* Spanish (es_ES)

If you have created your own language pack, or have an update of an existing one, you can send [gettext .po and .mo files](http://codex.wordpress.org/Translating_WordPress) to me so that I can bundle it into WPMU Global Search.

== Installation ==

Installation is easy:

1. Upload `wpmu-global-search` folder to the `wp-content/mu-plugins` directory.
2. Move `/wpmu-global-search/wpmu-global-search-loader.php` to `wp-content/mu-plugins`.
3. Create, activate or archived a blog from your admin.
4. Create a new page in your blog with default global search uri: `globalsearch`.
5. Place `[page_wpmu_search]` in the post content area.
6. Activate widget `WPMU Global Search`.

== Frequently Asked Questions ==

If you have any further questions, please submit them.

= Does this plugin work with WordPress 3.0 Multisite? =

This plugin only works with WPMU but I'm working in a new plugin, [Multisite Global Search](http://wordpress.org/extend/plugins/multisite-global-search), compatible with WordPress 3.0 Multisite.
WPMU Global Search is incompatible with WP 3 because it uses some deprecated functions and database tables are diferent.

== Screenshots ==

1. Global Search widget.

== Changelog == 

= V2.1 - 25.08.2010 =
* Bugfix : post password required form
* Bugfix : translate file metadata
* Bugfix : SQL inyection
* Changed : style for search results
* Changed : order of the search results
* Added : apply filter to search var
* Added : number of results

= V2.0 - 23.08.2010 =
* Bugfix : search through site blog
* Bugfix : add unarchived and activate blogs to search views
* Bugfix : drop archived and deactivate blogs from search views
* Changed : style for results page
* Changed : add strings to translate file
* Changed : shortcode page format
* Changed : add search var to query vars
* Changed : code optimization
* Added : style for widget
* Added : images dir

= V1.1 - 16.04.2010 =
* Bugfix : permalinks for search results
* Changed : style for results page

