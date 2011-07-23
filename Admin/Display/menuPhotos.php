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
echo '<input type="hidden" name="id" value="' . $this->album->id . '" />' . PHP_EOL;
echo '<table class="widefat">' . PHP_EOL;
echo '<tr>' . PHP_EOL;
echo '<th class="manage-column shashin_center">'
    . $this->generateOrderByLink('source', __('Source Order', 'shashin'))
    . '</th>' . PHP_EOL;
echo '<th class="manage-column shashin_center">'
    . $this->generateOrderByLink('id', __('Photo ID', 'shashin'))
    . '</th>' . PHP_EOL;
echo '<th class="manage-column shashin_center">'
    . $this->generateOrderByLink('filename', __('Filename', 'shashin'))
    . '</th>' . PHP_EOL;
echo '<th class="manage-column shashin_center">'
    . $this->generateOrderByLink('date', __('Date Taken', 'shashin'))
    . '</th>' . PHP_EOL;
echo '<th class="manage-column shashin_center">' . __("Include in Random?", 'shashin') . '</th>' . PHP_EOL;
echo '</tr>' . PHP_EOL;

$i = 1;

foreach ($dataObjects as $photo) {
    $photoDisplayer = $this->container->getPhotoDisplayer($photo);

    echo(($i % 2 == 0) ? '<tr class="shashin_center">' : '<tr class="alternate shashin_center">');
    echo PHP_EOL;
    echo '<td>' . $photoDisplayer->run('xsmall') . '</td>' . PHP_EOL;
    echo '<td>' . $photo->id . '</td>' . PHP_EOL;
    echo '<td>' . $photo->filename . '</td>' . PHP_EOL;
    echo '<td>' . (($photo->takenTimestamp == 0)
        ? 'Unknown' : date("d-M-Y H:i", $photo->takenTimestamp))
        . '</td>' . PHP_EOL;
    echo '<td>';
    echo ToppaHtmlFormField::quickBuild(
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
<td class="shashin_center"><input type="submit" name="updateRandomDisplay" class="button-primary" value="<?php _e("Update Random Display", 'shashin'); ?>" /></td>
</tr>
</table>
</form>
</div>
