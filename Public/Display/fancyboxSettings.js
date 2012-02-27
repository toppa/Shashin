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

    $(".shashinFancyboxVideo").fancybox({
        'padding': 0,
        'autoScale': false,
        'width': 640,
        'height': 480,
        'href': this.href,
        'type': 'swf',
        'swf': {
            'wmode': 'transparent',
            'allowfullscreen': 'true'
        }
    });

});



