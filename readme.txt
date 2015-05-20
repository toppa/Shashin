=== Shashin ===
Contributors: toppa
Donate link: http://www.toppa.com/shashin-wordpress-plugin
Tags: Picasa, Fancybox, prettyPhoto, Twitpic, Youtube, image, images, photo, photos, picture, pictures, gallery, widget, widgets, video
Requires at least: 3.0
Tested up to: 4.2.2
Stable tag: 3.4.11
License: GPLv2 or later

Shashin is a powerful WordPress plugin that lets you easily display photos and videos from Picasa, Twitpic, and Youtube in your WordPress site.

== Description ==

**I am currently no longer developing or supporting this plugin. I may resume development and support in the future, but I'm not sure when.**

**What's new in Shashin 3.4**

* Responsive design: your thumbnails will look good on any size display, and so will the expanded view.
* Social sharing: link directly to the full size display of any Shashin photo from Twitter, Facebook, or Pinterest (and you can just share a link directly with someone)
* Improved experience when navigating thumbnails of photos within an album
* Captions now overlay the bottom of thumbnails
* Updated to handle the latest changes from Google in how Google+ interacts with the Picasa API
* Removed support for viewers other than prettyPhoto and Fancybox. There is now too much custom javascript in Shashin for it work with any viewer.

Note: on the Shashin settings page, you will want to pick prettyPhoto as your viewer to take full advantage of the new responsive design and social sharing features.

**Overview**

Shashin has many features for displaying photos and videos from Picasa, Youtube, and Twitpic in your Wordpress posts and pages:

