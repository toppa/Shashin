<script type="text/javascript">
jQuery(document).ready(function($) {
    function shashinLoadPhotos() {
        $('#shashinMediaMenuLoading').show();
        var adminAjaxUrl = '<?php echo admin_url("admin-ajax.php"); ?>';
        var filterRoot = $('#shashinMediaMenuFilter').get();
        var albumId = $('#shashinFilterAlbum', filterRoot).val();
        var order = $('#shashinFilterOrder', filterRoot).val();
        var reverse = $('#shashinFilterReverse', filterRoot).attr('checked') ? 'y' : 'n';
        var page = shashinGetCurrentPage();
        var dataToSend = {
            action: 'shashinGetPhotosForMediaMenu',
            shashinAlbumId: albumId,
            shashinOrder: order,
            shashinReverse: reverse,
            shashinPage: page
        };

        $.post(adminAjaxUrl, dataToSend, function(dataReceived) {
            $('#shashinMediaMenuPhotoGrid img').remove();
            $('#shashinMediaMenuNav span').text(dataReceived['page'] + '/' + dataReceived['totalPages']);
            for (i in dataReceived['photos']) {
                var imgTag = shashinWriteImgTag(
                    'shashinMediaMenuThumb_' + dataReceived['photos'][i]['id'],
                    dataReceived['photos'][i]['contentUrl'] + '?imgmax=72&crop=1',
                    dataReceived['photos'][i]['description'],
                    72,
                    72
                );
                $('#shashinMediaMenuPhotoGrid').append(imgTag);
            }
            $('#shashinMediaMenuLoading').hide();
        }, 'json');
    }

    function shashinWriteImgTag(id, src, description, width, height) {
        return '<img id="' + id + '"'
            + ' src="' + src + '"'
            + ' alt="' + description + '"'
            + ' title="' + description + '"'
            + ' width="' + width + '"'
            + ' height="' + height + '" />';
    }

    function shashinGetCurrentPage() {
        var page = $('#shashinMediaMenuNav span').text().split('/');
        page[0] = parseFloat(page[0]);
        page[1] = parseFloat(page[1]);
        return page[0];
    }

    function shashinGetTotalPages() {
        var page = $('#shashinMediaMenuNav span').text().split('/');
        page[0] = parseFloat(page[0]);
        page[1] = parseFloat(page[1]);
        return page[1];
    }

    $('#shashinMediaMenuSelectedPhotos img').live('click', function() {
        $(this).remove();
    })

    $('#shashinMediaMenuPhotoGrid img').live('mouseover', function(e) {
        var offset = $('#shashinMediaMenuPhotoGrid').offset();
        offset.left = offset.left + 2;
        offset.top = offset.top + 2;
        if (e.clientX < (offset.left + 304)) offset.left = offset.left + 304;
        var imgSrc = $(this).attr('src').replace('?imgmax=72&crop=1', '?imgmax=288'); // for Picasa
        imgSrc = imgSrc.replace('/mini/', '/thumb/'); // for Twitpic
        var imgTag = shashinWriteImgTag(
            'shashinMediaMenuThumbPreview_' + $(this).attr('id').replace('shashinMediaMenuThumb_', ''),
            imgSrc,
            $(this).attr('alt')
        )

        $('#shashinMediaMenuPhotoPreview')
            .html(imgTag)
            .css('top', offset.top)
            .css('left', offset.left)
            .show();
    })

    $('#shashinMediaMenuPhotoGrid img').live('mouseout', function() {
        $('#shashinMediaMenuPhotoPreview').hide();
    });

    $('#shashinMediaMenuPhotoGrid img').live('click', function() {
        var imgTag = shashinWriteImgTag(
            'shashinMediaMenuThumbSelected_' + $(this).attr('id').replace('shashinMediaMenuThumb_', ''),
            $(this).attr('src'),
            $(this).attr('alt'),
            $(this).attr('width'),
            $(this).attr('height')
        )
        $('#shashinMediaMenuSelectedPhotos').append(imgTag);
    });

    $('#shashinInsertShortcode').click(function() {
        var selectedIds = new Array;
        $('#shashinMediaMenuSelectedPhotos img').each(function() {
            selectedIds.push($(this).attr('id').replace('shashinMediaMenuThumbSelected_',''));
        });

        if (selectedIds.length == 0) return alert('<?php _e('No photos selected', 'shashin') ?>');
        parent.send_to_editor('[shashin type="photo"'
            + ' id="' + selectedIds.join(',') + '"'
            + ' size="' + $('#shashinSize').val() + '"'
            + ' columns="' + $('#shashinColumns').val() + '"'
            + ' order="user"'
            +  (($('#shashinCaption').val() == 'y') ? (' caption="' + $('#shashinCaption').val() + '"') : '')
            +  (($('#shashinPosition').val() == '') ? '' : (' position="' + $('#shashinPosition').val() + '"'))
            +  (($('#shashinCrop').val() == 'y') ? (' crop="' + $('#shashinCrop').val() + '"') : '')
            + ']'
        );
    });

    $('#shashinFilterUpdate').click(function() {
        shashinLoadPhotos();
    });

    $('#shashinPreviousPage').click(function() {
        page = shashinGetCurrentPage() - 1;
        totalPages = shashinGetTotalPages();
        if (page < 1) return;
        $('#shashinMediaMenuNav span').text(page + '/' + totalPages);
        shashinLoadPhotos();
    });

    $('#shashinNextPage').click(function() {
        page = shashinGetCurrentPage() + 1;
        totalPages = shashinGetTotalPages();
        if (page > totalPages) return;
        $('#shashinMediaMenuNav span').text(page + '/' + totalPages);
        shashinLoadPhotos();
    });

    shashinLoadPhotos();
})
</script>

