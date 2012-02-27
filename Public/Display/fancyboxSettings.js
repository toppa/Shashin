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

    function shashinFancyboxVideo(title, width, height, href) {
        jQuery.fancybox({
            'padding': 0,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'title': title,
            'width': width,
            'height': height,
            'href': href,
            'type': 'swf',
            'swf': {
                'wmode': 'transparent',
                'allowfullscreen': 'true'
            }
        });

        return false;
    }
});