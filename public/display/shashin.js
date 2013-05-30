jQuery(document).ready(function($) {
    if (shashinJs.imageDisplayer == 'prettyphoto') {
        // The "-0" and "!!" below are for type casting, as all vars brought over
        // from wp_localize_script come in as strings
        var prettyPhotoSettings = {
            theme: shashinJs.prettyPhotoTheme,
            overlay_gallery: !!(shashinJs.prettyPhotoOverlayGallery-0),
            default_width: shashinJs.prettyPhotoDefaultWidth-0,
            default_height: shashinJs.prettyPhotoDefaultHeight-0,
            show_title: !!(shashinJs.prettyPhotoShowTitle-0),
            autoplay_slideshow: !!(shashinJs.prettyPhotoAutoplaySlideshow-0),
            slideshow: shashinJs.prettyPhotoSlideshow-0
        }

        if (shashinJs.prettyPhotoShowSocialButtons == 0) {
            prettyPhotoSettings['social_tools'] = false;
        }

        $("a[rel^='prettyPhoto']").prettyPhoto(prettyPhotoSettings);
    }

    else if (shashinJs.imageDisplayer == 'fancybox') {
        var fancyboxSettings = {
            'showCloseButton': false,
            'titlePosition': 'inside',
            'cyclic': !!(shashinJs.fancyboxCyclic-0),
            'transitionIn': shashinJs.fancyboxTransition,
            'transitionOut': shashinJs.fancyboxTransition,
            'onStart': setShashinFancyBoxCaption
        }

        /* The problem with videos in groups with Fancybox:
         *
         * You can mix videos with images in groups this way:
         * http://groups.google.com/group/fancybox/browse_thread/thread/8c50659a082f9272
         *
         * And you can dynamically set the dimensions of videos this way:
         * http://groups.google.com/group/fancybox/browse_thread/thread/22843096d7870691
         *
         * But the two solutions are not compatible
         *
         * Not setting the width and height at all will do. Fancybox sets a default size,
         * Unfortunately the aspect ratio may be wrong :-(
         */
        var fancyboxVideoSettings = {
            'padding': 0,
            'autoScale': false,
            'href': this.href,
            'type': 'swf',
            'cyclic': !!(shashinJs.fancyboxCyclic-0),
            'width': shashinJs.fancyboxVideoWidth-0,
            'height': shashinJs.fancyboxVideoHeight-0,
            'transitionIn': shashinJs.fancyboxTransition,
            'transitionOut': shashinJs.fancyboxTransition,
            'swf': {
                'wmode': 'transparent',
                'allowfullscreen': 'true'
            }
        }

        // made this a separarte function as it is used below as well
        function setShashinFancyBoxCaption(currentArray, currentIndex, currentOpts) {
            var link = currentArray[ currentIndex ];
            var linkId = $(link).attr('id');
            var linkIdParts = linkId.split('_');
            var captionId = '#shashinFancyboxCaption_' + linkIdParts[1]

            if (linkIdParts[2]) {
                captionId = captionId + '_' + linkIdParts[2];
            }

            this.title = $(captionId).html();
            this.title = this.title.replace('<!-- comment for image counter --></div>', '');
            this.title = this.title + 'Image ' + (currentIndex + 1) + ' of ' + currentArray.length + '</div>';
        }

        var fancyboxInterval = shashinJs.fancyboxInterval - 0;

        if (fancyboxInterval > 0) {
            setInterval($.fancybox.next, fancyboxInterval);
        }

        $(".shashinFancybox").fancybox(fancyboxSettings);
        $(".shashinFancyboxVideo").fancybox(fancyboxVideoSettings);
    }

    $('.shashinTableCell').on('click', '.shashinAlbumThumbLink', function(event) {
        linkId = '#' + $(this).attr('id');
        // to prevent the photos showing up twice if the user double-clicks
        if ($(linkId).data('clicked')) {
            return false;
        }

        $(linkId).data('clicked', true);

        var parentTable = $(this).parents('.shashinThumbnailsTable');
        var parentTableIdParts = $(parentTable).attr('id').split('_');
        var parentTableStyle = $(parentTable).attr('style');
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
            shashinParentAlbumTitle: albumTitle,
            shashinParentTableStyle: parentTableStyle
        };

        $.get(shashinJs.ajaxUrl, dataToSend, function(dataReceived) {
            shashinScrollTo(parentTable);

            $(parentTable).fadeOut('slow', function() {
                $(parentTable).after($(dataReceived).hide());
                $('#shashinAlbumPhotos_' + linkIdParts[2]).fadeIn('slow');
                shashinAdjustThumbnailDisplay();

                if (shashinJs.imageDisplayer == 'prettyphoto') {
                    $('#shashinAlbumPhotos_' + linkIdParts[2] + " a[rel^='prettyphoto']").prettyPhoto(prettyPhotoSettings);
                }

                // Fancybox isn't aware of photos not included in the initial page load
                // thank you http://jdmweb.com/resources/FancyBox (see comment reply to @pazul)
                else if (shashinJs.imageDisplayer == 'fancybox') {
                     $('#shashinAlbumPhotos_' + linkIdParts[2] + ' a.shashinFancybox').fancybox(fancyboxSettings);
                    $('#shashinAlbumPhotos_' + linkIdParts[2] + ' a.shashinFancyboxVideo').fancybox(fancyboxVideoSettings);
                }
            })
        });

        event.preventDefault();
    });

    function shashinAdjustThumbnailDisplay() {
        $('.shashinThumbnailsTable').each(function() {
            $(this).imagesLoaded(function() {
                $(this).find('.shashinTableCell').each(function() {

                    if ($(this).css('max-width') == 'none') {
                        $(this).css('max-width', $(this).find('.shashinThumbnailImage').width() + 'px');
                    }

                    // To keep the thumbnail caption from overflowing the thumbnails,
                    // the containing div's max-width is set to the width of the image. But
                    // there are cases where the image width is unknown until after the
                    // page renders, so use the imagesLoaded plugin to set the div's
                    // max-width dynamically.
                    var $caption = $(this).find('.shashinThumbnailCaption');

                    if ($caption.height() > ($(this).height() * .3)) {
                        $caption.css('display', 'none');
                    }

                    else {
                        $caption.css('display', 'block');
                    }
                });


                // if the desired sizes for the images in a sample row exceed the current
                // width of the containing element by too much, remove the row markers
                // (i.e. display the images in a single column), so the images don't
                // shrink too much.
                var rowWidth = 0;

                $(this).find('.shashinTableRow:first').find('.shashinTableCell').each(function() {
                    rowWidth += parseInt($(this).css('max-width'));
                    rowWidth += 10; // assume some space for margins
                });

                if ((rowWidth * .8) > $(this).parents('.shashinPhotoGroups').parent().width()) {
                    $(this).find('.shashinTableRowClear:not(:last)').css('clear', 'none');
                    $(this).find('.shashinTableRow').css('clear', 'none');
                    $(this).find('.shashinTableCell').css('width', '100%');
                }

                else {
                    $(this).find('.shashinTableRowClear').css('clear', 'both');
                    $(this).find('.shashinTableRow').css('clear', 'both');
                    $(this).find('.shashinTableCell').each(function() {
                        $(this).css('width', $(this).data('original_width'));
                    });
                }
            });
        });
    }

    $('.shashinPhotoGroups').on('click', '.shashinNext', function(event) {
        var $parentTable = $(this).closest('.shashinThumbnailsTable');
        var tableIdParts = $parentTable.attr('id').split('_');
        var currentTableId = '#shashinGroup_' + tableIdParts[1] + '_' + tableIdParts[2];
        var nextTableGroupCounter = parseInt(tableIdParts[1]) + 1;
        var nextTableId = '#shashinGroup_' + nextTableGroupCounter + '_' + tableIdParts[2];
        shashinScrollTo($parentTable);
        $(currentTableId).fadeOut('slow', function() {
            $(nextTableId).fadeIn('slow');
        })

        event.preventDefault();
    });

    $('.shashinPhotoGroups').on('click', '.shashinPrevious', function(event) {
        var $parentTable = $(this).closest('.shashinThumbnailsTable');
        var tableIdParts = $parentTable.attr('id').split('_');
        var currentTableId = '#shashinGroup_' + tableIdParts[1] + '_' + tableIdParts[2];
        var previousTableGroupCounter = parseInt(tableIdParts[1]) - 1;
        var previousTableId = '#shashinGroup_' + previousTableGroupCounter + '_' + tableIdParts[2];
        shashinScrollTo($parentTable);
        $(currentTableId).fadeOut('slow', function() {
            $(previousTableId).fadeIn('slow');
        })

        event.preventDefault();
    });

    function shashinScrollTo(element) {
        $('html, body').animate({
            scrollTop: $(element).offset().top
        }, 1000);
    }

    $('.shashinPhotoGroups').on('click', '.shashinReturn', function(event) {
        $('.shashinAlbumThumbLink').data('clicked', false); // ok to click an album thumbnail again
        var returnLinkIdParts = $(this).attr('id').split('_');
        var parentTableId = '#shashinGroup_' + returnLinkIdParts[1];
        var selectedAlbumPhotosId = '#shashinAlbumPhotos_' + returnLinkIdParts[2];

        $(selectedAlbumPhotosId).fadeOut('slow', function() {
            $(parentTableId).fadeIn('slow');
            shashinScrollTo($(parentTableId));
            $(selectedAlbumPhotosId).remove();
        })

        event.preventDefault();
    });

    // Keep this near the end of the file so it doesn't interfere with "on"
    // delegation calls above.
    shashinAdjustThumbnailDisplay();

    $(window).resize(function() {
        shashinAdjustThumbnailDisplay();
    });

    // for backward compatibility with Shashin 2 album links
    var shashinAlbumId = shashinGetParameterByName('shashin_album_key');

    if (shashinAlbumId && !isNaN(shashinAlbumId)) {
        var shashinSelectedAlbum = '#shashinAlbumThumbLink_img_' + shashinAlbumId;
        $(shashinSelectedAlbum).click();
    }

    // thank you - http://stackoverflow.com/questions/4548487/jquery-read-query-string
    function shashinGetParameterByName(name) {
        name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
        var regexS = "[\\?&]"+name+"=([^&#]*)";
        var regex = new RegExp(regexS);
        var results = regex.exec(window.location.href);
        if (results == null)
            return "";
        else
            return decodeURIComponent(results[1].replace(/\+/g, " "));
    }
});
