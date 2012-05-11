// The "-0" and "!!" below are for type casting, as all vars brought over
// from wp_localize_script come in as strings

jQuery(document).ready(function($) {
    if (shashinJs.imageDisplayer == 'fancybox') {
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

        $("a.shashinFancybox").fancybox();
        $("a.shashinFancyboxVideo").fancybox();

        var fancyboxInterval = shashinJs.fancyboxInterval - 0;

        if (fancyboxInterval > 0) {
            setInterval($.fancybox.next, fancyboxInterval);
        }

        $(".shashinFancybox").fancybox({
            'showCloseButton': false,
            'titlePosition': 'inside',
            'cyclic': !!(shashinJs.fancyboxCyclic-0),
            'transitionIn': shashinJs.fancyboxTransition,
            'transitionOut': shashinJs.fancyboxTransition,
            'onStart': setShashinFancyBoxCaption
        });

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
        $(".shashinFancyboxVideo").fancybox({
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
        });
    }

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
            $(parentTable).fadeOut('slow', function() {
				if($(".shashinPhotoGroups > div").length == 0){
					$(parentTable).after($(dataReceived).hide());
					$('#shashinAlbumPhotos_' + linkIdParts[2]).fadeIn('slow');

					// Fancybox isn't aware of photos not included in the initial page load
					// thank you http://jdmweb.com/resources/FancyBox (see comment reply to @pazul)
					if (shashinJs.imageDisplayer == 'fancybox') {
						$('#shashinAlbumPhotos_' + linkIdParts[2] + ' a.shashinFancybox').fancybox();
						$('#shashinAlbumPhotos_' + linkIdParts[2] + ' a.shashinFancyboxVideo').fancybox();
						$('#shashinAlbumPhotos_' + linkIdParts[2] + ' a.shashinFancybox').fancybox({
							'showCloseButton': false,
							'titlePosition': 'inside',
							'cyclic': !!(shashinJs.fancyboxCyclic-0),
							'transitionIn': shashinJs.fancyboxTransition,
							'transitionOut': shashinJs.fancyboxTransition,
							'onStart': setShashinFancyBoxCaption
						});

						$('#shashinAlbumPhotos_' + linkIdParts[2] + ' a.shashinFancyboxVideo').fancybox({
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
						});
					}
				}
            })
        });

        event.preventDefault();
    });

    $('.shashinPhotoGroups').delegate('.shashinReturn', 'click', function(event) {
        var returnLinkIdParts = $(this).attr('id').split('_');
        var parentTableId = '#shashinGroup_' + returnLinkIdParts[1];
        var selectedAlbumPhotosId = '#shashinAlbumPhotos_' + returnLinkIdParts[2]
        $(selectedAlbumPhotosId).fadeOut('slow', function() {
            $(parentTableId).fadeIn('slow');
            $(selectedAlbumPhotosId).remove();
        })

        event.preventDefault();
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
