<?php

echo '<div class="wrap">' . PHP_EOL;
require_once('donate.php');
echo '<h2>' . __("Manage Shashin Albums", 'shashin') . '</h2>' . PHP_EOL;;

if ($message) {
    require_once('message.php');
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
    echo '<th class="manage-column shashin_center">'
        . $this->generateOrderByLink('id', __('Album ID', 'shashin'))
        . '</th>' . PHP_EOL;
    echo '<th class="manage-column shashin_center">' . __("Sync", 'shashin') . "</th>" . PHP_EOL;
    echo '<th class="manage-column shashin_center">' . __("Delete", 'shashin') . "</th>" . PHP_EOL;
    echo '<th class="manage-column shashin_center">'
         . $this->generateOrderByLink('count', __('Photo Count', 'shashin'))
         . '</th>' . PHP_EOL;
    echo '<th class="manage-column shashin_center">'
         . $this->generateOrderByLink('date', __('Pub Date', 'shashin'))
         . '</th>' . PHP_EOL;
    echo '<th class="manage-column shashin_center">'
         . $this->generateOrderByLink('sync', __('Last Sync', 'shashin'))
         . '</th>' . PHP_EOL;
    echo '<th class="manage-column shashin_center">' . __("Include in Random?", 'shashin') . "</th>" . PHP_EOL;
    echo "</tr>" . PHP_EOL;

    $i = 1;
    foreach ($dataObjects as $album) {
        echo(($i % 2 == 0) ? "<tr>" : "<tr class='alternate'>");
        echo PHP_EOL;
        echo '<td>'
            . $this->generatePhotosMenuSwitchLink($album)
            . '</td>' . PHP_EOL;
        echo '<td class="shashin_center">'
            . $album->id . "</td>" . PHP_EOL;
        echo '<td class="shashin_center">'
            . $this->generateSyncLink($album)
            . '</td>' . PHP_EOL;
        echo '<td class="shashin_center">'
            . $this->generateDeleteLink($album)
            . '</td>' . PHP_EOL;
        echo '<td class="shashin_center">'
            . $album->photoCount . "</td>" . PHP_EOL;
        echo '<td class="shashin_center">' . $this->functionsFacade->dateI18n("d-M-Y", $album->pubDate) . "</td>" . PHP_EOL;
        echo '<td class="shashin_center">' . $this->functionsFacade->dateI18n("d-M-Y H:i", $album->lastSync) . "</td>" . PHP_EOL;
        echo '<td class="shashin_center">';

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
    <td class="shashin_center"><strong><?php echo $this->generateSyncAllLink(); ?></strong></td>
    <td colspan="4">&nbsp;</td>
    <td class="shashin_center"><input class="button-secondary" type="submit" name="update_random_display" value="<?php _e("Update Random Display", 'shashin'); ?>" /></td>
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

<!--    <p><?php _e("Shashin can display photos from <strong>public</strong> <em>Picasa albums</em>, <em>Flickr photostreams</em>, <em>Flickr sets</em>, and <em>Twitpic photostreams</em> by importing their RSS feeds. Please enter an RSS URL below.", 'shashin'); ?></p> -->


<p><?php _e("Shashin can display photos from <strong>public</strong> <em>Picasa albums</em> by importing their RSS feeds. Please enter an RSS URL below.", 'shashin'); ?></p>
<h4><a href="#" id="shashin_examples" class="shashin_admin_heading"><img src="<?php echo $this->functionsFacade->getPluginsUrl('images/plus.gif', __FILE__); ?>" id="shashin_examples_button" />Examples</a></h4>
<dl id="shashin_examples_section" class="shashin_examples_list">
<dt><strong><?php _e("All the Picasa albums for a user", 'shashin'); ?>:</strong> <?php _e("Look for the 'RSS' link on the bottom right of the Picasa user's home page", 'shashin'); ?></dt>
    <dd style="font-size: smaller;">Example: http://picasaweb.google.com/data/feed/base/user/michaeltoppa?alt=rss&amp;kind=album&amp;hl=en_US</dd>
<dt><strong><?php _e("A single Picasa album", 'shashin'); ?>:</strong> <?php _e("Look for the 'RSS' link in the sidebar of the alm's main page", 'shashin'); ?></dt>
    <dd style="font-size: smaller;">Example: http://picasaweb.google.com/data/feed/base/user/michaeltoppa/albumid/5269449390714706417?alt=rss&amp;kind=photo&amp;hl=en_US</dd>
<!--    <dt><strong><?php _e("A Youtube user's videos", 'shashin'); ?>:</strong> <?php _e("There is no link for this in the user's channel page, but this is what the URL looks like (substitute the desired username for 'mttoppa')", 'shashin'); ?></dt>
    <dd style="font-size: smaller;">Example: http://gdata.youtube.com/feeds/api/users/mttoppa/uploads</dd>
<dt><strong><?php _e("A Flickr set", 'shashin'); ?>:</strong> <?php _e("Look for the 'Feed' link on the bottom left of the set's main page.", 'shashin'); ?></dt>
    <dd style="font-size: smaller;">Example: http://api.flickr.com/services/feeds/photoset.gne?set=72157622514276629&amp;nsid=65384822@N00&amp;lang=en-us</dd>
<dt><strong><?php _e("A Flickr photostream", 'shashin'); ?>:</strong> <?php _e("Look for the 'Latest' link near the RSS icon on the bottom left of the photostream's main page.", 'shashin'); ?></dt>
    <dd style="font-size: smaller;">Example: http://api.flickr.com/services/feeds/photos_public.gne?id=65384822@N00&amp;lang=en-us&amp;format=rss_200</dd>
<dt><strong><?php _e("A Twitpic photostream", 'shashin'); ?>:</strong> <?php _e("Look for the RSS icon on the top right of the photostream page", 'shashin'); ?></dt>
    <dd style="font-size: smaller;">Example: http://twitpic.com/photos/mtoppa/feed.rss</dd> -->
</dl>
<p><strong><?php _e("RSS URL:", 'shashin'); ?></strong>
<?php echo ToppaHtmlFormField::quickBuild('rssUrl', $refData['dataUrl']); ?><br />
<?php _e("Include album's photos in random photo displays?", 'shashin'); ?>
<?php echo ToppaHtmlFormField::quickBuild('includeInRandom', $refData['includeInRandom'], "Y"); ?></p>

<p><input class="button-primary" type="submit" name="submit" value="<?php _e("Add Albums", 'shashin'); ?>" /></p>
</form>
</div>