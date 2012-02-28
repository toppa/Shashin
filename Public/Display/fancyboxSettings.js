jQuery(document).ready(function($) {
    $("a.shashinFancybox").fancybox();

    function shashinFancyboxFormatTitle(title, currentArray, currentIndex, currentOpts) {
        return '<div class="shashinFancyboxCaptionTitle"><span class="shashinFancyboxCaptionClose">'
            + '<a href="javascript:;" onclick="jQuery.fancybox.close();">'
            + '<img src="' + shashinFancyboxSettings.fancyboxDir + 'closelabel.gif" />'
            + '</a></span>'
            + (title && title.length ? '<b>' + title + '</b>' : '' )
            + 'Image ' + (currentIndex + 1) + ' of ' + currentArray.length
            + '</div>';
    }

    $(".shashinFancybox").fancybox({
        'showCloseButton': false,
        'titlePosition': 'inside',
        'titleFormat': shashinFancyboxFormatTitle
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
     * which is smaller than ideal but acceptable
     */
    $(".shashinFancyboxVideo").fancybox({
        'padding': 0,
        'autoScale': false,
        'href': this.href,
        'type': 'swf',
        'swf': {
            'wmode': 'transparent',
            'allowfullscreen': 'true'
        }
    });
});




