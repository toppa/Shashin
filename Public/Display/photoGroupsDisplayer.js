jQuery(document).ready(function($) {
    $('.shashinPhotoGroups').delegate('.shashinNext', 'click', function(event) {
        var parentTableId = $(this).closest('table').attr('id');
        var tableIdParts = parentTableId.split('_');
        var currentTableId = '#shashinGroup_' + tableIdParts[1] + '_' + tableIdParts[2];
        var nextTableGroupCounter = parseInt(tableIdParts[1]) + 1;
        var nextTableId = '#shashinGroup_' + nextTableGroupCounter + '_' + tableIdParts[2];
        $(currentTableId).fadeOut('slow', function() {
            $(nextTableId).fadeIn('slow');
        })

        event.preventDefault();
    });

    $('.shashinPhotoGroups').delegate('.shashinPrevious', 'click', function(event) {
        var parentTableId = $(this).closest('table').attr('id');
        var tableIdParts = parentTableId.split('_');
        var currentTableId = '#shashinGroup_' + tableIdParts[1] + '_' + tableIdParts[2];
        var previousTableGroupCounter = parseInt(tableIdParts[1]) - 1;
        var previousTableId = '#shashinGroup_' + previousTableGroupCounter + '_' + tableIdParts[2];

        $(currentTableId).fadeOut('slow', function() {
            $(previousTableId).fadeIn('slow');
        })

        event.preventDefault();
    });

    $('.shashinThumbnailDiv').delegate('.shashinAlbumThumbLink', 'click', function(event) {
        var parentTable = $(this).closest('table');
        var parentTableIdParts = $(parentTable).attr('id').split('_');
        var linkIdParts = $(this).attr('id').split('_');

        if (linkIdParts[1] == 'img') {
            var albumTitle = $(this).children('img').attr('alt');
        }

        else if (linkIdParts[1] == 'caption') {
            var albumTitle = $(this).text();
        }

        var dataToSend = {
            action: 'displayAlbumPhotos',
            shashinAlbumId: linkIdParts[2],
            shashinParentTableId: parentTableIdParts[1],
            shashinParentAlbumTitle: albumTitle
        };

        $.get(shashinPhotoGroupsDisplayer.ajaxurl, dataToSend, function(dataReceived) {
            $(parentTable).fadeOut('slow', function() {
                $(parentTable).after($(dataReceived).hide());
                $('#shashinPhotosForSelectedAlbum').fadeIn('slow');
            })
        });

        event.preventDefault();
    });


    $('.shashinPhotoGroups').delegate('.shashinReturn', 'click', function(event) {
        var returnLinkIdParts = $(this).attr('id').split('_');
        var parentTableId = '#shashinGroup_' + returnLinkIdParts[1];

        $('#shashinPhotosForSelectedAlbum').fadeOut('slow', function() {
            $(parentTableId).fadeIn('slow');
            $('#shashinPhotosForSelectedAlbum').remove();
        })

        event.preventDefault();
    });
});
