<?php

echo '<div class="wrap">' . PHP_EOL;
require_once('donate.php');
screen_icon();
echo '<h2>' . __("Manage Shashin Albums", 'shashin') . '</h2>' . PHP_EOL;;

if ($message) {
    echo '<div id="message" class="updated"><p>' . $message .'</p></div>';
    unset($message);
}

echo '<h3>' . __("Your Albums", 'shashin') . '</h3>' . PHP_EOL;

if ($dataObjects) {
    echo '<p>';
    _e("Click an album title to view its photos. Click a column header to order the album list by that column (and click again to reverse the order).", 'shashin');
    echo "</p>" . PHP_EOL;
    echo '<form method="post">' . PHP_EOL;
    $this->functionsFacade->createNonceFields('shashinNonceUpdate', 'shashinNonceUpdate');
    echo '<input type="hidden" name="shashinAction" value="updateIncludeInRandom" />' . PHP_EOL;
    echo '<table class="widefat">' . PHP_EOL;
    echo "<tr>" . PHP_EOL;
    echo '<th class="manage-column">'
        . $this->generateOrderByLink('title', __('Title', 'shashin'))
        . '</th>' . PHP_EOL;
    echo '<th class="manage-column shashinCenter">'
        . $this->generateOrderByLink('id', __('Album ID', 'shashin'))
        . '</th>' . PHP_EOL;
    echo '<th class="manage-column shashinCenter">'
        . $this->generateOrderByLink('source', __('Source', 'shashin'))
        . '</th>' . PHP_EOL;
    echo '<th class="manage-column shashinCenter">' . __("Sync", 'shashin') . "</th>" . PHP_EOL;
    echo '<th class="manage-column shashinCenter">' . __("Delete", 'shashin') . "</th>" . PHP_EOL;
    echo '<th class="manage-column shashinCenter">'
         . $this->generateOrderByLink('count', __('Photo Count', 'shashin'))
         . '</th>' . PHP_EOL;
    echo '<th class="manage-column shashinCenter">'
         . $this->generateOrderByLink('date', __('Pub Date', 'shashin'))
         . '</th>' . PHP_EOL;
    echo '<th class="manage-column shashinCenter">'
         . $this->generateOrderByLink('sync', __('Last Sync', 'shashin'))
         . '</th>' . PHP_EOL;
    echo '<th class="manage-column shashinCenter">' . __("Include in Random?", 'shashin') . "</th>" . PHP_EOL;
    echo "</tr>" . PHP_EOL;

    $i = 1;
    foreach ($dataObjects as $album) {
        echo(($i % 2 == 0) ? "<tr>" : "<tr class='alternate'>");
        echo PHP_EOL;
        echo '<td>'
            . $this->generatePhotosMenuSwitchLink($album)
            . '</td>' . PHP_EOL;
        echo '<td class="shashinCenter">'
            . $album->id . "</td>" . PHP_EOL;
        echo '<td class="shashinCenter">'
            . ucfirst($album->albumType) . "</td>" . PHP_EOL;
        echo '<td class="shashinCenter">'
            . $this->generateSyncLink($album)
            . '</td>' . PHP_EOL;
        echo '<td class="shashinCenter">'
            . $this->generateDeleteLink($album)
            . '</td>' . PHP_EOL;
        echo '<td class="shashinCenter">'
            . $album->photoCount . "</td>" . PHP_EOL;
        echo '<td class="shashinCenter">' . $this->functionsFacade->dateI18n("d-M-Y", $album->pubDate) . "</td>" . PHP_EOL;
        echo '<td class="shashinCenter">' . $this->functionsFacade->dateI18n("d-M-Y H:i", $album->lastSync) . "</td>" . PHP_EOL;
        echo '<td class="shashinCenter">';

        echo Lib_ShashinHtmlFormField::quickBuild(
            "includeInRandom[{$album->id}]",
            $refData['includeInRandom'],
            $album->includeInRandom);
        echo "</td>" . PHP_EOL;
        echo "</tr>" . PHP_EOL;
        $i++;
    } ?>

    <tr>
    <td colspan="2">&nbsp;</td>
    <td class="shashinCenter"><strong><?php echo $this->generateSyncAllLink(); ?></strong></td>
    <td colspan="5">&nbsp;</td>
    <td class="shashinCenter"><input class="button-secondary" type="submit" name="update_random_display" value="<?php _e("Update Random display", 'shashin'); ?>" /></td>
    </tr>
    </table>
    </form>
<?php }

