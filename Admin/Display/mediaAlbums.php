<script type="text/javascript">
jQuery(document).ready(function($) {

    $('#shashinAlbumInsert').click(function() {
        var selected = shashinGetSelected();
        if(selected.length == 0) return alert('<?php _e('No albums selected', 'shashin') ?>');
        parent.send_to_editor('[shashin'
            + ' type="' + $('#shashinAlbumType').val() + '"'
            + ' id="' + selected.join(',') + '"'
            + ' size="' + $('#shashinAlbumSize').val() + '"'
            + ' crop="' + $('#shashinAlbumCrop').val() + '"'
            + ' columns="' + $('#shashinAlbumColumns').val() + '"'
            + ' caption="' + $('#shashinAlbumCaption').val() + '"'
            + ' order="' + $('#shashinAlbumOrder').val() + '"'
            +  (($('#shashinAlbumReverse').val() == 'y') ? (' reverse="' + $('#shashinAlbumReverse').val() + '"') : '')
            +  (($('#shashinAlbumPosition').val() == '') ? '' : (' position="' + $('#shashinAlbumPosition').val() + '"'))
            + ']'
        );
    });

    function shashinGetSelected() {
        var selected = new Array;
        $('#shashinMediaMenuAlbumsSelected li').each(function() {
            selected.push($(this).attr('id').replace('shashinAlbum_',''));
        });
        return selected;
    }

    $('#shashinMediaMenuAlbumsList a').live('click', function(e) {
        e.preventDefault();
        $('#shashinMediaMenuAlbumsSelected ul').append($(this).parent());
    });

    $('#shashinMediaMenuAlbumsSelected a').live('click', function(e) {
        e.preventDefault();
        $('#shashinMediaMenuAlbumsList ul').append($(this).parent());
    });
});
</script>

<form id="shashinMediaMenu" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <div class="shashinMediaMenuAlbums" id="shashinMediaMenuAlbumsList">
        <h3><?php _e('Your Albums', 'shashin') ?></h3>
        <ul>
            <?php foreach ($albums as $album): ?>
                <li id="shashinAlbum_<?php echo $album->id ?>">
                    <a href="#"><?php echo $album->title ?><br />
                        <span>
                            <?php _e('Photos', 'shashin') ?>: <?php echo $album->photoCount ?> -
                            <?php _e('Published', 'shashin') ?>: <?php echo date("Y-m-d H:i", $album->pubDate) ?>
                        </span>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
    <div class="shashinMediaMenuAlbums" id="shashinMediaMenuAlbumsSelected">
        <h3><?php _e('Selected Albums', 'shashin') ?></h3>
        <ul></ul>
    </div>

    <br style="clear: both;" />

    <div id="shashinMediaMenuAlbumShortcodeCriteria">
        <h3><?php _e('Shortcode attributes', 'shashin') ?></h3>
        <table class="describe">
            <tbody>
                <tr>
                    <td><label for="shashinAlbumType"><?php _e('Type', 'shashin') ?></label></td>
                    <td><select name="shashinAlbumType" id="shashinAlbumType">
                        <option value="album"><?php _e('Album thumbnails', 'shashin') ?></option>
                        <option value="albumphotos"><?php _e('All album photos', 'shashin') ?></option>
                    </select></td>
                    <td><label for="shashinAlbumSize"><?php _e('Size', 'shashin') ?></label></td>
                    <td><select name="shashinAlbumSize" id="shashinAlbumSize">
                        <option value="xsmall"><?php _e('X-Small (72px)', 'shashin') ?></option>
                        <option value="small" selected="selected"><?php _e('Small (150px)', 'shashin') ?></option>
                        <option value="medium"><?php _e('Medium (300px)', 'shashin') ?></option>
                        <option value="large"><?php _e('Large (600px)', 'shashin') ?></option>
                        <option value="xlarge"><?php _e('X-Large (800px)', 'shashin') ?></option>
                        <option value="max"><?php _e('Max', 'shashin') ?></option>
                    </select></td>
                </tr>
                <tr>
                    <td><label for="shashinAlbumColumns"><?php _e('Columns', 'shashin') ?></label></td>
                    <td><select name="shashinAlbumColumns" id="shashinAlbumColumns">
                        <option value="max"><?php _e('Max', 'shashin'); ?></option>
                        <?php for ($i = 1; $i < 16; $i++) {
                            echo "<option value='$i'>$i</option>" . PHP_EOL;
                        } ?>
                    </select></td>
                    <td><label for="shashinAlbumCrop"><?php _e('Crop', 'shashin') ?></label></td>
                    <td><select name="shashinAlbumCrop" id="shashinAlbumCrop">
                        <option value="n"><?php _e('No', 'shashin'); ?></option>
                        <option value="y"><?php _e('Yes', 'shashin'); ?></option>
                    </select></td>
                </tr>
                <tr>
                    <td><label for="shashinAlbumOrder"><?php _e('Order', 'shashin') ?></label></td>
                    <td><select name="shashinAlbumOrder" id="shashinAlbumOrder" style="width: 150px;">
                        <option value="date"><?php _e('Date', 'shashin') ?></option>
                        <option value="random"><?php _e('Random', 'shashin') ?></option>
                        <option value="user"><?php _e('User (album thumbnails only)', 'shashin') ?></option>
                        <option value="title"><?php _e('Title (album thumbnails only)', 'shashin') ?></option>
                        <option value="location"><?php _e('Location (album thumbnails only)', 'shashin') ?></option>
                        <option value="count"><?php _e('Photo Count (album thumbnails only)', 'shashin') ?></option>
                        <option value="source"><?php _e('Source (album photos only)', 'shashin') ?></option>
                        <option value="filename"><?php _e('Filename (album photos only)', 'shashin') ?></option>
                    </select></td>
                    <td><label for="shashinAlbumPosition"><?php _e('Position', 'shashin') ?></label></td>
                    <td><select name="shashinAlbumPosition" id="shashinAlbumPosition">
                        <option value="center"><?php _e('Center', 'shashin'); ?></option>
                        <option value="left"><?php _e('Left', 'shashin'); ?></option>
                        <option value="right"><?php _e('Right', 'shashin'); ?></option>
                        <option value=""><?php _e('None', 'shashin'); ?></option>
                    </select></td>
                </tr>
                <tr>
                    <td><label for="shashinAlbumReverse"><?php _e('Reverse Order', 'shashin') ?></label></td>
                    <td><select name="shashinAlbumReverse" id="shashinAlbumReverse">
                            <option value="n"><?php _e('No', 'shashin') ?></option>
                            <option value="y"><?php _e('Yes', 'shashin') ?></option>
                    </select></td>
                    <td><label for="shashinAlbumCaption"><?php _e('Caption', 'shashin') ?></label></td>
                    <td><select name="shashinAlbumCaption" id="shashinAlbumCaption">
                        <option value="y"><?php _e('Yes', 'shashin') ?></option>
                        <option value="n"><?php _e('No', 'shashin') ?></option>
                    </select></td>
                </tr>
                <tr>
                    <td colspan="4"><input type="button" class="button" name="shashinAlbumInsert" id="shashinAlbumInsert" value="<?php _e('Insert shortcode', 'shashin') ?>"></td>
                </tr>
            </tbody>
        </table>
    </div>
</form>
