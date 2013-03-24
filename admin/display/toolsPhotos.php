<?php
echo '<div class="wrap">' . PHP_EOL;
require_once('donate.php');
screen_icon();
echo '<h2>' . __("Manage Shashin Albums", 'shashin') . '</h2>' . PHP_EOL;;

if ($message) {
    echo '<div id="message" class="updated"><p>' . $message .'</p></div>';
    unset($message);
}

echo '<p><a href="?page=ShashinToolsMenu">&laquo; '
    . __("Return to Albums Menu", 'shashin') . "</a></p>" . PHP_EOL;

echo '<h3>' . __('Photos for Album: ', 'shashin') . $this->album->title . '</h3>' . PHP_EOL;

echo '<form method="post">' . PHP_EOL;
echo '<input type="hidden" name="shashinAction" value="updateIncludeInRandom" />' . PHP_EOL;
$this->functionsFacade->createNonceFields('shashinNonceUpdate', 'shashinNonceUpdate');
echo '<input type="hidden" name="id" value="' . $this->album->id . '" />' . PHP_EOL;
echo '<table class="widefat">' . PHP_EOL;
echo '<tr>' . PHP_EOL;
echo '<th class="manage-column shashinCenter">'
    . $this->generateOrderByLink('source', __('Source Order', 'shashin'))
    . '</th>' . PHP_EOL;
echo '<th class="manage-column shashinCenter">'
    . $this->generateOrderByLink('id', __('Photo ID', 'shashin'))
    . '</th>' . PHP_EOL;
echo '<th class="manage-column shashinCenter">'
    . $this->generateOrderByLink('filename', __('Filename', 'shashin'))
    . '</th>' . PHP_EOL;
echo '<th class="manage-column shashinCenter">'
    . $this->generateOrderByLink('date', __('Date Taken', 'shashin'))
    . '</th>' . PHP_EOL;
echo '<th class="manage-column shashinCenter">' . __("Include in Random?", 'shashin') . '</th>' . PHP_EOL;
echo '</tr>' . PHP_EOL;

$i = 1;

foreach ($dataObjects as $photo) {
    $photoDisplayer = $this->container->getDataObjectDisplayer($this->shortcode, $photo, null, 'source');
    echo(($i % 2 == 0) ? '<tr class="shashinCenter">' : '<tr class="alternate shashinCenter">');
    echo PHP_EOL;
    echo '<td>' . $photoDisplayer->run() . '</td>' . PHP_EOL;
    echo '<td>' . $photo->id . '</td>' . PHP_EOL;
    echo '<td>' . $photo->filename . '</td>' . PHP_EOL;
    echo '<td>' . (($photo->takenTimestamp == 0)
        ? 'Unknown' : date("d-M-Y H:i", $photo->takenTimestamp))
        . '</td>' . PHP_EOL;
    echo '<td>';
    echo Lib_ShashinHtmlFormField::quickBuild(
        "includeInRandom[{$photo->id}]",
        $refData['includeInRandom'],
        $photo->includeInRandom);
    echo '</td>' . PHP_EOL;
    echo '</tr>' . PHP_EOL;
    $i++;
}
?>

<tr>
<td colspan="4">&nbsp;</td>
<td class="shashinCenter"><input type="submit" name="updateRandomDisplay" class="button-primary" value="<?php _e("Update Random display", 'shashin'); ?>" /></td>
</tr>
</table>
</form>
</div>
