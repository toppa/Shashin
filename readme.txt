=== Shashin ===
Contributors: toppa
Donate link: http://www.toppa.com/shashin-wordpress-plugin
Tags: Picasa, Highslide, Twitpic, Youtube, image, images, photo, photos, picture, pictures, gallery, widget, widgets, video
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 3.0.9

Shashin is a powerful WordPress plugin that lets you easily display photos and videos from Picasa, Twitpic, and Youtube in your WordPress site.

== Description ==

**Installation of [Toppa Plugin Libraries for WordPress](http://wordpress.org/extend/plugins/toppa-plugin-libraries-for-wordpress/) is required. Please download and activate it before installing Shashin.**

**What's new in Shashin 3**

* Support for Youtube and Twitpic. You can now mix and match photos and videos from Picasa, Youtube, and Twitpic
* New, easier to read shortcode format (the old format is still supported)
* Fixed issues many were experiencing with album synchronizing
* Cool jQuery based display of album photos and photo paging
* Easier to use widget
* Display photo and album thumbnails in any size
* Includes the latest version of Highslide for displaying photos

**What's removed in Shashin 3**

* Support for private Picasa albums. There are simply too many variations in web host PHP configurations and security settings for me to continue supporting this feature
* The [salbumlist] tag is deprecated - it will now render as a regular album thumbnail display (I intend to include a proper list layout in a future version)
* The "c" option for captions is deprecated (it is now treated as an "n")
* The Shashin 2 PHP functions you could call directly have been removed, and replace with a new one
* The previous widgets have been replaced with a new one

**Overview**

Shashin has many features for displaying photos and videos from Picasa, Youtube, and Twitpic in your Wordpress posts and pages:

* Show a gallery of all your albums, photos, and videos, with multiple options for organizing them
* Use a jQuery based WYSIWYG media browser for easily adding photos, videos, and albums to your posts
* Show your photos and vidoes with your favorite image viewer. Highslide is included with Shashin, but you can use a different viewer of your choice
* Pick individual photos or videos to display, in any size, including captions and EXIF data
* Pick photos and videos from any combination of Picasa, Youtube, and Twitpic to display in groups of thumbnails
* Show thumbnails of your newest photos and videos, from one or more albums
* Display album thumbnails for albums you choose, or all your albums, sorted however you like. Includes links to Google Maps
* Display any number of random photos and videos. You can also choose to exclude certain photos or albums from random display
* Use a widget to display Shashin photos in your sidebar
* Customize the Shashin and Highslide stylesheets to suit the theme of your site.
* Internationalization: Shashin supports translations into other languages (please contribute a translation if you're bilingual!)
* Schedule daily automatic synchronization of Shashin with your feeds from Picasa, Twitpic, and Youtube.

== Installation ==

**Requirements**

* Wordpress 3.0 or higher
* PHP 5.1.2 or higher
* mySQL 4.1 or higher

**First time installation**

1. Download and activate [Toppa Plugin Libraries for WordPress](http://wordpress.org/extend/plugins/toppa-plugin-libraries-for-wordpress/), which contains required libraries.
1. Then download and activate Shahsin just like any other plugin
1. Go to the Shashin Tools Menu to add your Picasa, Youtube, or Twitpic feeds (expand the "Examples" section for more details)
1. Go to the Shashin Settings Menu to review and update your preferences
  * If you use an image viewer other than Highslide, you need to set it up and configure it yourself. The Shashin Settings Menu provides several options to support the use of other image viewers.
1. Go to the WordPress widget menu to add the Shashin widget to your sidebar if you want
1. Edit a post or page and use the Shashin media browser to add photos and albums
1. Note that Shashin will add two tables to your WordPress database, named wp\_shashin\_album and wp\_shashin\_photo. **It's important to include these tables when making backups of your WordPress tables.** The Shashin shortcodes rely on ID numbers from these tables that will be permanently lost if anything happens to these tables.

**Upgrading from Shashin 2**

1. Download and activate [Toppa Plugin Libraries for WordPress](http://wordpress.org/extend/plugins/toppa-plugin-libraries-for-wordpress/), which contains required libraries.
1. For Shashin, do the standard download, de-activation, and re-activation steps for upgrading a plugin.
1. Go to the Shashin Tools Menu and click "sync all" to complete the upgrade
1. Go to the Shashin Settings Menu and select the option to support the old shortcode format (or you can revise your shortcodes to the new format)
1. Carefully review pages and posts that contain Shashin tags to make sure everything looks correct
1. If all is well, click the link on the Shashin Tools Menu to remove the Shashin 2 database table backups
1. Go to the WordPress widget menu to add the Shashin widget to your sidebar if you want (the old widgets are gone)
1. If you were calling Shashin functions directly in PHP, they are no longer supported. There is a new function you can call. See the documentation page linked from the FAQ section.

== Upgrade Notice ==

You need to install "Toppa Plugin Libraries for WordPress" before upgrading to Shashin 3. Please follow the upgrade instructions in the "Installation" section of this readme file.

== Frequently Asked Questions ==

Please go to [the Shashin page on my site](http://www.toppa.com/shashin-wordpress-plugin) for a Usage Guide and other information.

For troubleshooting help, please [post a comment on my latest Shashin post](http://www.toppa.com/category/wordpress-plugins/).

== Screenshots ==

1. Album thumbnails
2. Photos in a post
3. The Shashin media browser
4. A Highslide slideshow

== Changelog ==

= 3.0.9 = Modified Shashin media menu to make it compatible with WordPress 3.3. There is no longer a separate Shashin media button. The Shashin menus are now tabs within WordPress 3.3's new, unified media button

= 3.0.8 = Bug fix: spurious Highslide navbars will no longer appear when navigating through multiple albums

= 3.0.7 =
* Bug fix: support uploaded_timestamp for ordering in old shortcodes
* Now shows error message if specified shortcode does not return any thumbnails
* Fixed uninstaller

= 3.0.6 = Bug fix: fix 'return' link bug introduced in 3.0.5, when viewing album photos

= 3.0.5 =
* Now correctly handles when multiple album thumbnails are opened at the same time to show their album photos
* Improves handling of commas in EXIF "exposure" data

= 3.0.4 = Automatically crop photos as appropriate when reading old shortcodes

= 3.0.3 =
* Fixed display of "crop" input field on media menu for photos and albums
* Improved exception handling
* Handle commas in "exposure" in EXIF data (treat as a string)


= 3.0.2 =
* Added .pot translation file
* Album photos table now inherits position from parent album thumbnail table
* Now handles Shashin 2 shashin_album_key query string arg, for old links
* Bug fix: numeric fields (like photo count, pub date) were getting cleared when updating 'include in random' settings
* Bug fix: now checks for 'include in random' flag on albums and photos when generating random thumbnail display
* Bug fix: fixed size for album thumbnails using old shortcode format
* Aligned 'update include in random' button on Tools menu with radio button column
* Updated explanation on Settings menu for photo thumbnails

= 3.0.1 = Bug fix: first-time synchronizing was failing when upgrading from 2.6.3

= 3.0 =
* Complete rewrite
* Added Twitpic and Youtube Support
* New shortcode format (the old format is still supported)
* Fixed issues many were experiencing with album synchronizing
* Added jQuery based display of album photos and photo paging
* Now uses a single widget
* Displays photo and album thumbnails in any size
* Includes Highslide 4.19
* Removed support for private Picasa albums (too unreliable)
* Deprecated [salbumlist] tag (now renders as a regular album thumbnail display)
* Deprecated the "c" option for captions (it is now treated as an "n")
* Removed the 2 Shashin PHP functions that could be called directly, and replaced with ShashinWP::display()

= 2.6.3 =
* Added support for child themes
* Now compatible with WP 3 beta (WP 3 appears to automatically unserialize options)
* Can now uninstall and delete Shashin from the main plugin page (added uninstall hook for existing uninstall function)

= 2.6.2 = Bug fix to unlisted album support for Google authentication servers outside the US
= 2.6.1 = Bug fix to the EXIF data bug fix in 2.6 - actually works in Windows now!
= 2.6 =
* Added support for unlisted Picasa albums (finally!). You must have the [PHP curl extension](http://www.php.net/manual/en/book.curl.php) installed to use this feature. Most PHP installations include curl, but some hosting providing may need you to ask them to turn it on for you.
* Added ability to group albums by Picasa user accounts when using the [salbumthumbs] tag.
* Bug fix: the EXIF support added in Shashin 2.4 caused an incompatibility with Windows servers that is now fixed. Many thanks to MC for letting me run tests on his Windows server.
* Bug fix: Shashin's automatic album syncing was interfering with scheduled jobs from other plugins in some circumstances. This is fixed. Note that you also need WordPress 2.9.1 or higher, as this was related to a wp-cron bug in WordPress 2.9.

= 2.5 =
* jQuery based WYSIWYG browser for adding Shashin photos to your posts
* Option to automatically sync albums several times per day, now that Picasa video URLs expire every 11 hours
* Bug fix: album title links in the album thumbnails sidebar widget now point to the correct URL
* Bug fix: "next" and "previous" links for album photos display now work in Google Chrome and Safari (actually a workaround for a webkit bug)

= 2.4.2 =
* Added back missing Shashin::getAlbumList() function

= 2.4.1 =
* corrected version numbers used with wp_enqueue_script calls

= 2.4 =
* Support for image viewers other than Highslide, such as Lightbox, Fancybox, etc.
* Display album photos using the order you’ve set in Picasa.
* Customizable pagination of album photos.
* New settings for customizing Highslide’s borders, navigation bar, and background color/opacity.
* Dynamically set thumbnail sizes and the number of thumbnail columns to suit your WordPress theme (this means you don’t have to worry about images being too large or small if you switch to a wider or narrower theme).
* Show camera EXIF data in Highslide captions.
* Improved usability for the Shashin admin screens, with detailed examples of Shashin tags.
* Align images and groups of thumbnails to the center.
* Specify an alternate image to use as a thumbnail – this is often useful for videos.
* Includes the latest version of Highslide (4.1.4)

= 2.3.5 = Bug fixes: Fixed incomplete localization code for widget menus; In the album photo admin menu, now correctly saves whether photos should be included in random display; The salbumphotos tag can now handle sort order options with spaces (e.g. "pub_date desc"); The salbumphotos tag no longer shows a "go back" link, as there’s nothing to go back to.
= 2.3.4 = Bug fix: photos lacking a date indicating when they were taken failed to add in mySQL on Windows (Shashin now adds a 0 timestamp to them). Bug fix: the code for the [salbumlist] tag was not updated in the 2.3 rewrite so it was broken (I overlooked it in my previous testing). Bug fix: you can now put more than one [salbumthumbs] or [salbumlist] tag on a page (it never occurred to me to try this before, but someone wanted to, and now it’ll work).
= 2.3.3 = Rewrote album photo syncing method for faster performance. Bug fix: was not correctly handling photos when they were moved from one Picasa album to another. Can now handle # character at end of Picasa URLs when adding an album (these were tripping up the RSS feed URL). Improvements to localization file.
= 2.3.2 = Changed album thumbnail widget to always point to Picasa for viewing photos, instead of trying to load them all in the sidebar. Bug fix: was adding duplicate entries for albums when they were synced. Added Dutch localization file.
= 2.3.1 = Bug fix: correctly loads language localization files. Bug fix: was reporting a database error on album syncs when there wasn’t one.

= 2.3 =
* A complete rewrite of Shashin, with better security and better error handling
* Added option for daily automatic synchronization with Picasa
* Added internationalization support
* Simplified [salbumthumbs] so that, when you click an album thumbnail, the page will reload and display all the photos for that album
* Added options to control the layout of album photos when an album thumbnail is clicked
* Re-purporsed the [salbumphotos] tag so that it can be used to display all the photos for a specified album, without having to click an album thumbnail first
* [srandom] and [snewest] now support multiple album keys
* Improved usability of admin menus
* Added "float" and "clear" options for each of the widgets
* Added an uninstall option
* Bug fix: when listing photo keys or album keys in tags, they’ll now always appear in the order they were listed
* Several minor bug fixes to the widgets

= 2.2 =
* Includes the latest version of Highslide
* Added ability to play Picasa videos in Highslide
* New option for autoplaying Highslide slideshows
* Checks for custom versions of shashin.css and highslide.css in your active theme directory
* Smarter about handling the CSS paths to Highslide images, for WordPress installations in a subdirectory
* Added "c" option for displaying captions
* Added option to make thumbnails unclickable

= 2.1 =
* Simplified methods for displaying album photos. The page containing the [salbumphotos] tag now displays all your album thumbnails by default, instead of breaking if you call it without arguments. It then displays photos for the specified album if you do call it with arguments. Also, no longer tries to manipulate the page title (there is currently no clean way to do this in WordPress).
* Bug fix: thumbnails in the admin panels display correctly again now (this broke after I added Highslide support)
* Bug fix: with certain Highslide settings, captions weren’t always showing on the page when requested.
* Bug fix: fixed minor XHTML validation error in [salbumlist] markup.

= 2.0.4 = Added "global $wpdb" to the top of Shashin.php – necessary for compatibility with WordPress 2.5 (otherwise the Shashin table names come out wrong). Also added mp4 as a supported video type.
= 2.0.3 = Adjusted for new location of video data in the Picasa RSS feed.
= 2.0.2 = Adjusted for new location of the content_url in the Picasa RSS feed.

= 2.0.1 =
* Bug fix: if you select the option to display your photos at Picasa in a new browser window, it actually works now (the formatting of the anchor tag was incorrect).
* Bug fix: now performs a preg_escape on the URL for the page containing your salbumphotos tag. This fixes a warning that was being displayed for some URLs in PHP 5.
* Bug fix: Shashin now correctly detects your WordPress installation directory if you’ve installed it in a subdirectory (except for paths in shashin/display/highslide.css which are hardcoded – you’ll need to edit those by hand).
* Added a full copy of the GPL license.

= 2.0 =
* 3 options for image display: link to Picasa (as before), link to Picasa in a new window, or show in Highslide (note videos still display at Picasa).
* Show thumbnails for all photos in an album, linked from the album thumbnail (salbumphotos tag).
* Show album thumbnails side-by-side with the album descriptions (salbumlist tag).
* Option to prefix album titles on photo captions.
* For database table changes, now analyzes tables directly instead of just checking the Shashin version number.
* Bug fix: Album "sync all" feature wasn’t working properly when there was more than one username.

= 1.2.3 = Bug fix: on the admin page, there was a foreach loop error, trying to display Picasa album usernames even if no albums were loaded yet in Shashin.
= 1.2.2 = Bug fix: users upgrading to v1.2 correctly had a new unique index set on their photo table’s photo_ids, but new user’s didn’t, which could cause problems when syncing albums.

= 1.2.1 =
* Bug fix: specifying "any" album when displaying random photos will no longer fail because of the function randomly selecting an album with too few photos.
* Complete rewrite of the algorithm for selecting random photos. If you specify "any" album, it will now return a random set of photos from across all your albums (previously it would select a random album, and then return random photos only from that album).
* Now sets the character set for the Shashin tables to UTF-8, as not all mysql configurations use UTF-8 by default. This may fix reported bugs with multibyte (e.g. Chinese) characters.

= 1.2 =
* Wrote a completely knew parser for the Picasa RSS feed. This fixes an incompatibility with WordPress 2.3.3. Shashin is no longer dependent on the constantly changing WordPress RSS tools (yay!).
* Can now sync and add all of your albums at once (i.e. per Picasa username).
* Added display of multiple album thumbnails (for selected albums, or all your albums with a sort order you choose) – can be done in posts, pages, or as a widget.
* Bug fix: the sthumbs tag now displays the photos in the order you specified.
* Soft delete of photos: if pictures are removed from a Picasa album, they are now flagged as deleted in Shashin, but are not actually removed from the database.
* Smarter album syncing: if you move photos from one Picasa album to another, you can now do this without the original Shashin photo key being lost.

= 1.1 =
* Added widgets for all the Shashin functions (single photos, random photos, newest photos, photo thumbnail tables, and album thumbnails)
* Bug fix: ShashinPhoto::getRandomMarkup() was including photos from excluded albums when the album key was set to "any"
* Fixed notification: if an album sync or album add fails, this is now correctly reported as a temporary failure to read the Picasa RSS feed, not as a Shashin database error.
* Added wrapper methods for calling Shashin functions directly. This means, if you want to use Shashin in your sidebar and you’re not using widgets, the code you have to include is now less complicated.

= 1.0.7 = bug fix: ShashinPhoto::getRandomMarkup() was failing when only 1 photo was requested
= 1.0.6 = fixed documentation for sthumbs tag, and help link now points to the Shashin FAQ at wordpress.org
= 1.0.5 = bug fix: fixed display of icons on Shashin admin page; added this change log
= 1.0.4 = updates to readme.txt and minor code cleanup
= 1.0.3 = updates to readme.txt and minor code cleanup
= 1.0.2 = updates to readme.txt and minor code cleanup
= 1.0.1 = bug fix: support arbitrary name for Shashin plugin directory

= 1.0 =
* Added "snewest" tag, which displays a table of thumbnails for a variable number of the latest photos in an album you specify (or from all albums). Thumbnail size, display of captions, and CSS "float" and "clear" for the table also can be specified.
* "sthumbs" now includes option for showing a caption. Note this change is not backwards compatible, as the argument order has been changed slightly. You only need to change existing sthumbs tags if you set values for float or clear.
* Changed "srandom" to display a table of random thumbnails for a variable number of photos in an album you specify (or any album). Note this change is not backwards compatible as the arguments have been changed to support the new features.
* Added options admin menu: can now set options for your Picasa server URL, image div padding, and thumbnail div padding.
* Set default values for options listed above when installing
* Bug fix: now use htmlspecialchars on image alt text (which comes from the Picasa image description)
* Bug fix: now reads the Picasa feed with the correct character set (UTF-8)

= 0.6 = Beta version. First public release.