* Show a gallery of all your Shashin albums, photos, and videos, with multiple options for how they are laid out and ordered
* Use the Media Manager to easily add Shashin photos and albums to your posts, in any size, including captions and EXIF data
* Show your photos and vidoes with PrettyPhoto or Fancybox, which are included with Shashin.
* Pick photos and videos from any combination of Picasa, Youtube, and Twitpic to display in groups of thumbnails
* Show thumbnails of your newest photos and videos, from one or more albums
* Display album thumbnails for albums you choose, or all your albums, sorted however you like.
* Display any number of random photos and videos. You can also choose to exclude certain photos or albums from random display
* Use a widget to display Shashin photos in your sidebar
* Customize the Shashin stylesheet to suit the theme of your site.
* Internationalization: Shashin supports translations into other languages (please contribute a translation if you're bilingual!)
* Schedule daily automatic synchronization of Shashin with your feeds from Picasa, Twitpic, and Youtube.

Shashin is multi-site compatible.

**Get Help**

Enter a post in [the wordpress.org support forum for Shashin](http://wordpress.org/support/plugin/shashin), and I'll respond there.

**Give Help**

* Provide a language translation - [here's how](http://weblogtoolscollection.com/archives/2007/08/27/localizing-a-wordpress-plugin-using-poedit/) or use the [online PO Editor](https://poeditor.com/)
* Fork [the Shashin repository on github](https://github.com/toppa/Shashin) and make a code contribution
* If you're savvy user of the plugin, [answer questions in the support forum](http://wordpress.org/support/plugin/shahsin)
* If you tip your pizza delivery guy, tip your plugin developer - [make a donation](http://www.toppa.com/shashin/)

== Installation ==

**Requirements**

* Wordpress 3.0 or higher
* PHP 5.1.2 or higher
* mySQL 4.1 or higher
* jQuery 1.7 or higher

**First time installation**

1. Download and activate Shahsin just like any other plugin
1. Go to the Shashin Tools Menu to add your Picasa, Youtube, or Twitpic feeds (there are detailed instructions on the page)
1. Go to the Shashin Settings Menu to review and update your preferences
1. Go to the WordPress widget menu to add the Shashin widget to your sidebar if you want
1. Edit a post or page and use the media manager to add photos and albums from Shashin
1. Note that Shashin will add two tables to your WordPress database, named wp\_shashin\_album and wp\_shashin\_photo. **It's important to include these tables when making backups of your WordPress tables.** The Shashin shortcodes rely on ID numbers stored in these tables.

**Upgrading from Shashin 2**

1. Download the current version of Shashin, and then deactivate and reactivate it from the main plugin menu.
1. Go to the Shashin Tools Menu and click "sync all" to complete the upgrade
1. Go to the Shashin Settings Menu and select the option to support the old shortcode format (or you can revise your shortcodes to the new format)
1. Carefully review pages and posts that contain Shashin tags to make sure everything looks correct
1. If all is well, click the link on the Shashin Tools Menu to remove the Shashin 2 database table backups
1. Go to the WordPress widget menu to add the Shashin widget to your sidebar if you want (the old widgets are gone)
1. If you were calling Shashin functions directly in PHP, they are no longer supported. There is a new function you can call. See the documentation page linked from the FAQ section.

== Frequently Asked Questions ==

Please go to [the Shashin page on my site](http://www.toppa.com/shashin-wordpress-plugin) for a detailed usage guide. Also check out [the examples page](http://www.toppa.com/shashin-3-development-progress/).

If you have a question, enter a post in [the wordpress.org support forum for Shashin](http://wordpress.org/support/plugin/shashin), and I'll respond there. Please understand I have a busy schedule and my support for Shashin is free - it may take me up to a week to reply.

== Screenshots ==

1. Album thumbnails
2. Photos in a post
3. The Shashin media browser
4. A Fancybox slideshow

== Changelog ==

= 3.4.11 =

* Security fix: integrate prettyPhoto 3.1.6 to get its latest security fix
* Bug fix: get prettyPhoto working with YouTube videos again

= 3.4.10 =

* Bug fix: captions were not showing after the first set of photos when type="albumphotos"
* Bug fix: the photo direct link URL was not working

= 3.4.9 =

* Bug fix: the popup for copying and pasting a photo's URL wasn't working (bug introduced in 3.4.7)
* Bug fix: allow a photo's TakenTimestamp to be null, and allow negative values (for times before 1970, or times not defined)

= 3.4.8 = Bug fix: call debounce function for resizing only when the window is resized (bug introduced in 3.4.7)

= 3.4.7 =

* Bug fix: album photos were still sometimes being deleted when they shouldn't, if a sync call failed. Fixed this by making sure we have the expected number of photos synced before deleting any old photos
* Remove unnecessay call to the Shashin autoloader during plugin activation
* Namespaced Shashin's custom javascript
* Make Shashin's javascript dependencies explicit whe nloading javascript libraries

= 3.4.6 =

* Bug fix: do smart truncation of text in album thumbnail captions (don't truncate the HTML!)
* Do more namepsacing of Shashin's version of prettyPhoto, to avoid conflicts with any already installed versions of prettyPhoto
* Add jQuery version requirement to readme

= 3.4.5 =

* Bug fix: fix positioning of caption overlays in Firefox
* Bug fix: make captions visible for "square" thumbnail presentation
* Degrade gracefully for thumbnail display for versions of IE less than 9 (always show "square" caption style, and don't try to dynamically set caption widths via JS, which old IE can't handle).

= 3.4.4 =

* Bug fix: make changelog readable in the readme file
* For caption overlays on thumbnails, have them fade-in after they are fully positoned, so you don't see their position and size adjusting as the page loads.
* Bug fix: get Fancybox working in Firefox
* Update instructions for Picasa and YouTube

= 3.4.3 =

* Bug fix: get thumbnail columns to wrap gracefully as the screen size shrinks (and not just fall back to 1 column)
* Bug fix: fix display of the prettyPhoto themes (other than the default one) for mobile

= 3.4.2 =

* Add option to continue using the previous thumbnail style, with square corners and captions underneath the thumbnails (and the captions are not truncated)
* Bug fix: make sure number of calculated columns never exceeds number of thumbnails (fixes thumbnails appearing too small)
* Bug fix: revise css and js so thumbnails naturally appear at their actual size (they were showing up slightly smaller)
* Bug fix: get portrait-oriented thumbnails to always be centered in a gallery
* Performance improvement: save thumbnail dimensions via ajax if they weren't previously saved, so we don't have to do dimension calculations in javascript every page load
* Performance improvement: use "smartresize" jquery plugin, to reduce the number of resize events fired in certain browsers when the window is resized
* Performance improvement: make display of truncated captions smarter
* Performance improvement: use a smarter technique to hide text of prettyPhoto links
* Update to the current version of the "imagesloaded" jquery plugin
* Open Google Maps links in a new window

= 3.4.1 =

* bug fix: thumbnails were showing up too small in some cases with 'colums="max"' in the shortcode.
* bug fix: 'position="center"' was not working correctly; fix by adding table layout styles to stylesheet

= 3.4 =

* Responsive design
  * No longer use tables for layout!
  * Responsive images
  * If thumbnails shrink to less than 80% of their intended size, show them all in a single column
  * Dynamically update responsive display when the window is resized
  * Enhanced prettyPhoto to show images at the full width of the display on mobile devices
* Social sharing
  * Enhanced prettyPhoto to include Twitter, Facebook, and Pinterest buttons that will share links for opening the full size image directly in prettyPhoto
  * Also added a "link" button to prettyPhoto, so you can share a link directly for the photo, via email, in a web page, etc.
* When viewing thumbnails of photos in an album:
  * Using the navigation controls will automatically scroll the view to the top row of photos
  * The title and navigation controls now appear at the top and bottom
  * Bug fix: in certain situations the pagination of thumbnails was not working correctly
* Thumbnail captions
  * Overlay captions on thumbnails
  * Use trunk8 to intelligently truncate captions so they don't overflow the image
  * Don't show captions on thumbnails if they take up more than 30% of the height
  * Don't show details in an album thumbnail caption if the thumbnail width is less than 300px
* Add prettyPhoto setting for "Show social connect buttons?"
* Update for how Google+ now handles viewing photos that are limited to people who have a link with an authkey
* On the Shashin Tools menu, only default to the "How does this work?" tab if there are no albums added yet
* Remove support for viewers other thank prettyPhoto and Fancybox (there is now too much custom javascript to support any viewer)

= 3.3.3 =
* Bug fix: Fancybox galleries weren't being recognized (they were showing only one photo at a time) - fixed
* Bug fix: show EXIF data in prettyPhoto captions when requested by setting (and include "View at Picasa", etc links)
* Add video support for flv files (they aren't listed as a supported type for Picasa, but they work)
* Update link for help on Tools Menu to point to the support forum
* Added Slovenian translation
* Removed old, out-of-date translations
* Bug fix for certain versions of mySQL on Windows: unable to add albums because of the NOT NULL constraint on video fields in the photo's table - fixed

= 3.3.2 = Don't use lcfirst since it requires PHP 5.3

= 3.3.1 = Gracefully handle shutting down the _Highside for Shashin_ add-on for anyone using it

= 3.3 =
* Add prettyPhoto, deprecate Fancybox
* Add jQuery tabs to settings menu
* Remove dependencies on Toppa Plugin Libraries plugin, and replicate its functionality within Shashin. This shoudl eliminate cross-plugin fragility problems some have experienced when upgrading.
* Allow negative timestamp values on photos. This should help with rare timestamp issues when using mySql on Windows.
* Ignore unrecognized shortcode attributes (previously Shashin deliberately raised an error). Some themes pass additional attributes with widgets, so we don't want them to trigger errors.

= 3.2.6 =
* Bug fix: album photos were being displayed multiple times if someone clicked an album thumbnail multiple times. Now only loads them once.
* Bug fix: on rare occasions, album synchronizing resulted in photos being deleted and reloaded, which broke photo tags relying on the original ID numbers. I've made a change to the synchronizing process that should fix this.

= 3.2.5 =
* Bug fix: handle Picasa authkeys that includes hyphens
* Workaround: WP 3.4.1 is returning 1.8.20 for the version of $wp_scripts->->query('jquery-ui-core'), which is not available through Google APIs. The Shashin Tools screen relies on this, so hardcoded to 1.8.18 for now
* Various internal improvements to ShashinDataObject and ShashinPhoto, driven by new unit tests

= 3.2.4 =
* Add support for limited view Picasa albums (works with Picasa URLs only, not Google+)
* Bug fix: setup to enqueue scripts and styles with wp_enqueue_scripts() (not template_redirect)
* Support additional formats of Picasa and Google+ URLs

= 3.2.3 = Remove explicit depedency on Fancybox stylesheet when loading the Shashin stylesheet (to facilitate support of other photo viewers)

= 3.2.2 = Check for version 1.3.5 of Toppa Plugin Libaries, which has a bug fix for compatibility with PHP 5.2.1

= 3.2.1 = Bug fix: temporarily continue to pass an autoloader object to ToppaDatabaseFacade, as the old version may still be running when upgrading Shashin

= 3.2 =
* More user friendly UI for adding albums, photos, and videos (uses jQuery tabs menu)
* Accepts Google+ URLs for adding albums
* Bug fix: When "include in random" is set for an album, it now correctly cascades to the photos in the album
* Adjust fancybox css z-index so it appears over the Twenty Eleven header
* Code refactor: Removed unneeded passing around of autoloader object
* Code refactor: Refactored setup for synchronizing

= 3.1.5 =
* Improve handling of dependencies on Toppa Plugin Libraries, so there are no PHP error messages if you have an old or missing version of the libraries
* Perform settings and other updates without relying on plugin re-activation
* Option to not load Shashin's copy of Fancybox if you already have your own
* Add a 30 second timeout when synchronizing albums
* Turn off SSL verification when synchronizing albums (gets around misconfigured sites, and we trust Google anyway...)

= 3.1.4 =
* Update settings even if plugin is not deactivated/reactivated, as WP no longer runs activation hooks during plugin auto-upgrades
* Bug fix: Fancybox slideshow was not launching in Firefox (just needed to move the setShashinFancyboxCaption function)

= 3.1.3 =
* Also implement improved captions for photos shown after clicking an album thumbnail (also fixes bug with "return" link for albums)
* In FancyBox caption, show exif data if requested, even if there is no photo caption text
* Bug fix: show correct FancyBox captions on photos shown after clicking an album thumbnail, on pages that have a mix of photo groups and album thumbnails
* Possible bug fix: add a 30 second buffer to the time check when synchronizing albums, in case there is a delay between synchronizing the album's meta data and the album's photos (this is to try addressing occasional reports of photo ID numbers changing, which means they are being deleted and re-added).

= 3.1.2 =
* Improve code for handling display of Fancybox captions
* Adjust css so Fancybox nav controls do not overlay video controls

= 3.1.1 = Bug fix: catch exception when checking "imageDisplay" setting on a new installation

= 3.1 =
* Removed Highslide due to licensing conflict (Highslide is not GPL and therefore is not allowed in the wordpress.org plugin repository). Replaced with Fanybox 1.3.4
* Added support for WordPress multi-site installations
* Improved error reporting when album synchronization fails
* General cleanup of PHP warnings when running WordPress in debug mode
* Improve handling of htmlentities in photo descriptions (as img alt text - never double convert)
* Bug fix: correctly handle recursive arrays in settings data
* Bug fix: correctly clear checkboxes as needed in saving data from settings menu
* Bug fix: "other viewer" settings now set photo groups "rel" attribute correctly when clicking through from album thumbnails

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
