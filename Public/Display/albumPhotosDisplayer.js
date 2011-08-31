jQuery(document).ready(function($) {
    $(".shashinNext").click(function(event) {
        var dataToSend = {
            action: 'displayAlbumPhotos'
        };

        $.get(shashinAlbumPhotosDisplayer.ajaxurl, dataToSend, function(dataReceived) {
            $("#shashinGroup1").fadeOut('slow', function() {
                $("#shashinGroup1").replaceWith($(dataReceived).hide());
                $("#shashinGroup1").fadeIn('slow');
            })
        });

        event.preventDefault();
    });
});