<form id="shashinMediaMenu" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <div id="shashinMediaMenuFilter">
        <div><?php _e('Album', 'shashin') ?><br />
            <select style="width: 100px;" name="shashinFilterAlbum" id="shashinFilterAlbum">
                <option value="0"><?php _e('All albums', 'shashin') ?></option>
                <?php foreach ($albums as $album): ?>
                    <option value="<?php echo $album->id ?>"><?php echo $album->title ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div><?php _e('Order', 'shashin') ?><br />
            <select style="width: 100px" name="shashinFilterOrder" id="shashinFilterOrder">
                <option value="date"><?php _e('Date Taken', 'shashin') ?></option>
                <option value="id"><?php _e('Shashin ID', 'shashin') ?></option>
                <option value="filename"><?php _e('Filename', 'shashin') ?></option>
                <option value="source"><?php _e('Source order', 'shashin') ?></option>
            </select>
        </div>

        <div><?php _e('Reverse<br />order?', 'shashin') ?>
            <input type="checkbox" checked="checked" name="shashinFilterReverse" id="shashinFilterReverse" value="1" />
        </div>

        <div>&nbsp;<br />
            <input type="button" class="button" name="shashinFilterUpdate" id="shashinFilterUpdate" value="<?php _e('Update', 'shashin') ?>" />
        </div>

        <div id="shashinMediaMenuNav"><br />
            <input type="button" class="button" name="shashinPreviousPage" id="shashinPreviousPage" value="<?php _e('Previous', 'shashin') ?>" />
                <span>1/1</span>
            <input type="button" class="button" name="shashinNextPage" id="shashinNextPage" value="<?php _e('Next', 'shashin') ?>" />
        </div>
    </div>
    <div id="shashinMediaMenuLoading">
        <?php _e('Loading photos', 'shashin') ?>
        <img src="<?php echo $loaderUrl ?>" alt="Loading..." width="220" height="19" />
    </div>
    <div id="shashinMediaMenuPhotoPreview">&nbsp;</div>
    <div id="shashinMediaMenuPhotoGrid"></div>

    <div id="shashinMediaMenuSelectedPhotos"><h3><?php _e('Selected photos', 'shashin'); ?></h3></div>
    <div id="shashinMediaMenuShortcodeCriteria">
        <h3><?php _e('Shortcode attributes', 'shashin') ?></h3>
        <table id="shashinMediaMenuShortcodeCriteriaTable">
            <tbody>
                <tr>
                    <td><label for="shashinSize"><?php _e('Size', 'shashin') ?></label></td>
                    <td><select name="shashinSize" id="shashinSize">
                        <option value="xsmall"><?php _e('X-Small (72px)', 'shashin') ?></option>
                        <option value="small" selected="selected"><?php _e('Small (150px)', 'shashin') ?></option>
                        <option value="medium"><?php _e('Medium (300px)', 'shashin') ?></option>
                        <option value="large"><?php _e('Large (600px)', 'shashin') ?></option>
                        <option value="xlarge"><?php _e('X-Large (800px)', 'shashin') ?></option>
                        <option value="max"><?php _e('Max', 'shashin') ?></option>
                    </select></td>
                    <td><label for="shashinColumns"><?php _e('Columns', 'shashin') ?></label></td>
                    <td><select name="shashinColumns" id="shashinColumns">
                        <option value="max"><?php _e('Max', 'shashin'); ?></option>
                        <?php for ($i = 1; $i < 16; $i++) {
                            echo "<option value='$i'>$i</option>" . PHP_EOL;
                        } ?>
                    </select></td>
                </tr>
                <tr>
                    <td><label for="shashinCaption"><?php _e('Caption', 'shashin') ?></label></td>
                    <td><select name="shashinCaption" id="shashinCaption">
                        <option value="n"><?php _e('No', 'shashin'); ?></option>
                        <option value="y"><?php _e('Yes', 'shashin'); ?></option>
                    </select></td>
                    <td><label for="shashinPosition"><?php _e('Position', 'shashin') ?></label></td>
                    <td><select name="shashinPosition" id="shashinPosition">
                        <option value="center"><?php _e('Center', 'shashin'); ?></option>
                        <option value="left"><?php _e('Left', 'shashin'); ?></option>
                        <option value="right"><?php _e('Right', 'shashin'); ?></option>
                        <option value=""><?php _e('None', 'shashin'); ?></option>
                    </select></td>
                </tr>
                <tr>
                    <td><label for="shashinCrop"><?php _e('Crop', 'shashin') ?></label></td>
                    <td><select name="shashinCrop" id="shashinCrop">
                        <option value="n"><?php _e('No', 'shashin'); ?></option>
                        <option value="y"><?php _e('Yes', 'shashin'); ?></option>
                    </select></td>
                    <td colspan="2"><input type="button" class="button" name="shashinInsertShortcode" id="shashinInsertShortcode" value="<?php _e('Insert shortcode', 'shashin') ?>" /></td>
                </tr>
            </tbody>
        </table>
    </div>
</form>


