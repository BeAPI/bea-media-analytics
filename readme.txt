=== BEA - Media Analytics ===
Contributors: beapi, maximeculea, momo360modena
Donate link: http://paypal.me/BeAPI
Tags: find, media, usage, find media, analytics
Requires at least: 4.6
Requires php: 5.6
Tested up to: 4.9.3
Stable tag: 2.1.2
License: GPLv3 or later
License URI: https://github.com/BeAPI/bea-media-analytics/blob/master/LICENSE.md

Find where and how media are used across your site.

== Description ==

If you want to see which media is used and where? This plugin is for you!

By installing this plugin you will index where your media are used, display further informations about how they are used and also warn you about deleting used ones.
Therefore, you will easily delete unused medias.

A table is created for indexing where media are used. This is done when saving contents but could also by activating the plugin or be forced with the wp-cli commands.

## Features

For now the supported contents for indexation are post types by focusing on :
<ul>
  <li>Post content ( gallery, image, links )</li>
  <li>Post thumbnail</li>
  <li><a href="https://www.advancedcustomfields.com/">Advanced Custom Fields PRO</a>'s fields :
      <ul>
        <li>"content fields" :
          <ul>
            <li>File</li>
            <li>Gallery</li>
            <li>Image</li>
            <li>Post object</li>
            <li>Page link</li>
            <li>Relationship</li>
            <li>Textarea</li>
            <li>Wysiwyg</li>
          </ul>
        </li>
        <li>"structuring fields" which can reuse the above "content fields"
          <ul>
            <li>Clone</li>
            <li>Flexible</li>
            <li>Group</li>
            <li>Repeater</li>
            <li><a href="https://acf-component-field.gummi.io/">Component</a></li>
          </ul>
        </li>
        <li>ACF Options</li>
      </ul>
  </li>
  <li><a href="https://codecanyon.net/item/image-map-pro-for-wordpress-interactive-image-map-builder/2826664">Image Map Pro</a></li>
</ul>

### Third party support

This plugin has third party support with following plugins :

* [Content Sync Fusion](https://github.com/BeAPI/bea-content-sync-fusion) : the usage counter will now consider synced sites to reveal how many times a media has been used across all synchronized sites.

## More features to come

As you can see, some [issues](https://github.com/BeAPI/bea-media-analytics/issues?q=is%3Aissue+is%3Aopen+label%3Aquestion) are feature requests :
- More fields support (excerpt, post meta, etc)
- More type of support (widget, etc)
- More support (elementor, visual composer, polylang, -wpml-, etc)
- Media expiration
- Media replacement
- More file's mime types

## Next Roadmap
- [39](https://github.com/BeAPI/bea-media-analytics/issues/39) : Improve display
- [33](https://github.com/BeAPI/bea-media-analytics/issues/33) : Ensure a lot of file's mime types support

## Who ?

Created by [Be API](https://beapi.fr), the French WordPress leader agency since 2009. Based in Paris, we are more than 30 people and always [hiring](https://beapi.workable.com) some fun and talented guys. So we will be pleased to work with you.

This plugin is only maintained, which means we do not guarantee some free support. Consider reporting an [issue](https://github.com/BeAPI/bea-media-analytics/issues) and be patient.

To facilitate the process of submitting an issue and quicker answer, we only use Github, so don't use WP.Org support, it will not be considered. 

== Installation ==

At plugin activation, a single event will be added in order to index your contents. It generally takes 10-15 min to do so.

At plugin deactivation, all data stored by this plugin will be **deleted** to ensure not to weighing the DB and also for further safe reactivation.

= Requirements =
- WordPress 4.6+
- Tested up to 4.9.3.
- PHP 5.6+

= WordPress =
- Download and install using the built-in WordPress plugin installer.
- Site activate in the "Plugins" area of the admin.
- Nothing more, this plugin is ready to use !

== Frequently Asked Questions ==

= Can I use BEA - Media Analytics into a single site ? =

Yes.

This plugin simply plug on the media's admin area and index all the supported contents for further info display. 

= Can I use BEA - Media Analytics into a multisite ? =

Yes.

For the indexation of all the supported contents, the blog id is used. So on the media's admin area of each site, the data is not mixed between sites.

== Screenshots ==

1. In the single edit view, a bloc has been added to display the number of usages, where and what type.
2. In the media library modal view, when a media is selected, a bloc has been added to display the number of usages.
3. On the media admin library view, an admin column has been added to display the number of usages.
4. On media delete, if it has usages (based on indexed contents), a warning will prompt to confirm the media deletion.

== Changelog ==

= 2.1.1 - 05 Apr 2018 =
- Fix admin notice display on plugin activation.

= 2.1.0 - 04 Apr 2018 =
- Handle Group, Clone and Repeater ACF fields.
- Add ACF's Textarea and Wysiwyg support.
- Add WP CLI command to list and delete unused media.
- Finally lower PHP requirements to 5.6.
- Fix wp-cli command registering.
- Improve speed of wp-cli.
- Add wp-cli progress bar for indexation command.
- Add Component Field support.
- Add Image Map Pro support.
- Add ACF Options support.
- Add admin notices on plugin activation.
- Add admin notices on plugin update.
- Force reindexation with cron on plugin update.

= 2.0.2 - 04 Feb 2018 =
- Go to WP.Org

= 2.0.3 - 02 Feb 2018 =
- Rename plugin from "BEA - Find Media" into "BEA - Media Analytics"
- Improve readme
- English screenshots

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
