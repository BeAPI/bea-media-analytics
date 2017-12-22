=== BEA - Find Media ===
Contributors: beapi, maximeculea
Donate link: http://paypal.me/BeAPI
Tags: find, media, usage, find media, multisite, single site,
Requires at least: 4.7
Requires php: 7.0
Tested up to: 4.9.1
Stable tag: trunk
License: GPLv3 or later
License URI: https://github.com/BeAPI/bea-find-media/blob/master/LICENSE.md

Find where and how medias are used across your site.

== Description ==

If you want to see which media is used and where? This plugin is for you!

By installing this plugin you will index where your medias are used, display further informations about where they are used and also warn you about deleting used ones.

A table is created for indexing where media are used. This is done when saving contents but could also by activating the plugin or be forced with the wp-cli command.  

## Features 

For now the supported contents for indexation are post types by focusing on :

- Post content ( gallery, image, links )
- Post thumbnail
- [Advanced Custom Fields](https://fr.wordpress.org/plugins/advanced-custom-fields/)

### Third party support

This plugin has third party support with following plugins :

* [Content Sync Fusion](https://github.com/BeAPI/bea-content-sync-fusion) : the usage counter will now consider synced sites to reveal how many times a media has been used across all synchronized sites.

## More features to come

As you can see, some [issues](https://github.com/BeAPI/bea-find-media/issues?q=is%3Aissue+is%3Aopen+label%3Aquestion) are feature requests :
- More fields support (excerpt, post meta, etc)
- More type of support (widget, etc)
- More support (elementor, visual composer, polylang, -wpml-, etc)
- Media expiration
- Find unused media
- Media replacement
- More file's mime types

## Next Roadmap
- [39](https://github.com/BeAPI/bea-find-media/issues/39) : Improve display
- [33](https://github.com/BeAPI/bea-find-media/issues/33) : Ensure a lot of file's mime types support

= Who ?

Created by [Be API](https://beapi.fr), the French WordPress leader agency since 2009. Based in Paris, we are more than 30 people and always [hiring](https://beapi.workable.com) some fun and talented guys. So we will be pleased to work with you.

This plugin is only maintained by [Maxime CULEA](https://maximeculea.fr) and the [Be API team](https://beapi.fr), which means we do not guarantee some free support. Consider reporting an [issue](https://github.com/BeAPI/bea-find-media/issues) and be patient.

== Installation ==

At plugin activation, a single event will be added in order to index your contents. It generally takes 10-15 min to do so.

At plugin deactivation, all blog's data are deleted.

= Requirements =
- WordPress 4.7+
- Tested up to 4.9.1.
- PHP 7.0 is required !!

= WordPress =
- Download and install using the built-in WordPress plugin installer.
- Site activate in the "Plugins" area of the admin.
- Nothing more, this plugin is ready to use !

= Composer =
- Include `"wpackagist-plugin/bea-find-media": "dev-trunk"`
- Nothing more, this plugin is ready to use !

== Frequently Asked Questions ==

= Can I use BEA - Find Media into a single site ? =

Yes.

This plugin simply plug on the media's admin area and index all the supported contents for further info display. 

= Can I use BEA - Find Media into a multisite ? =

Yes.

For the indexation of all the supported contents, the blog id is used. So on the media's admin area of each site, the data is not mixed between sites.

== Screenshots ==

1. In the single edit view, a bloc has been added to display the number of usages, where and what type.
2. In the media library modal view, when a media is selected, a bloc has been added to display the number of usages.
3. On the media admin library view, an admin column has been added to display the number of usages.
4. On media delete, if it has usages (based on indexed contents), a warning will prompt to confirm the media deletion.

== Changelog ==

= 1.0.1 - 12 Dec 2017 =
- On activation, handle force indexation.
- Update languages with CSF strings
- Improve readme and add project templates.
- Add Content Sync Fusion support.

= 1.0.0 - 7 Dec 2017 =
- First version of the plugin.
- Add screenshots.
- Add usages to REST Api and WP Json.
- Add wp-cli.
- Add plugin's .pot.
- Add French translation (po/mo).
- Add composer.json !
- Display usages to media archive / single.
- Prompt warnings for media delete.
- Create plugin with table and indexation.
- Init plugin.
