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

        echo ToppaHtmlFormField::quickBuild(
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
    <td class="shashinCenter"><input class="button-secondary" type="submit" name="update_random_display" value="<?php _e("Update Random Display", 'shashin'); ?>" /></td>
    </tr>
    </table>
    </form>
<?php }

else {
    echo "<p><em>" . __("You have not added any albums yet.", 'shashin') . "</em></p>" . PHP_EOL;
} ?>

<h3><?php _e("Add Albums", 'shashin'); ?></h3>

<form method="post">
<?php $this->functionsFacade->createNonceFields('shashinNonceAdd', 'shashinNonceAdd'); ?>
<input type="hidden" name="shashinAction" value="addAlbums" />

<p><?php _e('Shashin can display photos from public <em>Picasa</em> albums, videos from <em>YouTube</em>, and <em>Twitpic</em> photos by importing their RSS feeds. Please enter an RSS URL below (click "examples" for further details).', 'shashin'); ?></p>

<h4><a href="#" id="shashinExamples" class="shashinAdminHeading"><img src="<?php echo $this->functionsFacade->getPluginsUrl('images/plus.gif', __FILE__); ?>" id="shashinExamplesButton" />Examples</a></h4>
<dl id="shashinExamplesSection" class="shashinExamplesList">
<dt><strong><?php _e("All the Picasa albums for a user", 'shashin'); ?>:</strong> <?php _e("Look for the 'RSS' link on the bottom right of the Picasa user's home page", 'shashin'); ?></dt>
    <dd>Example: http://picasaweb.google.com/data/feed/base/user/<strong>michaeltoppa</strong>?alt=rss&amp;kind=album&amp;hl=en_US</dd>
<dt><strong><?php _e("A single Picasa album", 'shashin'); ?>:</strong> <?php _e("Look for the 'RSS' link in the sidebar of the album's main page", 'shashin'); ?></dt>
    <dd>Example: http://picasaweb.google.com/data/feed/base/user/<strong>michaeltoppa</strong>/albumid/5269449390714706417?alt=rss&amp;kind=photo&amp;hl=en_US</dd>
<dt><strong><?php _e("A YouTube user's videos", 'shashin'); ?>:</strong> <?php _e("Youtube does not display links for its feeds. You need to type in the RSS URL yourself", 'shashin'); ?></dt>
    <dd>Example: https://gdata.youtube.com/feeds/api/users/<strong>mttoppa</strong>/uploads</dd>
<dt><strong><?php _e('Most popular YouTube videos', 'shashin'); ?>:</strong> <?php _e('Youtube has many standard feeds available (top rated, etc) - see the', 'shashin'); ?>
    <a href="http://code.google.com/apis/youtube/2.0/developers_guide_protocol.html#Retrieving_and_searching_for_videos"><?php _e('YouTube API page for more', 'shashin'); ?></a></dt>
    <dd>Example: https://gdata.youtube.com/feeds/api/standardfeeds/most_popular</dd>
<dt><strong><?php _e("A Twitpic user's photos", 'shashin'); ?>:</strong> <?php _e('Look for the RSS link near the top right of the user\'s page', 'shashin'); ?></dt>
    <dd>Example: http://twitpic.com/photos/<strong>mtoppa</strong>/feed.rss</dd>
<!--
<dt><strong><?php _e("A Flickr set", 'shashin'); ?>:</strong> <?php _e("Look for the 'Feed' link on the bottom left of the set's main page.", 'shashin'); ?></dt>
    <dd>Example: http://api.flickr.com/services/feeds/photoset.gne?set=72157622514276629&amp;nsid=65384822@N00&amp;lang=en-us</dd>
<dt><strong><?php _e("A Flickr photostream", 'shashin'); ?>:</strong> <?php _e("Look for the 'Latest' link near the RSS icon on the bottom left of the photostream's main page.", 'shashin'); ?></dt>
    <dd>Example: http://api.flickr.com/services/feeds/photos_public.gne?id=65384822@N00&amp;lang=en-us&amp;format=rss_200</dd>
 -->
</dl>
<p><strong><?php _e("RSS URL:", 'shashin'); ?></strong>
<?php echo ToppaHtmlFormField::quickBuild('rssUrl', $refData['dataUrl']); ?><br />
<?php _e("Include album's photos in random photo displays?", 'shashin'); ?>
<?php echo ToppaHtmlFormField::quickBuild('includeInRandom', $refData['includeInRandom'], "Y"); ?></p>

<p><input class="button-primary" type="submit" name="submit" value="<?php _e("Add Albums", 'shashin'); ?>" /></p>
</form>
</div>