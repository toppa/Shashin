<?php
echo '<div class="wrap">' . PHP_EOL;
require_once('donate.php');
echo '<h2>' . __("Manage Shashin Albums", 'shashin') . '</h2>' . PHP_EOL;;

if ($message) {
    require_once('message.php');
}

echo '<p><a href="?page=Shashin3AlphaToolsMenu">&laquo; '
    . __("Return to Albums Menu", 'shashin') . "</a></p>" . PHP_EOL;

echo '<h3>' . __('Photos for Album: ', 'shashin') . $this->album->title . '</h3>' . PHP_EOL;

echo '<form method="post">' . PHP_EOL;
echo '<input type="hidden" name="shashinAction" value="updateIncludeInRandom" />' . PHP_EOL;
$this->functionsFacade->createNonceFields('shashinNonceUpdate', 'shashinNonceUpdate');
echo '<input type="hidden" name="albumKey" value="' . $this->album->albumKey . '" />' . PHP_EOL;
echo '<table class="widefat">' . PHP_EOL;
echo '<tr>' . PHP_EOL;
echo '<th class="manage-column shashin_center">'
    . $this->generateOrderByLink('userOrder', __('Server Order', 'shashin'))
    . '</th>' . PHP_EOL;
echo '<th class="manage-column shashin_center">'
    . $this->generateOrderByLink('photoKey', __('Photo Key', 'shashin'))
    . '</th>' . PHP_EOL;
echo '<th class="manage-column shashin_center">'
    . $this->generateOrderByLink('title', __('Filename', 'shashin'))
    . '</th>' . PHP_EOL;
echo '<th class="manage-column shashin_center">'
    . $this->generateOrderByLink('takenTimestamp', __('Date Taken', 'shashin'))
    . '</th>' . PHP_EOL;
echo '<th class="manage-column shashin_center">' . __("Include in Random?", 'shashin') . '</th>' . PHP_EOL;
echo '</tr>' . PHP_EOL;

$i = 1;

foreach ($this->photos as $photo) {
    $this->photoDisplayer->setPhoto($photo);
    $this->photoDisplayer->setRequestedSize(72);
    $this->photoDisplayer->setRequestedCropped(true);

    echo(($i % 2 == 0) ? '<tr class="shashin_center">' : '<tr class="alternate shashin_center">');
    echo PHP_EOL;
    echo '<td>' . $this->photoDisplayer->run() . '</td>' . PHP_EOL;
    echo '<td>' . $photo->photoKey . '</td>' . PHP_EOL;
    echo '<td>' . $photo->title . '</td>' . PHP_EOL;
    echo '<td>' . (($photo->takenTimestamp == 0)
        ? 'Unknown' : date("d-M-Y H:i", $photo->takenTimestamp))
        . '</td>' . PHP_EOL;
    echo '<td>';
    echo ToppaHtmlFormField::quickBuild(
        "includeInRandom[{$photo->photoKey}]",
        $this->photoRef->includeInRandom,
        $photo->includeInRandom);
    echo '</td>' . PHP_EOL;
    echo '</tr>' . PHP_EOL;
    $i++;
}
?>

<tr>
<td colspan="4">&nbsp;</td>
<td class="shashin_center"><input type="submit" name="updateRandomDisplay" class="button-primary" value="<?php _e("Update Random Display", 'shashin'); ?>" /></td>
</tr>
</table>
</form>
</div>