else {
    echo "<p><em>" . __("You have not added any albums yet.", 'shashin') . "</em></p>" . PHP_EOL;
} ?>

<h3><?php _e("Add Albums", 'shashin'); ?></h3>

<div id="shashinTabs">
    <ul>
        <li><a href="#shashinTabs-overview">How does this work?</a></li>
        <li><a href="#shashinTabs-picasa">Picasa/Google+</a></li>
        <li><a href="#shashinTabs-youtube">YouTube</a></li>
        <li><a href="#shashinTabs-twitpic">Twitpic</a></li>
    </ul>
    <div id="shashinTabs-overview">
        <p><?php _e('Shashin can display photos and videos from several photo and video sharing sites. Shashin does not make copies of the photos or videos. Instead it downloads data about them into your WordPress site via JSON data feeds, and then makes it easy for you to display them on your site by making them available in the post editor\'s media browser. You will want to "synchronize" Shashin with your photos or videos when you upload new ones (on the Shashin settings page, you can set Shashin do this automatically). To get started, click the tab for the sites you want to synchronize with Shashin.', 'shashin'); ?></p>
    </div>
    <div id="shashinTabs-picasa">
        <form method="post">
        <?php $this->functionsFacade->createNonceFields('shashinNonceAddPicasa', 'shashinNonceAddPicasa'); ?>
        <input type="hidden" name="shashinAction" value="addAlbums" />
        <input type="hidden" name="shashinAlbumType" value="picasa" />

        <p><?php _e('Enter the URL for a Picasa/Google+ user to synchronize Shashin with all of his or her albums, or enter the URL for an individual album.', 'shashin'); ?>
        <p><strong><?php _e('Important notes:', 'shashin'); ?></strong></p>
        <ul>
        <li><?php _e('Google is migrating Picasa to Google+ and making changes without advance notification. If you are unable to add an album, please', 'shashin'); ?>
            <a href="http://www.toppa.com/category/technical/wordpress-plugins/support/"><?php _e('let me know', 'shashin'); ?></a>.</li>
        <li><?php _e('Shashin works with albums where the visibility is set to "public." Also, for albums with visibility set to "limited, anyone with the link", they will work if you include the "authkey" when pasting the URL in the form below (this is supported for Picasa, not Google+).', 'shashin'); ?></li>
        </ul>
        <p><strong><?php _e('Examples', 'shashin'); ?>:</strong></p>
        <dl>
        <dt><?php _e("All the Picasa/Google+ albums for a user", 'shashin'); ?>:</dt>
            <dd>https://picasaweb.google.com/100291303544453276374/</dd>
            <dd>https://picasaweb.google.com/michaeltoppa/</dd>
            <dd>https://plus.google.com/photos/100291303544453276374/albums</dd>
            <dd></dd>
        <dt><?php _e("A single Picasa/Google+ album", 'shashin'); ?>:</dt>
            <dd>https://picasaweb.google.com/100291303544453276374/2012WordCampNashville</dd>
            <dd>https://picasaweb.google.com/michaeltoppa/2012WordCampNashville</dd>
            <dd>https://plus.google.com/photos/100291303544453276374/albums/5733852964209389153</dd>
        </dl>

        <p><strong><?php _e("URL:", 'shashin'); ?></strong>
            <?php echo Lib_ShashinHtmlFormField::quickBuild('userUrl', $refData['dataUrl']); ?><br />
            <?php _e("Include these photos in random photo displays?", 'shashin'); ?>
            <?php echo Lib_ShashinHtmlFormField::quickBuild('includeInRandom', $refData['includeInRandom'], 'Y'); ?></p>
        <p><input class="button-primary" type="submit" name="submit" value="<?php _e("Add Albums", 'shashin'); ?>" /></p>
        </form>

    </div>
    <div id="shashinTabs-youtube">
        <form method="post">
        <?php $this->functionsFacade->createNonceFields('shashinNonceAddYoutube', 'shashinNonceAddYoutube'); ?>
        <input type="hidden" name="shashinAction" value="addAlbums" />
        <input type="hidden" name="shashinAlbumType" value="youtube" />

        <p><?php _e('For YouTube, you need to enter the RSS URL for the feed you want to synchronize.', 'shashin'); ?></p>
        <p><strong><?php _e('Important notes:', 'shashin'); ?></strong></p>
            <ul>
            <li><?php _e('Unfortunately Youtube does not display links for its feeds, but there are many standard feeds available (top rated, etc) - see the', 'shashin'); ?>
                <a href="http://code.google.com/apis/youtube/2.0/developers_guide_protocol.html#Retrieving_and_searching_for_videos"><?php _e('YouTube API page for examples', 'shashin'); ?>.</a>
                <?php _e('It also explains how to write the URLs for other kinds of feeds.', 'shashin'); ?></li>
            <li><?php _e('Shashin will synchronize with the first 50 videos in a YouTube feed.', 'shashin'); ?></li>
            </ul>
        <p><strong><?php _e('Examples', 'shashin'); ?>:</strong></p>
        <dl>
        <dt><?php _e("A YouTube user's videos (put his or her username in the URL)", 'shashin'); ?>:</dt>
            <dd>https://gdata.youtube.com/feeds/api/users/<strong>mttoppa</strong>/uploads</dd>
        <dt><?php _e('Most popular YouTube videos', 'shashin'); ?>:</strong></dt>
            <dd>https://gdata.youtube.com/feeds/api/standardfeeds/most_popular</dd>
        <dt><?php _e('Most recent videos in the comedy category', 'shashin'); ?>:</dt>
            <dd>https://gdata.youtube.com/feeds/api/videos/-/Comedy</dd>
        </dl>

        <p><strong><?php _e("RSS URL:", 'shashin'); ?></strong>
            <?php echo Lib_ShashinHtmlFormField::quickBuild('userUrl', $refData['dataUrl']); ?><br />
            <?php _e("Include these videos in random photo/video displays?", 'shashin'); ?>
            <?php echo Lib_ShashinHtmlFormField::quickBuild('includeInRandom', $refData['includeInRandom'], 'Y'); ?></p>
        <p><input class="button-primary" type="submit" name="submit" value="<?php _e("Add Videos", 'shashin'); ?>" /></p>
        </form>

    </div>
    <div id="shashinTabs-twitpic">
        <form method="post">
            <?php $this->functionsFacade->createNonceFields('shashinNonceAddTwitpic', 'shashinNonceAddTwitpic'); ?>
            <input type="hidden" name="shashinAction" value="addAlbums" />
            <input type="hidden" name="shashinAlbumType" value="twitpic" />

            <p><?php _e('Enter the URL for a Twitpic user to synchronize Shashin with all of his or her photos.', 'shashin'); ?>

            <p><strong><?php _e('Example', 'shashin'); ?>:</strong></p>

            <dl>
            <dt><?php _e("A Twitpic user's photos", 'shashin'); ?>:</dt>
            <dd>http://twitpic.com/photos/<strong>mtoppa</strong></dd>
            </dl>
            <p><strong><?php _e("URL:", 'shashin'); ?></strong>
                <?php echo Lib_ShashinHtmlFormField::quickBuild('userUrl', $refData['dataUrl']); ?><br />
                <?php _e("Include these photos in random photo displays?", 'shashin'); ?>
                <?php echo Lib_ShashinHtmlFormField::quickBuild('includeInRandom', $refData['includeInRandom'], 'Y'); ?></p>
            <p><input class="button-primary" type="submit" name="submit" value="<?php _e("Add Photos", 'shashin'); ?>" /></p>
        </form>

    </div>
</div>

</div>
